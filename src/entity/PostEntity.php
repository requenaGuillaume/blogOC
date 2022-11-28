<?php

namespace App\Entity;

use DateTime;

final class PostEntity extends AbstractEntity
{
    private int $id;
    private array $comments;
    private string $title;
    private int $authorId;
    private string $head;
    private DateTime $lastUpdate;
    private string $status;
    private string $content;
    private string $slug;


    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setComments(array $comments): self
    {
        $this->comments = $comments;
        return $this;
    }
    
    public function addComments(array $comment): self
    {
        $this->comments[] = $comment;
        return $this;
    }

    public function getComments(): array
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

    public function getLastUpdate(): DateTime
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
    
    public function denormalize(): array
    {
        $array = [];

        foreach($this as $key => $value){
            $array[$key] = $value;
        }

        return $array;
    }
}