<?php

namespace App\Entity;


final class UserEntity extends AbstractEntity
{
    private string $role;
    private string $mail;
    private string $pseudo;
    private string $password;

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    public const REGEX_PSEUDO = '/^[A-Za-z0-9]+[A-Za-z0-9-_]{0,}[A-Za-z0-9]+$/';
    public const REGEX_EMAIL = "/^[a-zA-Z0-9.!#$%&'*+=?^_`{|}~-]+@{1}[a-zA-Z0-9]+[-]{0,}[a-zA-Z0-9]+\.{1}[a-zA-Z]{2,}/";
    public const REGEX_PASSWORD_1 = "/[a-z]{1}/";
    public const REGEX_PASSWORD_2 = "/[A-Z]{1}/";
    public const REGEX_PASSWORD_3 = "/[0-9]{1}/";
    public const REGEX_PASSWORD_4 = "/[.#~+=*\-_+²$=¤]{1}/";

    public function listProperties(): array
    {
        $properties = [];
        
        foreach($this as $key => $value){
            $properties[] = $key;
        }

        return $properties;
    }

    public function setRole($role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setMail($mail): self
    {
        $this->mail = $mail;
        return $this;
    }

    public function getMail(): string
    {
        return $this->mail;
    }

    public function setPseudo($pseudo): self
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function setPassword($password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

}