<?php

namespace App\Controller;

use App\Entity\UserEntity;
use App\Repository\UserRepository;
use App\Service\NormalizerService;

class UserAdminController extends AbstractController
{

    public function run()
    {
        $normalizer = new NormalizerService();
        $userRepository = new UserRepository();

        $usersArray = $userRepository->findAll();
        $users = [];

        foreach($usersArray as $userInArray){
            $users[] = $normalizer->normalize($userInArray, UserEntity::class);
        }

        return $this->render('AllUsersAdminTemplate', ['users' => $users]);
    }

}