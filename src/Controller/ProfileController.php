<?php

namespace App\Controller;

use App\Controller\AppController;

class ProfileController extends AppController
{
    public function isAuthorized($user)
    {
        return true;
    }
    
    public function index($uuid)
    {
        
    }
}
