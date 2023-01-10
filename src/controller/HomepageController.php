<?php

namespace App\Controller;

use App\Entity\PostEntity;
use App\Entity\UserEntity;
use App\Interface\FormInterface;
use App\Service\ValidatorService;
use App\Interface\ValidatorInterface;
use App\Service\MailService;

final class HomepageController extends AbstractFormController implements FormInterface
{
    private const VALID_POST_FIELDS_NAME = ['lastName', 'firstName', 'email', 'content'];

    public function run()
    {
        if($_POST){
            $validator = new ValidatorService();
            $formContainsError = $this->formHasError($validator);

            if($formContainsError){
                return $this->render('HomepageTemplate');
            }

            $userId = $this->getUser()?->getId();

            $data = [
                'fullName' => htmlspecialchars($_POST['firstName']).' '.htmlspecialchars($_POST['lastName']),
                'email' => htmlspecialchars($_POST['email']),
                'content' => htmlspecialchars($_POST['content']),
                'userId' => $userId
            ];

            $mailService = new MailService();

            if($mailService->send($data)){
                $this->addFlash('success', 'Your message has been sent');
            }else{
                $this->addFlash('danger', 'An error has occured, please try again');
            }

            return $this->render('HomepageTemplate');
        }

        return $this->render('HomepageTemplate');
    }


    // =============== FormInterface functions =============== \\

    public function formHasError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyInputCount(count($_POST), 4);
        if($error){
            $this->addFlash('danger', $error);
            return true;
        }

        $error = $this->verifyInputsValidity($_POST, self::VALID_POST_FIELDS_NAME);
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

        return false;
    }


    public function inputsHasDataLengthError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyDataLenght($validator, 'firstName', 2, 40);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataLenght($validator, 'lastName', 2, 40);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataLenght($validator, 'email', 2, 40);
        if($error){
            $this->addFlash('danger', $error);   
            return true;               
        }

        $error = $this->verifyDataLenght($validator, 'content', 5, 1000);
        if($error){
            $this->addFlash('danger', $error);   
            return true;               
        }

        return false;
    }


    public function dataHasFormatError(ValidatorInterface $validator): bool
    {
        $error = $this->verifyDataFormat($validator, 'firstName', UserEntity::REGEX_PSEUDO);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataFormat($validator, 'lastName', UserEntity::REGEX_PSEUDO);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataFormat($validator, 'email', UserEntity::REGEX_EMAIL);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        $error = $this->verifyDataFormat($validator, 'content', PostEntity::REGEX_TEXT);
        if($error){
            $this->addFlash('danger', $error);  
            return true;                
        }

        return false;
    }
}