<?php

namespace App\Entity;

class UserEntity
{
    private int $id;
    private string $role;
    private string $mail;
    private string $pseudo;
    private string $password;


    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
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