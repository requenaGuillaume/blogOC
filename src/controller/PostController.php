<?php

namespace App\Controller;

use DateTime;
use App\Entity\PostEntity;
use App\Entity\UserEntity;
use App\Entity\CommentEntity;
use App\Interface\FormInterface;
use App\Interface\AdminInterface;
use App\Service\ValidatorService;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\NormalizerService;
use App\Controller\AdminController;
use App\Interface\ValidatorInterface;
use App\Repository\CommentRepository;


final class PostController extends AdminController implements AdminInterface, FormInterface
{

    private const VALID_POST_FIELDS_NAME = ['title', 'status', 'head', 'content'];


    public function run()
    {
        $normalizer = new NormalizerService();
        $postRepository = new PostRepository();

        $postsArray =  $postRepository->findBy([], ['last_update' => 'DESC']);
        $posts = [];

        foreach($postsArray as $postInArray){
            $posts[] = $normalizer->normalize($postInArray, PostEntity::class);
        }

        return $this->render('ShowAllPostsTemplate', ['posts' => $posts]);
    }


    // =============== AdminInterface functions =============== \\

    public function list()
    {
        if(!$this->getUser() || !$this->currentUserIsAdmin()){
            $this->redirect('http://blogoc/?page=homepage');
        }

        $posts = $this->getAll(PostRepository::class);
        return $this->render('AllPostsAdminTemplate', ['posts' => $posts]);
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
            'comments' => $comments,
            'user' => $this->getUser(),
            'validStatus' => CommentEntity::STATUS_VALID
        ]);     
    }


    public function create()
    {
        if($_POST){

            $validator = new ValidatorService();
            $formContainsError = $this->formHasError($validator);

            if($formContainsError){
                return $this->render('CreatePostTemplate');
            }

            if(!in_array($_POST['status'], [PostEntity::STATUS_DRAFT, PostEntity::STATUS_ONLINE])){
                $this->addFlash('danger', 'Status is not valid');
                $this->redirect('http://blogoc/?page=post&action=create');
            }
            
            $title = htmlspecialchars($_POST['title']);            
            $slug = preg_replace('/\s+/', '-', strtolower($title));

            $postRepository = new PostRepository();
            $slugAlreadyExist = $postRepository->findOneBy(['slug' => $slug]);

            if($slugAlreadyExist){
                $this->addFlash('danger', 'Slug already taken, use another title to avoid that');
                $this->redirect('http://blogoc/?page=post&action=create');
            }

            $now = new DateTime();

            $postEntity = new PostEntity();
            $postEntity->setAuthorId($this->getUser()->getId())
                       ->setTitle($title)
                       ->setStatus(htmlspecialchars($_POST['status']))
                       ->setContent(htmlspecialchars($_POST['content']))
                       ->setHead(htmlspecialchars($_POST['head']))
                       ->setSlug($slug)
                       ->setLastUpdate($now->format('Y-m-d H:i:s'));

            $normalizer = new NormalizerService();
            $post = $normalizer->denormalize($postEntity);
            
            $postRepository->create($post);

            $this->addFlash('success', "The post {$postEntity->getTitle()} has been successfully created !");
            $this->redirect("http://blogoc/?page=post&action=list");
        }

        return $this->render('CreatePostTemplate');
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

        $this->addFlash('success', "The post has been deleted");
        $this->redirect("http://blogoc/?page=post&action=list");
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

        if(!$postArray){
            $this->addFlash('danger', 'This post does not exist');
            $this->redirect('http://blogoc/?page=post&action=list');
        }

        $normalizer = new NormalizerService();
        $post = $normalizer->normalize($postArray, PostEntity::class);
        $currentStatus = $post->getStatus();
        
        $newInfos = [];

        if($currentStatus === PostEntity::STATUS_DRAFT){
            $newInfos = ['status' => PostEntity::STATUS_ONLINE];
        }else{
            $newInfos = ['status' => PostEntity::STATUS_DRAFT];
        }

        $postRepository->update($newInfos, $id);

        $this->addFlash('success', "The post n°$id has been updated");
        $this->redirect('http://blogoc/?page=post&action=list');
    }


    // =============== FormInterface functions =============== \\

    public function formHasError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyInputCount(count($_POST), 4);
        if($error){
            $this->addFlash('danger', $error);
            return true;
        }

        $error = $this->verifyInputsValidity($_POST, self::VALID_POST_FIELDS_NAME);
        if($error){
            $this->addFlash('danger', $error); 
            return true;                 
        }

        if($this->inputsHasDataLengthError($validator)){
            return true;
        }

        if($this->dataHasFormatError($validator)){
            return true;
        }

        return false;
    }


    public function inputsHasDataLengthError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyDataLenght($validator, 'title', 2, 40);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataLenght($validator, 'status', strlen(PostEntity::STATUS_DRAFT), strlen(PostEntity::STATUS_ONLINE));
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataLenght($validator, 'head', 5, 200);
        if($error){
            $this->addFlash('danger', $error);   
            return true;               
        }

        $error = $this->verifyDataLenght($validator, 'content', 5, 1000);
        if($error){
            $this->addFlash('danger', $error);   
            return true;               
        }

        return false;
    }


    public function dataHasFormatError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyDataFormat($validator, 'title', PostEntity::REGEX_TEXT);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataFormat($validator, 'head', PostEntity::REGEX_TEXT);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataFormat($validator, 'content', PostEntity::REGEX_TEXT);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        return false;
    }
}