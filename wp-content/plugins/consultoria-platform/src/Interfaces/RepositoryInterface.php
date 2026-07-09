<?php

namespace Consultoria\Interfaces;

interface RepositoryInterface {
    public function find(int $id): ?object;
    public function findAll(array $criteria = []): array;
    public function create(array $data): ?int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
