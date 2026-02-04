<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

class OneSignal {

    public function sendNotification($appId, $restApiKey, $title, $message) {
        $client = Services::curlrequest();
        
        $url = 'https://onesignal.com/api/v1/notifications';
        
        $data = [
            'app_id' => $appId,
            'included_segments' => array('All'),
            'data' => array("foo" => "bar"),
            'headings' => ['en' => $title],
            'contents' => ['en' => $message],
        ];
        
        $headers = [
            'Authorization' => 'Basic ' . $restApiKey,
            'Content-Type' => 'application/json',
        ];
        
        try {
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $data,
            ]);
            
            $responseBody = json_decode($response->getBody(), true);
            
            // Check if the response contains an 'id', which means it was successful
            if (isset($responseBody['id'])) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
