<?php

namespace App\Controller;

class HomepageController extends AbstractController
{
    public function run()
    {
        $user = $this->getUser();

        var_dump($user); // logged in

        return $this->render('HomepageTemplate');
    }
}