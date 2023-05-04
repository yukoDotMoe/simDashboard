<?php


namespace App\Repositories;

use App\Models\Balance;

class BalanceRepository implements BalanceRepositoryInterface
{
    public function all(string $userid = null)
    {
        if (!empty($userid))
        {
            $result = Balance::where('accountId', $userid)->get();
        }else{
            $result = Balance::all();
        }
        return (empty($result) ? false : $result);
    }

    public function find(string $uniqueId)
    {
        $result = Balance::where('uniqueId', $uniqueId)->first();
        return (empty($result) ? false : $result);
    }

    public function create(array $info)
    {
        $result = Balance::create($info);
        return (empty($result) ? false : $result);
    }

    public function update(string $uniqueId, array $info)
    {
        $result = Balance::where('uniqueId', $uniqueId);
        if ($result) {
            $result->update($info);
            return $result;
        }
        return false;
    }

    public function delete(string $uniqueId)
    {
        $result = Balance::where('uniqueId', $uniqueId);
        if ($result) {
            $result->delete();
            return $result;
        }
        return false;
    }

    public function restore(string $uniqueId)
    {
        $result = Balance::where('uniqueId', $uniqueId);
        if ($result) {
            $result->restore();
            return $result;
        }
        return false;
    }

    public function findByActivity(string $uniqueId)
    {
        $result = Balance::where('activityId', $uniqueId)->first();
        return (empty($result) ? false : $result);
    }
}