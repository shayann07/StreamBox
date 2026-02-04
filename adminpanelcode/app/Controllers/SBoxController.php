<?php 
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Libraries\PasswordGenerator;
use App\Models\TokenCodeModel;
use App\Models\ActivationModel;
use App\Models\DeviceModel;

class SBoxController extends ResourceController {
    
    const USER_TYPE_STREAM = 'stream';
    const USER_TYPE_XUI = 'xui';
    const STATUS_ACTIVE = '1';
    
    public function index() {
        
        $data_arr = array();
        
        // Validate Token
        $token = $this->request->getGet('token') ?? '';
        if (empty($token)) {
            return $this->respondError("Token parameter is missing. Please provide a valid token to proceed.");
        }
        
        $tokenCodeModel = new TokenCodeModel();
        if ($tokenCodeModel->isTokenValid($token)) {
            return $this->respondError("The token provided is invalid. Please check the token and try again.");
        }

        // Validate Action
        $action = $this->request->getGet('action') ?? '';
        if (empty($action)) {
            return $this->respondError("Action parameter is missing. Please specify the action you want to perform.");
        }
        
        if ($action === 'create') {
            return $this->handleCreateRequest();
        } else {
            return $this->respondError("The action '{$action}' is not recognized. Please provide a valid action.");
        }
    }

    private function respondError($message) {
        return $this->respond([
            'status' => 'error',
            'message' => $message
        ]);
    }

    private function handleCreateRequest() {
        $data_arr = array();
        
        // Validate required parameters
        $requiredParams = ['username', 'password', 'dns', 'create_type'];
        foreach ($requiredParams as $param) {
            if (empty($this->request->getGet($param))) {
                return $this->respondError("{$param} is required. Please provide a valid {$param}.");
            }
        }
        
        $username = $this->request->getGet('username');
        $password = $this->request->getGet('password');
        $dns = $this->request->getGet('dns');
        $createType = $this->request->getGet('create_type');
        
        // Handle the different create types
        switch ($createType) {
            case 'device_id_stream':
            case 'device_id_xtream':
                return $this->createDevice($username, $password, $dns, $createType);
            case 'activation_stream':
            case 'activation_xtream':
                return $this->createActivation($username, $password, $dns, $createType);
            default:
                return $this->respondError("The specified create type ('{$createType}') is invalid. Please check the create type and try again.");
        }
    }

    private function createDevice($username, $password, $dns, $createType) {
        $device_id = $this->request->getGet('device_id') ?? '';
        if (empty($device_id)) {
            return $this->respondError("Missing required parameter: device_id");
        }
        
        $data = [
            'user_type' => ($createType === 'device_id_stream') ? self::USER_TYPE_STREAM : self::USER_TYPE_XUI,
            'user_name' => $username,
            'user_password' => $password,
            'dns_base' => $dns,
            'device_id' => $device_id,
            'registered_on' => strtotime(date('d-m-Y h:i:s A')),
            'status' => self::STATUS_ACTIVE
        ];
        
        $deviceModel = new DeviceModel();
        $deviceModel->insert($data);
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Created successfully.'
        ]);
    }

    private function createActivation($username, $password, $dns, $createType) {
        $activationCode = PasswordGenerator::make(10, "d");
        $activation = $activationCode;
        
        $data = [
            'user_type' => ($createType === 'activation_stream') ? self::USER_TYPE_STREAM : self::USER_TYPE_XUI,
            'user_name' => $username,
            'user_password' => $password,
            'dns_base' => $dns,
            'activation_code' => $activation,
            'registered_on' => strtotime(date('d-m-Y h:i:s A')),
            'status' => self::STATUS_ACTIVE
        ];
        
        $activationModel = new ActivationModel();
        $activationModel->insert($data);
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Created successfully.',
            'activation_code' => $activation
        ]);
    }
}