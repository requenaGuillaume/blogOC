<?php

namespace App\Controller;

use App\Entity\UserEntity;
use App\Repository\UserRepository;
use App\Service\NormalizerService;
use App\Service\ValidatorService;

class RegisterController extends AbstractFormController
{

    private const VALID_REGISTER_FIELDS_NAME = ['pseudo', 'email', 'pass', 'verifpass'];


    public function run()
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
            $this->redirect('http://blogoc/?page=homepage');
        }

        return $this->render('RegisterTemplate');
    }


    private function formHasError(ValidatorService $validator): bool
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


    private function userAlreadyExist(UserRepository $userRepository, UserEntity $userEntity): bool
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


    private function inputsHasDataLengthError(ValidatorService $validator): bool
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


    private function dataHasFormatError(ValidatorService $validator): bool
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