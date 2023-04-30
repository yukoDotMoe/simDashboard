<?php


namespace App\Services;


use App\Models\User;
use App\Repositories\ActivityRepositoryInterface;
use App\Repositories\BalanceRepositoryInterface;
use App\Repositories\SimsRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use DB;

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

    public function subtractBalance(string $userid, string $requestId, string $amount, bool $hold = false)
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

            $activity = $this->activityRepo->find($requestId);
            if(!$activity)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Activity not found');
                return [
                    'status' => 0,
                    'error' => 'Activity not found'
                ];
            }

            $finalAmount = $user->balance - $amount;
            if($finalAmount < 0)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Account fund not valid');
                return [
                    'status' => 0,
                    'error' => 'Account fund not valid'
                ];
            }

            DB::beginTransaction();
            $createTransaction = $this->balanceRepo->create([
                'uniqueId' => substr(sha1(date("Y-m-d H:i:s")),0,10),
                'accountId' => $userid,
                'oldBalance' => $user->balance,
                'newBalance' => $finalAmount,
                'totalChange' => $amount,
                'status' => ($hold) ? 2 : 1, // 2: hold ; 1: success
                'reason' => ($hold) ? 'Hold balance for request ' . $requestId : 'Balance changes from request ' . $requestId,
                'activityId' => $requestId
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
                'data' => ($hold) ? 'Balance has been hold' : 'Successfully subtract balance'
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
                $changedTransaction = $this->update($transaction['uniqueId'], ['status' => 0]); // Changed to 'refund' status

                $user->balance = $user->balance + $transaction['totalChange']; // Refund money
                $user->save();
            }else{
                $changedTransaction = $this->update($transaction['uniqueId'], ['status' => 1]); // Changed to 'successfully' status
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