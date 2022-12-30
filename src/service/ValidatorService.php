<?php

namespace App\Service;

use App\Interface\ValidatorInterface;


class ValidatorService implements ValidatorInterface
{

    public function checkValidity(string $data, string $regex): bool
    {
        if(preg_match($regex, $data)){
            return true;
        }

        return false;
    }

    public function checkLength(string $data, int $minLength, int $maxLength): bool
    {
        $dataLength = strlen($data);

        if($dataLength >= $minLength && $dataLength <= $maxLength){
            return true;
        }

        return false;
    }

}