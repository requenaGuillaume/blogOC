<?php

namespace App\Controller;

use App\Entity\CommentEntity;
use App\Service\NormalizerService;
use App\Repository\CommentRepository;

class CommentAdminController extends AbstractController
{

    public function run()
    {
        $normalizer = new NormalizerService();
        $commentRepository = new CommentRepository();

        $commentsArray = $commentRepository->findAll();
        $comments = [];

        foreach($commentsArray as $commentInArray){
            $comments[] = $normalizer->normalize($commentInArray, CommentEntity::class);
        }

        return $this->render('AllCommentsAdminTemplate', ['comments' => $comments]);
    }

}