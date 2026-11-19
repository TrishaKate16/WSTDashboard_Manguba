<?php

namespace App\Traits;

trait ApiResponses
{
    protected function ok($message, $data = [])
    {
        return $this->success($message, $data, 200);
    }

    protected function success($message, $data = [], $statusCode = 200, $pagination = null)
    {
        $response = [
            'data' => $data,
            'message' => $message,
            'status' => $statusCode,
        ];

        if ($pagination !== null) {
            $response['meta'] = $pagination['meta'];
            $response['links'] = $pagination['links'];
        }

        return response()->json($response, $statusCode);
    }

    protected function error($message, $statusCode)
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }
}
