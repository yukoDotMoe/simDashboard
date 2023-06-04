<?php


namespace App\Services;


use App\Models\User;
use App\Repositories\ActivityRepositoryInterface;
use App\Repositories\BalanceRepositoryInterface;
use App\Repositories\SimsRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use DB;
use Pusher\Pusher;

class BalanceService
{
    protected $balanceRepo;
    protected $activityRepo;
    protected $simsRepo;

    public function __construct(BalanceRepositoryInterface $balanceRepo, ActivityRepositoryInterface $activityRepo, SimsRepositoryInterface $simsRepo)
    {
        $this->balanceRepo = $balanceRepo;
        $this->activityRepo = $activityRepo;
        $this->simsRepo = $simsRepo;
    }

    public function subtractBalance(string $userid, string $amount, bool $hold = false)
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

            $finalAmount = $user->balance - $amount;
            if($finalAmount < 0)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Account fund not valid');
                return [
                    'status' => 0,
                    'error' => 'Your balance is not satisfied the transaction, please top-up more'
                ];
            }

            $id = substr(sha1(date("Y-m-d H:i:s")),0,10);
            DB::beginTransaction();
            $createTransaction = $this->balanceRepo->create([
                'uniqueId' => $id,
                'accountId' => $userid,
                'oldBalance' => $user->balance,
                'newBalance' => $finalAmount,
                'totalChange' => $amount,
                'status' => 1, // 2: hold ; 1: success
                'reason' => ($hold) ? 'Hold balance for request ' : 'Balance changes from request ',
                'activityId' => 'a',
                'type' => '-',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $user->balance = $finalAmount;
            $user->save();
            DB::commit();

            if(!$createTransaction)
            {
                DB::rollBack();
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to create transaction');
                return [
                    'status' => 0,
                    'error' => 'Failed to create transaction'
                ];
            }

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => ($hold) ? 'Balance has been hold' : 'Successfully subtract balance',
                'id' => $id
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

    public function update(string $balanceId, array $info)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $transaction = $this->balanceRepo->find($balanceId);
            if(!$transaction)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Transaction not found');
                return [
                    'status' => 0,
                    'error' => 'Transaction not found'
                ];
            }
            DB::beginTransaction();
            $result = $this->balanceRepo->update($balanceId, $info);
            DB::commit();
            if(!$result)
            {
                DB::rollBack();
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update transaction');
                return [
                    'status' => 0,
                    'error' => 'Failed to update transaction'
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Updated balance successfully'
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

    public function handleHoldBalance(string $requestId)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $activity = $this->activityRepo->find($requestId);
            if(!$activity)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Activity not found');
                return [
                    'status' => 0,
                    'error' => 'Activity not found'
                ];
            }

            $transaction = $this->balanceRepo->findByActivity($requestId);
            $phone = $this->simsRepo->findByPhone($activity['phone']);
            $user = User::where('id', $transaction['accountId'])->first();

            DB::beginTransaction();
            if ($activity['status'] > 1) // activity still not finish
            {
                $changedTransaction = $this->update($transaction['uniqueId'], ['status' => 0, 'reason' => 'Refunded due to exceptions', 'handleByVendor' => $activity['handleByVendor'] ?? null]); // Changed to 'refund' status
                DB::beginTransaction();
                $activityUpdate = $this->activityRepo->update($requestId, ['status' => 0, 'reason' => 'Failed due to timeout']);
                DB::commit();

                $user->balance = $user->balance + $transaction['totalChange']; // Refund money
                $user->save();

                if(!$activityUpdate)
                {
                    DB::rollBack();
                    Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update phone number status');
                    return [
                        'status' => 0,
                        'error' => 'Failed to update phone number status'
                    ];
                }
            }else{
                $changedTransaction = $this->update($transaction['uniqueId'], ['status' => 1, 'reason' => 'Successfully charged', 'handleByVendor' => $activity['handleByVendor'] ?? null]); // Changed to 'successfully' status
                $user->totalRent = $user->totalRent++;
                $user->save();
            }

            $updatePhone = $this->simsRepo->update($phone['uniqueId'], ['status' => 1]); // Make phone available
            DB::commit();

            if(!$updatePhone)
            {
                DB::rollBack();
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update phone number status');
                return [
                    'status' => 0,
                    'error' => 'Failed to update phone number status'
                ];
            }

            if(!$changedTransaction)
            {
                DB::rollBack();
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update transaction');
                return [
                    'status' => 0,
                    'error' => 'Failed to update transaction'
                ];
            }

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => 'Balance has been updated'
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