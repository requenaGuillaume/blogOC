<?php 

namespace App\Interface;

interface NormalizerInterface
{

    public function normalize(array $array, string $entityClass): object;

    public function denormalize($entity): array;

}