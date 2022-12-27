<?php

namespace App\Entity;


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

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ONLINE = 'online';
    public const REGEX_TEXT = '/^[A-Za-z0-9]+[A-Za-z0-9,;:€$£)(°&|\'-_\s]{0,}[A-Za-z0-9!?.]+$/';


    public function listProperties(): array
    {
        $properties = [];
        
        foreach($this as $key => $value){
            $properties[] = $key;
        }

        return $properties;
    }

    public function setComments($comments): self
    {
        $this->comments = $comments;
        return $this;
    }
    
    public function addComments($comment): self
    {
        $this->comments[] = $comment;
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

}