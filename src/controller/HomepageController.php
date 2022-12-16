<?php

namespace App\Controller;

class HomepageController extends AbstractController
{
    public function run()
    {
        return $this->render('HomepageTemplate');
    }
}