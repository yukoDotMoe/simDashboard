<?php


namespace App\Services;


use App\Models\Balance;
use App\Models\Network;
use App\Models\Service;
use App\Models\Sims;
use App\Models\User;
use App\Repositories\BalanceRepositoryInterface;
use App\Repositories\SimsRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Exception;
use DB;
use Auth;

class CustomerService
{
    protected $balanceRepo;
    protected $simsRepo;
    public function __construct(BalanceRepositoryInterface $balanceRepo, SimsRepositoryInterface $simsRepo)
    {
        $this->balanceRepo = $balanceRepo;
        $this->simsRepo = $simsRepo;
    }
    public function getBalance(int $userid)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $user = User::where('id', $userid)->first();
            if(empty($user))
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - User not found');
                return [
                    'status' => 0,
                    'error' => 'User not found'
                ];
            }
            if($user->admin)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - admin dau co balance dau ma coi');
                return [
                    'status' => 0,
                    'error' => 'admin dau co balance dau ma coi'
                ];
            }

            if ($user->balance < 0 ) // recorrect
            {
                $user->balance = 0;
                $user->save();
            }
            
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => $user->balance
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

    public function dashboardView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $transactions = Balance::where([
                ['accountId', Auth::user()->id]
            ])->paginate(15);
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'transactions' => $transactions ?? []
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

    public function adminUsersView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $users = User::paginate(20);
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['users' => $users ?? []]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function adminSimsView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $sims = Sims::paginate(20);
            $networks = Network::all();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'sims' => $sims ?? [],
                    'networks' => $networks ?? []
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

    public function adminServicesView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $services = Service::paginate(20);
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'services' => $services ?? []
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

    public function adminNetworksView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $network = Network::paginate(20);
            $networks = Network::all();
            $phoneList = [];
            foreach ($networks as $net)
            {
                $phoneCount = Sims::where('networkId', $net['uniqueId'])->count();
                $phoneList[$net['id']] = $phoneCount;
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'networks' => $network ?? [],
                    'simList' => $phoneList
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

    public function editBalance(string $userid, string $type, int $balance, string $reason)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $user = User::where('id', $userid)->first();
            if(empty($user))
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - User not found');
                return [
                    'status' => 0,
                    'error' => 'User not found'
                ];
            }
            if ($type == 'plus')
            {
                Balance::insert([
                    'uniqueId' => substr(sha1(date("Y-m-d H:i:s")),0,10),
                    'accountId' => $userid,
                    'oldBalance' => $user->balance,
                    'newBalance' => $user->balance + $balance,
                    'totalChange' => $balance,
                    'status' => 3, // 2: hold ; 1: success
                    'reason' => $reason,
                    'activityId' => 'adminAction'.substr(sha1(date("Y-m-d H:i:s")),0,10).'321',
                    'type' => '+',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $user->balance = $user->balance + $balance;
                $user->save();
            }else{
                $finalAmount = $user->balance - $balance;
                if ($finalAmount < 0) $finalAmount = 0;
                Balance::insert([
                    'uniqueId' => substr(sha1(date("Y-m-d H:i:s")),0,10),
                    'accountId' => $userid,
                    'oldBalance' => $user->balance,
                    'newBalance' => $finalAmount,
                    'totalChange' => $balance,
                    'status' => 4, // 2: hold ; 1: success
                    'reason' => $reason,
                    'activityId' => 'adminAction'.substr(sha1(date("Y-m-d H:i:s")),0,10).'321',
                    'type' => '-',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $user->balance = $finalAmount;
                $user->save();
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function updateUser(string $userid, string $name, string $email)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $user = User::where('id', $userid)->first();
            $user->name = $name;
            $user->email = $email;
            $user->save();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function updateSim(string $uniqueId, string $status = null, string $network = null, bool $delete = false)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $sim = Sims::where('uniqueId', $uniqueId)->first();
            if ($delete)
            {
                $sim->delete();
            }else {
                $sim->status = $status;
                $sim->networkId = $network;
                $sim->save();
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function updateService(string $uniqueId, string $status = null, int $price = null, bool $delete = false)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $service = Service::where('id', $uniqueId)->first();
            if ($delete)
            {
                $service->delete();
            }else {
                $service->status = $status;
                $service->price = $price;
                $service->save();
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function updateNetwork(string $uniqueId, string $status = null, bool $delete = false)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $network = Network::where('id', $uniqueId)->first();
            if ($delete)
            {
                $network->delete();
            }else {
                $network->status = $status;
                $network->save();
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

}