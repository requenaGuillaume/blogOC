<?php

namespace App\Controller;

use App\Entity\UserEntity;
use App\Repository\UserRepository;
use App\Service\NormalizerService;
use App\Controller\AdminController;

class UserController extends AdminController
{

    public function run()
    {
        $this->render('404Template');
    }


    public function admin()
    {
        $normalizer = new NormalizerService();
        $userRepository = new UserRepository();

        $usersArray = $userRepository->findAll();
        $users = [];

        foreach($usersArray as $userInArray){
            $users[] = $normalizer->normalize($userInArray, UserEntity::class);
        }

        return $this->render('AllUsersAdminTemplate', ['users' => $users]);
    }


    public function show()
    {
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = intval($_GET['id']);

            if(!$id){
                return $this->render('404Template');
            }

            $userRepository = new UserRepository();
            $userArray = $userRepository->find($id);

            if(!$userArray){
                $this->addFlash('danger', 'This user does not exist');
                $this->redirect('http://blogoc/?page=homepage');
            }

            $normalizer = new NormalizerService();
            $user = $normalizer->normalize($userArray, UserEntity::class);

            return $this->render('UserProfilTemplate', ['user' => $user]);
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

        $this->deleteEntity($id, UserRepository::class);
    }


    public function update()
    {
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = intval($_GET['id']);

            if(!$id){
                return $this->render('404Template');
            }
        }else{
            return $this->render('404Template');
        }

        $userRepository = new UserRepository();
        $userArray = $userRepository->find($id);

        // if admin or current user....

        if(!$userArray){
            $this->addFlash('danger', 'This user does not exist');
            $this->redirect('http://blogoc/?page=user&action=admin');
        }

        $normalizer = new NormalizerService();
        $newInfos = [];
        $user = $normalizer->normalize($userArray, UserEntity::class);

        $currentUserRole = $user->getRole();

        if($currentUserRole === UserEntity::ROLE_USER){
            $newInfos = ['role' => UserEntity::ROLE_ADMIN];
        }else{
            $newInfos = ['role' => UserEntity::ROLE_USER];
        }

        $userRepository->update($newInfos, $id);

        $this->addFlash('success', "Role of the user n°$id has been changed");
        $this->redirect('http://blogoc/?page=user&action=admin');
    }

}