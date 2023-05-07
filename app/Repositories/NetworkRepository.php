<?php


namespace App\Repositories;

use App\Models\Network;

class NetworkRepository implements NetworkRepositoryInterface
{
    public function all()
    {
        $result = Network::all();
        return (empty($result) ? false : $result);
    }

    public function find(string $uniqueId)
    {
        $result = Network::where('uniqueId', $uniqueId)->first();
        return (empty($result) ? false : $result);
    }

    public function create(array $info)
    {
        $result = Network::create($info);
        return (empty($result) ? false : $result);
    }

    public function update(string $uniqueId, array $info)
    {
        $result = Network::where('uniqueId', $uniqueId);
        if ($result) {
            $result->update($info);
            return $result;
        }
        return false;
    }

    public function delete(string $uniqueId)
    {
        $result = Network::where('uniqueId', $uniqueId);
        if ($result) {
            $result->delete();
            return $result;
        }
        return false;
    }

    public function restore(string $uniqueId)
    {
        $result = Network::where('uniqueId', $uniqueId);
        if ($result) {
            $result->restore();
            return $result;
        }
        return false;
    }

    public function findByName(string $name)
    {
        $result = Network::where('networkName', 'like', '%' . $name . '%')->first();
        return (empty($result) ? false : $result);
    }

    public function allActive()
    {
        $result = Network::where('status', 1)->get();
        return (empty($result) ? false : $result);
    }
}