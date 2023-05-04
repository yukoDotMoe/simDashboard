<?php


namespace App\Repositories;


interface ActivityRepositoryInterface
{
    public function all(string $userid = null);
    public function fetchUserWorking(string $userid);
    public function fetchUserWorkingCustom(string $userid);
    public function findByPhone(string $phone);
    public function find(string $uniqueId);
    public function findByPhoneAndBusy(string $phone);
    public function create(array $info);
    public function update(string $uniqueId, array $info);
    public function delete(string $uniqueId);
    public function restore(string $uniqueId);
}