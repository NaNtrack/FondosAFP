<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    public function isAuthorized($user)
    {
        if (!empty($user['role']) && $user['role'] !== 'admin') {
            if (strpos($this->request->url, 'remove') > 0) {
                return false;
            }
        }

        return true;
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['logout', 'googleLogin', 'googleCallback']);
    }

    /**
     * Login method
     *
     * @return \Cake\Network\Response|null
     */
    public function login()
    {
        if ($this->Auth->user()) {
            $this->Flash->error(__('Ya has iniciado sesión'));
            return $this->redirect($this->Auth->redirectUrl());
        }
        $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }

    /**
     * Logout method
     *
     * @return \Cake\Network\Response|null
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function savePreferences()
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $data = $this->request->data;
            $this->loadModel('Preferences');

            $saved = $this->Preferences->savePreferences(
                $this->Auth->user('id'),
                $data['afp'],
                $data['fondo']);

            if ($saved) {
                $this->reloadSession();
                $result = [
                    'ok' => true
                ];
            }
        } catch (\Exception $ex) {
            $result = [
                'ok' => false,
                'message' => $ex->getMessage()
            ];
        }
        $this->set(compact('result'));
    }

    public function saveEmail()
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $data = $this->request->data;

            $saved = $this->Users->saveEmail(
                $this->Auth->user('id'),
                $data['email']);

            if ($saved) {
                $this->reloadSession();
                $result = [
                    'ok' => true
                ];
            }
        } catch (\Exception $ex) {
            $result = [
                'ok' => false,
                'message' => $ex->getMessage()
            ];
        }
        $this->set(compact('result'));
    }

    public function updateAfp()
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $data = $this->request->data;
            $Preferences = \Cake\ORM\TableLocator::get('preferences');
            $saved = $Preferences->updateAfp(
                $this->Auth->user('id'),
                $data['afpId']);

            if ($saved) {
                $this->reloadSession();
                $result = [
                    'ok' => true
                ];
            }
        } catch (\Exception $ex) {
            $result = [
                'ok' => false,
                'message' => $ex->getMessage()
            ];
        }
        $this->set(compact('result'));
    }

    public function remove($userId = null)
    {
        if (empty($userId)) {
            $this->Flash->error(__('Debe indicar el id del usuario'));
            $this->redirect($this->referer());
        }

        $this->Users->removeUser($userId);
        $this->Flash->success(__('Se ha eliminado el usuario'));
        $this->redirect($this->referer());
    }
}
