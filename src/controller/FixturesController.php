<?php

namespace App\Controller;

use App\Entity\UserEntity;
use App\Repository\UserRepository;

class FixturesController
{
    public function run(): void
    {
        echo 'Test Router Fixtures';

        // $userEntity = new UserEntity();

        // $userEntity->setMail('testMail@fixtures.fr')
        //      ->setPassword('testPassword')
        //      ->setPseudo('testPseudo')
        //      ->setRole('user');

        // $user = $userEntity->denormalize();

        // $userRepository = new UserRepository();

        // $userRepository->create($user);

        // $userFound = $userRepository->findOneBy(['pseudo' => 'testPseudo']);
    }

}