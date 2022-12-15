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
        var_dump($_POST);
        $errors = [];

        if($_POST){
            echo 'We sent data from our form<br>';

            if(count($_POST) !== 4){
                // Throw exception !
                $errors[] = 'We got less or more fields than 4';
                print_r($errors);die();
            }

            foreach($_POST as $inputName => $inputValue){
                
                // Verify if there is only authorized fields
                if(!in_array($inputName, self::VALID_REGISTER_FIELDS_NAME)){
                    // Throw exception !
                    $errors[] = "Invalid input name : $inputName";
                    print_r($errors);die();
                }

                // And field is not empty
                if(isset($_POST[$inputName]) && empty($_POST[$inputName])){
                    // Throw exception !
                    $errors[] = "Data missing in the field : $inputName";
                    print_r($errors);die();
                }
            }
            
            $validator = new ValidatorService();

            // // If LENGTH aren't good
            if(!$validator->checkLength($_POST['pseudo'], 2, 40)){
                // Throw exception !
                $errors[] = "Your pseudo length should be at least 2 characters and max 40 characters";
                print_r($errors);die();
            }

            if(!$validator->checkLength($_POST['email'], 2, 40)){
                // Throw exception !
                $errors[] = "Your email length should be at least 2 characters and max 40 characters";
                print_r($errors);die();
            }

            if(!$validator->checkLength($_POST['pass'], 6, 40)){
                // Throw exception !
                $errors[] = "Your pass length should be at least 6 characters and max 40 characters";
                print_r($errors);die();
            }

            // Verify the two password matches
            if($_POST['pass'] !== $_POST['verifpass'] ){
                // Throw exception !
                $errors[] = "Passwords fields does not match";
                print_r($errors);die();
            }

            // If REGEX does not matches
            if(!$validator->checkValidity($_POST['pseudo'], UserEntity::REGEX_PSEUDO)){
                // Throw exception !
                $errors[] = "Your pseudo doesn't respect the format";
                print_r($errors);die();
            }

            if(!$validator->checkValidity($_POST['email'], UserEntity::REGEX_EMAIL)){
                // Throw exception !
                $errors[] = "Your email doesn't respect the format";
                print_r($errors);die();
            }

            if(
                !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_1)
                || !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_2)
                || !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_3)
                || !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_4)
            ){
                // Throw exception !
                $errors[] = "Your pass should contain at least 1 min letter, 1 maj letter, 1 number 
                             and one of the following special character [.#~+=*\-_+²$=¤]";
                print_r($errors);die();
            }

            // If all good, secure data then put it into  UserEntity
            $userEntity = new UserEntity();
            $userEntity->setPseudo(htmlspecialchars($_POST['pseudo']))
                       ->setMail(htmlspecialchars($_POST['email']))
                       ->setPassword(password_hash($_POST['pass'], PASSWORD_DEFAULT))
                       ->setRole(UserEntity::ROLE_USER);

            // Then insert the user in database
            $normalizer = new NormalizerService();
            
            $user = $normalizer->denormalize($userEntity);

            $userRepository = new UserRepository();

            $userAlreadyExist = $userRepository->findOneBy(['mail' => $userEntity->getMail()]);

            if($userAlreadyExist){
                // Throw exception !
                $errors[] = "The email {$userEntity->getMail()} is already used";
                print_r($errors);die();
            }

            $userRepository->create($user); // works

            // return redirection + flash message
            $this->redirect('http://blogoc/?page=homepage');
        }

        return $this->render('RegisterTemplate');
    }

}