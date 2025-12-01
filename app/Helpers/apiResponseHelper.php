<?php

if (!function_exists('apiResponse')) {
    /**
     * Standardize API responses
     *
     * @param bool $status
     * @param string $message
     * @param mixed $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    function apiResponse(bool $status, string $message, $data = [], int $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}