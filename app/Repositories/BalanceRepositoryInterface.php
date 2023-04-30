<?php


namespace App\Repositories;


interface BalanceRepositoryInterface
{
    public function all();
    public function find(string $uniqueId);
    public function create(array $info);
    public function update(string $uniqueId, array $info);
    public function delete(string $uniqueId);
    public function restore(string $uniqueId);
}