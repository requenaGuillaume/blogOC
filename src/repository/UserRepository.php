<?php

namespace App\Repository;

class UserRepository extends PDOAbstractRepository
{
    protected array $columns = ['id', 'role', 'pseudo', 'mail'];
    protected string $table = 'user';

    public function run()
    {
        echo 'test<br>';
        // $user = $this->find(1); // ok
        // $users = $this->findAll();  // ok
        $user = $this->findOneBy([
            'mail' => 'testUser@symfony.com',
            'pseudo' => 'testUser'
        ]); // ok then => TODO add LIMIT
        var_dump($user);
    }
}