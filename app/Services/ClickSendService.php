<?php

namespace App\Services;

use GuzzleHttp\Client;

class ClickSendService
{
    protected $client;
    protected $username;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->username = env('CLICKSEND_USERNAME');
        $this->apiKey = env('CLICKSEND_API_KEY');
    }

    public function sendSMS($to, $message)
    {
        $response = $this->client->post('https://rest.clicksend.com/v3/sms/send', [
            'auth' => [$this->username, $this->apiKey],
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'messages' => [
                    [
                        'source' => 'php',
                        'from' => 'SibulNHS',
                        'body' => $message,
                        'to' => $to,
                        'schedule' => null
                    ]
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
