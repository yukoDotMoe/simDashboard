<?php


namespace App\Repositories;

use App\Models\Network;
use App\Models\Service;
use App\Models\Sims;
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

    protected function rotatePhoneNumber($phone, $network = null)
    {
        $querry = [
            ['status', 1],
            ['deleted_at', null],
            ['phone', '!=', $phone]
        ];

        if (!empty($network)) {
            $querry['networkId'] = $network;
            $result = Sims::where($querry)->first();
        }else{
            $result = Sims::where($querry)->whereIn('networkId', $this->getActiveNetwork())->orderBy('updated_at', 'ASC')->first();
        }
        return (empty($result) ? false : $result);
    }

    protected function cooldownSim($serviceId, $phone)
    {
        // 1: return result
        // 2: rotate number

        $result = Sims::where('phone', $phone)->whereIn('networkId', $this->getActiveNetwork())->orderBy('updated_at', 'ASC')->first();
        $currentLocked = json_decode($result->locked_services, true);

        $service = Service::where('uniqueId', $serviceId)->first();
        // check if sim's service has been lock
        if(!isset($currentLocked[$serviceId])) {
            $currentLocked[$serviceId] = [
                'name' => $service->serviceName,
                'cooldown' => Carbon::now()->addHours($service['cooldown'])
            ];
            $gonnaReturn = 2;
        }else{
            // check if cooldown expired
            if (Carbon::parse($currentLocked[$serviceId]['cooldown'])->lte(Carbon::now()))
            {
                DB::table('sim_activities')->where([
                    ['phoneNumber', $phone],
                    ['serviceId', $serviceId],
                    ['deleted_at', NULL]
                ])->update(['deleted_at' => Carbon::now()]);
                unset($currentLocked[$serviceId]);
                $gonnaReturn = 1;
            }else{
                $currentLocked[$serviceId] = [
                    'name' => $service->serviceName,
                    'cooldown' => Carbon::now()->addHours($service['cooldown'])
                ];
                $gonnaReturn = 2;
            }
        }

        $result->locked_services = json_encode($currentLocked);
        $result->save();

        return $gonnaReturn;
    }

    public function newestPhone($serviceId, $network = null)
    {
        // get serivce
        $service = Service::where('uniqueId', $serviceId)->first();

        $querry = [
            ['status', 1],
            ['deleted_at', null]
        ];

        if (!empty($network)) $querry['networkId'] = $network;

        // get random sim
        $result = Sims::where($querry)->whereIn('networkId', $this->getActiveNetwork())->orderBy('updated_at', 'ASC')->first();

        if (empty($result)) return false;

        // check if service has use limit
        if ($service['limit'] >= 1)
        {
            // get sim's uses by service id and count it
            $useCount = DB::table('sim_activities')->where([
                ['phoneNumber', $result['phone']],
                ['serviceId', $serviceId],
                ['deleted_at', NULL]
            ])->count();

            if ($useCount >= $service['limit'])
            {
                $cooldownNumber = $this->cooldownSim($serviceId, $result['phone']);
                if ($cooldownNumber > 1) return $this->rotatePhoneNumber($result['phone'], $network);
            }
        }

        if ($service['success'] >= 1)
        {
            $successCount = DB::table('success_records')->where([
                ['phone', $result['phone']],
                ['serviceId', $serviceId],
                ['deleted_at', NULL]
            ])->count();

            if ($successCount >= $service['success'])
            {
                $cooldownNumber = $this->cooldownSim($serviceId, $result['phone']);
                if ($cooldownNumber > 1) return $this->rotatePhoneNumber($result['phone'], $network);
            }
        }

        if ($service['fail'] >= 1)
        {
            $failedCount = DB::table('failed_records')->where([
                ['phone', $result['phone']],
                ['serviceId', $serviceId],
                ['deleted_at', NULL]
            ])->count();

            if ($failedCount >= $service['fail'])
            {
                $cooldownNumber = $this->cooldownSim($serviceId, $result['phone']);
                if ($cooldownNumber > 1) return $this->rotatePhoneNumber($result['phone'], $network);
            }
        }

        return $result;
    }
}