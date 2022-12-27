<?php

namespace App\Controller;

use App\Service\NormalizerService;


class AdminController extends AbstractController
{

    public function run()
    {
        return $this->render('AdminTemplate');
    }


    protected function deleteEntity(int $id, string $repositoryClass): array
    {
        $repositoryName = $this->getRepositoryName($repositoryClass);
        $entity = strtolower(str_replace('Repository', '', $repositoryName));

        $repository = new $repositoryClass();
        $entityInArray = $repository->find($id);

        if(!$entityInArray){
            $this->addFlash('danger', "This $entity does not exist");
            $this->redirect("http://blogoc/?page=$entity&action=list");
        }

        $repository->delete($id);

        return $entityInArray;
    }


    protected function getIdFromUrl(): ?int
    {
        if(isset($_GET['id']) && !empty($_GET['id'])){
            return intval($_GET['id']);
        }

        return null;
    }


    protected function getAll(string $repositoryClass): array
    {
        $entityClassName = str_replace('Repository', 'Entity', $repositoryClass);

        $normalizer = new NormalizerService();
        $repository = new $repositoryClass();

        $entitiesInArray = $repository->findAll();
        $entities = [];

        foreach($entitiesInArray as $entityInArray){
            $entities[] = $normalizer->normalize($entityInArray, $entityClassName);
        }

        return $entities;
    }


    private function getRepositoryName(string $repositoryClass): string
    {
        $afterLastAntiSlash = strrchr($repositoryClass, '\\');
        $withoutAntiSlash = str_replace('\\', '', $afterLastAntiSlash);

        return $withoutAntiSlash;
    }

}