<?php

namespace App\Controller;

use App\Entity\UserEntity;
use App\Interface\FormInterface;
use App\Interface\AdminInterface;
use App\Service\ValidatorService;
use App\Repository\UserRepository;
use App\Service\NormalizerService;
use App\Controller\AdminController;
use App\Interface\ValidatorInterface;

final class UserController extends AdminController implements AdminInterface, FormInterface
{

    private const VALID_REGISTER_FIELDS_NAME = ['pseudo', 'email', 'pass', 'verifpass'];


    public function run()
    {
        $user = $this->getUser();

        if(!$user){
            $this->addFlash('warning', 'You must be logged in to access your user profil page');
            $this->redirect('http://blogoc/?page=login');
        }

        return $this->render('UserProfilTemplate', ['user' => $user]);
    }


    // =============== AdminInterface functions =============== \\

    public function list()
    {
        if(!$this->getUser() || !$this->currentUserIsAdmin()){
            $this->redirect('http://blogoc/?page=homepage');
        }

        $users = $this->getAll(UserRepository::class);
        return $this->render('AllUsersAdminTemplate', ['users' => $users]);
    }


    public function show()
    {
        if(!$this->getUser() || !$this->currentUserIsAdmin()){
            $this->redirect('http://blogoc/?page=homepage');
        }

        $id = $this->getIdFromUrl();

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


    public function create()
    {
        if($_POST){

            $validator = new ValidatorService();
            $formContainsError = $this->formHasError($validator);

            if($formContainsError){
                return $this->render('RegisterTemplate');
            }

            $userEntity = new UserEntity();
            $userEntity->setPseudo(htmlspecialchars($_POST['pseudo']))
                       ->setMail(htmlspecialchars($_POST['email']))
                       ->setPassword(password_hash($_POST['pass'], PASSWORD_DEFAULT))
                       ->setRole(UserEntity::ROLE_USER);

            $normalizer = new NormalizerService();
            $user = $normalizer->denormalize($userEntity);

            $userRepository = new UserRepository();
            $userEmailAlreadyExist = $this->userAlreadyExist($userRepository, $userEntity);

            if($userEmailAlreadyExist){
                return $this->render('RegisterTemplate');
            }

            $userRepository->create($user);

            $this->addFlash('success', 'You succedeed to sign up !');
            $this->redirect('http://blogoc/?page=login');
        }

        return $this->render('RegisterTemplate');
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

        $this->addFlash('success', "The user has been deleted");
        $this->redirect("http://blogoc/?page=user&action=list");
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

        $userRepository = new UserRepository();
        $userArray = $userRepository->find($id);

        if(!$userArray){
            $this->addFlash('danger', 'This user does not exist');
            $this->redirect('http://blogoc/?page=user&action=list');
        }

        $normalizer = new NormalizerService();
        $user = $normalizer->normalize($userArray, UserEntity::class);
        $currentUserRole = $user->getRole();
        
        $newInfos = [];

        if($currentUserRole === UserEntity::ROLE_USER){
            $newInfos = ['role' => UserEntity::ROLE_ADMIN];
        }else{
            $newInfos = ['role' => UserEntity::ROLE_USER];
        }

        $userRepository->update($newInfos, $id);

        $this->addFlash('success', "Role of the user n°$id has been updated");
        $this->redirect('http://blogoc/?page=user&action=list');
    }


    // =============== FormInterface functions =============== \\

    public function formHasError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyInputCount(count($_POST), 4);
        if($error){
            $this->addFlash('danger', $error);
            return true;               
        }

        $error = $this->verifyInputsValidity($_POST, self::VALID_REGISTER_FIELDS_NAME);
        if($error){
            $this->addFlash('danger', $error); 
            return true;                 
        }

        if($this->inputsHasDataLengthError($validator)){
            return true;
        }

        if($_POST['pass'] !== $_POST['verifpass'] ){
            $this->addFlash('danger', "Passwords fields does not match");  
            return true;           
        }

        if($this->dataHasFormatError($validator)){
            return true;
        }

        $error = $this->verifyPasswordFormat($validator);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        return false;
    }


    public function userAlreadyExist(UserRepository $userRepository, UserEntity $userEntity): bool
    {
        $userPseudoAlreadyExist = $userRepository->findOneBy(['pseudo' => $userEntity->getPseudo()]);
        $userEmailAlreadyExist = $userRepository->findOneBy(['mail' => $userEntity->getMail()]);

        if($userPseudoAlreadyExist){
            $this->addFlash('danger', "The pseudo {$userEntity->getPseudo()} is already used");
            return true;
        }

        if($userEmailAlreadyExist){
            $this->addFlash('danger', "The email {$userEntity->getMail()} is already used");
            return true;
        }

        return false;
    }


    public function inputsHasDataLengthError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyDataLenght($validator, 'pseudo', 2, 40);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataLenght($validator, 'email', 2, 40);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataLenght($validator, 'pass', 6, 40);
        if($error){
            $this->addFlash('danger', $error);   
            return true;               
        }

        return false;
    }


    public function dataHasFormatError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyDataFormat($validator, 'pseudo', UserEntity::REGEX_PSEUDO);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataFormat($validator, 'email', UserEntity::REGEX_EMAIL);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        return false;
    }

}