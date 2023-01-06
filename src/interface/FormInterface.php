<?php

namespace App\Interface;

use App\Interface\ValidatorInterface;


interface FormInterface
{

    public function formHasError(ValidatorInterface $validator): bool;

    public function inputsHasDataLengthError(ValidatorInterface $validator): bool;

    public function dataHasFormatError(ValidatorInterface $validator): bool;

}