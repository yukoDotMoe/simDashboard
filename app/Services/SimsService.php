<?php


namespace App\Services;


use App\Events\SmsDelivered;
use App\Models\Activity;
use App\Models\Network;
use App\Models\Sims;
use App\Models\User;
use App\Repositories\ActivityRepositoryInterface;
use App\Repositories\NetworkRepositoryInterface;
use App\Repositories\ServiceRepositoryInterface;
use App\Repositories\SimsRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use DB;
use Pusher\Pusher;

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

    protected function sendNotify(string $userid, array $data)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $options = array(
                'cluster' => 'ap1',
                'encrypted' => true
            );

            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            $pusher->trigger('user-flow.' . $userid, 'simUpdateNotify', $data);
            return array(
                'status' => 1,
                'data' => 'Done'
            );
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function basicRentView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $services = $this->serviceRepo->allActive();
            $workingTask = $this->activityRepo->fetchUserWorking(Auth::user()->id);
            foreach ($workingTask as $task)
            {
                $service = $this->serviceRepo->find($task['serviceId']);
                $task['serviceName'] = $service['serviceName'];
                $task['servicePrice'] = $service['price'];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'services' => $services ?? [],
                    'workingTask' => $workingTask ?? []
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function rentHistoryView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $activities = Activity::where('userid', Auth::user()->id)->paginate(15);
            foreach ($activities as $task)
            {
                $service = $this->serviceRepo->find($task['serviceId']);
                $task['serviceName'] = $service['serviceName'];
                $task['servicePrice'] = $service['price'];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'activities' => $activities ?? [],
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function customRentView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $services = $this->serviceRepo->allActive();
            $networks = $this->networkRepo->allActive();
            $workingTask = $this->activityRepo->fetchUserWorkingCustom(Auth::user()->id);
            foreach ($workingTask as $task)
            {
                $service = $this->serviceRepo->find($task['serviceId']);
                $task['serviceName'] = $service['serviceName'];
                $task['servicePrice'] = $service['price'];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'services' => $services ?? [],
                    'networks' => $networks ?? [],
                    'workingTask' => $workingTask ?? []
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function fetchRequest(string $requestId)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $activity = $this->activityRepo->find($requestId);
            if (!$activity)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Request not found');
                return [
                    'status' => 0,
                    'error' => 'Request not found'
                ];
            }
            $service = $this->serviceRepo->find($activity['serviceId']);
            if (!$service) $service['serviceName'] = 'Deleted Service';
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'requestId' => $activity['uniqueId'],
                    'phoneNumber' => $activity['phone'],
                    'countryCode' => $activity['countryCode'],
                    'serviceId' => $activity['serviceId'],
                    'serviceName' => $service['serviceName'],
                    'status' => $activity['status'],
                    'createdTime' => $activity['created_at'],
                    'smsContent' => $activity['smsContent'],
                    'code' => $activity['code'],
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
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
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
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
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine() . ' - ' . $e->getMessage());
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
            if ($token != config('simConfig.adminToken')) return [
                'status' => 0,
                'error' => 'Unauthorized Access'
            ];
            $phoneData = $this->simsRepo->findByPhone($phone);
            if (!$phoneData)
            {
                DB::beginTransaction();
                $result = $this->create($phone, 84, $network);
                DB::commit();
                if($result['status'] == 0)
                {
                    DB::rollBack();
                    Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                    return [
                        'status' => 0,
                        'error' => $result['error']
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

                $this->sendNotify($activity['userid'], [
                    'uniqueId' => $activity['uniqueId'],
                    'status' => 1,
                    'content' => $content,
                    'code' => $code
                ]);

                $service = $this->serviceRepo->find($activity['serviceId']);
                $this->serviceRepo->update($activity['serviceId'], ['used' => $service['used']+1]);

                $user = User::where('id', $activity['userid'])->first();
                $user->totalRent = $user->totalRent + 1;
                $user->save();

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

    public function rentFunc(string $token, string $phoneNumber, string $serviceId, bool $custom = false)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $phoneNumber);
            $user = User::where('api_token', $token)->first();

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

            $phone = $this->simsRepo->findByPhone($phoneNumber);
            if(!$phone)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - No phone number available');
                return [
                    'status' => 0,
                    'error' => 'No phone number available'
                ];
            }
            if ($phone['status'] != 1)
            {
                return [
                    'status' => 0,
                    'error' => 'Phone number not available'
                ];    
            }

            $network = $this->networkRepo->find($phone['networkId']);
            if (!$network)
            {
                return [
                    'status' => 0,
                    'error' => 'Network not found'
                ];
            }

            Log::info($network);
            if ($network['status'] < 1)
            {
                return [
                    'status' => 0,
                    'error' => 'No phone number available'
                ];
            }
            
            DB::beginTransaction();
            // Hold user balance
            $holdBalance = $this->balanceService->subtractBalance($user->id, $service['price'], true);
            DB::commit();
            if($holdBalance['status'] == 0)
            {
                DB::rollBack();
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $holdBalance['error']);
                return [
                    'status' => 0,
                    'error' => $holdBalance['error']
                ];
            }

            DB::beginTransaction();
            // Change status to busy
            $updatePhone = $this->update($phone['uniqueId'], ['status' => 2]);
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

            DB::beginTransaction();
            // Create request for easy working
            $createRequest = $this->activityService->create($user->id, $phone['phone'], $phone['networkId'], $phone['countryCode'], $serviceId, $holdBalance['id'], $custom);
            DB::commit();
            if($createRequest['status'] == 0)
            {
                DB::rollBack();
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $createRequest['error']);
                return [
                    'status' => 0,
                    'error' => $createRequest['error']
                ];
            }

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => [
                    'phone' => $phone['phone'],
                    'country' => $phone['countryCode'],
                    'balance' => $user->balance - $service['price'],
                    'requestId' => $createRequest['id'],
                    'createdTime' => date('Y-m-d H:i:s')
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

    public function basicRent(string $token, string $serviceId, string $phoneNumber = null)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            if (!empty($phoneNumber))
            {
                $phone = $this->simsRepo->findByPhone($phoneNumber);
                if(!$phone)
                {
                    Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Phone not found');
                    return [
                        'status' => 0,
                        'error' => 'Phone not found'
                    ];
                }
            }else{
                $phone = $this->simsRepo->newestPhone();
            }
            if (!$phone)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - No phone number available');
                return [
                    'status' => 0,
                    'error' => 'No phone number available'
                ];
            }
            $result = $this->rentFunc($token, $phone['phone'], $serviceId);
            if($result['status'] == 0)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - '. $result['error']);
                return [
                    'status' => 0,
                    'error' => $result['error']
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => $result['data']
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

    public function networkRent(string $token, string $serviceId, string $network)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $network);
            $network = $this->networkRepo->find($network);
            if(!$network)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Network not found');
                return [
                    'status' => 0,
                    'error' => 'Network not found'
                ];
            }
            $phone = Sims::where([
                ['status', 1],
                ['networkId', $network['uniqueId']]
            ])->first();
            if(!$phone)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - No phone number available');
                return [
                    'status' => 0,
                    'error' => 'No phone number available'
                ];
            }
            $result = $this->rentFunc($token, $phone['phone'], $serviceId, true);
            if($result['status'] == 0)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - '. $result['error']);
                return [
                    'status' => 0,
                    'error' => $result['error']
                ];
            }

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => $result['data']
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

    public function rentStartWith(string $token, string $serviceId, string $number, bool $include = true)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $dbQuery = [
                ['status', 1],
            ];
            if ($include)
            {
                $dbQuery[] = ['phone', 'LIKE', $number . '%'];
            }else{
                $dbQuery[] = ['phone', 'NOT LIKE', $number . '%'];
            }
            $phone = Sims::where($dbQuery)->whereIn('networkId', $this->getActiveNetwork())->first();
            $result = $this->rentFunc($token, $phone['phone'], $serviceId, true);
            if($result['status'] == 0)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - '. $result['error']);
                return [
                    'status' => 0,
                    'error' => $result['error']
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => $result['data']
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