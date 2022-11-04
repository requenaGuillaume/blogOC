<?php

namespace App\Core;

require_once 'vendor/autoload.php';

final class Router 
{    
    private const ROUTE = [
        'homepage' => ['action' => 'Controller\HomepageController'],
        'article' => ['action'=> 'Controller\ArticleController'],
        'login' => ['action' => 'Controller\LoginController'],
        'logout' => ['action' => 'Controller\LogoutController']
    ];


    public function route(): void 
    {
        $page = 'homepage';

        if(isset($_GET['page']) && isset(self::ROUTE[$_GET['page']])) {
            $page = $_GET['page'];
        }
        
        $controllerName = 'App\\'.self::ROUTE[$page]['action'];
        $controller = new $controllerName();
        $controller->run();       
    }

}