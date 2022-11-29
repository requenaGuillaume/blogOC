<?php

namespace App\Entity;

final class CommentEntity extends AbstractEntity
{
    protected int $id;
    private int $postId;
    private int $authorId;
    private string $status;
    private string $content;


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

    public function normalize(array $array)
    {
        foreach($array as $key => $value){
            $method = 'set'.ucfirst($key);
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