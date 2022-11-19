<?php

namespace App\Repository;

class UserRepository extends PDOAbstractRepository
{
    protected array $columns = ['id', 'role', 'pseudo', 'mail'];
    protected string $table = 'user';

    public function run()
    {
        // $user = $this->find(1); // ok
        // $users = $this->findAll();  // ok

        // $user = $this->findOneBy([
        //     'mail' => 'testUser@symfony.com',
        //     'pseudo' => 'testUser'
        // ]); // ok

        // $this->delete(7); // ok

        // $user = $this->findBy(data: [
        //     'pseudo' => 'testUser',
        //     'role' => 'user'
        // ], limit: 5, offset: 0); // ok

        $user = $this->findBy(data: [
            'pseudo' => 'testUser'            
        ],
        orderCriterias: [
            'role' => 'ASC',
            'id' => 'ASC'
        ], 
        limit: 5, offset: 0); // ok

        var_dump($user);
    }


    public function create(): void
    {
        // TODO
    }

    public function update(): void
    {
        // TODO
    }

}