<?php

namespace App\Repository;

final class PostRepository extends PDOAbstractRepository
{
    protected string $table = 'post';
    protected array $optionnalColumns = ['comments' => 'comments', 'created_at' => 'created_at'];
    protected array $requiredColumns = [
        'title' => 'title', 
        'author_id' => 'author_id', 
        'head' => 'head',
        'last_update' => 'last_update',
        'status' => 'status',
        'content' => 'content',
        'slug' => 'slug'
    ];

}