<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\VendorsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;

class VendorController
{
    protected $vendorService;
    public function __construct(VendorsService $vendorServices)
    {
        $this->vendorService = $vendorServices;
    }
    public function dashboard()
    {
        return view('vendors.dashboard');
    }

    public function sims()
    {
        $result = $this->vendorService->simsListView();
        if ($result['status'] == 0 )
        {
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $result['error']
                )
            );
        }
        return view('vendors.sims', ['data' => $result['data']]);
    }

    public function simsActivities(string $simId)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $simId);
            $result = $this->vendorService->fetchSimActivities($simId);
            if ($result['status'] == 0 )
            {
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult($result['data']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function dashboardFilter(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $start = $request->startDate;
            $end = $request->endDate;
            $result = $this->vendorService->dashboardView($start, $end);
            if ($result['status'] == 0 )
            {
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult($result['data']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

//    public function simsFilter()
//    {
//        try {
//            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
//
//            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
//            return response()->json(ApiService::returnResult($result['data']));
//        } catch (Exception $e) {
//            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
//            return response()->json(
//                ApiService::returnResult(
//                    [],
//                    502,
//                    $e->getMessage()
//                )
//            );
//        }
//    }

    public function accountView()
    {
        return view('vendors.account');
    }

}