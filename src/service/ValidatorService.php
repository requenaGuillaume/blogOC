<?php

namespace App\Service;

class ValidatorService
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

        if($dataLength > $minLength && $dataLength < $maxLength){
            return true;
        }

        return false;
    }

}