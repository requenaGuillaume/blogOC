<?php

namespace App\Controller;

use App\Entity\PostEntity;
use App\Entity\UserEntity;
use App\Entity\CommentEntity;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\NormalizerService;
use App\Repository\CommentRepository;

class PostController extends AbstractController
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

        return $this->render('ShowAllPostsTemplate', ['posts' => $posts]);
    }

    public function show()
    {
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = intval($_GET['id']);

            if(!$id){
                return $this->render('404Template');
            }

            $normalizer = new NormalizerService();
            $postRepository = new PostRepository();
            $userRepository = new UserRepository();
            $commentRepository = new CommentRepository();

            $post = $normalizer->normalize($postRepository->find($id), PostEntity::class);
            $author = $normalizer->normalize($userRepository->find($post->getAuthorId()), UserEntity::class);

            $commentsArray = $commentRepository->findBy(['post_id' => $post->getId()]);
            $comments = [];

            foreach($commentsArray as $commentArray){

                $comment = $normalizer->normalize($commentArray, CommentEntity::class);

                $comments[] = [
                    'comment' => $comment,
                    'author' => $normalizer->normalize($userRepository->find($comment->getAuthorId()), UserEntity::class) 
                ];
            }

            return $this->render('ShowOnePostTemplate', [
                'post' => $post,
                'author' => $author,
                'comments' => $comments
            ]);
        }
        
        return $this->render('404Template');       
    }

    public function create(): void
    {
        echo 'Test Router Article create()'; // test ok        
    }
}