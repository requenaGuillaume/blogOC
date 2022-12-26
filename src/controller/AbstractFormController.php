<?php

namespace App\Controller;

use App\Entity\UserEntity;
use App\Service\ValidatorService;
use App\Controller\AbstractController;


abstract class AbstractFormController extends AbstractController
{
    protected function verifyInputCount(int $numberOfInputFound, int $expected): ?string
    {
        if($numberOfInputFound !== $expected){
            return "We got less or more fields than $expected";
        }

        return null;
    }

    protected function verifyInputsValidity(array $postArray, array $validInputsName): ?string
    {
        foreach($postArray as $inputName => $inputValue){
                
            if(!in_array($inputName, $validInputsName)){
                return "Invalid input name : $inputName";
            }

            if(isset($postArray[$inputName]) && empty($postArray[$inputName])){
                return "Data missing in the field : $inputName";
            }
        }

        return null;
    }

    protected function verifyDataLenght(ValidatorService $validator, string $inputName, int $minLength, int $maxLength): ?string
    {
        if(!$validator->checkLength($_POST[$inputName], $minLength, $maxLength)){
            return "Your $inputName length should be at least $minLength characters and max $maxLength characters";
        }

        return null;
    }

    protected function verifyDataFormat(ValidatorService $validator, string $inputName, string $regex): ?string
    {
        if(!$validator->checkValidity($_POST[$inputName], $regex)){
            return "Your $inputName doesn't respect the format";
        }

        return null;
    }

    protected function verifyPasswordFormat(ValidatorService $validator): ?string
    {
        if(
            !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_1)
            || !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_2)
            || !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_3)
            || !$validator->checkValidity($_POST['pass'], UserEntity::REGEX_PASSWORD_4)
        ){
            return "
                    Your pass should contain at least 1 min letter, 1 maj letter, 1 number 
                    and one of the following special character [.#~+=*\-_+²$=¤]
                    "
                ;
        }

        return null;
    }
}