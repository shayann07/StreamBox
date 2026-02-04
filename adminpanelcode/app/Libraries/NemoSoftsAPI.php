<?php
namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class NemoSoftsAPI{
    
    private const METHOD_LATEST_VERSION = 'latest_version';
    private const METHOD_CHECK_UPDATE = 'check_update';
    private const METHOD_ENVATO_PURCHASE_CODE = 'envato_purchase_code';
    private const METHOD_ACTIVATE_LICENSE = 'activate_license';
    private const METHOD_DEACTIVATE_LICENSE = 'deactivate_license';
    private const METHOD_ANDROID_APP = 'android_app';
    
    private const PRODUCT_ID = '52621164';
    private const API_URL = 'https://api.nemosofts.com/v2/market/author';
    
    private function getAPIRequest(array $data){
        $client = \Config\Services::curlrequest();
        $response = $client->post(self::API_URL, [
            'form_params' => $data,
            'timeout' => 30,
            'connect_timeout' => 30,
        ]);
        return json_decode($response->getBody(), true);
    }
    
    public static function getProductID(){
        return self::PRODUCT_ID;
    }
    
    public static function getPortfolioURL(){
        return "https://1.envato.market/Py1XZX";
    }
    
    public static function getMoreProductsURL(){
        return "https://1.envato.market/R5JMNv";
    }

    public function getLatestVersion(){
        return $this->getAPIRequest([
            'method_name' => self::METHOD_LATEST_VERSION,
            'item_id' => self::PRODUCT_ID
        ]);
    }

    public function checkUpdate(){
        return $this->getAPIRequest([
            'method_name' => self::METHOD_CHECK_UPDATE,
            'item_id' => self::PRODUCT_ID
        ]);
    }

    public function verifyEnvatoPurchaseCode(string $license){
        return $this->getAPIRequest([
            'method_name' => self::METHOD_ENVATO_PURCHASE_CODE,
            'item_id' => self::PRODUCT_ID,
            'license_code' => $license
        ]);
    }

    public function verifyLicenseAndroid(string $license, string $package_name){
        return $this->getAPIRequest([
            'method_name' => self::METHOD_ANDROID_APP,
            'envato_purchase_code' => $license,
            'buyer_admin_url' => $this->getBaseUrlPanel(),
            'package_name' => $package_name
        ]);
    }

    public function deactivateLicense(string $deactivate_password){
        $response = $this->getAPIRequest([
            'method_name' => self::METHOD_DEACTIVATE_LICENSE,
            'deactivate_password' => $deactivate_password
        ]);
        if($response['status']){
            $current_path = realpath(__DIR__);
            $license_file = $current_path.'/.nemosofts';
            @chmod($license_file, 0640);
            if(file_exists($license_file) && is_writable($license_file)){
                unlink($license_file);
            }
        }
        return $response;
    }

    public function activateLicense(string $license, string $client, bool $create_lic = true){
        $response = $this->getAPIRequest([
            'method_name' => self::METHOD_ACTIVATE_LICENSE,
            'item_id' => self::PRODUCT_ID,
            'license_code' => $license,
            'client_name' => $client,
            'base_url' => $this->getBaseUrlPanel()
        ]);
        $current_path = realpath(__DIR__);
        $license_file = $current_path.'/.nemosofts';
        if(!empty($create_lic)){
            if($response['status']){
                $licfile = trim('ATV9U5143X4DVP5ZLM690J2QRJYL5J');
                file_put_contents($license_file, $licfile, LOCK_EX);
            } else {
                @chmod($license_file, 0640);
                if(file_exists($license_file) && is_writable($license_file)){
                    unlink($license_file);
                }
            }
        }
        return $response;
    }

    public function getIpFromThirdParty(){
        $client = \Config\Services::curlrequest();
        $response = $client->get('http://ipecho.net/plain', [
            'timeout' => 10,
            'connect_timeout' => 10,
        ]);
        return $response->getBody();
    }

    private function getBaseUrlPanel(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        $dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return htmlspecialchars("{$protocol}://{$host}{$dir}/", ENT_QUOTES);
    }
}
