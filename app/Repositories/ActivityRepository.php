<?php


namespace App\Repositories;

use App\Models\Activity;

class ActivityRepository implements ActivityRepositoryInterface
{
    public function all(string $userid = null)
    {
        if (!empty($userid))
        {
            $result = Activity::where('userid', $userid)->get();
        }else{
            $result = Activity::all();

        }
        return (empty($result) ? false : $result);
    }

    public function find(string $uniqueId)
    {
        $result = Activity::where('uniqueId', $uniqueId)->first();
        return (empty($result) ? false : $result);
    }

    public function create(array $info)
    {
        $result = Activity::create($info);
        return (empty($result) ? false : $result);
    }

    public function update(string $uniqueId, array $info)
    {
        $result = Activity::where('uniqueId', $uniqueId);
        if ($result) {
            $result->update($info);
            return $result;
        }
        return false;
    }

    public function delete(string $uniqueId)
    {
        $result = Activity::where('uniqueId', $uniqueId);
        if ($result) {
            $result->delete();
            return $result;
        }
        return false;
    }

    public function restore(string $uniqueId)
    {
        $result = Activity::where('uniqueId', $uniqueId);
        if ($result) {
            $result->restore();
            return $result;
        }
        return false;
    }

    public function findByPhone(string $phone)
    {
        $result = Activity::where('phone', $phone)->first();
        return (empty($result) ? false : $result);

    }

    public function findByPhoneAndBusy(string $phone)
    {
        $result = Activity::where([
            ['phone', $phone],
            ['status', 2]
        ])->first();
        return (empty($result) ? false : $result);
    }

    public function fetchUserWorking(string $userid)
    {
        $result = Activity::where([
            ['userid', $userid],
            ['status', 2],
            ['customRent', 0]
        ])->orderBy('created_at', 'DESC')->get();
        return (empty($result) ? false : $result);
    }

    public function fetchUserWorkingCustom(string $userid)
    {
        $result = Activity::where([
            ['userid', $userid],
            ['status', 2],
            ['customRent', 1]
        ])->orderBy('created_at', 'DESC')->get();
        return (empty($result) ? false : $result);
    }
}