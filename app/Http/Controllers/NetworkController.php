<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\NetworkService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NetworkController extends Controller
{
    protected $networkService;
    public function __construct(NetworkService $networkService)
    {
        $this->networkService = $networkService;
    }

    public function getAll()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $result = $this->networkService->getAll();
            if($result['status'] == 0)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return [
                    'status' => 0,
                    'error' => $result['error']
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return response()->json(ApiService::returnResult($result['data']));
        } catch (Exception $e)
        {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }
}
