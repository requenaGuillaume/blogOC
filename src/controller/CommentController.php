<?php

namespace App\Controller;

use App\Entity\CommentEntity;
use App\Service\NormalizerService;
use App\Controller\AdminController;
use App\Repository\CommentRepository;

class CommentController extends AdminController
{

    public function run()
    {
        return $this->render('404Template');
    }


    public function list()
    {
        $comments = $this->getAll(CommentRepository::class);
        return $this->render('AllCommentsAdminTemplate', ['comments' => $comments]);
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

        $this->deleteEntity($id, CommentRepository::class);
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

        $commentRepository = new CommentRepository();
        $commentArray = $commentRepository->find($id);

        if(!$commentArray){
            $this->addFlash('danger', 'This comment does not exist');
            $this->redirect('http://blogoc/?page=comment&action=list');
        }

        $normalizer = new NormalizerService();
        $newInfos = [];
        $comment = $normalizer->normalize($commentArray, CommentEntity::class);

        $nextStatus = $_GET['status'];

        if($nextStatus === CommentEntity::STATUS_VALID){
            $newInfos = ['status' => CommentEntity::STATUS_VALID];
        }

        if($nextStatus === CommentEntity::STATUS_INVALID){
            $newInfos = ['status' => CommentEntity::STATUS_INVALID];
        }

        $commentRepository->update($newInfos, $id);

        $this->addFlash('success', "The comment n°{$comment->getId()} has been updated");
        $this->redirect('http://blogoc/?page=comment&action=list');
    }

}