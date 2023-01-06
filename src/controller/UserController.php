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
    private const VALID_UPDATE_FIELDS_NAME = ['pseudo', 'email'];

    private bool $isRegister = false;

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
            $this->isRegister = true;

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
        if(!$this->getUser()){
            $this->addFlash('danger', 'You must be logged in to access this page');
            $this->redirect('http://blogoc/?page=homepage');
        }

        $id = $this->getIdFromUrl();

        if(!$id){
            return $this->render('404Template');
        }

        if(!$this->currentUserIsAdmin() && $this->getUser()->getId() !== $id){
            $this->addFlash('danger', 'You cannot access to another member\'s profile page');
            $this->redirect('http://blogoc/?page=homepage');
        }

        $userRepository = new UserRepository();
        $userArray = $userRepository->find($id);

        if(!$userArray){
            $this->addFlash('danger', 'This user does not exist');
            $this->redirect('http://blogoc/?page=homepage');
        }

        $normalizer = new NormalizerService();
        $user = $normalizer->normalize($userArray, UserEntity::class);

        if($_POST){
            $this->isRegister = false;
            $validator = new ValidatorService();
            $formContainsError = $this->formHasError($validator);

            if($formContainsError){
                return $this->render('UserProfilTemplate', ['user' => $user]);
            }

            $newPseudo = $_POST['pseudo'];
            $newEmail = $_POST['email'];

            $hasUpdate = false;

            if($user->getPseudo() !== $newPseudo){
                $user->setPseudo(htmlspecialchars($newPseudo));
                $hasUpdate = true;
            }

            if($user->getMail() !== $newEmail){
                $user->setMail(htmlspecialchars($newEmail));
                $hasUpdate = true;
            }

            if(!$hasUpdate){
                $this->addFlash('warning', 'No changes detected');
                return $this->render('UserProfilTemplate', ['user' => $user]);
            }

            $userWithNewDataArray = $normalizer->denormalize($user);
            $userRepository->update($userWithNewDataArray, $user->getId());

            $_SESSION['user'] = $user;
            $this->addFlash('success', 'Your data has been updated !');
            $this->redirect("http://blogoc/?page=user&action=update&id={$user->getId()}");
        }

        return $this->render('UserProfilTemplate', ['user' => $user]);
    }


    // =============== Other function =============== \\

    public function updateStatus()
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
        $numberOfFields = $this->isRegister ? 4 : 2;
        $error = $this->verifyInputCount(count($_POST), $numberOfFields);
        if($error){
            $this->addFlash('danger', $error);
            return true;               
        }

        $validFields = $this->isRegister ? self::VALID_REGISTER_FIELDS_NAME : self::VALID_UPDATE_FIELDS_NAME;
        $error = $this->verifyInputsValidity($_POST, $validFields);
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

        if($this->isRegister){
            if($_POST['pass'] !== $_POST['verifpass'] ){
                $this->addFlash('danger', "Passwords fields does not match");  
                return true;
            }
    
            $error = $this->verifyPasswordFormat($validator);
            if($error){
                $this->addFlash('danger', $error);
                return true;
            }
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

        if($this->isRegister){
            $error = $this->verifyDataLenght($validator, 'pass', 6, 40);
            if($error){
                $this->addFlash('danger', $error);
                return true;
            }
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