<?php

namespace App\Repository;

final class CommentRepository extends PDOAbstractRepository
{
    protected string $table = 'comment';
    protected array $optionnalColumns = ['status' => 'status'];
    protected array $requiredColumns = [
        'post_id' => 'post_id', 
        'author_id' => 'author_id', 
        'author_id' => 'author_id', 
        'content' => 'content'
    ];

}