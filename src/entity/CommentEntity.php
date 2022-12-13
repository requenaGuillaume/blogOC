<?php

namespace App\Entity;


final class CommentEntity extends AbstractEntity
{
    private int $postId;
    private int $authorId;
    private string $status;
    private string $content;
    private string $createdAt;


    public function listProperties(): array
    {
        $properties = [];
        
        foreach($this as $key => $value){
            $properties[] = $key;
        }

        return $properties;
    }

    public function setPostId(int $postId): self
    {
        $this->postId = $postId;
        return $this;
    }

    public function getPostId(): int
    {
        return $this->postId;
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

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

}