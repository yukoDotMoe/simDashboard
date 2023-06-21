<?php


namespace App\Repositories;

use App\Models\Network;
use App\Models\Service;
use App\Models\Sims;
use App\Services\SimsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimsRepository implements SimsRepositoryInterface
{
    protected function getActiveNetwork()
    {
        $result = Network::where('status', 1)->get();
        $returnFinal = [];
        foreach ($result as $network)
        {
            $returnFinal[] = $network['uniqueId'];
        }
        return $returnFinal;
    }
    public function all()
    {
        $result = Sims::all();
        return (empty($result) ? false : $result);
    }

    public function find(string $uniqueId)
    {
        $result = Sims::where([
            ['uniqueId', $uniqueId]
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

    public function rotatePhoneNumber($service, $network = null)
    {
        $locked = DB::table('sim_lock')->where([
            ['services', $service]
        ])->get()->transform(function ($item) {
            return $item->phone;
        });
        
        if (!empty($network)) {
            $result = Sims::where([
            ['status', 1],
            ['networkId', $network]
            ])->whereNotIn('phone', $locked)->orderBy('updated_at', 'ASC')->first();
        }else{
            $result = Sims::where('status', 1)->whereNotIn('phone', $locked)->whereIn('networkId', $this->getActiveNetwork())->orderBy('updated_at', 'ASC')->first();
        }
        return (empty($result) ? false : $result);
    }

    public function newestPhone($serviceId, $network = null)
    {
        $service = Service::where('uniqueId', $serviceId)->first();
        $querry = [
            ['status', 1],
            ['deleted_at', null]
        ];
        if (!empty($network)) $querry['networkId'] = $network;
        $lockedList = DB::table('sim_lock')->where([
            ['services', $service]
        ])->get()->transform(function ($item) {
            return $item->phone;
        });
        $result = Sims::where($querry)->whereNotIn('phone', $lockedList)->whereIn('networkId', $this->getActiveNetwork())->orderBy('updated_at', 'ASC')->first();
        if (empty($result)) return false;
        
        $locked = DB::table('sim_lock')->where([
            ['phone', $result->phone],
            ['services', $serviceId]
        ])->first();
        
        if(empty($locked))
        {
            return $result;
        }else{
            if (Carbon::parse($locked->cooldown)->lte(Carbon::now()))
            {
                SimsService::deleteAllInTable($serviceId, $result->uniqueId);
                DB::table('sim_lock')->delete($locked->id);
                return $result;
            }
            return $this->rotatePhoneNumber($serviceId, $network);
        }
        // Log::info($result);
        // $currentLocked = json_decode($result->locked_services, true);
        // Log::info($serviceId);
        // Log::info($currentLocked);
        // if( array_key_exists($serviceId, $currentLocked ?? [])) {
        //     Log::info('locked found');
        //     return $this->rotatePhoneNumber($result->phone, $network);
        // };
        // return $result;
    }
}