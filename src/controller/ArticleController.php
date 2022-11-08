<?php

namespace App\Controller;

class ArticleController // extends AbstractController
{
    public function run(): void
    {
        echo 'Test Router Article'; // test ok
    }

    public function create(): void
    {
        echo 'Test Router Article create()'; // test ok        
    }
}