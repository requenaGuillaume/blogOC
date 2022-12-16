<?php

namespace App\Entity;

abstract class AbstractEntity
{
    protected int $id;
    
    // Child cannot use it for denormalize - TODO : try again to remove duplicate code in every entity !
    // public function listProperties(): array
    // {
    //     $properties = [];
        
    //     foreach($this as $key => $value){
    //         $properties[] = $key;
    //     }

    //     return $properties;
    // }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
}