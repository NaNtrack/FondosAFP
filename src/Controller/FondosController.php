<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class FondosController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['index']);
    }

    public function index($afp_api = null, $fondo_api = null, $type_api = null)
    {
        $this->set('title', 'Fondos');
        $this->loadModel('Afps');
        $this->loadModel('Fondos');
        $afps = $this->Afps->find('active');
        $fondos = $this->Fondos->find('active');
        $this->set(compact('afps', 'fondos'));
        $this->set(compact('afp_api', 'fondo_api', 'type_api'));
    }
}
