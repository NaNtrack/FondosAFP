<?php
namespace App\Controller;

use App\Controller\AppController;

class DashboardController extends AppController
{
    public function isAuthorized($user)
    {
        return true;
    }

    public function index()
    {
        $metrics = [
          'consistencia' => 0,
          'rendimiento' => 0,
          'rentabilidad' => 0,
          'ganancia' => null,
        ];
        $this->loadModel('Changes');

        if ($this->request->is('post')) {
            $form = $this->request->data['form'];
            switch ($form) {
                case 'settings':
                    $this->loadModel('Preferences');
                    $this->Preferences->updatePreferences($this->Auth->user('id'), $this->request->data);
                    $this->reloadSession();
                    return $this->redirect(['controller' => 'Dashboard', 'action' => 'index', '#' => $form]);
                default:
                    break;
            }
        }

        $preference = $this->Auth->user('preference');

        $afpId = $preference['afp_id'];
        $lastChanges = $this->Changes->getLatestChange($this->Auth->user('id'), $afpId);
        if (!empty($lastChanges)) {
            $this->loadModel('Cuotas');
            foreach ($lastChanges as $lastChange) {
                $cuota = $this->Cuotas->getLatestValue($afpId, $lastChange['to_fondo_id']);
                $metrics['ganancia'][] = [
                    'valor' => $cuota->valor - $lastChange['to_value'],
                    'lastChange' => $lastChange
                ];
                if ($lastChange['to_fondo_id'] != $preference['fondo_id'] && $lastChange['from_fondo_id'] != $lastChange['to_fondo_id']) {
                    $this->loadModel('Preferences');
                    $this->Preferences->savePreferences(
                        $this->Auth->user('id'),
                        $preference['afp_id'],
                        $lastChange['to_fondo_id']);
                        $this->reloadSession();
                    $preference['fondo_id'] = $lastChange['to_fondo_id'];
                }
            }
        }
        $metrics['rendimiento'] = $this->Changes->getPerformance($this->Auth->user('id'), $this->Auth->user('preference.afp_id'));
        $metrics['rentabilidad'] = $this->Changes->getRentability($this->Auth->user('id'), $this->Auth->user('preference.afp_id'));
        $metrics['consistencia'] = $this->Changes->getConsistency($this->Auth->user('id'), $this->Auth->user('preference.afp_id'));

        $this->loadModel('Afps');
        $this->loadModel('Fondos');
        $afps = $this->Afps->find('active');
        $fondos = $this->Fondos->find('active');
        $this->set(compact('afps', 'fondos'));

        $this->set(compact('metrics'));
        $this->set('preference', $preference);
        $this->set('title', 'Dashboard');
    }
}
