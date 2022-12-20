<?php

namespace App\Controller;

use App\Entity\PostEntity;
use App\Repository\PostRepository;
use App\Service\NormalizerService;

class PostAdminController extends AbstractController
{

    public function run()
    {
        $normalizer = new NormalizerService();
        $postRepository = new PostRepository();

        $postsArray =  $postRepository->findAll();
        $posts = [];

        foreach($postsArray as $postInArray){
            $posts[] = $normalizer->normalize($postInArray, PostEntity::class);
        }

        return $this->render('AllPostsAdminTemplate', ['posts' => $posts]);
    }

}