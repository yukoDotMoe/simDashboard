<?php


namespace App\Services;


use App\Repositories\NetworkRepositoryInterface;
use App\Repositories\ServiceRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class ServiceService
{
    protected $serviceRepo;
    public function __construct(ServiceRepositoryInterface $serviceRepo)
    {
        $this->serviceRepo = $serviceRepo;
    }

    public function getAll()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $result = $this->serviceRepo->all();
            if (!$result || empty($result))
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot fetch service list');
                return [
                    'status' => 0,
                    'error' => 'Failed to fetch service list'
                ];
            }
            $services = [];
            foreach ($result as $service)
            {
                $services[] = [
                    'id' => $service['uniqueId'],
                    'name' => $service['serviceName'],
                    'price' => $service['price'],
                ];
            }
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => $services
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
            $result = $this->serviceRepo->create([
                'uniqueId' => substr(sha1(date("Y-m-d H:i:s")),0,10),
                'serviceName' => $name,
                'price' => $price,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - end - error - Cannot create service');
                return [
                    'status' => 0,
                    'error' => 'Cannot create service'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Created service ' . $name
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
            $service = $this->serviceRepo->find($id);
            if(!$service)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Service not found.');
                return [
                    'status' => 0,
                    'error' => 'Service not found.'
                ];
            }
            $result = $this->serviceRepo->update($id, $info);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update service');
                return [
                    'status' => 0,
                    'error' => 'Failed to update service'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Updated service successfully'
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
            $service = $this->serviceRepo->find($id);
            if(!$service)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Service not found.');
                return [
                    'status' => 0,
                    'error' => 'Service not found.'
                ];
            }
            $result = $this->serviceRepo->delete($id);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to delete service');
                return [
                    'status' => 0,
                    'error' => 'Failed to delete service'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Deleted service successfully'
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
            $service = $this->serviceRepo->find($id);
            if(!$service)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Service not found.');
                return [
                    'status' => 0,
                    'error' => 'Service not found.'
                ];
            }
            $result = $this->serviceRepo->restore($id);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to restore service');
                return [
                    'status' => 0,
                    'error' => 'Failed to restore service'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Restored service successfully'
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

    public function addUseCount(string $id)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $service = $this->serviceRepo->find($id);
            if(!$service)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Service not found');
                return [
                    'status' => 0,
                    'error' => 'Service not found'
                ];
            }
            $result = $this->update($id,[
                'used' => $service->used++
            ]);
            if($result['status'] == 0)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return [
                    'status' => 0,
                    'error' => $result['error']
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Added to service ' . $id
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