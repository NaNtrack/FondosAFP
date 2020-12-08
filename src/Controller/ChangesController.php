<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Changes Controller
 *
 * @property \App\Model\Table\ChangesTable $Changes
 */
class ChangesController extends AppController
{

    public function isAuthorized($user)
    {
        return true;
    }

    /**
     * Carga los movimientos de la cuenta obligatoria desde un archivo PDF
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        if ($this->request->is(['patch', 'post', 'put']))
        {
            if (!empty($this->request->data['upload']))
            {
                try {
                    $afpId = $this->Auth->user('preference.afp_id');
                    $this->loadModel('Afps');
                    $afp = $this->Afps->findById($afpId)->first();

                    $this->loadModel('Changes');
                    $movimientos = $this->Changes->cargarMovimientos(
                        $this->Auth->user('id'),
                        $this->request->data['upload']['tmp_name'],
                        $afp->name);
                    $cambios = $this->Changes->guardarCambios(
                        $this->Auth->user('id'),
                        $afp->name,
                        $movimientos['movimientos']);
                    $this->set(compact('cambios'));

                    $this->Flash->success(__('Se ha cargado el certificado de cotizaciones obligatorias'));
                    return $this->redirect(['controller' => 'Changes', 'action' => 'index', '?' => ['t' =>'cargar']]);
                } catch (\Exception $ex) {
                    $this->Flash->error($ex->getMessage());
                    //$this->Changes->changesUploadErrorEmail($this->Auth->user('id'), $this->request->data['upload']['tmp_name']);

                    return $this->redirect(['controller' => 'Changes', 'action' => 'index', '?' => ['t' =>'cargar']]);
                }
            }
            elseif (!empty($this->request->data['form']) && $this->request->data['form'] === 'add-history')
            {
                try {
                    $afpId = $this->Auth->user('preference.afp_id');

                    $this->loadModel('Changes');
                    $this->Changes->addChange(
                        $this->Auth->user('id'),
                        $afpId,
                        $this->request->data['fecha'],
                        $this->request->data['desde'],
                        $this->request->data['hasta']);

                    $this->Flash->success(__('Se ha cargado el cambio de fondo'));
                    return $this->redirect(['controller' => 'Changes', 'action' => 'index', '?' => ['t' =>'history']]);
                } catch (\Exception $ex) {
                    $this->Flash->error($ex->getMessage());

                    return $this->redirect(['controller' => 'Changes', 'action' => 'index', '?' => ['t' => 'history']]);
                }
            }
        }

        try
        {//for circleci mysql 5.7 issue, remove when cakephp/cakephp >= 3.3.6
                $this->paginate = [
                    'limit' => 100,
                    'contain' => ['FromFondos', 'ToFondos', 'Afps']
                ];

                $rows = $this->paginate($this->Changes
                    ->findByUserId($this->Auth->user('id'),[
                      'contains' => ['FromFondos', 'ToFondos'],
                    ])
                    ->order(['change_dt' => 'DESC', 'FromFondos.name' => 'ASC', 'ToFondos.name' => 'ASC']))
                    ->toArray();

                $changes = [];
                foreach ($rows as $i => $c) {
                    $changes[$c->cuota_dt->i18nFormat("yyyy-MM-dd")][$c->afp->name][$c->from_fondo->name][$c->to_fondo->name] = [
                        'from_value' => $c->from_value,
                        'to_value' => $c->to_value,
                        'profits_loss' => $c->profits_loss,
                        'performance' => $c->performance,
                        'source' => $c->source,
                        'id' => $c->id,
                    ];
                }

                $changesASC = $this->Changes
                    ->findByUserId($this->Auth->user('id'))
                    ->order(['cuota_dt' => 'ASC'])
                    ->group(['afp_id', 'change_dt', 'from_fondo_id', 'id'])
                    ->toArray();

                $cambiosData = [];
                $cambiosLabels = [];
                $cambiosValues = [];
                $sum = 0;
                foreach ($changesASC as $i => $c) {
                    $sum += $c->profits_loss;
                    $cambiosData[] = [
                      'x' => $i,
                      'y' => $sum,
                    ];
                    $cambiosLabels[] = $c->cuota_dt->format('Y-m-d');
                    $cambiosValues[] = $c->profits_loss;
                }

                $afpId = $this->Auth->user('preference.afp_id');
                $this->loadModel('Afps');
                $afp = $this->Afps->findById($afpId)->first();
                $lastChanges = $this->Changes->getLatestChange($this->Auth->user('id'), $afpId);
                if (!empty($lastChanges)) {
                    $this->loadModel('Cuotas');
                    $cuotas = $ganancias = [];
                    $gananciaTotal = 0;
                    foreach ($lastChanges as $lastChange) {
                        $c = $this->Cuotas->getLatestValue($afpId, $lastChange['to_fondo_id']);
                        $g = $c->valor - $lastChange['to_value'];
                        $cuotas[] = $c;
                        $ganancias[] = $g;
                        $gananciaTotal += $g;
                    }

                    $cambiosData[] = [
                        'x' => count($cambiosData)+1,
                        'y' => $cambiosData[count($cambiosData)-1]['y'] + $gananciaTotal
                    ];
                    $cambiosLabels[] = $cuotas[0]['fecha']->format('Y-m-d');
                    $cambiosValues[] = $gananciaTotal;

                    $this->set(compact('cuotas', 'ganancias', 'lastChanges'));
                }

                $this->loadModel('Afps');
                $this->loadModel('Fondos');
                $afps = $this->Afps->find('active');
                $fondos = $this->Fondos->find('active');
                $this->set(compact('afps', 'fondos'));
                $this->set(compact('changes', 'changesTmp', 'cambiosData', 'cambiosLabels', 'cambiosValues'));
                $this->set('title', 'Cambios');
        }
        catch(\Exception $ex)
        {
            var_dump($ex->getMessage(). PHP_EOL);
            foreach (explode(PHP_EOL, $ex->getTraceAsString()) as $trace) {
                echo $trace.PHP_EOL;
            }
        }
    }

    public function remove()
    {
        $this->response->type('application/json');
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->layout('ajax');

        try {
            $change = $this->Changes->find()
                ->where([
                    'user_id' => $this->Auth->user('id'),
                    'id' => $this->request->data['id'],
                ])
                ->first();

            if (!empty($change)) {
                $this->Changes->delete($change);
                $this->Changes->recalculateMetrics($this->Auth->user('id'));
            }

            $response = [
                'ok' => true
            ];
        } catch (\Exception $ex) {
            $response = [
                'ok' => false,
                'message' => $ex->getMessage()
            ];
        }

        $this->set(compact('response'));
        unset($this->viewVars['_serialize']);
    }
}
