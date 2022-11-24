<?php

namespace App\Repository;

class PostRepository extends PDOAbstractRepository
{
    protected string $table = 'post';
    protected array $requiredColumns = ['role' => 'role', 'pseudo' => 'pseudo', 'mail' => 'mail', 'password' => 'password'];
    protected array $optionnalColumns = [];

}