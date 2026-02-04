<?php

namespace App\Controllers;

use App\Libraries\TimeHelper;

use App\Models\PanelModel;
use App\Models\WebModel;
use App\Models\TokenCodeModel;
use App\Models\DeviceModel;
use App\Models\ActivationModel;
use App\Models\NotificationModel;
use App\Models\ExtreamModel;
use App\Models\StreamModel;

class ApiWebController extends BaseController {
    
    protected $apiHeader;
    private $settings;

    public function __construct(){
        $this->apiHeader = getenv('API_HEADER_WEB');
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
    }
    
    public function index() {
        
        // Validate Token
        $token = $this->request->getPost('token') ?? '';
        if (empty($token)) {
            return $this->sendResponse(['MSG' => 'Token parameter is missing. Please provide a valid token to proceed.', 'success' => '0']);
        }
        
        $tokenCodeModel = new TokenCodeModel();
        if ($tokenCodeModel->isTokenValid($token)) {
            return $this->sendResponse(['MSG' => 'The token provided is invalid. Please check the token and try again.', 'success' => '0']);
        }
        
        $action = $this->request->getPost('helper_name') ?? '';
        if (empty($action)) {
            return $this->sendResponse(['MSG' => 'Directory access is forbidden.', 'success' => '0']);
        }
        
        if($action == "web_details"){
            return $this->getWebDetails();
        } else if($action == "get_dns"){
            return $this->getDns();
        } else if($action == "get_notification"){
            return $this->getNotification();
        } else if($action == "get_device_user"){
            
            $jsonObj= array();	
            
            $device_id = $this->request->getPost('device_id') ?? '0';
            
            $deviceModel = new DeviceModel();
            $result = $deviceModel->where('device_id', $device_id)
                                  ->where('status', 1)
                                  ->orderBy('id', 'DESC')
                                  ->findAll();
            if (!empty($result)) {
                foreach ($result as $data) {
                    $row['id'] = $data['id'];
                    $row['user_type'] = $data['user_type'];
                    $row['user_name'] = $data['user_name'];
                    $row['user_password'] = $data['user_password'];
                    $row['dns_base'] = $data['dns_base'];
                    $row['device_id'] = $data['device_id'];
                  	
                  	array_push($jsonObj,$row);
                }
            }
            
            if (!empty($result)) {
                return $this->sendResponse($jsonObj);
            } else {
                return $this->sendResponse(['MSG' => 'This device id invalid', 'success' => '0']);
            }
        } else if($action == "get_activation_code"){
            $jsonObj= array();	
            
            $activation_code = $this->request->getPost('activation_code') ?? '0';
            
            $activationModelModel = new ActivationModel();
            $result = $activationModelModel->where('activation_code', $activation_code)
                                  ->where('status', 1)
                                  ->orderBy('id', 'DESC')
                                  ->findAll();
            if (!empty($result)) {
                foreach ($result as $data) {
                    $row['id'] = $data['id'];
                    $row['user_type'] = $data['user_type'];
                    $row['user_name'] = $data['user_name'];
                    $row['user_password'] = $data['user_password'];
                    $row['dns_base'] = $data['dns_base'];
                    $row['activation_code'] = $data['activation_code'];
                  	
                  	array_push($jsonObj,$row);
                }
            }

            if (!empty($result)) {
                return $this->sendResponse($jsonObj);
            } else {
                return $this->sendResponse(['MSG' => 'This Activation code invalid', 'success' => '0']);
            }
        } else {
           return $this->sendResponse(['MSG' => 'Directory access is forbidden.', 'success' => '0']);
        }
    }
    
    // Fetch website details
    private function getWebDetails() {
        $webModel = new WebModel();
        $data = $webModel->getSettings();
        
        if (empty($data)) {
            return $this->sendResponse(['MSG' => 'Directory access is forbidden.', 'success' => '0']);
        }
        
        $jsonObj = [
            'site_name' => $data['site_name'],
            'site_description' => $data['site_description'],
            'site_keywords' => $data['site_keywords'],
            'copyright_text' => $data['copyright_text'],
            'web_logo_1' => base_url('images/'.$data['web_logo_1']),
            'web_logo_2' => base_url('images/'.$data['web_logo_2']),
            'web_favicon' => base_url('images/'.$data['web_favicon']),
            'header_code' => $data['header_code'],
            'footer_code' => $data['footer_code'],
            'privacy_page_title' => $data['privacy_page_title'],
            'privacy_content' => $data['privacy_content'],
            'terms_of_use_page_title' => $data['terms_of_use_page_title'],
            'terms_of_use_content' => $data['terms_of_use_content'],
            
            'is_select_xui' => $data['is_select_xui'],
            'is_select_stream' => $data['is_select_stream'],
            'is_select_device_id' => $data['is_select_device_id'],
            'is_select_activation_code' => $data['is_select_activation_code'],
            'is_select_single' => $data['is_select_single'],
            
            'is_maintenance' => $data['is_maintenance'],
            
            'is_xui_dns' => $data['is_xui_dns'],
            'is_stream_dns' => $data['is_stream_dns']
        ];
        return $this->sendResponse($jsonObj);
    }
    
    private function getDns() {
        // Extream Details -------------------------------------------------
        $jsonObj = array();
        $data_arr = array();
        
        $extreamModel = new ExtreamModel();
        $dataExtream = $extreamModel->orderBy('id', 'DESC')->where('status', 1)->findAll();
        
        if (!empty($dataExtream)) {
            foreach ($dataExtream as $data2) {
                $data_arr['id'] = $data2['id'];
                $data_arr['dns_title'] = $data2['dns_title'];
                $data_arr['dns_base'] = $data2['dns_base'];
                
                array_push($jsonObj, $data_arr);
            }
        }
        $row['xui_dns'] = $jsonObj;
        
        // Stream Details --------------------------------------------------
        $jsonObj = array();
        $data_arr = array();
        
        $streamModel = new StreamModel();
        $dataStream = $streamModel->orderBy('id', 'DESC')->where('status', 1)->findAll();
        
        if (!empty($dataStream)) {
            foreach ($dataStream as $data3) {
                $data_arr['id'] = $data3['id'];
                $data_arr['dns_title'] = $data3['dns_title'];
                $data_arr['dns_base'] = $data3['dns_base'];
                
                array_push($jsonObj, $data_arr);
            }
        }
        $row['stream_dns'] = $jsonObj;
        
        return $this->sendResponse($row);
    }

    private function getNotification() {
        $notificationModel = new NotificationModel();
        $dataNotification = $notificationModel->orderBy('id', 'DESC')->findAll();
        
        if (empty($dataNotification)) {
            return $this->sendResponse(['MSG' => 'Directory access is forbidden.', 'success' => '0']);
        }
        
        $jsonObj = array_map(function($data) {
            return [
                'id' => $data['id'],
                'notification_title' => $data['notification_title'],
                'notification_msg' => $data['notification_msg'],
                'notification_description' => $data['notification_description'],
                'notification_on' => TimeHelper::calculateTimeSpan($data['notification_on'],true)
            ];
        }, $dataNotification);
        
        return $this->sendResponse($jsonObj);
    }
    
    // Helper method to check purchase code validity
    private function isPurchaseCodeValid() {
        return !empty($this->settings['envato_buyer_name']) && 
               !empty($this->settings['envato_purchase_code']) && 
               !empty($this->settings['envato_api_key']);
    }

    // Reusable method for sending JSON responses
    private function sendResponse($data) {
        $set[$this->apiHeader] = $data;
        return $this->response->setJSON($set);
    }
}