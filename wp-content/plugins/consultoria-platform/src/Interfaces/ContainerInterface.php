<?php

namespace Consultoria\Interfaces;

interface ContainerInterface {
    public function get(string $id): ?object;
    public function set(string $id, object $service): void;
    public function has(string $id): bool;
}
