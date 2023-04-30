<?php


namespace App\Services;


use App\Repositories\ActivityRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class ActivitiesService
{
    protected $activityRepo;
    public function __construct(ActivityRepositoryInterface $activityRepo)
    {
        $this->activityRepo = $activityRepo;
    }

    public function getAll()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $result = $this->activityRepo->all();
            if (!$result || empty($result))
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot fetch Activity list');
                return [
                    'status' => 0,
                    'error' => 'Failed to fetch Activity list'
                ];
            }
            $Activitys = [];
            foreach ($result as $Activity)
            {
                $Activitys[] = [
                    'id' => $Activity['uniqueId'],
                    'name' => $Activity['ActivityName']
                ];
            }
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => $Activitys
            ];
        } catch (Exception $e)
        {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    public function create(string $name, string $price)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $result = $this->activityRepo->create([
                'ActivityName' => $name,
                'price' => $price,
                'status' => 1
            ]);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - end - error - Cannot create Activity');
                return [
                    'status' => 0,
                    'error' => 'Cannot create Activity'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Created Activity ' . $name
            ];
        } catch (Exception $e)
        {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    public function update(string $id, array $info)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $Activity = $this->activityRepo->find($id);
            if(!$Activity)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Activity not found.');
                return [
                    'status' => 0,
                    'error' => 'Activity not found.'
                ];
            }
            $result = $this->activityRepo->update($id, $info);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update Activity');
                return [
                    'status' => 0,
                    'error' => 'Failed to update Activity'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Updated Activity successfully'
            ];
        } catch (Exception $e)
        {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    public function delete(string $id)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $Activity = $this->activityRepo->find($id);
            if(!$Activity)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Activity not found.');
                return [
                    'status' => 0,
                    'error' => 'Activity not found.'
                ];
            }
            $result = $this->activityRepo->delete($id);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to delete Activity');
                return [
                    'status' => 0,
                    'error' => 'Failed to delete Activity'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Deleted Activity successfully'
            ];
        } catch (Exception $e)
        {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    public function restore(string $id)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $Activity = $this->activityRepo->find($id);
            if(!$Activity)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Activity not found.');
                return [
                    'status' => 0,
                    'error' => 'Activity not found.'
                ];
            }
            $result = $this->activityRepo->restore($id);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to restore Activity');
                return [
                    'status' => 0,
                    'error' => 'Failed to restore Activity'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Restored Activity successfully'
            ];
        } catch (Exception $e)
        {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}