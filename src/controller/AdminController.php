<?php

namespace App\Controller;


class AdminController extends AbstractController
{

    public function run()
    {
        return $this->render('AdminTemplate');
    }


    protected function deleteEntity(int $id, string $repositoryClass)
    {
        $afterLastAntiSlash = strrchr($repositoryClass, '\\');
        $withoutAntiSlash = str_replace('\\', '', $afterLastAntiSlash);
        $entity = strtolower(str_replace('Repository', '', $withoutAntiSlash));

        $repository = new $repositoryClass();
        $entityInArray = $repository->find($id);

        if(!$entityInArray){
            $this->addFlash('danger', "This $entity does not exist");
            $this->redirect("http://blogoc/?page=$entity&action=admin");
        }

        $repository->delete($id);

        $this->addFlash('success', "The $entity n°{$entityInArray['id']} has been deleted");
        $this->redirect("http://blogoc/?page=$entity&action=admin");
    }


    protected function getIdFromUrl(): ?int
    {
        if(isset($_GET['id']) && !empty($_GET['id'])){
            return intval($_GET['id']);
        }

        return null;
    }

}