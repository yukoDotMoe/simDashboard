<?php


namespace App\Services;


use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class SimsService
{
    public function create(string $phone, string $countryCode, string $networkId)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');

        } catch (Exception $e)
        {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}