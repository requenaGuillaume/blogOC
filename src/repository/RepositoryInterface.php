<?php

namespace App\Repository;


interface RepositoryInterface
{

    public function update(array $values, int $id): void;

    public function create(array $values): void;
    
    public function delete(int $id): void;

    public function find(int $id): ?array;

    public function findAll(): array;

    public function findOneBy(array $data): ?array;

    public function findBy(array $data, ?array $orderCriterias = null, ?int $limit = null, ?int $offset = null): array;

}