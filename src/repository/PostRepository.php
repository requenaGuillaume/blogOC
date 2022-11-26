<?php

namespace App\Repository;

class PostRepository extends PDOAbstractRepository
{
    protected string $table = 'post';
    protected array $optionnalColumns = [];
    protected array $requiredColumns = [
        'comments' => 'comments', 
        'title' => 'title', 
        'author_id' => 'author_id', 
        'head' => 'head',
        'last_update' => 'last_update',
        'status' => 'status',
        'content' => 'content',
        'slug' => 'slug'
    ];

}