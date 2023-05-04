<?php


namespace App\Repositories;

use App\Models\Service;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function all()
    {
        $result = Service::all();
        return (empty($result) ? false : $result);
    }

    public function find(string $uniqueId)
    {
        $result = Service::where('uniqueId', $uniqueId)->first();
        return (empty($result) ? false : $result);
    }

    public function create(array $info)
    {
        $result = Service::create($info);
        return (empty($result) ? false : $result);
    }

    public function update(string $uniqueId, array $info)
    {
        $result = Service::where('uniqueId', $uniqueId);
        if ($result) {
            $result->update($info);
            return $result;
        }
        return false;
    }

    public function delete(string $uniqueId)
    {
        $result = Service::where('uniqueId', $uniqueId);
        if ($result) {
            $result->delete();
            return $result;
        }
        return false;
    }

    public function restore(string $uniqueId)
    {
        $result = Service::where('uniqueId', $uniqueId);
        if ($result) {
            $result->restore();
            return $result;
        }
        return false;
    }

    public function allActive()
    {
        $result = Service::where('status', 1)->get();
        return (empty($result) ? false : $result);
    }
}