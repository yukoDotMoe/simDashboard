<?php


namespace App\Services;


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
}