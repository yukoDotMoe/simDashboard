<?php


namespace App\Services;


use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiService
{
    // 200: success
    // 401: unauthorized
    // 404: not found
    // 500: server error
    public static function returnResult(array $data, int $status = 200, string $message = null): array
    {
        return [
            'status' => $status,
            'success' => !(($status > 200)),
            'message' => $message,
            'data' => $data
        ];
    }

    public function updateToken(string $userid = null)
    {
        $token = Str::random(80);
        User::where('id', (!empty($userid)) ? $userid : Auth::user()->id)->update(['api_token' => $token]);
        return ['status' => 1, 'data' => 'Successfully', 'token' => $token];
    }

    public function getDoc()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $siteInfo = DB::table('webinfo')->get();
            $result = [];
            Log::info(count($siteInfo));
            if (count($siteInfo) < 1)
            {
                DB::table('webinfo')->insert([
                    'name' => 'apiDocs',
                    'value' => null,
                ]);
            }else{
                foreach ($siteInfo as $attr)
                {
                    $result[$attr->name] = [
                        'value' => $attr->value,
                        'lastUpdate' => $attr->updated_at
                    ];
                }
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [ 'status' => 1, 'data' => $result ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function updateApiDoc($content)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $table = DB::table('webinfo')->where('name', 'apiDocs')->update(['value' => $content]);
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' =>  1,
                'data' => 'Successfully'
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function checkApi(string $apiKey)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');

            $user = User::where('api_token', $apiKey)->first();
            if (empty($user)) return [
                'status' => 0,
                'error' => 'API Key not found'
            ];

            if ($user['ban'] == 1) return [
                'status' => 0,
                'error' => 'Your account has been banned. Please contact administrator'
            ];

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');

            return [
                'status' => 1,
                'data' => [
                    'userId' => $user['id'],
                    'balance' => $user['balance'],
                    'role' => $user['admin'] ? 'admin' : ($user['tier'] >= 10 ? 'vendor' : 'user')
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
}