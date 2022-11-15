<?php

namespace App\Repository;

class UserRepository extends PDOAbstractRepository
{
    protected string $table = 'user';

    public function run()
    {
        echo 'test';
        $user = $this->find(1);
        var_dump($user);
    }
}