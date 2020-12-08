<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class ReportsController extends AppController
{
    public function isAuthorized($user)
    {
        if ($user['role'] !== 'admin') {
            $this->Flash->error(__('No se encuentra autorizado para ver este recurso'));
        }
        return $user['role'] == 'admin';
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function index()
    {

    }

    public function reporteDeUsuarios()
    {
        $this->loadModel('Users');
        $users = $this->Users->getReport();
        $this->loadModel('Afps');
        $this->loadModel('Fondos');
        $afps = $this->Afps->find('active');
        $fondos = $this->Fondos->find('active');
        $this->set(compact('afps', 'fondos'));
        $this->set(compact('users'));
    }

    public function rentabilidadDeMiFondoUltimos12Meses()
    {

    }

    public function rentabilidadDeMiAfpVsOtrasAfpsUltimos12Meses()
    {

    }
}
