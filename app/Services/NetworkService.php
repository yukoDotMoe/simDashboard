<?php


namespace App\Services;


use App\Models\Network;
use Exception;
use Illuminate\Support\Facades\Log;

class NetworkService
{
    protected $networkRepo;
    public function __construct(Network $networkRepo)
    {
        $this->networkRepo = $networkRepo;
    }

    public function getAll(bool $sims = false)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $result = $this->networkRepo->all();
            if (!$result || empty($result))
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot fetch network list');
                return [
                    'status' => 0,
                    'error' => 'Failed to fetch network list'
                ];
            }
            $networks = [];
            foreach ($result as $network)
            {
                $networks[] = [
                    'id' => $network['uniqueId'],
                    'name' => $network['networkName']
                ];
            }
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => $networks
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
            $result = $this->networkRepo->create([
                'networkName' => $name,
                'price' => $price,
                'status' => 1
            ]);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - end - error - Cannot create network');
                return [
                    'status' => 0,
                    'error' => 'Cannot create network'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Created network ' . $name
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
            $network = $this->networkRepo->find($id);
            if(!$network)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - network not found.');
                return [
                    'status' => 0,
                    'error' => 'network not found.'
                ];
            }
            $result = $this->networkRepo->update($id, $info);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update network');
                return [
                    'status' => 0,
                    'error' => 'Failed to update network'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Updated network successfully'
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
            $network = $this->networkRepo->find($id);
            if(!$network)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Network not found.');
                return [
                    'status' => 0,
                    'error' => 'Network not found.'
                ];
            }
            $result = $this->networkRepo->delete($id);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to delete Network');
                return [
                    'status' => 0,
                    'error' => 'Failed to delete Network'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Deleted Network successfully'
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
            $network = $this->networkRepo->find($id);
            if(!$network)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - network not found.');
                return [
                    'status' => 0,
                    'error' => 'network not found.'
                ];
            }
            $result = $this->networkRepo->restore($id);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to restore network');
                return [
                    'status' => 0,
                    'error' => 'Failed to restore network'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Restored network successfully'
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