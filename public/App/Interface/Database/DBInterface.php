<?php

namespace App\Interface\Database;

interface DBInterface
{
    public function db_connect(): bool;
    public function table(string $table): object;
    //GET OR ACTION
    public function get(): ?array;
    public function first(): ?array;
    public function delete(): bool;
    public function count(): int;
    public function max(string $field): array;
    public function min(string $field): array;
    public function avg(string $field): array;
    public function sum(string $field): array;
    public function insert(array $fields): bool; // [] or [[], []]
    public function insertGetId(array $fields): int; // []
    public function update(array $fields): bool; // [] 
    public function increment(string $field): bool;
    public function decrement(string $field): bool;
    //GET OR ACTION
}
