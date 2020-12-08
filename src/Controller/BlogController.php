<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Utility\Inflector;

class BlogController extends AppController
{
    public function isAuthorized($user)
    {
        if (!empty($user['role'])) {
            if ($user['role'] !== 'admin') {
                return false;
            } elseif (strpos($this->request->url, 'add') > 0) {
                return true;
            }
        }

        return true;
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['index', 'view']);
        $this->loadModel('Articles');
    }

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Flash'); // Include the FlashComponent
    }

    public function index()
    {
        $articles = $this->Articles->find('all', [
            'order' => ['Articles.created' => 'DESC']
        ]);
        $this->set(compact('articles'));
    }

    public function view($id = null)
    {
        $article = $this->Articles->get($id);
        $this->set(compact('article'));
    }

    public function add()
    {
        $article = $this->Articles->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $data['slug'] = Inflector::slug($data['title']);
            $article = $this->Articles->patchEntity($article, $data);
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Se ha agregado el art&iacute;culo'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo agregar el art&iacute;culo.'));
        }
        $this->set('article', $article);
    }
}
