<?php
namespace App\Controller;

use App\Controller\AppController;

class RankingController extends AppController
{
    public function isAuthorized($user)
    {
        return ($user['role'] === 'admin');
    }

    public function index()
    {
        $this->loadModel('Rankings');
        $ranking = $this->Rankings->getRanking();
        $this->set(compact('ranking'));
    }

}
