<?php

namespace App\Controller;

class RegisterController extends AbstractController
{
    private const VALID_REGISTER_FIELDS_NAME = ['pseudo', 'email', 'pass', 'verifpass'];

    public function run()
    {
        var_dump($_POST);
        $errors = [];

        if($_POST){
            echo 'We sent data from our form<br>';

            foreach($_POST as $inputName => $inputValue){
                
                if(!in_array($inputName, self::VALID_REGISTER_FIELDS_NAME)){
                    // Throw exception !
                    $errors[] = "Invalid input name : $inputName";
                    print_r($errors);die();
                }

                // If REGEX does not matches, notify the user
                // if(){

                // }

                // If all good, secure data then put it into  UserEntity ?

                // Then insert the user in database

            }
        }

        return $this->render('RegisterTemplate');
    }

}