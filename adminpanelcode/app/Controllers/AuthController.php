<?php

namespace App\Controllers;

use App\Libraries\CIAuth;
use App\Libraries\NemoSoftsAPI;
use App\Libraries\PasswordGenerator;
use App\Libraries\PasswordVerify;
use App\Libraries\EmailService;

use App\Models\CIAuthModel;
use App\Models\PanelModel;
use App\Models\AdminModel;

class AuthController extends BaseController {
    
    public function __construct(){
        $this->db = \Config\Database::connect();
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
        $this->details = new AdminModel();
    }

    public function index() {
        if(CIAuth::check()){
            return redirect()->to(base_url('ns-admin/dashboard'));
        } else {
            return redirect()->to(base_url('ns-admin/login'));
        }
    }
    
    public function loginForm() {
        if(CIAuth::check()){
            return redirect()->to(base_url('ns-admin/dashboard'));
        }
        
        $data = [
            'pageTitle' => 'Login',
            'validation' => null
        ];
        $data['settings'] = $this->settings;
        
        return view('login', $data);
    }
    
    public function loginHandler() {
        
        $username =  $this->request->getVar('user_login');
        $password = $this->request->getVar('nsofts_password_input');
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin'));
        }
        
        // Check if reCAPTCHA is enabled in settings
        if (!empty($this->settings['recaptcha_site_key']) && !empty($this->settings['recaptcha_secret_key']) && in_array($this->settings['is_recaptcha'], [true, "true", 1, "1"], true)) {
            $recaptchaSecret = $this->settings['recaptcha_secret_key'];
            $recaptchaResponse = $this->request->getVar('g-recaptcha-response');
            
            if (empty($recaptchaResponse)) {
                $message = array('message' => "Please complete the reCAPTCHA." ,'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin'));
            }
            
            // Verify with Google
            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
            $captchaResult = json_decode($verify);
            
            if (!$captchaResult->success) {
                $message = array('message' => "reCAPTCHA verification failed." ,'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin'));
            }
        }
        
        // Continue with your login or processing logic
        $adminModel = new CIAuthModel($this->db);
        $query = $adminModel->validateAdmin($username);
        if (!$query) {
            $message = array('message' => lang('Validation.login_failed'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin'));
        }
        
        if ($query->status == 0) {
            $message = array('message' => lang('Validation.registration_approve'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin'));
        }
     
        if (PasswordVerify::verify($password, $query->password)) {
            CIAuth::setCIAuth($query);
            return redirect()->to(base_url('ns-admin'));
        } else {
            $message = array('message' => lang('Validation.password_invalid'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin'));
        }
    }
    
    public function logout(){
        CIAuth::forget();
        $message = array('message' => lang('Validation.logout'),'class' => 'success');
        session()->set('response_msg', $message);
        return redirect()->to(base_url('ns-admin'));
    }
    
    // Forgot Password ---------------------------------------------------------
    public function registerForm(){
        $data = [
            'pageTitle' => 'Forgot Password',
            'validation' => null
        ];
        $data['settings'] = $this->settings;
        return view('auth_register', $data);
    }
    
    public function registerHandler(){
        
        $name =  $this->request->getVar('register_name');
        $email =  $this->request->getVar('register_email');
        $password =  $this->request->getVar('register_password');
        $confirmPassword =  $this->request->getVar('register_confirm_password');
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/register'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/register'));
        }
        
        // Check if reCAPTCHA is enabled in settings
        if (!empty($this->settings['recaptcha_site_key']) && !empty($this->settings['recaptcha_secret_key']) && in_array($this->settings['is_recaptcha'], [true, "true", 1, "1"], true)) {
            $recaptchaSecret = $this->settings['recaptcha_secret_key'];
            $recaptchaResponse = $this->request->getVar('g-recaptcha-response');
            
            if (empty($recaptchaResponse)) {
                $message = array('message' => "Please complete the reCAPTCHA." ,'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin/register'));
            }
            
            // Verify with Google
            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
            $captchaResult = json_decode($verify);
            
            if (!$captchaResult->success) {
                $message = array('message' => "reCAPTCHA verification failed." ,'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin/register'));
            }
        }
        
        if($this->details->getUserByEmail($email)){
            $message = array('message' => lang('Validation.email_exist'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/register'));
        }
        
        if($confirmPassword != $password){
            $message = array('message' => lang('Validation.password_not_matched'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/register'));
        } else {
            
            $hashedPassword = PasswordVerify::hash($password);
            
            $data = array( 
                'username'  =>  $name,
                'email'  =>  $email,
                'admin_type'  =>  $this->request->getPost('admin_type'),
                'password'  =>  $hashedPassword,
                'image'  =>  '',
                'status' => '0'
            );
            $this->details->insert($data);
            
            $emailService = new EmailService();
            $toName   = 'Recipient Register';
            $subject = '[IMPORTANT] '.$this->settings['app_name'].' Registration Successful';
            $msg  = '<tr>
        		<img src="'.base_url('images/'.$this->settings['app_logo']).'" alt="header" />
        		</br>
        		<h1>Hello! '.$name.'</h1>
        		<p>'.lang('Validation.registration_approve').'</p>
        		<p>You can log in using this link: '.base_url('ns-admin').'</p>
        		<p>Thank you.</p>
        		';
            if ($emailService->sendEmail($email, $toName, $subject, $msg)) {
                
            }
            
            $message = array('message' => lang('Validation.registration_approve'),'class' => 'success');  
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin'));
        }
    }
    
    // Forgot Password ---------------------------------------------------------
    public function forgotPassword(){
        $data = [
            'pageTitle' => 'Register',
            'validation' => null
        ];
        $data['settings'] = $this->settings;
        return view('auth_pass_recovery', $data);
    }
    
    public function forgotPasswordHandler(){
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
             return redirect()->to(base_url('ns-admin/forgot-password'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
             return redirect()->to(base_url('ns-admin/forgot-password'));
        }

        $email =  $this->request->getVar('forgot_email');
        $userData = $this->details->getUserByEmail($email);
        if($userData){
            
            $newPassword = bin2hex(random_bytes(4));
            
            $hashedPassword = PasswordVerify::hash($newPassword);
            
            $data = array(
                'password'  =>  $hashedPassword
            );
            $this->details->update($userData['id'], $data);
            
            $emailService = new EmailService();
            $toName   = 'Recipient Password Reset';
            $subject = '[IMPORTANT] '.$this->settings['app_name'].' Your Password Reset';
            $msg  = '<tr>
        		<img src="'.base_url('images/'.$this->settings['app_logo']).'" alt="header" />
        		</br>
        		<h1>Hello! '.$userData['username'].'</h1>
        		<p style="font-size: medium;">Your new password is:  <strong style="background-color: #e1e1e1; border-radius: 5px; 
        		color: #000; display: inline-block; font-weight: bold; padding-left: 10px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px; font-size: large; letter-spacing: 5px;">
        		'.$newPassword.'
        		</strong></p>
        		<p>You can log in using this link: '.base_url('ns-admin').'</p>
        		<p>Please login and change your password immediately.</p>
        		<p>Thank you.</p>
        		';
            if ($emailService->sendEmail($userData['email'], $toName, $subject, $msg)) {
                $message = array('message' => lang('Validation.email_sent'),'class' => 'success');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin'));
            } else {
                $message = array('message' => lang('Validation.email_sent_failed'),'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin/forgot-password'));
            }
        } else {
            $message = array('message' => lang('Validation.email_not_found'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/forgot-password'));
        }
    }
    
    // Profile -----------------------------------------------------------------
    public function profileForm(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $dataDetails = $this->details->find(CIAuth::id());
        
        $data = [
            'pageTitle' => 'Edit Profile',
            'currentFile' => 'edit_profile',
            'validation' => null,
            'user_id' => $dataDetails['id'],
            'row' => $dataDetails,
        ];
        
        $data['settings'] = $this->settings;
        return view('auth_profile', $data);
    }
    
    public function profileHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/profile'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/profile'));
        }
        
        $editID = CIAuth::id();
        if(empty($editID)){
            CIAuth::forget();
            $message = array('message' => lang('Validation.Login'),'class' => 'success');
            $session->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin'));
        }
        
        $dataDetails = $this->details->find($editID);
        if($dataDetails){
            if ($this->request->getPost('submit') !== null) {
                $imageName = $this->handleImageUpload($dataDetails['image']);
                $data = array( 
                    'username'  =>  $this->request->getPost('username'),
                    'email'  =>  $this->request->getPost('email'),
                    'image'  =>  $imageName,
                );
                $this->details->update($editID, $data);
                $message = array('message' => lang('Validation.updated'),'class' => 'success');
                CIAuth::forget();
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin'));
                
            } else if ($this->request->getPost('submit_password') !== null) {
                
                $confirmPassword = $this->request->getPost('register_confirm_password');
                $password = $this->request->getPost('register_password');
                
                if($confirmPassword != $password){
                    $message = array('message' => lang('Validation.password_not_matched'),'class' => 'error');
                    session()->set('response_msg', $message);
                    return redirect()->to(base_url('ns-admin/profile'));
                } else {
                    
                    $hashedPassword = PasswordVerify::hash($password);
                    
                    $data = array(
                        'password'  =>  $hashedPassword
                    );
                    $this->details->update($editID, $data);
                    $message = array('message' => lang('Validation.updated'),'class' => 'success');  
                    CIAuth::forget();
                    session()->set('response_msg', $message);
                    return redirect()->to(base_url('ns-admin'));
                }
            } else {
                $message = array('message' => lang('Validation.updated_failed'),'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin/profile'));
            }
        } else {
            $message = array('message' => lang('Validation.updated_failed_user'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/profile'));
        }
    }
    
    // Admin -------------------------------------------------------------------
    public function admin_index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Manage Admin Users',
            'currentFile' => 'manage_admin',
            'tableName' => 'tbl_admin'
        ];
        
        $data['settings'] = $this->settings;
        
        $keyword = $this->request->getGet('keyword') ?? '';
        if (!empty($keyword)) {
            $data['result'] = $this->details
                ->orderBy('id', 'DESC')
                ->like('username', $keyword)
                ->orLike('email', $keyword)
                ->findAll();
        } else {
            $data['result'] = $this->details->orderBy('id', 'DESC')->findAll();
        }
        return view('manage_admin', $data);
    }
    
    public function create(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Create User',
            'currentFile' => 'manage_admin',
            'pageSave' => 'Create'
        ];
        $data['settings'] = $this->settings;
        return view('create_admin', $data);
    }
    
    public function edit($id = null){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $dataDetails = $this->details->find($id);
        
        $data = [
            'pageTitle' => 'Edit User',
            'currentFile' => 'manage_admin',
            'pageSave' => 'Save',
            'user_id' => $dataDetails['id'],
            'row' => $dataDetails,
        ];
        $data['settings'] = $this->settings;
        return view('create_admin', $data);
    }
    
    public function createAdminHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $editID = $this->request->getPost('user_id');
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            if(empty($editID)){
                return redirect()->to(base_url('ns-admin/manage-admin'));
            } else {
                return redirect()->to(base_url('ns-admin/create-admin/'.$editID));
            }
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            if(empty($editID)){
                return redirect()->to(base_url('ns-admin/manage-admin'));
            } else {
                return redirect()->to(base_url('ns-admin/create-admin/'.$editID));
            }
        }
        
        if(empty($editID)){
            
            $name = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            if($this->details->getUserByEmail($email)){
                $message = array('message' => lang('Validation.email_exist'),'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin/create-admin/'));
            }
            
            $imageName = $this->handleImageUpload();
            
            $password = $this->request->getPost('password');
            $hashedPassword = PasswordVerify::hash($password);
            
            $data = array( 
                'username'  =>  $this->request->getPost('username'),
                'email'  =>  $this->request->getPost('email'),
                'admin_type'  =>  $this->request->getPost('admin_type'),
                'password'  =>  $hashedPassword,
                'image'  =>  $imageName,
                'status' => '1'
            );
            $this->details->insert($data);
            
            $emailService = new EmailService();
            $toName   = 'Recipient Register';
            $subject = '[IMPORTANT] '.$this->settings['app_name'].' Registration Successful';
            $msg  = '<tr>
        		<img src="'.base_url('images/'.$this->settings['app_logo']).'" alt="header" />
        		</br>
        		<h1>Hello! '.$name.'</h1>
        		<p>'.lang('Validation.registration').'</p>
        		<p>You can log in using this link: '.base_url('ns-admin').'</p>
        		<p>Thank you.</p>
        		';
            if ($emailService->sendEmail($email, $toName, $subject, $msg)) {
                
            }
            
            $message = array('message' => lang('Validation.added'),'class' => 'success');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/manage-admin'));
        
        } else {
            
            $dataDetails = $this->details->find($editID);
            if($dataDetails){
                if ($this->request->getPost('submit') !== null) {
                    $imageName = $this->handleImageUpload($dataDetails['image']);
                    $data = array( 
                        'username'  =>  $this->request->getPost('username'),
                        'email'  =>  $this->request->getPost('email'),
                        'admin_type'  =>  $this->request->getPost('admin_type'),
                        'image'  =>  $imageName,
                    );
                    $this->details->update($editID, $data);
                    $message = array('message' => lang('Validation.updated'),'class' => 'success');
                    
                } else if ($this->request->getPost('submit_password') !== null) {
                    
                    $confirmPassword = $this->request->getPost('register_confirm_password');
                    $password = $this->request->getPost('register_password');
                    
                    if($confirmPassword != $password){
                        $message = array('message' => lang('Validation.password_not_matched'),'class' => 'error');
                    } else {
                        $hashedPassword = PasswordVerify::hash($password);
                        $data = array(
                            'password'  =>  $hashedPassword
                        );
                        $this->details->update($editID, $data);
                        $message = array('message' => lang('Validation.updated'),'class' => 'success');  
                    }
                } else {
                    $message = array('message' => lang('Validation.updated_failed'),'class' => 'error');
                }
            } else {
                $message = array('message' => lang('Validation.updated_failed_user'),'class' => 'error');
            }
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/create-admin/'.$editID));
        }
    }
    
    public function delete($id = null){
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
    
    public function status($id = null){
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
        $record = $this->details->find($id);
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
            $message = ($for_action === 'enable') ? lang('Validation.enabled') : lang('Validation.disabled');
            
            $this->details->update($id, $data);
            
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
    
    // Verification ------------------------------------------------------------
    public function verification_index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Verification',
            'currentFile' => 'verification'
        ];
        $data['settings'] = $this->settings;
        return view('verification', $data);
    }
    
    public function verificationHandler(){
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
            return redirect()->to(base_url('ns-admin/verification'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/verification'));
        }
        
        // Check if Envato bypass mode is enabled
        $envatoConfig = config('Envato');
        if ($envatoConfig->bypassVerification === true) {
            return $this->handleBypassVerification();
        }
        
        // Original verification flow with real Envato API
        $api = new NemoSoftsAPI();
        $envato = $api->verifyEnvatoPurchaseCode(trim($this->request->getPost('envato_purchase_code')));
        if (empty($envato)) {
            $message = array('message' => lang('Validation.extension_missing'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/verification'));
        } else if($envato['status'] == false){
            $message = array('message' => lang('Validation.purchase_wrong'),'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/verification'));
        } else if($envato['status'] == true){
            
            $verify = $api->verifyLicenseAndroid(trim($this->request->getPost('envato_purchase_code')), trim($this->request->getPost('envato_package_name')));
            if (empty($verify['status'])) {
                $message = array('message' => lang('Validation.extension_missing'),'class' => 'error');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin/verification'));
            } else {
                $data = array(
                    'envato_buyer_name' => trim($this->request->getPost('envato_buyer_name')),
                    'envato_purchase_code' => trim($this->request->getPost('envato_purchase_code')),
                    'envato_api_key' => $verify['api_key'],
                    'envato_package_name' => trim($this->request->getPost('envato_package_name'))
                );
                $panelModel = new PanelModel();
                $panelModel->update('1', $data);
                
                $message = array('message' => $verify['message'] ,'class' => 'success');
                session()->set('response_msg', $message);
                return redirect()->to(base_url('ns-admin/verification'));
            }
        }
    }

    /**
     * Handle Envato bypass verification (development/test mode)
     * Accepts any credentials and validates package name
     */
    private function handleBypassVerification(){
        $envatoConfig = config('Envato');
        
        // Get form inputs
        $buyer_name = trim($this->request->getPost('envato_buyer_name'));
        $purchase_code = trim($this->request->getPost('envato_purchase_code'));
        $package_name = trim($this->request->getPost('envato_package_name'));
        
        // Validate inputs are not empty
        if (empty($buyer_name) || empty($purchase_code) || empty($package_name)) {
            $message = array('message' => 'All fields are required (even in bypass mode)', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/verification'));
        }
        
        // Validate package name format
        if (!preg_match('/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)*$/', $package_name)) {
            $message = array('message' => 'Invalid package name format. Use format: com.example.app', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/verification'));
        }
        
        // Generate API key from provided data
        $api_key = 'bypass_' . md5($buyer_name . $purchase_code . time());
        
        // Update settings with bypass credentials
        $data = array(
            'envato_buyer_name' => $buyer_name,
            'envato_purchase_code' => $purchase_code,
            'envato_api_key' => $api_key,
            'envato_package_name' => $package_name
        );
        
        $panelModel = new PanelModel();
        $panelModel->update('1', $data);
        
        // Log the bypass verification
        if ($envatoConfig->logBypassAttempts === true) {
            $this->logBypassAttempt($buyer_name, $package_name, 'verification');
        }
        
        $message = array('message' => 'Verification successful (Bypass Mode - Development Only)', 'class' => 'success');
        session()->set('response_msg', $message);
        return redirect()->to(base_url('ns-admin/verification'));
    }

    /**
     * Log bypass verification attempts
     */
    private function logBypassAttempt($buyer_name, $package_name, $action) {
        $log_file = WRITEPATH . 'logs/envato_bypass_' . date('Y-m-d') . '.log';
        $log_message = "[" . date('Y-m-d H:i:s') . "] Action: {$action}, Buyer: {$buyer_name}, Package: {$package_name}, User: " . CIAuth::id() . "\n";
        @file_put_contents($log_file, $log_message, FILE_APPEND);
    }

    private function handleImageUpload($existingImage = '') {
        $suffix = '_panel';
        $file = $this->request->getFile('image');
        
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
