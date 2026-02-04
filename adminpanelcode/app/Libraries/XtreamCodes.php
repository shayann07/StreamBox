<?php
namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\ResponseInterface;

class XtreamCodes {
    
    protected $client;
    
    public function __construct(){
        $this->client = \Config\Services::curlrequest();
    }
    
    private function getAPIRequest($url){
        try {
            $response = $this->client->get($url, [
                'timeout' => 30,
                'connect_timeout' => 30
            ]);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(), true);
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function generatePassword($length = 12) {
        return bin2hex(random_bytes($length / 2));
    }
    
    // AdminAPI ----------------------------------------------------------------
    public function getPackages($IP, $apiKey){
        return $this->getAPIRequest("$IP/?api_key=$apiKey&action=get_packages");
    }
    
    // ResellerAPI -------------------------------------------------------------
    public function createLine($IP, $apiKey, $username, $packageID){
        $password = $this->generatePassword();
        return $this->getAPIRequest("$IP/?api_key=$apiKey&action=create_line&trial=1&username=$username&password=$password&package=$packageID");
    }
    
    
    public function getStatus($IP, $username, $password){
        $response = $this->getAPIRequest("$IP/player_api.php?username=$username&password=$password");
        
        if (!$response) {
            return "0";
        }
        
        if (!isset($response['user_info']['status'])) {
            return "0";
        }
        
        // Check if status is "Active"
        if ($response['user_info']['status'] === 'Active') {
            
            // Check if timestamp_now and exp_date exist
            if (isset($response['server_info']['timestamp_now'], $response['user_info']['exp_date'])) {
                $currentTime = (int) $response['server_info']['timestamp_now'];
                $expTime = (int) $response['user_info']['exp_date'];
                
                // Check if the subscription is expired
                return $currentTime > $expTime ? '2' : '1';
            }
        }
        
        return "0";
    }
}