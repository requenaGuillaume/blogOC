<?php

namespace App\Controller;


abstract class AbstractController
{

    protected function render(string $path, ?array $variables = null): void
    {
        if($variables){
            extract($variables);
        }

        ob_start();
        require('src/template/'. $path .'.phtml');
        $pageContent = ob_get_clean();

        require('src/template/SiteBaseTemplate.phtml');
    }


    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }


    protected function addFlash(string $bootstrapClass, string $message): void
    {
        $_SESSION['flash'][$bootstrapClass][] = $message;
    }

}