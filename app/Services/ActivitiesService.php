<?php


namespace App\Services;


use App\Repositories\ActivityRepositoryInterface;
use App\Repositories\ServiceRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class ActivitiesService
{
    protected $activityRepo;
    protected $serviceRepo;
    public function __construct(ActivityRepositoryInterface $activityRepo, ServiceRepositoryInterface $serviceRepo)
    {
        $this->activityRepo = $activityRepo;
        $this->serviceRepo = $serviceRepo;
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

    public function create(string $phone, string $networkId, string $country, string $serviceId)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $id = substr(sha1(date("Y-m-d H:i:s")),0,10);
            $result = $this->activityRepo->create([
                'uniqueId' => $id,
                'phone' => $phone,
                'networkId' => $networkId,
                'countryCode' => $country,
                'serviceId' => $serviceId,
                'status' => 2,
                'reason' => 'Activity started',
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
                'data' => 'Created Activity',
                'id' => $id
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

    public function fetch(string $requestId)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $activity = $this->activityRepo->find($requestId);
            if(!$activity)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Request not found');
                return [
                    'status' => 0,
                    'error' => 'Request not found'
                ];
            }
            $service = $this->serviceRepo->find($activity['serviceId']);
            if(!$service) $service['serviceName'] = 'Deleted Service';

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => [
                    'requestId' => $requestId,
                    'phoneNumber' => $activity['phone'],
                    'countryCode' => $activity['countryCode'],
                    'serviceId' => $activity['serviceId'],
                    'serviceName' => $service['serviceName'],
                    'status' => $activity['status'],
                    'smsContent' => $activity['smsContent'],
                    'code' => $activity['code'],
                    'createdTime' => $activity['created_at'],
                ]
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