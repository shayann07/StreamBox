<?php

namespace App\Controllers;

use App\Libraries\CIAuth;
use App\Libraries\EmailService;

use App\Models\PanelModel;
use App\Models\TokenCodeModel;
use App\Models\WebModel;
use App\Models\AdvertisementModel;
use App\Models\SmtpModel;
use App\Models\SelectPageModel;

class SettingsController extends BaseController {
    
    public function __construct(){
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
    }

    // Settings Panel ----------------------------------------------------------
    public function panel_index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Settings admin panel',
            'currentFile' => 'settings_panel',
        ];
        
        $smtpModel = new SmtpModel();
        $data['row'] = $smtpModel->where('id', 1)->first();
        
        $data['settings'] = $this->settings;
        return view('settings_panel', $data);
    }
    
    public function panelHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-panel'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-panel'));
        }
        
        if ($this->request->getPost('submit_general') !== null) {
            
            $imageName = $this->handleImageUploadAdmin($this->settings['app_logo']);

            $data = array(
                'app_name'  =>  $this->request->getPost('app_name'),
                'app_logo'  =>  $imageName,
                'site_description'  =>  $this->request->getPost('site_description'),
                'site_keywords'  =>  $this->request->getPost('site_keywords'),
            );
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('submit_style') !== null) {
            
            $data = array(
                'header_code'  =>  htmlentities(trim($this->request->getPost('header_code'))),
                'footer_code'  => htmlentities(trim($this->request->getPost('footer_code')))
            );
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('submit_rtl') !== null) {
            
            $data = array(
                'site_direction' => ($this->request->getPost('site_direction') !== null && $this->request->getPost('site_direction')) ? '1' : '0'
            );
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
        } else if ($this->request->getPost('submit') !== null) {
            
            $key = ($this->request->getPost('smtpIndex') == 'gmail') ? '0' : '1';
            $password = '';
            if ($this->request->getPost('smtp_password')[$key] != '') {
                $password = $this->request->getPost('smtp_password')[$key];
            } else {
                if ($key == 0) {
                    $password = $this->request->getPost('smtp_gpassword');
                } else {
                    $password = $this->request->getPost('smtp_password');
                }
            }
            if ($key == 0) {
                $data = array(
                    'smtp_type'  =>  'gmail',
                    'smtp_ghost'  =>  $this->request->getPost('smtp_host')[$key],
                    'smtp_gemail'  =>  $this->request->getPost('smtp_email')[$key],
                    'smtp_gpassword'  =>  $password,
                    'smtp_gsecure'  =>  $this->request->getPost('smtp_secure')[$key],
                    'gport_no'  =>  $this->request->getPost('port_no')[$key]
                );
            } else {
                $data = array(
                    'smtp_type'  =>  'server',
                    'smtp_host'  =>  $this->request->getPost('smtp_host')[$key],
                    'smtp_email'  =>  $this->request->getPost('smtp_email')[$key],
                    'smtp_password'  =>  $password,
                    'smtp_secure'  =>  $this->request->getPost('smtp_secure')[$key],
                    'port_no'  =>  $this->request->getPost('port_no')[$key]
                );
            }
            
            $smtpModel = new SmtpModel();
            $smtpRecord = $smtpModel->find(1);
            if ($smtpRecord) {
                $smtpModel->update('1', $data);
            } else {
                $smtpModel->insert($data);
            }
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('btn_send') !== null) {
            
            $email = $this->request->getPost('email');
            
            $emailService = new EmailService();
            $toName   = 'Recipient SMTP Configuration';
            $subject = '[IMPORTANT] '.$this->settings['app_name'].' Check SMTP Configuration';
            $msg  = '<tr>
        		<img src="'.base_url('images/'.$this->settings['app_logo']).'" alt="header" />
        		</br>
        		<h1>Hello!</h1>
        		<p>'.lang('Validation.email_configuration').'</p>
        		<p>Thank you.</p>
        		';
            if ($emailService->sendEmail($email, $toName, $subject, $msg)) {
                $message = array('message' => lang('Validation.email_sent'),'class' => 'success');
            } else {
                $message = array('message' => lang('Validation.email_sent_failed'),'class' => 'error');
            }
            session()->set('response_msg', $message);
        }
        
        else if ($this->request->getPost('submit_recaptcha') !== null) {
            
            $data = array(
                'is_recaptcha' => ($this->request->getPost('is_recaptcha') !== null && $this->request->getPost('is_recaptcha')) ? '1' : '0',
                'recaptcha_site_key'  =>  $this->request->getPost('recaptcha_site_key'),
                'recaptcha_secret_key'  =>  $this->request->getPost('recaptcha_secret_key'),
            );
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
        } 
        
        return redirect()->to(base_url('ns-admin/settings-panel'));
    }
    
    // Settings App ------------------------------------------------------------
    public function app_index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Settings App',
            'currentFile' => 'settings_app',
        ];
        
        $data['settings'] = $this->settings;
        return view('settings_app', $data);
    }
    
    public function appHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-app'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-app'));
        }
        
        if ($this->request->getPost('submit_general') !== null) {
            
            $data = array(
                'app_email'  =>  $this->request->getPost('app_email'),
                'app_author'  =>  $this->request->getPost('app_author'),
                'app_contact'  =>  $this->request->getPost('app_contact'),
                'app_website'  =>  $this->request->getPost('app_website'),
                'app_developed_by'  =>  $this->request->getPost('app_developed_by'),
                'app_description'  =>  $this->request->getPost('app_description')
            );
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('app_submit') !== null) {
            
            $fields = [
                'is_dowload', 'is_rtl', 'is_maintenance', 'is_screenshot', 'is_apk', 'is_vpn',
                'is_select_xui', 'is_select_stream', 'is_select_playlist', 'is_select_device_id',
                'is_select_single', 'is_local_storage', 'is_select_activation_code',
                'is_xui_dns', 'is_xui_radio', 'is_stream_dns', 'is_stream_radio', 'is_trial_xui', 'is_ovpn'
            ];
            
            $data = [];
            foreach ($fields as $field) {
                $data[$field] = !empty($this->request->getPost($field)) ? 'true' : 'false';
            }
            
            $data['app_orientation'] = $this->request->getPost('app_orientation');
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('policy_submit') !== null) {
            
            $data = array('app_privacy_policy'  =>  $this->request->getPost('app_privacy_policy'));
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('terms_submit') !== null) {
            
            $data = array('app_terms'  =>  $this->request->getPost('app_terms'));
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('notification_submit') !== null) {
            
            $data = array(
              'onesignal_app_id' => trim($this->request->getPost('onesignal_app_id')),
              'onesignal_rest_key' => trim($this->request->getPost('onesignal_rest_key')),
            );
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('app_update_submit') !== null) {
            
            $data = array(
                'app_update_status'  =>  ($this->request->getPost('app_update_status')) ? 'true' : 'false',
                'app_new_version'  =>  trim($this->request->getPost('app_new_version')),
                'app_update_desc'  =>  trim($this->request->getPost('app_update_desc')),
                'app_redirect_url'  =>  trim($this->request->getPost('app_redirect_url'))
            );
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
        }
        
        return redirect()->to(base_url('ns-admin/settings-app'));
    }
    
    // Settings App UI ---------------------------------------------------------
    public function app_ui_index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Settings App UI',
            'currentFile' => 'settings_app_ui',
        ];
        
        $selectPage = new SelectPageModel();
        $data['resultPage'] = $selectPage->orderBy('id', 'DESC')->findAll();
        
        $data['settings'] = $this->settings;
        return view('settings_app_ui', $data);
    }
    
    public function appUiHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-app-ui'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-app-ui'));
        }
        
        if ($this->request->getPost('app_theme') !== null) {
            
            $data = array('is_theme'  =>  $this->request->getPost('is_theme'));
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('app_theme_epg') !== null) {
            
            $data = array('is_epg'  =>  $this->request->getPost('is_epg'));
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
        }
        
        return redirect()->to(base_url('ns-admin/settings-app-ui'));
    }
    
    public function createGallery(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $type = $this->request->getGet('type') ?? '';
        if (empty($type)) {
            return redirect()->to(base_url('ns-admin/settings-app-ui'));
        }
        
        $data = [
            'pageTitle' => 'Manage Poster',
            'currentFile' => 'settings_app_ui',
        ];
        
        $data['settings'] = $this->settings;
        $posterGalleryModel = new GalleryModel();
        $data['result'] = $posterGalleryModel->where('poster_type', $type)->orderBy('id', 'DESC')->findAll();
        return view('create_gallery', $data);
    }
    
    public function deleteGallery($id = null){
        // Check if the user is authorized to perform the action
        if (!CIAuth::check()) {
            return $this->response->setJSON(['status' => 0, 'message' => 'Unauthorized']);
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return $this->response->setJSON(['status' => 0, 'message' => 'Unauthorized']);
        }
        
        // Validate ID
        if (is_null($id) || !is_numeric($id)) {
            return $this->response->setJSON(['status' => 0, 'message' => 'Invalid ID']);
        }
        
        // Check if the record exists
        $posterGalleryModel = new GalleryModel();
        $record = $posterGalleryModel->find($id);
        if (!$record) {
            return $this->response->setJSON(['status' => 0, 'message' => 'Record not found']);
        }
        
        try {
            // Attempt to delete the record
            if ($posterGalleryModel->delete($id)) {
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
    
    public function statusGallery($id = null){
        // Ensure user is authenticated
        if (!CIAuth::check()) {
            return $this->response->setStatusCode(401)
                ->setJSON(['status' => 0, 'msg' => 'Unauthorized']);
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
             return $this->response->setStatusCode(401)
                ->setJSON(['status' => 0, 'msg' => 'Unauthorized']);
        }
        
        // Validate ID
        if (is_null($id) || !is_numeric($id)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Invalid ID']);
        }
        
        // Check if record exists
        $posterGalleryModel = new GalleryModel();
        $record = $posterGalleryModel->find($id);
        if (!$record) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Record not found']);
        }
        
        // Ensure the request is a POST request
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)
                ->setJSON(['status' => 0, 'msg' => 'Method Not Allowed']);
        }
        
        try {
            $for_action = $this->request->getPost('for_action');
            $column = $this->request->getPost('column');
            
            // Validate column name to prevent SQL injection
            $allowedColumns = ['status']; // Add other allowed columns if needed
            if (!in_array($column, $allowedColumns)) {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Invalid column specified'
                ]);
            }
            
            // Update data
            $data = [$column => ($for_action === 'enable' ? '1' : '0')];
            $message = ($for_action === 'enable') ?  lang('Validation.enabled') : lang('Validation.disabled');
            
            $posterGalleryModel->update($id, $data);
            
            return $this->response->setJSON([
                'status' => 1,
                'action' => $for_action,
                'msg' => $message,
                'class' => 'success'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 0,
                'msg' => 'An error occurred while updating the record'
            ]);
        }
    }
    
    public function createGalleryHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
             return redirect()->to(base_url('ns-admin'));
        }
        
        $poster_type = $this->request->getPost('type');
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/create-gallery?type='.$poster_type));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/create-gallery?type='.$poster_type));
        }

        $poster_image = $this->handleImageUpload();
        $data = array(
            'poster_type'  =>  $poster_type,
            'poster_image'  =>  $poster_image
        );  
        
        $posterGalleryModel = new GalleryModel();
        $posterGalleryModel->insert($data);
        
        return redirect()->to(base_url('ns-admin/create-gallery?type='.$poster_type));
    }

    // Settings API ------------------------------------------------------------
    public function api_index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
             return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Settings API',
            'currentFile' => 'settings_api',
        ];
        
        $tokenCode = new TokenCodeModel();
        $data['result'] = $tokenCode->orderBy('id', 'DESC')->findAll();
        
        $data['settings'] = $this->settings;
        return view('settings_api', $data);
    }
    
    public function apiHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
             return redirect()->to(base_url('ns-admin'));
        }
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-api'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-api'));
        }
        
        if ($this->request->getPost('submit_general') !== null) {
            
            $data = array(
                'account_delete_intruction'  =>  $this->request->getPost('account_delete_intruction')
            );
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('submit_panel_api') !== null) {
            
            $data = array(
                'login_access_code'  =>  $this->request->getPost('login_access_code'),
                
                'admin_access_code'  =>  $this->request->getPost('admin_access_code'),
                'admin_api_key'  =>  $this->request->getPost('admin_api_key'),
                'admin_packages'  =>  $this->request->getPost('admin_packages'),
                
                'reseller_access_code'  =>  $this->request->getPost('reseller_access_code'),
                'reseller_api_key'  =>  $this->request->getPost('reseller_api_key'),
                
                'admin_trial_note'  =>  $this->request->getPost('admin_trial_note')
            );
            
            $panelModel = new PanelModel();
            $panelModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
        }
        
        return redirect()->to(base_url('ns-admin/settings-api'));
    }
    
    // Settings WEB ------------------------------------------------------------
    public function web_index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
             return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Settings Web',
            'currentFile' => 'settings_web',
        ];
        
        $webModel = new WebModel();
        $data['settings_data'] = $webModel->getSettings();

        $data['settings'] = $this->settings;
        return view('settings_web', $data);
    }
    
    public function webHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
             return redirect()->to(base_url('ns-admin'));
        }
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-web'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-web'));
        }
        
        $webModel = new WebModel();
        $webData = $webModel->getSettings();
        
        if ($this->request->getPost('submit_general') !== null) {
            
            $imageNameFavicon = '';
            $file = $this->request->getFile('web_favicon');
            if ($file->isValid() && !$file->hasMoved()) {
                if ($webData['web_favicon']!="" && file_exists("images/{$webData['web_favicon']}")) {
                    unlink("images/{$webData['web_favicon']}");
                }
                $imageNameFavicon = $file->getRandomName();
                $file->move('images/', $imageNameFavicon);
            } else {
                $imageNameFavicon = $webData['web_favicon'];
            }
            
            $imageName = '';
            $file = $this->request->getFile('web_logo_1');
            if ($file->isValid() && !$file->hasMoved()) {
                if ($webData['web_logo_1']!="" && file_exists("images/{$webData['web_logo_1']}")) {
                    unlink("images/{$webData['web_logo_1']}");
                }
                $imageName = $file->getRandomName();
                $file->move('images/', $imageName);
            } else {
                $imageName = $webData['web_logo_1'];
            }
            
            $data = array(
                'site_name'  =>  $this->request->getPost('site_name'),
                'site_description'  =>  $this->request->getPost('site_description'),
                'site_keywords'  =>  $this->request->getPost('site_keywords'),
                'web_favicon'  =>  $imageNameFavicon,
                'web_logo_1'  =>  $imageName,
                'copyright_text'  =>  $this->request->getPost('copyright_text'),
                'header_code'  =>  htmlentities(trim($this->request->getPost('header_code'))),
                'footer_code'  => htmlentities(trim($this->request->getPost('footer_code')))
            );
            
            $webModel = new WebModel();
            $webModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        } else if ($this->request->getPost('submit_privacy') !== null) {
            
            $data = array(
                'privacy_page_title'  =>  $this->request->getPost('privacy_page_title'),
                'privacy_content'  =>  $this->request->getPost('privacy_content')
            );
            
            $webModel = new WebModel();
            $webModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        }  else if ($this->request->getPost('submit_terms') !== null) {
            
            $data = array(
                'terms_of_use_page_title'  =>  $this->request->getPost('terms_of_use_page_title'),
                'terms_of_use_content'  =>  $this->request->getPost('terms_of_use_content')
            );
            
            $webModel = new WebModel();
            $webModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
        } else if ($this->request->getPost('web_settings_submit') !== null) {
            
            $fields = [
                'is_select_xui', 'is_select_stream', 'is_select_device_id', 
                'is_select_activation_code', 'is_select_single', 'is_maintenance',
                'is_xui_dns', 'is_stream_dns'
            ];
            
            $data = [];
            foreach ($fields as $field) {
                $data[$field] = !empty($this->request->getPost($field)) ? 'true' : 'false';
            }
            
            $webModel = new WebModel();
            $webModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
            
        }
        
        return redirect()->to(base_url('ns-admin/settings-web'));
    }
    
    // Settings Advertisement --------------------------------------------------
    public function ads_index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
             return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Settings Advertisement',
            'currentFile' => 'settings_ads',
        ];
        
        
        $advertisementModel = new AdvertisementModel();
        $data['settings_data'] = $advertisementModel->where('id', 1)->first();
        
        $data['settings'] = $this->settings;
        return view('settings_ads', $data);
    }
    
    public function advertisementHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
             return redirect()->to(base_url('ns-admin'));
        }
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-ads'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-ads'));
        }
        
        $ad_network = $this->request->getPost('ad_network');
        if($ad_network == 'admob'){
            
            $data = array(
                'ad_status'  =>  ($this->request->getPost('ad_status')) ? 'true' : 'false',
                'ad_network'  =>  $this->request->getPost('ad_network'),
                
                'admob_publisher_id'  => $this->request->getPost('admob_publisher_id'),
                'admob_banner_unit_id'  =>  $this->request->getPost('admob_banner_unit_id'),
                'admob_interstitial_unit_id'  =>  $this->request->getPost('admob_interstitial_unit_id'),
                'admob_reward_ad_unit_id'  =>  $this->request->getPost('admob_reward_ad_unit_id'),
                
                'interstital_ad_click'  =>  $this->request->getPost('interstital_ad_click'),
                'reward_minutes'  =>  $this->request->getPost('reward_minutes'),
                
                'banner_movie'  =>  ($this->request->getPost('banner_movie')) ? 'true' : 'false',
                'banner_series'  =>  ($this->request->getPost('banner_series')) ? 'true' : 'false',
                'banner_epg'  =>  ($this->request->getPost('banner_epg')) ? 'true' : 'false',
                
                'interstitial_post_list'  =>  ($this->request->getPost('interstitial_post_list')) ? 'true' : 'false',
                
                'reward_ad_on_movie'  =>  ($this->request->getPost('reward_ad_on_movie')) ? 'true' : 'false',
                'reward_ad_on_episodes'  =>  ($this->request->getPost('reward_ad_on_episodes')) ? 'true' : 'false',
                'reward_ad_on_live'  =>  ($this->request->getPost('reward_ad_on_live')) ? 'true' : 'false',
                'reward_ad_on_single'  =>  ($this->request->getPost('reward_ad_on_single')) ? 'true' : 'false',
                'reward_ad_on_local'  =>  ($this->request->getPost('reward_ad_on_local')) ? 'true' : 'false'
            );
            
            $advertisementModel = new AdvertisementModel();
            $advertisementModel->update('1', $data);
            $message = array('message' => lang('Validation.updated'),'class' => 'success');
            session()->set('response_msg', $message);
        }
        
        return redirect()->to(base_url('ns-admin/settings-ads'));
    }


   private function handleImageUploadAdmin($existingImage = '') {
    $suffix = '_admin';
        $file = $this->request->getFile('app_logo');
        
        // Check if the file exists
        if (!$file) {
            return $existingImage; // Return the existing image if no file was uploaded
        }
        
        if ($file->isValid() && !$file->hasMoved()) {
            
            // Validate file type (only images allowed)
            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml','image/webp', 'image/heic', 'image/heif'])) {
                return $existingImage; // Return existing image if the file is not an image
            }
            
            $imageDirectory = 'images/';
            
            // Check if the directory exists; if not, create it
            if (!is_dir($imageDirectory)) {
                mkdir($imageDirectory, 0755, true); // Create directory with safer permissions
            }
            
            // Delete existing image if it exists
            if ($existingImage && file_exists($imageDirectory . $existingImage)) {
                unlink($imageDirectory . $existingImage);
            }
            
            // Generate new image name with a suffix
            $extension = $file->getExtension(); // Get file extension
            $imageName = rand(0, 99999) . $suffix . '.' . $extension; // Append suffix
            
            // Move the file with the new name
            $file->move($imageDirectory, $imageName);
            
            return $imageName;
        }
        return $existingImage;
   }

    private function handleImageUpload($existingImage = '') {
        $suffix = '_poster';
        $file = $this->request->getFile('poster_image');
        
        // Check if the file exists
        if (!$file) {
            return $existingImage; // Return the existing image if no file was uploaded
        }
        
        if ($file->isValid() && !$file->hasMoved()) {
            
            // Validate file type (only images allowed)
            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml','image/webp', 'image/heic', 'image/heif'])) {
                return $existingImage; // Return existing image if the file is not an image
            }
            
            $imageDirectory = 'images/';
            
            // Check if the directory exists; if not, create it
            if (!is_dir($imageDirectory)) {
                mkdir($imageDirectory, 0755, true); // Create directory with safer permissions
            }
            
            // Delete existing image if it exists
            if ($existingImage && file_exists($imageDirectory . $existingImage)) {
                unlink($imageDirectory . $existingImage);
            }
            
            // Generate new image name with a suffix
            $extension = $file->getExtension(); // Get file extension
            $imageName = rand(0, 99999) . $suffix . '.' . $extension; // Append suffix
            
            // Move the file with the new name
            $file->move($imageDirectory, $imageName);
            
            return $imageName;
        }
        return $existingImage;
    }
}
