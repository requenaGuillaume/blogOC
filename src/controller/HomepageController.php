<?php

namespace App\Controller;

final class HomepageController extends AbstractController
{
    public function run()
    {
        return $this->render('HomepageTemplate');
    }
}