<?php

namespace App\Controller;

use App\Entity\UserEntity;

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


    protected function getUser(): ?UserEntity
    {
        if(isset($_SESSION['user'])){
            return $_SESSION['user'];
        }

        return null;
    }


    protected function currentUserIsAdmin(): bool
    {
        $user = $this->getUser();

        if($user->getRole() === UserEntity::ROLE_ADMIN){
            return true;
        }

        return false;
    }

}