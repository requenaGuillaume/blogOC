<?php

namespace App\Core;

require_once 'vendor/autoload.php';

final class Router 
{    
    private const ROUTE = [
        'homepage' => 'Controller\HomepageController',
        'admin' => 'Controller\AdminController',
        'user' => 'Controller\UserController',
        'post' => 'Controller\PostController',
        'comment' => 'Controller\CommentController',
        'register' => 'Controller\RegisterController',
        'login' => 'Controller\LoginController',
        'logout' => 'Controller\LogoutController',
        '404' => 'Controller\NotFoundController',
        'fixtures' => 'Controller\FixturesController'
    ];

    private const METHOD = [
        'create' => 'create',
        'show' => 'show',
        'update' => 'update',
        'updateStatus' => 'updateStatus',
        'delete' => 'delete',
        'list' => 'list'
    ];

    public function route(): void 
    {
        $page = 'homepage';

        if(!isset($_GET['page']) || !isset(self::ROUTE[$_GET['page']])) {
            header("Location: http://blogoc/?page=404");
            exit;
        }

        $page = htmlspecialchars($_GET['page']);
        
        $controllerName = 'App\\'.self::ROUTE[$page];
        $controller = new $controllerName();

        if(isset($_GET['action'])){

            if(!isset(self::METHOD[$_GET['action']]) || !method_exists($controller, $_GET['action'])){
                header("Location: http://blogoc/?page=404");
                exit;
            }

            $method = htmlspecialchars($_GET['action']);

            $controller->$method();
            exit;
        }
        
        $controller->run();
    }
}