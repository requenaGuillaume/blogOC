<?php

namespace App\Entity;

final class UserEntity extends AbstractEntity
{
    private string $role;
    private string $mail;
    private string $pseudo;
    private string $password;


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

    public function normalize(array $array): self
    {
        foreach($array as $key => $value){
            $method = 'set'.ucfirst($key);

            if(str_contains($method, '_')){
                $letterAfterUnderscore = $method[strpos($method, '_') + 1];
                $letterTouppercase = strtoupper($letterAfterUnderscore);
                $method = str_replace("_$letterAfterUnderscore", $letterTouppercase, $method);
            }

            $this->$method($value);
        }

        return $this;
    }

    public function denormalize(): array
    {
        $array = [];

        foreach($this as $key => $value){
            $array[$key] = $value;
        }

        return $array;
    }

}