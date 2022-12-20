<?php

namespace App\Controller;


class AdminController extends AbstractController
{

    public function run()
    {
        return $this->render('AdminTemplate');
    }

}