<?php

namespace App\Controller;

use App\Entity\UserEntity;
use App\Repository\UserRepository;
use App\Service\NormalizerService;
use App\Service\ValidatorService;

class RegisterController extends AbstractController
{

    private const VALID_REGISTER_FIELDS_NAME = ['pseudo', 'email', 'pass', 'verifpass'];


    public function run()
    {
        $errors = [];

        if($_POST){

            if(count($_POST) !== 4){
                $errors[] = 'We got less or more fields than 4';
                print_r($errors);die();
            }

            foreach($_POST as $inputName => $inputValue){
                
                if(!in_array($inputName, self::VALID_REGISTER_FIELDS_NAME)){
                    $errors[] = "Invalid input name : $inputName";
                    print_r($errors);die();
                }

                if(isset($_POST[$inputName]) && empty($_POST[$inputName])){
                    $errors[] = "Data missing in the field : $inputName";
                    print_r($errors);die();
                }
            }
            
            $validator = new ValidatorService();

            if(!$validator->checkLength($_POST['pseudo'], 2, 40)){
                $errors[] = "Your pseudo length should be at least 2 characters and max 40 characters";
                print_r($errors);die();
            }

            if(!$validator->checkLength($_POST['email'], 2, 40)){
                $errors[] = "Your email length should be at least 2 characters and max 40 characters";
                print_r($errors);die();
            }

            if(!$validator->checkLength($_POST['pass'], 6, 40)){
                $errors[] = "Your pass length should be at least 6 characters and max 40 characters";
                print_r($errors);die();
            }

            if($_POST['pass'] !== $_POST['verifpass'] ){
                $errors[] = "Passwords fields does not match";
                print_r($errors);die();
            }

            if(!$validator->checkValidity($_POST['pseudo'], UserEntity::REGEX_PSEUDO)){
                $errors[] = "Your pseudo doesn't respect the format";
                print_r($errors);die();
            }

            if(!$validator->checkValidity($_POST['email'], UserEntity::REGEX_EMAIL)){
                $errors[] = "Your email doesn't respect the format";
                print_r($errors);die();
            }

            // if(
            //     !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_1)
            //     || !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_2)
            //     || !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_3)
            //     || !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_4)
            // ){
            //     $errors[] = "Your pass should contain at least 1 min letter, 1 maj letter, 1 number 
            //                  and one of the following special character [.#~+=*\-_+²$=¤]";
            //     print_r($errors);die();
            // }

            $userEntity = new UserEntity();
            $userEntity->setPseudo(htmlspecialchars($_POST['pseudo']))
                       ->setMail(htmlspecialchars($_POST['email']))
                       ->setPassword(password_hash($_POST['pass'], PASSWORD_DEFAULT))
                       ->setRole(UserEntity::ROLE_USER);

            $normalizer = new NormalizerService();
            $user = $normalizer->denormalize($userEntity);

            $userRepository = new UserRepository();
            $userPseudoAlreadyExist = $userRepository->findOneBy(['pseudo' => $userEntity->getPseudo()]);
            $userEmailAlreadyExist = $userRepository->findOneBy(['mail' => $userEntity->getMail()]);

            if($userPseudoAlreadyExist){
                $errors[] = "The pseudo {$userEntity->getPseudo()} is already used";
                print_r($errors);die();
            }

            if($userEmailAlreadyExist){
                $errors[] = "The email {$userEntity->getMail()} is already used";
                print_r($errors);die();
            }

            $userRepository->create($user);

            // return redirection + flash message
            $this->addFlash('success', 'You succedeed to sign up !');
            $this->redirect('http://blogoc/?page=homepage');
        }

        return $this->render('RegisterTemplate');
    }

}