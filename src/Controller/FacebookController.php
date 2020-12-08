<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class FacebookController extends AppController
{

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['login', 'callback']);
    }

    /**
     * Facebook login method
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
        try {
            if (session_status() == PHP_SESSION_NONE) {
                @session_start();
            }

            $fb = new \Facebook\Facebook(Configure::read('Facebook'));
            $helper = $fb->getRedirectLoginHelper();
            $url = $helper->getLoginUrl(Router::url(['action' => 'callback'], true));
            $this->redirect($url);
        } catch (\Exception $ex) {
            $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }
    }

    /**
     * Facebook callback method
     *
     * @return \Cake\Network\Response|null
     */
    public function callback()
    {
        if ($this->Auth->user()) {
            $this->Flash->error(__('You are already logged'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        $this->autoRender = false;
        try {
            $fb = new \Facebook\Facebook(Configure::read('Facebook'));
            $helper = $fb->getRedirectLoginHelper();
            $_SESSION['FBRLH_state']=$_GET['state'];
            $accessToken = $helper->getAccessToken();


            if(!isset($accessToken)){
                $this->Flash->error(__('Error when login with facebook, unable to get access token'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
            }

            $oAuth2Client = $fb->getOAuth2Client();
            $tokenMetadata = $oAuth2Client->debugToken($accessToken);
            $tokenMetadata->validateAppId(Configure::read('Facebook.app_id'));
            $tokenMetadata->validateExpiration();

            if (! $accessToken->isLongLived()) {
                // Exchanges a short-lived access token for a long-lived one
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            }
            $_SESSION['fb_access_token'] = (string) $accessToken;

            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,first_name,last_name,email', $accessToken);


            $user = $response->getGraphUser();
            if (empty($user['email'])) {
                $user['email'] = $user['id'].'@facebook.com';
            }
            $image = "https://graph.facebook.com/".$user['id']."/picture?width=200";

            $this->loadModel('Users');
            if (!empty($user)) {
                $result = $this->Users
                    ->query()
                    ->where(['social_id' => $user['id']])
                    ->contain(['Preferences'])
                    ->first();
                if (!empty( $result )) {
                    $this->Users->query()
                        ->update()
                        ->set([
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'social_id' => $user['id'],
                            'social_source' => 'facebook',
                            'picture' => $image,
                            'verified' => 1
                        ])
                        ->where(['id' => $result->id])
                        ->execute();
                    $this->Users->setLoginDates($result->toArray());
                    $this->Auth->setUser($result->toArray());

                    return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
                } else {
                    $data = array();
                    $data['email'] = $user['email'];
                    $data['first_name'] = $user['first_name'];
                    $data['last_name'] = $user['last_name'];
                    $data['social_id'] = $user['id'];
                    $data['social_source'] = 'facebook';
                    $data['picture'] = $image;
                    $data['verified'] = 1;
                    $userData = $this->Users->newEntity($data);
                    if ($this->Users->save( $userData )) {
                        $data['id'] = $userData->id;
                        $userData['preference'] = null;
                        $this->Users->setLoginDates($data);
                        $this->Auth->setUser($userData->toArray());

                        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
                    } else {
                        $this->Flash->error(__('Error when saving the user data'));
                        return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
                    }
                }
            } else {
                $this->Flash->error(__('Error when login with facebook, cant get user data'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
            }
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            $this->Flash->error(__('Error when login with facebook, Graph returned an error: ' . $e->getMessage()));
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            $this->Flash->error(__('Error when login with facebook, Facebook SDK returned an error ' . $e->getMessage()));
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        } catch (\Exception $e) {
            $this->Flash->error(__('Error when login with facebook: '.$e->getMessage()));
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }
    }
}
