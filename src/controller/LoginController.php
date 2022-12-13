<?php

namespace App\Controller;

class LoginController extends AbstractController
{
    public function run()
    {
        

        return $this->render('LoginTemplate');
    }
}