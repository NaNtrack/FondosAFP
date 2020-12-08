<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class GoogleController extends AppController
{

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['login', 'callback']);
    }

    /**
     * Google login method
     *
     * @return \Cake\Network\Response|null
     */
    public function login()
    {
        if ($this->Auth->user()) {

            $this->Flash->error(__('Ya ha iniciado sesión'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        $this->autoRender = false;
        $client = new \Google_Client(Configure::read('Google'));

        $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));

        $client->setApprovalPrompt('auto');
        $url = $client->createAuthUrl();
        $this->redirect($url);
    }

    /**
     * Google callback method
     *
     * @return \Cake\Network\Response|null
     */
    public function callback()
    {
        if ($this->Auth->user()) {

            $this->Flash->error(__('Ya ha iniciado sesi&oacute;n'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        $this->autoRender = false;
        $client = new \Google_Client(Configure::read('Google'));
        $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));
        $client->setApprovalPrompt('auto');

        $oauth2 = new \Google_Service_Oauth2($client);

        if(isset($_GET['code'])) {
            $client->authenticate($_GET['code']); // Authenticate
            $_SESSION['access_token'] = $client->getAccessToken(); // get the access token here
        }

        if(isset($_SESSION['access_token'])) {
            $client->setAccessToken($_SESSION['access_token']);
        }

        if ($client->getAccessToken()) {

            $this->loadModel('Users');
            $_SESSION['access_token'] = $client->getAccessToken();
            $user = $oauth2->userinfo->get();
            try {
                if (!empty($user)) {
                    $result = $this->Users
                        ->findByEmail($user['email'])
                        ->contain(['Preferences'])
                        ->first();
                    if (!empty( $result )) {
                        $this->Users->query()
                            ->update()
                            ->set([
                                'first_name' => $user['given_name'],
                                'last_name' => $user['family_name'],
                                'social_id' => $user['id'],
                                'social_source' => 'google',
                                'picture' => $user['picture'],
                                'verified' => 1
                            ])
                            ->where(['id' => $result->id])
                            ->execute();

                        $this->Users->setLoginDates($result->toArray());
                        $this->Auth->setUser($result->toArray());

                        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']); // '/'.$homepage
                    } else {
                        $data = array();
                        $data['email'] = $user['email'];
                        $data['first_name'] = $user['given_name'];
                        $data['last_name'] = $user['family_name'];
                        $data['social_id'] = $user['id'];
                        $data['social_source'] = 'google';
                        $data['picture'] = $user['picture'];
                        $data['verified'] = 1;

                        $userData = $this->Users->newEntity($data);
                        if ($this->Users->save( $userData )) {
                            $data['id'] = $userData->id;
                            $data['preference'] = null;
                            $userData['preference'] = null;
                            $this->Users->setLoginDates($data);
                            $this->Auth->setUser($data);

                            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']); // $this->Auth->redirectUrl()
                        } else {
                            $this->Flash->error(__('Ha ocurrido un error al iniciar sesión'));
                            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
                        }
                    }
                } else {
                    $this->Flash->error(__('Error when login with google, cant get user data'));
                    return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
                }
            } catch (\Exception $e) {
                $this->Flash->error(__('Error when login with google: '.$e->getMessage()));
                return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
            }
        } else {
            $this->Flash->error(__('Error when login with google, cant get access token'));
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }
    }
}
