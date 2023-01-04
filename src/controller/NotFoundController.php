<?php

namespace App\Controller;

class NotFoundController extends AbstractController
{

    public function run()
    {
        return $this->render('404Template');
    }

}