<?php

namespace App\Core;

use App\Core\Router;

require_once 'vendor/autoload.php';

class Kernel
{
    public function process()
    {
        $router = new Router();
        $router->route();
    }
}