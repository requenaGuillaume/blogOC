<?php

namespace App\Service;


class NormalizerService
{

    public function normalize(array $array, string $entityClass): object
    {
        $entity = new $entityClass();

        foreach($array as $key => $value){
            $method = 'set'.ucfirst($key);

            if(str_contains($method, '_')){
                $letterAfterUnderscore = $method[strpos($method, '_') + 1];
                $letterTouppercase = strtoupper($letterAfterUnderscore);
                $method = str_replace("_$letterAfterUnderscore", $letterTouppercase, $method);
            }

            $entity->$method($value);
        }

        return $entity;
    }


    public function denormalize($entity): array
    {
        $array = [];
        $properties = $entity->listProperties();

        foreach($properties as $property){
            $getProperty = 'get'.ucfirst($property);
            $array[$property] = $entity->$getProperty();
        }

        return $array;
    }

}