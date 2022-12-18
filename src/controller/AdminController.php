<?php

namespace App\Controller;

use App\Entity\PostEntity;
use App\Entity\UserEntity;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\NormalizerService;

class AdminController extends AbstractController
{

    public function run()
    {
        $normalizer = new NormalizerService();
        $userRepository = new UserRepository();
        $postRepository = new PostRepository();

        $usersArray = $userRepository->findAll();
        $users = [];

        foreach($usersArray as $userInArray){
            $users[] = $normalizer->normalize($userInArray, UserEntity::class);
        }

        $postsArray =  $postRepository->findAll();
        $posts = [];

        foreach($postsArray as $postInArray){
            $posts[] = $normalizer->normalize($postInArray, PostEntity::class);
        }


        return $this->render('AdminTemplate', [
            'users' => $users,
            'posts' => $posts
        ]);
    }

}