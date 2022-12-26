<?php

namespace App\Controller;

use App\Entity\PostEntity;
use App\Entity\UserEntity;
use App\Entity\CommentEntity;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\NormalizerService;
use App\Controller\AdminController;
use App\Repository\CommentRepository;

class PostController extends AdminController
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
        $id = $this->getIdFromUrl();

        if(!$id){
            return $this->render('404Template');
        }
            
        $postRepository = new PostRepository();            
        $postArray = $postRepository->find($id);            

        if(!$postArray){
            $this->addFlash('danger', 'This post doesn\'t exist');
            $this->redirect('http://blogoc/?page=post');
        }

        $normalizer = new NormalizerService();
        $post = $normalizer->normalize($postRepository->find($id), PostEntity::class);

        $userRepository = new UserRepository();
        $authorArray = $userRepository->find($post->getAuthorId());

        if($authorArray){
            $author = $normalizer->normalize($authorArray, UserEntity::class);
        }else{
            $author = new UserEntity();
            $author->setPseudo('Unknown');
        }

        $commentRepository = new CommentRepository();
        $commentsArray = $commentRepository->findBy(['post_id' => $post->getId()]);
        $comments = [];

        foreach($commentsArray as $commentArray){

            $comment = $normalizer->normalize($commentArray, CommentEntity::class);
            $author = $normalizer->normalize($userRepository->find($comment->getAuthorId()), UserEntity::class);

            $comments[] = [
                'comment' => $comment,
                'author' => $author 
            ];
        }

        return $this->render('ShowOnePostTemplate', [
            'post' => $post,
            'author' => $author,
            'comments' => $comments
        ]);     
    }


    public function list()
    {
        $posts = $this->getAll(PostRepository::class);
        return $this->render('AllPostsAdminTemplate', ['posts' => $posts]);
    }


    public function create()
    {
        // code
    }


    public function delete()
    {
        if(!$this->getUser() || !$this->currentUserIsAdmin()){
            $this->redirect('http://blogoc/?page=homepage');
        }

        $id = $this->getIdFromUrl();

        if(!$id){
            return $this->render('404Template');
        }

        $this->deleteEntity($id, PostRepository::class);
    }


    public function update()
    {
        if(!$this->getUser() || !$this->currentUserIsAdmin()){
            $this->redirect('http://blogoc/?page=homepage');
        }

        $id = $this->getIdFromUrl();

        if(!$id){
            return $this->render('404Template');
        }

        $postRepository = new PostRepository();
        $postArray = $postRepository->find($id);

        // if admin or current user....

        if(!$postArray){
            $this->addFlash('danger', 'This post does not exist');
            $this->redirect('http://blogoc/?page=post&action=list');
        }

        $normalizer = new NormalizerService();
        $newInfos = [];
        $post = $normalizer->normalize($postArray, PostEntity::class);

        $currentStatus = $post->getStatus();

        if($currentStatus === PostEntity::STATUS_DRAFT){
            $newInfos = ['status' => PostEntity::STATUS_ONLINE];
        }else{
            $newInfos = ['status' => PostEntity::STATUS_DRAFT];
        }

        $postRepository->update($newInfos, $id);

        $this->addFlash('success', "The post n°$id has been updated");
        $this->redirect('http://blogoc/?page=post&action=list');
    }
}