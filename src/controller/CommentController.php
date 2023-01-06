<?php

namespace App\Controller;

use App\Entity\PostEntity;
use App\Entity\CommentEntity;
use App\Interface\FormInterface;
use App\Interface\AdminInterface;
use App\Service\ValidatorService;
use App\Service\NormalizerService;
use App\Controller\AdminController;
use App\Interface\ValidatorInterface;
use App\Repository\CommentRepository;


final class CommentController extends AdminController implements AdminInterface, FormInterface
{

    private const VALID_COMMENT_FIELDS_NAME = ['content'];


    public function run()
    {
        return $this->render('404Template');
    }


    // =============== AdminInterface functions =============== \\

    public function list()
    {
        if(!$this->getUser() || !$this->currentUserIsAdmin()){
            $this->redirect('http://blogoc/?page=homepage');
        }
        
        $comments = $this->getAll(CommentRepository::class);
        return $this->render('AllCommentsAdminTemplate', ['comments' => $comments]);
    }


    /** Actually there is no link leading to this route in our website */
    public function show()
    {
        $id = $this->getIdFromUrl();

        if(!$id){
            return $this->render('404Template');
        }

        $commentRepository = new CommentRepository();
        $commentArray = $commentRepository->find($id);

        if(!$commentArray){
            $this->addFlash('danger', 'This comment doesn\'t exist');
            $this->redirect('http://blogoc/?page=post');
        }

        $this->redirect("http://blogoc/?page=post&action=show&id={$commentArray['id']}");
    }


    public function create()
    {
        if($_POST){

            $validator = new ValidatorService();
            $formContainsError = $this->formHasError($validator);

            if($formContainsError){
                $this->redirect($_SERVER['HTTP_REFERER']);
            }

            parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $queries);
            $postId = intval($queries['id']);

            $commentEntity = new CommentEntity();
            $commentEntity->setContent(htmlspecialchars($_POST['content']))
                          ->setStatus(CommentEntity::STATUS_WAITING)
                          ->setAuthorId($this->getUser()->getId())
                          ->setPostId($postId);

            $normalizer = new NormalizerService();
            $comment = $normalizer->denormalize($commentEntity);

            $commentRepository = new CommentRepository();
            $commentRepository->create($comment);

            $this->addFlash('success', 'Your comment has been sent, we will verify it before showing it.');
            $this->redirect($_SERVER['HTTP_REFERER']);

        }else{
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
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

        $this->deleteEntity($id, CommentRepository::class);

        $this->addFlash('success', "The comment has been deleted");
        $this->redirect("http://blogoc/?page=comment&action=list");
    }


    public function update()
    {
        // not editable
        $this->render('404Template');
    }


    // =============== Other functions =============== \\

    public function updateStatus()
    {
        if(!$this->getUser() || !$this->currentUserIsAdmin()){
            $this->redirect('http://blogoc/?page=homepage');
        }

        $id = $this->getIdFromUrl();

        if(!$id){
            return $this->render('404Template');
        }

        $commentRepository = new CommentRepository();
        $commentArray = $commentRepository->find($id);

        if(!$commentArray){
            $this->addFlash('danger', 'This comment does not exist');
            $this->redirect('http://blogoc/?page=comment&action=list');
        }

        $normalizer = new NormalizerService();
        $comment = $normalizer->normalize($commentArray, CommentEntity::class);
        
        $nextStatus = $_GET['status'];
        $newInfos = [];
        
        if($nextStatus === CommentEntity::STATUS_VALID){
            $newInfos = ['status' => CommentEntity::STATUS_VALID];
        }elseif($nextStatus === CommentEntity::STATUS_INVALID){
            $newInfos = ['status' => CommentEntity::STATUS_INVALID];
        }

        $commentRepository->update($newInfos, $id);

        $this->addFlash('success', "The comment n°{$comment->getId()} has been updated");
        $this->redirect('http://blogoc/?page=comment&action=list');
    }


    // =============== FormInterface functions =============== \\

    public function formHasError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyInputCount(count($_POST), 1);
        if($error){
            $this->addFlash('danger', $error);
            return true;
        }

        $error = $this->verifyInputsValidity($_POST, self::VALID_COMMENT_FIELDS_NAME);
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
        $error = $this->verifyDataLenght($validator, 'content', 3, 1000);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        return false;
    }


    public function dataHasFormatError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyDataFormat($validator, 'content', PostEntity::REGEX_TEXT);
        if($error){
            $this->addFlash('danger', $error);
            return true;             
        }

        return false;
    }

}