<?php

namespace App\Core;

use App\Core\Router;

require_once 'vendor/autoload.php';

final class Kernel
{
    public function process(): void
    {
        $router = new Router();
        $router->route();
    }
}