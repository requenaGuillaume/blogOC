<?php

namespace App\Controller;

use App\Entity\CommentEntity;
use App\Service\NormalizerService;
use App\Repository\CommentRepository;
use App\Controller\AbstractController;

class CommentController extends AbstractController
{

    public function run()
    {
        return $this->render('404Template');
    }


    public function admin()
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


    public function create()
    {
        // code
    }


    public function delete()
    {
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = intval($_GET['id']);

            if(!$id){
                return $this->render('404Template');
            }
        }else{
            return $this->render('404Template');
        }

        $commentRepository = new CommentRepository();
        $commentArray = $commentRepository->find($id);

        // if admin or current user....

        if(!$commentArray){
            $this->addFlash('danger', 'This comment does not exist');
            $this->redirect('http://blogoc/?page=comment&action=admin');
        }

        $commentRepository->delete($id);

        $this->addFlash('success', "The comment n°{$commentArray['id']} has been deleted");
        $this->redirect('http://blogoc/?page=comment&action=admin');
    }


    public function update()
    {
        // code
    }

}