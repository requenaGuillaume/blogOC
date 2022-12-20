<?php

namespace App\Core;

require_once 'vendor/autoload.php';

final class Router 
{    
    private const ROUTE = [
        'admin' => 'Controller\AdminController',
        'admin-user' => 'Controller\UserAdminController',
        'admin-post' => 'Controller\PostAdminController',
        'admin-comment' => 'Controller\CommentAdminController',
        'homepage' => 'Controller\HomepageController',
        'post' => 'Controller\PostController',
        'register' => 'Controller\RegisterController',
        'login' => 'Controller\LoginController',
        'logout' => 'Controller\LogoutController',
        'fixtures' => 'Controller\FixturesController'
    ];

    private const METHOD = [
        'create' => 'create',
        'show' => 'show',
        'update' => 'update',
        'delete' => 'delete'
    ];

    public function route(): void 
    {
        $page = 'homepage';

        if(!isset($_GET['page']) || !isset(self::ROUTE[$_GET['page']])) {
            echo 'Redirect 404';
            die();
        }

        $page = htmlspecialchars($_GET['page']);
        
        $controllerName = 'App\\'.self::ROUTE[$page];
        $controller = new $controllerName();

        if(isset($_GET['action'])){

            if(!isset(self::METHOD[$_GET['action']]) || !method_exists($controller, $_GET['action'])){
                echo 'Redirect 404';
                die();
            }

            $method = htmlspecialchars($_GET['action']);

            $controller->$method();
            die();
        }
        
        $controller->run();
    }
}