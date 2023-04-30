<?php


namespace App\Services;


use App\Models\User;
use App\Repositories\ActivityRepositoryInterface;
use App\Repositories\NetworkRepositoryInterface;
use App\Repositories\ServiceRepositoryInterface;
use App\Repositories\SimsRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use DB;

class SimsService
{
    protected $simsRepo;
    protected $networkRepo;
    protected $activityService;
    protected $balanceService;
    protected $serviceRepo;
    protected $activityRepo;
    public function __construct(
        SimsRepositoryInterface $simsRepo,
        NetworkRepositoryInterface $networkRepo,
        ActivitiesService $activityService,
        BalanceService $balanceService,
        ServiceRepositoryInterface $serviceRepo,
        ActivityRepositoryInterface $activityRepo
    )
    {
        $this->simsRepo = $simsRepo;
        $this->networkRepo = $networkRepo;
        $this->activityService = $activityService;
        $this->balanceService = $balanceService;
        $this->serviceRepo = $serviceRepo;
        $this->activityRepo = $activityRepo;
    }
    
    public function create(string $phone, string $countryCode, string $networkName)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $network = $this->networkRepo->findByName($networkName);
            if(!$network)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Network not found');
                return [
                    'status' => 0,
                    'error' => 'Network not found'
                ];
            }
            $id = substr(sha1(date("Y-m-d H:i:s")),0,10);
            $result = $this->simsRepo->create([
                'uniqueId' => $id,
                'phone' => $phone,
                'networkId' => $network['uniqueId'],
                'countryCode' => $countryCode,
                'status' => 1,
                'success' => 0,
                'failed' => 0,
            ]);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot create Sim');
                return [
                    'status' => 0,
                    'error' => 'Cannot create Sim'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Created sim under number: '.$phone,
                'id' => $id
            ];
        } catch (Exception $e)
        {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
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
            $sims = $this->simsRepo->find($id);
            if(!$sims)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - sims not found.');
                return [
                    'status' => 0,
                    'error' => 'sims not found.'
                ];
            }
            $result = $this->simsRepo->update($id, $info);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update sims');
                return [
                    'status' => 0,
                    'error' => 'Failed to update sims'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Updated sims successfully'
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
            $sims = $this->simsRepo->find($id);
            if(!$sims)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - sims not found.');
                return [
                    'status' => 0,
                    'error' => 'sims not found.'
                ];
            }
            $result = $this->simsRepo->delete($id);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to delete sims');
                return [
                    'status' => 0,
                    'error' => 'Failed to delete sims'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Deleted sims successfully'
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
            $sims = $this->simsRepo->find($id);
            if(!$sims)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - sims not found.');
                return [
                    'status' => 0,
                    'error' => 'sims not found.'
                ];
            }
            $result = $this->simsRepo->restore($id);
            if(!$result)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to restore sims');
                return [
                    'status' => 0,
                    'error' => 'Failed to restore sims'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Restored sims successfully'
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

    public function handleClientRequest(string $token, string $phone, string $network, string $content = null, string $code = null)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $countryCode = substr($phone, 0, 2);
            $phone = substr($phone, 2);
            $phoneData = $this->simsRepo->findByPhone($phone);
            if (!$phoneData)
            {
                DB::beginTransaction();
                $result = $this->create($phone, $countryCode, $network);
                DB::commit();
                if(!$result)
                {
                    DB::rollBack();
                    Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot create sim');
                    return [
                        'status' => 0,
                        'error' => 'Cannot create sim'
                    ];
                }
                Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
                return [
                    'status' => 1,
                    'data' => 'Sim created',
                    'id' => $result['id']
                ];
            }

            if ($phoneData['status'] > 1 && !empty($content))
            {
                $activity = $this->activityRepo->findByPhoneAndBusy($phone);
                if(!$activity)
                {
                    Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot find activity?');
                    return [
                        'status' => 0,
                        'error' => 'Cannot find activity?'
                    ];
                }

                DB::beginTransaction();
                $updateActivity = $this->activityService->update($activity['uniqueId'], [
                    'status' => 1,
                    'smsContent' => $content,
                    'code' => $code,
                    'reason' => 'Successfully'
                ]);
                DB::commit();

                if(!$updateActivity)
                {
                    DB::rollBack();
                    Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update activity');
                    return [
                        'status' => 0,
                        'error' => 'Failed to update activity'
                    ];
                }

                $balanceUpdate = $this->balanceService->handleHoldBalance($activity['uniqueId']);
                if($balanceUpdate['status'] == 0)
                {
                    DB::rollBack();
                    Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update balance transaction');
                    return [
                        'status' => 0,
                        'error' => 'Failed to update balance transaction'
                    ];
                }

                $finalResult = [
                    'status' => 1,
                    'data' => 'Code returned successfully'
                ];
            }else{
                $finalResult = [
                    'status' => 1,
                    'data' => 'All good, phone number seem to be breathing'
                ];
            }

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return $finalResult;
        } catch (Exception $e)
        {
            DB::rollBack();
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    // How renting works:
    // 1. User request to server. Then specific rent function will handle the request
    // 2. - Server mark phone number as status 2 (working)
    //    - Create hold balance
    //    - Create activity log
    // 3. Wait for client to send update then send to user.

    public function basicRent(string $token, string $serviceId)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $user = User::where('token', $token)->first();

            if(empty($user))
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot find user');
                return [
                    'status' => 0,
                    'error' => 'Cannot find user'
                ];
            }

            $service = $this->serviceRepo->find($serviceId);
            if(!$service)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Service not found');
                return [
                    'status' => 0,
                    'error' => 'Service not found'
                ];
            }

            $phone = $this->simsRepo->newestPhone();
            if(!$phone)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - No phone number available');
                return [
                    'status' => 0,
                    'error' => 'No phone number available'
                ];
            }
            DB::beginTransaction();
            // Change status to busy
            $updatePhone = $this->update($phone['uniqueId'], ['status' => 2]);

            // Create request for easy working
            $createRequest = $this->activityService->create($phone['phone'], $phone['networkId'], $phone['countryCode'], $serviceId);

            // Hold user balance
            $holdBalance = $this->balanceService->subtractBalance($user->id, $createRequest['id'], $service['price'], true);
            DB::commit();

            if(!$updatePhone)
            {
                DB::rollBack();
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot change phone number status');
                return [
                    'status' => 0,
                    'error' => 'Cannot change phone number status'
                ];
            }

            if(!$createRequest)
            {
                DB::rollBack();
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot start request');
                return [
                    'status' => 0,
                    'error' => 'Cannot start request'
                ];
            }

            if($holdBalance)
            {
                DB::rollBack();
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot update balance');
                return [
                    'status' => 0,
                    'error' => 'Cannot update balance'
                ];
            }

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => [
                    'phone' => $phone['phone'],
                    'country' => $phone['countryCode'],
                    'balance' => $user->balance - $service['price'],
                    'requestId' => $createRequest['id']
                ],
            ];
        } catch (Exception $e)
        {
            DB::rollBack();
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}