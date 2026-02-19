<?php

namespace App\Libraries;

class OneSignal {

    public function sendNotification($appId, $restApiKey, $title, $message) {
        
        // Trim whitespace that may have been stored in DB
        $restApiKey = trim($restApiKey);
        $appId = trim($appId);
        
        // Log key info for debugging (masked for security)
        $keyLen = strlen($restApiKey);
        $keyPreview = substr($restApiKey, 0, 8) . '...' . substr($restApiKey, -4);
        log_message('info', 'OneSignal sending with App ID: ' . substr($appId, 0, 8) . '... | Key length: ' . $keyLen . ' | Key preview: ' . $keyPreview);
        
        $url = 'https://onesignal.com/api/v1/notifications';
        
        $data = [
            'app_id' => $appId,
            'included_segments' => ['All'],
            'headings' => ['en' => $title],
            'contents' => ['en' => $message],
        ];
        
        $jsonData = json_encode($data);
        
        // Try with 'Key' prefix first (new OneSignal API format)
        $result = $this->doRequest($url, $jsonData, 'Key ' . $restApiKey);
        
        if ($result === true) {
            return true;
        }
        
        // Fall back to 'Basic' prefix (legacy format)
        log_message('info', 'OneSignal: Retrying with Basic auth...');
        return $this->doRequest($url, $jsonData, 'Basic ' . $restApiKey);
    }
    
    private function doRequest($url, $jsonData, $authHeader) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: ' . $authHeader,
            ],
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            log_message('error', 'OneSignal cURL error: ' . $curlError);
            return false;
        }
        
        $responseBody = json_decode($response, true);
        log_message('info', 'OneSignal API [' . $authHeader . '] response [' . $httpCode . ']: ' . $response);
        
        if ($httpCode === 200 && isset($responseBody['id'])) {
            return true;
        } else {
            $errorMsg = isset($responseBody['errors']) ? json_encode($responseBody['errors']) : $response;
            log_message('error', 'OneSignal failed [' . $authHeader . '] [HTTP ' . $httpCode . ']: ' . $errorMsg);
            return false;
        }
    }
}

