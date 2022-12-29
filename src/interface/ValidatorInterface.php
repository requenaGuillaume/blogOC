<?php

namespace App\Interface;


interface ValidatorInterface
{

    public function checkValidity(string $data, string $regex): bool;

    public function checkLength(string $data, int $minLength, int $maxLength): bool;

}