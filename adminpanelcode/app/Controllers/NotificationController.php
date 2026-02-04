<?php

namespace App\Controllers;

use App\Libraries\CIAuth;
use App\Libraries\OneSignal;

use App\Models\PanelModel;
use App\Models\NotificationModel;

class NotificationController extends BaseController {
    
    public function __construct(){
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
        $this->details = new NotificationModel();
    }
    
    public function index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        if(CIAuth::adminType() == 2){
           return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Manage Notification',
            'currentFile' => 'manage_notification',
            'tableName' => 'tbl_notification'
        ];
        
        $data['settings'] = $this->settings;
        
        $keyword = $this->request->getGet('keyword') ?? '';
        if (!empty($keyword)) {
            $data['result'] = $this->details->orderBy('id', 'DESC')->like('notification_title', $keyword)->findAll();
        } else {
            $data['result'] = $this->details->orderBy('id', 'DESC')->findAll();
        }
        return view('manage_notification', $data);
    }
    
    public function onesignal_index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        if(CIAuth::adminType() == 2){
           return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Notification Onesignal',
            'currentFile' => 'notification_onesignal'
        ];
        $data['settings'] = $this->settings;
        
        return view('onesignal', $data);
    }
    
    public function onesignalHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        if(CIAuth::adminType() == 2){
           return redirect()->to(base_url('ns-admin'));
        }
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/notification-onesignal'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/notification-onesignal'));
        }
        
        $YOUR_ONESIGNAL_APP_ID = $this->settings['onesignal_app_id'];
        $YOUR_ONESIGNAL_REST_API_KEY = $this->settings['onesignal_rest_key'];
        
        if(empty($YOUR_ONESIGNAL_APP_ID)){
            $message = array('message' => lang('Validation.notification_failed_id'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/notification-onesignal'));
        }
        
        if(empty($YOUR_ONESIGNAL_REST_API_KEY)){
            $message = array('message' => lang('Validation.notification_failed_key'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/notification-onesignal'));
        }
        
        $title = $this->request->getPost('notification_title');
        $titleSub = $this->request->getPost('notification_des');
        $msg = $this->request->getPost('notification_msg');
        
        $oneSignal = new OneSignal();
        $success = $oneSignal->sendNotification($YOUR_ONESIGNAL_APP_ID, $YOUR_ONESIGNAL_REST_API_KEY, $title, $titleSub);
        if ($success) {
            $data = array(
                'notification_title' => $title,
                'notification_msg' => $titleSub,
                'notification_description' => $msg,
                'notification_on' =>  strtotime(date('d-m-Y h:i:s A')) 
            );
            
            $this->details->insert($data);
            $message = array('message' => lang('Validation.notification_send'),'class' => 'success');
        } else {
            $message = array('message' => lang('Validation.notification_send_failed'),'class' => 'error');
        }
        session()->set('response_msg', $message);
        return redirect()->to(base_url('ns-admin/notification-onesignal'));
    }
    
    public function delete($id = null){
        // Check if the user is authorized to perform the action
        if (!CIAuth::check()) {
            return $this->response->setJSON(['status' => 0, 'message' => 'Unauthorized']);
        }
        
        if(CIAuth::adminType() == 2){
           return $this->response->setJSON(['status' => 0, 'message' => 'Unauthorized']);
        }
        
        // Validate ID
        if (is_null($id) || !is_numeric($id)) {
            return $this->response->setJSON(['status' => 0, 'message' => 'Invalid ID']);
        }
        
        // Check if the record exists
        $record = $this->details->find($id);
        if (!$record) {
            return $this->response->setJSON(['status' => 0, 'message' => 'Record not found']);
        }
        
        try {
            // Attempt to delete the record
            if ($this->details->delete($id)) {
                session()->set('response_msg', ['message' => lang('Validation.deleted'), 'class' => 'success']);
                return $this->response->setJSON(['status' => 1, 'message' => 'Delete successful']);
            } else {
                return $this->response->setJSON(['status' => 0, 'message' => 'Failed to delete']);
            }
        } catch (\Exception $e) {
            // Log and handle potential errors during deletion
            log_message('error', 'Error deleting record: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 0, 'message' => 'An error occurred while deleting']);
        }
    }
}