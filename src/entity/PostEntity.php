<?php

namespace App\Entity;

use DateTime;

final class PostEntity extends AbstractEntity
{
    private ?string $comments;
    private string $title;
    private int $authorId;
    private string $head;
    private string $lastUpdate;
    private string $status;
    private string $content;
    private string $slug;


    public function setComments($comments): self
    {
        // json_encode
        $this->comments = $comments;
        return $this;
    }
    
    public function addComments($comment): self
    {
        // $this->comments[] = $comment;
        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setAuthorId(int $authorId): self
    {
        $this->authorId = $authorId;
        return $this;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function setHead(string $head): self
    {
        $this->head = $head;
        return $this;
    }

    public function getHead(): string
    {
        return $this->head;
    }

    public function setLastUpdate(string $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    public function getLastUpdate(): string
    {
        return $this->lastUpdate;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
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