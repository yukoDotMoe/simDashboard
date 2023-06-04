<?php


namespace App\Repositories;


interface SimsRepositoryInterface
{
    public function all();
    public function find(string $uniqueId);
    public function findByPhone(string $phone);
    public function newestPhone(string $service);
    public function create(array $info);
    public function update(string $uniqueId, array $info);
    public function delete(string $uniqueId);
    public function restore(string $uniqueId);
}