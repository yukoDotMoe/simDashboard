<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Log;

class CustomerService
{
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
}