<?php

namespace App\Repository;

final class UserRepository extends PDOAbstractRepository
{
    protected string $table = 'user';
    protected array $requiredColumns = ['role' => 'role', 'pseudo' => 'pseudo', 'mail' => 'mail', 'password' => 'password'];
    protected array $optionnalColumns = ['id' => 'id'];


    public function run()
    {
        // $user = $this->find(1); // ok
        // $users = $this->findAll();  // ok

        // $user = $this->findOneBy([
        //     'mail' => 'testUser@symfony.com',
        //     'pseudo' => 'testUser'
        // ]); // ok

        // $this->delete(300002); // ok

        // $user = $this->findBy(data: [
        //     'pseudo' => 'testUser',
        //     'role' => 'user'
        // ], limit: 5, offset: 0); // ok

        // $user = $this->findBy(data: [
        //     'pseudo' => 'testUser'            
        // ],
        // orderCriterias: [
        //     'role' => 'ASC',
        //     'id' => 'ASC'
        // ], 
        // limit: 5, offset: 0); // ok

        // var_dump($users);

        // $values = [
        //     'role' => 'user',
        //     'pseudo' => 'ok',
        //     'mail' => 'ok@symfony.com',
        //     'password' => 'ok',
        //     'id' => 78530002
        //     // 'ptdr' => 'ptdr' // value not allowed, cause an exception
        // ]; 
        // $this->create($values); // ok        

        // $user = $this->find(60);
        // var_dump($user);
        
        // $values = [
        //     'pseudo' => 'rahrahrah',
        //     'role' => 'user',
        //     'mail' => 'addressMail@lol.fr',
        //     'password' => 'passwordHashé'
        //     // 'ptdr' => 'ptdr'
        // ];
        // $this->update($values, 60); // ok

        // $user = $this->find(60);
        // var_dump($user);
    }

}