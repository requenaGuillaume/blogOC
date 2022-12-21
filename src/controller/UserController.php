<?php

namespace App\Controller;

use App\Entity\UserEntity;
use App\Repository\UserRepository;
use App\Service\NormalizerService;

class UserController extends AbstractController
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
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = intval($_GET['id']);

            if(!$id){
                return $this->render('404Template');
            }
        }

        $userRepository = new UserRepository();
        $userArray = $userRepository->find($id);

        // if admin or current user....

        if(!$userArray){
            $this->addFlash('danger', 'This user does not exist');
            $this->redirect('http://blogoc/?page=user&action=admin');
        }

        $userRepository->delete($id);

        $this->addFlash('success', "The user n°{$userArray['id']} has been deleted");
        $this->redirect('http://blogoc/?page=homepage');
    }


    public function update()
    {
        // code
    }

}