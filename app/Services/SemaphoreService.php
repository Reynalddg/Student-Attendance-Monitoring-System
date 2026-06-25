<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SemaphoreService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('SEMAPHORE_API_KEY');
    }

    public function sendSMS($to, $message, $senderName = 'TalaveraSHS')
    {
        try {
            $response = Http::post('https://api.semaphore.co/api/v4/messages', [
                'apikey'     => $this->apiKey,
                'number'     => $to,
                'message'    => $message,
                'sendername' => $senderName,
            ]);

            if ($response->successful()) {
                return [
                    'status'  => 'success',
                    'data'    => $response->json(),
                    'message' => 'Message sent successfully!',
                ];
            }

            return [
                'status'  => 'error',
                'http_code' => $response->status(),
                'body'     => $response->body(),
            ];


        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
