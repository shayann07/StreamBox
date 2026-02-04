<?php

namespace App\Controllers;

use App\Libraries\CIAuthReseller;
use App\Libraries\PasswordVerify;
use App\Libraries\PasswordGenerator;

use App\Models\PanelModel;
use App\Models\AdminModel;
use App\Models\CIAuthModel;

use App\Models\TokenCodeModel;
use App\Models\ActivationModel;
use App\Models\DeviceModel;

class ResellerController extends BaseController {
    
    public function __construct(){
        $this->db = \Config\Database::connect();
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
        $this->details = new AdminModel();
    }

    public function index() {
        if(!CIAuthReseller::check()){
           return redirect()->to(base_url('reseller/login'));
        }
        
        $data = [
            'pageTitle' => 'Create'
        ];
        $data['settings'] = $this->settings;
        return view('reseller_create', $data);
    }
    
    public function createHandler() {
        if(!CIAuthReseller::check()){
           return redirect()->to(base_url('reseller/login'));
        }
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('reseller'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('reseller'));
        }
        
        $stream_type = $this->request->getVar('stream_type');
        if ($stream_type === 'm3u') {
            
            $m3u_link = $this->request->getVar('m3u_link');
            
            // Validate URL
            if (filter_var($m3u_link, FILTER_VALIDATE_URL)) {
            
                // Parse the full URL
                $parsed_url = parse_url($m3u_link);
            
                // Build base URL (with or without port)
                $base_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];
                if (isset($parsed_url['port'])) {
                    $base_url .= ':' . $parsed_url['port'];
                }
            
                // Parse query string to get username & password
                parse_str($parsed_url['query'], $params);
            
                $username = $params['username'] ?? null;
                $password = $params['password'] ?? null;
            
                // Optional: check if all needed values are present
                if ($username && $password) {
                    
                    $actionSelect = $this->request->getVar('actionSelect');
                    if($actionSelect === 'get_code' ){
                        $code = $this->createActivation($username, $password, $base_url);
                        $message = array('message' => "Created successfully.",'class' => 'success', 'return_code' => $code);
                        session()->set('response_msg', $message);
                        return redirect()->to(base_url('reseller/done'));
                    } else if($actionSelect === 'add_device' ){
                        $device_id = $this->request->getVar('device_id');
                        $code = $this->createDevice($username, $password, $base_url, $device_id);
                        $message = array('message' => "Created successfully.",'class' => 'success', 'return_code' => $code);
                        session()->set('response_msg', $message);
                        return redirect()->to(base_url('reseller/done'));
                    } else {
                        $message = array('message' => "Error action type",'class' => 'error');
                        session()->set('response_msg', $message);
                        return redirect()->to(base_url('reseller'));
                    }
                } else {
                    $message = array('message' => "Missing username or password",'class' => 'error');
                    session()->set('response_msg', $message);
                    return redirect()->to(base_url('reseller'));
                }
            } else {
                $message = array('message' => "Invalid URL",'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('reseller'));
            }
        } else if ($stream_type === 'xtream') {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');
            $base_url = $this->request->getVar('server');
            
            $actionSelect = $this->request->getVar('actionSelect');
            if($actionSelect === 'get_code' ){
                $code = $this->createActivation($username, $password, $base_url);
                $message = array('message' => "Created successfully.",'class' => 'success', 'return_code' => $code);
                session()->set('response_msg', $message);
                return redirect()->to(base_url('reseller/done'));
            } else if($actionSelect === 'add_device' ){
                $device_id = $this->request->getVar('device_id');
                $code = $this->createDevice($username, $password, $base_url, $device_id);
                $message = array('message' => "Created successfully.",'class' => 'success', 'return_code' => $code);
                session()->set('response_msg', $message);
                return redirect()->to(base_url('reseller/done'));
            } else {
                $message = array('message' => "Error action type",'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('reseller'));
            }
        } else {
            $message = array('message' => "no data",'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('reseller'));
        }
    }
    
    public function done() {
        if(!CIAuthReseller::check()){
           return redirect()->to(base_url('reseller/login'));
        }
        
        $data = [
            'pageTitle' => 'Created successfully',
            'validation' => null
        ];
        $data['settings'] = $this->settings;
        
        return view('reseller_done', $data);
    }
    
    public function loginHandler() {
        
        $username =  $this->request->getVar('user_login');
        $password = $this->request->getVar('nsofts_password_input');
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('reseller'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('reseller'));
        }
        
        // Check if reCAPTCHA is enabled in settings
        if (!empty($this->settings['recaptcha_site_key']) && !empty($this->settings['recaptcha_secret_key']) && in_array($this->settings['is_recaptcha'], [true, "true", 1, "1"], true)) {
            $recaptchaSecret = $this->settings['recaptcha_secret_key'];
            $recaptchaResponse = $this->request->getVar('g-recaptcha-response');
            
            if (empty($recaptchaResponse)) {
                $message = array('message' => "Please complete the reCAPTCHA." ,'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('reseller'));
            }
            
            // Verify with Google
            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
            $captchaResult = json_decode($verify);
            
            if (!$captchaResult->success) {
                $message = array('message' => "reCAPTCHA verification failed." ,'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('reseller'));
            }
        }
        
        // Continue with your login or processing logic
        $adminModel = new CIAuthModel($this->db);
        $query = $adminModel->validateAdmin($username);
        if (!$query) {
            $message = array('message' => lang('Validation.login_failed'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('reseller'));
        }
        
        if ($query->status == 0) {
            $message = array('message' => lang('Validation.registration_approve'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('reseller'));
        }
        
        if ($query->admin_type == 0) {
            $message = array('message' => 'This user is not allowed to log in.','class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('reseller'));
        }
     
        if (PasswordVerify::verify($password, $query->password)) {
            CIAuthReseller::setCIAuth($query);
            return redirect()->to(base_url('reseller/create'));
        } else {
            $message = array('message' => lang('Validation.password_invalid'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('reseller'));
        }
    }
    
    public function logout(){
        CIAuthReseller::forget();
        $message = array('message' => lang('Validation.logout'),'class' => 'success');
        session()->set('response_msg', $message);
        return redirect()->to(base_url('reseller'));
    }
    
    public function loginForm() {
        if(CIAuthReseller::check()){
            return redirect()->to(base_url('reseller/create'));
        }
        
        $data = [
            'pageTitle' => 'Login',
            'validation' => null
        ];
        $data['settings'] = $this->settings;
        
        return view('reseller_login', $data);
    }
    
    private function createDevice($username, $password, $dns, $device_id) {
        
        $adminID = CIAuthReseller::id();
        
        $data = [
            'user_type' => 'xui',
            'user_name' => $username,
            'user_password' => $password,
            'dns_base' => $dns,
            'device_id' => $device_id,
            'registered_on' => strtotime(date('d-m-Y h:i:s A')),
            'status' => '1',
            'admin_id' => $adminID
        ];
        
        $deviceModel = new DeviceModel();
        $deviceModel->insert($data);
        
        return $device_id;
    }
    
    private function createActivation($username, $password, $dns) {
        $activationCode = PasswordGenerator::make(10, "d");
        $activation = $activationCode;
        
        $adminID = CIAuthReseller::id();
        
        $data = [
            'user_type' => 'xui',
            'user_name' => $username,
            'user_password' => $password,
            'dns_base' => $dns,
            'activation_code' => $activation,
            'registered_on' => strtotime(date('d-m-Y h:i:s A')),
            'status' => '1',
            'admin_id' => $adminID
        ];
        
        $activationModel = new ActivationModel();
        $activationModel->insert($data);
        
        return $activation;
    }
}