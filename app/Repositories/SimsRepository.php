<?php


namespace App\Repositories;

use App\Models\Sims;

class SimsRepository implements SimsRepositoryInterface
{
    public function all()
    {
        $result = Sims::all();
        return (empty($result) ? false : $result);
    }

    public function find(string $uniqueId)
    {
        $result = Sims::where([
            ['uniqueId', $uniqueId],
            ['deleted_at', null]
        ])->first();
        return (empty($result) ? false : $result);
    }

    public function create(array $info)
    {
        $result = Sims::create($info);
        return (empty($result) ? false : $result);
    }

    public function update(string $uniqueId, array $info)
    {
        $result = Sims::where('uniqueId', $uniqueId);
        if ($result) {
            $result->update($info);
            return $result;
        }
        return false;
    }

    public function delete(string $uniqueId)
    {
        $result = Sims::where('uniqueId', $uniqueId);
        if ($result) {
            $result->delete();
            return $result;
        }
        return false;
    }

    public function restore(string $uniqueId)
    {
        $result = Sims::where('uniqueId', $uniqueId);
        if ($result) {
            $result->restore();
            return $result;
        }
        return false;
    }

    public function findByPhone(string $phone)
    {
        $result = Sims::where('phone', $phone)->first();
        return (empty($result) ? false : $result);
    }

    public function newestPhone()
    {
        $result = Sims::where([
            ['status', 1],
            ['deleted_at', null]
        ])->orderBy('updated_at', 'DESC')->first();
        return (empty($result) ? false : $result);
    }
}