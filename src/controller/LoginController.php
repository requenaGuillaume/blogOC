<?php

namespace App\Controller;

use App\Entity\UserEntity;
use App\Service\ValidatorService;
use App\Repository\UserRepository;
use App\Service\NormalizerService;
use App\Controller\AbstractFormController;

class LoginController extends AbstractFormController
{

    private const VALID_LOGIN_FIELDS_NAME = ['email', 'pass'];


    public function run()
    {
        if($_POST){

            // TODO => add a ValidatorInterface ? and in method's signature type ValidatorInterface ???
            $validator = new ValidatorService();
            $formContainsError = $this->formHasError($validator);

            if($formContainsError){
                return $this->render('LoginTemplate');
            }

            $userRepository = new UserRepository();

            $user = $userRepository->findOneBy(['mail' => htmlspecialchars($_POST['email'])]);

            if(!$user){
                $this->addFlash('danger', 'User not found');
                return $this->render('LoginTemplate');
            }

            $normalizer = new NormalizerService();

            $userEntity = $normalizer->normalize($user, UserEntity::class);

            if(password_verify($_POST['pass'], $userEntity->getPassword())){
                $_SESSION['user'] = $userEntity;
            }

            $this->addFlash('success', 'You succedeed to log in !');
            $this->redirect('http://blogoc/?page=homepage');
        }

        return $this->render('LoginTemplate');
    }


    private function formHasError(ValidatorService $validator): bool
    {
        $error = $this->verifyInputCount(count($_POST), 2);
        if($error){
            $this->addFlash('danger', $error);
            return true;               
        }

        $error = $this->verifyInputsValidity($_POST, self::VALID_LOGIN_FIELDS_NAME);
        if($error){
            $this->addFlash('danger', $error); 
            return true;                 
        }

        if($this->inputsHasDataLengthError($validator)){
            return true;
        }

        $error = $this->verifyDataFormat($validator, 'email', UserEntity::REGEX_EMAIL);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyPasswordFormat($validator);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        return false;
    }


    private function inputsHasDataLengthError(ValidatorService $validator): bool
    {
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

}