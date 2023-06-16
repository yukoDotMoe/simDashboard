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
use Carbon\Carbon;
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
    protected $networkService;
    public function __construct(
        SimsRepositoryInterface $simsRepo,
        NetworkRepositoryInterface $networkRepo,
        NetworkService $networkService,
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
        $this->networkService = $networkService;
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
            $networks = $this->networkRepo->allActive();
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
                    'networks' => $networks ?? [],
                    'workingTask' => $workingTask ?? []
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine() . ' - ' . $e->getMessage());
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
            if (!$service)
            {
                $service['serviceName'] = 'Deleted Service';
                $service['serviceId'] = 'Deleted Service';
            };
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'requestId' => $activity['uniqueId'],
                    'phoneNumber' => '+'. $activity['countryCode'] . $activity['phone'],
                    'serviceId' => $activity['serviceId'],
                    'serviceName' => $service['serviceName'],
                    'status' => $activity['status'],
                    'createdTime' => date_format(date_create($activity['created_at']),"Y-m-d H:i:s"),
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
    
    public function create(string $phone, string $countryCode, string $networkName, bool $vendor = false, string $vendorId = null)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $id = substr(sha1(date("Y-m-d H:i:s") . rand(11, 1223)),1,11);
            $payload = [
                'uniqueId' => $id,
                'phone' => $phone,
                'networkId' => $networkName,
                'countryCode' => $countryCode,
                'status' => 1,
                'success' => 0,
                'failed' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            if($vendor) $payload['userid'] = $vendorId;
            $result = $this->simsRepo->create($payload);
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

    public static function addSimResult($phone, $service, $request, $status, $reason = null)
    {
        $table = ($status == 1) ? 'success_records' : 'failed_records';
        $id = substr(sha1(date("Y-m-d H:i:s")),0,10);
        $result = DB::table($table)->insertGetId([
            'uniqueId' => $id,
            'phone' => $phone,
            'serviceId' => $service,
            'requestId' => $request,
            'reason' => $reason,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        return (empty($result)) ? false : $id;
    }

    public function handleClientRequest($data, $vendor = false, $vendorId = null)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $data . ' - ' . $vendor . ' - ' . $vendorId);
//             let assume the data will be:
             $test = [
// For sending heartbeat
'0237548237' => [
    'code' => null,
    'network' => 'Viettel'
],

// For update code
'0237548237' => [
    'code' => 'noi dung sms',
    'network' => 'Viettel'
],

// For create new number
'0237548237' => [
    'code' => null,
    'network' => 'Viettel'
]
            ];

            $data = json_decode($data, true);
            $returnVar = [];
            foreach ($data as $simNumber => $simData)
            {
                $simNumber = str_replace(' ', '', $simNumber);
                $phoneData = Sims::where('phone', $simNumber)->first();

                if (!isset($simData['network']))
                {
                    Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Missing action');
                    $returnVar[$simNumber] = [
                        'status' => 0,
                        'error' => 'Missing network'
                    ];
                    continue;
                }

                $network = Network::where([
                    ['networkName', $simData['network']]
                ])->first();

                if (empty($network))
                {
                    $createNetwork = $this->networkService->create($simData['network']);
                    $network = $this->networkRepo->find($createNetwork['uniqueId']);
                }

                if (strlen($simNumber) > (env('DEFAULT_SIM_LENGTH') ?? 11))
                {
                    $returnVar[$simNumber] = [
                        'status' => 0,
                        'error' => 'Wrong number format'
                    ];
                    continue;
                }

                if (!$phoneData)
                {
                    DB::beginTransaction();
                    $result = $this->create($simNumber, 84, $network['uniqueId'], $vendor, $vendorId);
                    DB::commit();
                    if($result['status'] == 0)
                    {
                        DB::rollBack();
                        Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                        $returnVar[$simNumber] = [
                            'status' => 0,
                            'error' => $result['error']
                        ];
                        continue;
                    }
                    Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
                    $returnVar[$simNumber] = [
                        'status' => 1,
                        'data' => 'Successfully added to database.'
                    ];
                    continue;
                }

                if ($phoneData['status'] > 1)
                {
                    $activity = $this->activityRepo->findByPhoneAndBusy($simNumber);
                    if(empty($activity))
                    {
                        Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot find activity?');
                        $returnVar[$simNumber] = [
                            'status' => 0,
                            'error' => 'Cannot find queued job for this number, switched to send heartbeat..'
                        ];

                        $phoneData->updated_at = Carbon::now();
                        $phoneData->save();

                        continue;
                    }

                    if (empty($simData['code']))
                    {
                        Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Content cannot be null');
                        $returnVar[$simNumber] = [
                            'status' => 0,
                            'error' => 'You cannot provide an empty content, please send with content to finish request'
                        ];

                        $phoneData->updated_at = Carbon::now();
                        $phoneData->save();

                        continue;
                    }

                    $service = $this->serviceRepo->find($activity['serviceId']);
                    if (empty($service['valid']) || empty($service['structure']))
                    {
                        Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - This service cannot handle your request right now.');
                        $returnVar[$simNumber] = [
                            'status' => 0,
                            'error' => 'This service cannot handle your request right now. Please contact site admin'
                        ];

                        $phoneData->updated_at = Carbon::now();
                        $phoneData->save();

                        continue;
                    }

                    // Check the content if the content valid
                    $arrayToCheckFromDB = preg_split("/\r\n|\n|\r/", $service['valid']);
                    Log::info(json_encode($arrayToCheckFromDB));
                    $noValidFound = true;
                    foreach ($arrayToCheckFromDB as $validContent) {
                        if (preg_match("/\b$validContent\b/", $simData['code'])) {
                            $noValidFound = false;
                            break;
                        }
                    }
                    if ($noValidFound) {
                        Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Sms content not valid');
                        $returnVar[$simNumber] = [
                            'status' => 0,
                            'error' => 'Sms content not valid'
                        ];

                        $phoneData->updated_at = Carbon::now();
                        $phoneData->save();

                        continue;
                    }

                    $extractor = preg_match($service['structure'], $simData['code'], $extractedCode);
                    if (!$extractor)
                    {
                        Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Cannot extract code');
                        $returnVar[$simNumber] = [
                            'status' => 0,
                            'error' => 'Cannot extract code from your content, please try again'
                        ];

                        $phoneData->updated_at = Carbon::now();
                        $phoneData->save();

                        continue;
                    }

                    DB::beginTransaction();
                    $updateContent = [
                        'status' => 1,
                        'code' => $extractedCode[0],
                        'smsContent' => $simData['code'],
                        'reason' => 'Code successfully returned',
                        'handleByVendor' => $vendorId
                    ];
                    $updateActivity = $this->activityService->update($activity['uniqueId'], $updateContent);
                    DB::commit();

                    if(!$updateActivity)
                    {
                        DB::rollBack();
                        Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update activity');
                        $returnVar[$simNumber] = [
                            'status' => 0,
                            'error' => 'Failed to update activity'
                        ];

                        $phoneData->updated_at = Carbon::now();
                        $phoneData->save();

                        continue;
                    }

                    $metadataRequest = json_decode($activity['metadata'], true);
                    Log::info(json_encode($metadataRequest));
                    if (isset($metadataRequest['isApi']))
                    {
                        if (!$metadataRequest['isApi'])
                        {
                            $this->sendNotify($activity['userid'], [
                                'uniqueId' => $activity['uniqueId'],
                                'status' => 1,
                                'code' => $extractedCode[0]
                            ]);
                        }
                    }

                    DB::beginTransaction();
                    $this->serviceRepo->update($activity['serviceId'], ['used' => ((is_int($service['used']) ? $service['used'] : 0))+1]);
                    Sims::where('uniqueId', $phoneData['uniqueId'])->update(['success' => $phoneData['success'] + 1, 'status' => 1, 'networkId' => $network['uniqueId'], 'userid' => $vendorId]);

                    $user = User::where('id', $activity['userid'])->first();
                    $user->totalRent = $user->totalRent + 1;
                    $user->save();
                    DB::commit();

                    $balanceUpdate = $this->balanceService->handleHoldBalance($activity['uniqueId']);
                    if($balanceUpdate['status'] == 0)
                    {
                        DB::rollBack();
                        Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update balance transaction');
                        $returnVar[$simNumber] = [
                            'status' => 0,
                            'error' => 'Failed to update balance transaction'
                        ];

                        $phoneData->updated_at = Carbon::now();
                        $phoneData->save();

                        continue;
                    }
                    SimsService::addSimResult($phoneData['uniqueId'], $service['uniqueId'], $activity['uniqueId'], 1,'Returned code successfully');

                    if ($vendor)
                    {
                        $vendorRow = User::where('id', $vendorId)->first();
                        $vendorRow->increment('balance', ($service['price'] * $vendorRow->profit) / 100);
                        DB::table('vendors_balance')->insert([
                            'uniqueId' => substr(sha1(date("Y-m-d H:i:s") . rand(423, 1223)),1,11),
                            'vendorId' => $vendorId,
                            'userID' => $activity['userid'],
                            'requestID' => $activity['uniqueId'],
                            'amount' => ($service['price'] * $vendorRow->profit) / 100,
                            'type' => '+',
                            'reason' => 'Pay for request ' . $activity['uniqueId'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }

                    $returnVar[$simNumber] = [
                        'status' => 1,
                        'data' => 'Successfully return code to request.'
                    ];
                }else{
                    if($phoneData->status < 1) $phoneData->status = 1;
                    
                    $phoneData->updated_at = Carbon::now();
                    $phoneData->save();

                    $returnVar[$simNumber] = [
                        'status' => 1,
                        'data' => 'Successfully ping the number.'
                    ];
                }
            }
            return $returnVar;
        } catch (Exception $e)
        {
            DB::rollBack();
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine() . ' - ' . $e->getMessage());
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

    public function rentFunc(string $token, string $phoneNumber, string $serviceId, bool $api, bool $custom = false)
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

//            $working_services = json_decode($phone['working_services'], true);
//            $working_services[] = $serviceId;
//            $updatePhone = $this->update($phone['uniqueId'], ['working_service' => json_encode($working_services)]);

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
            $createRequest = $this->activityService->create($user->id, $phone['phone'], $phone['networkId'], $phone['countryCode'], $serviceId, $holdBalance['id'], $api, $custom);
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

            DB::table('sim_activities')->insert([
                'phoneNumber' => $phone['phone'],
                'serviceId' => $serviceId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => [
                    'phone' => $phone['phone'],
                    'balance' => $user->balance - $service['price'],
                    'price' => $service['price'],
                    'name' => $service['serviceName'],
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

    public function basicRent(string $token, string $serviceId, string $networkId, bool $api, string $phoneNumber = null)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $token . ' - '. $serviceId . ' - '. $networkId . ' - '. $api . ' - '. $phoneNumber . ' - ');
            if (!empty($phoneNumber))
            {
                if (substr($phoneNumber, 0, strlen('84')) == '84') {
                    $phoneNumber = substr($phoneNumber, strlen('84'));
                }
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
                if ($networkId != 'all')
                {
                    $network = Network::where('uniqueId', $networkId)->first();
                    if (empty($network)) return [
                        'status' => 0,
                        'error' => 'Network not found'
                    ];
                }
                $phone = $this->simsRepo->newestPhone($serviceId, $networkId == 'all' ? null : $networkId);
            }
            if (!$phone)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - No phone number available');
                return [
                    'status' => 0,
                    'error' => 'No phone number available'
                ];
            }
            $result = $this->rentFunc($token, $phone['phone'], $serviceId, $api);
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
//            $phone = Sims::where([
//                ['status', 1],
//                ['networkId', $network['uniqueId']]
//            ])->first();
            $phone = $this->simsRepo->newestPhone($serviceId, $network['uniqueId']);
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