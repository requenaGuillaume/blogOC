<?php

namespace App\Controller;

final class LogoutController extends AbstractController
{
    public function run()
    {
        $_SESSION = [];
        session_destroy();

        session_start();

        $this->addFlash('success', 'You\'ve been disconnected');
    
        $this->redirect('http://blogoc/?page=login');
    }
}