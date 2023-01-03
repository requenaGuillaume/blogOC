<?php

namespace App\Repository;

final class UserRepository extends PDOAbstractRepository
{
    protected string $table = 'user';
    protected array $optionnalColumns = ['id' => 'id'];
    protected array $requiredColumns = [
        'role' => 'role', 
        'pseudo' => 'pseudo', 
        'mail' => 'mail', 
        'password' => 'password'
    ];
}