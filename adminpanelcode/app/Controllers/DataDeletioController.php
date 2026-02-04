<?php

namespace App\Controllers;

use App\Libraries\CIAuth;

use App\Models\PanelModel;
use App\Models\DataDeletioModel;

class DataDeletioController extends BaseController {
    
    public function __construct(){
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
        $this->details = new DataDeletioModel();
    }

    public function index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'Data Deletion Policy',
            'currentFile' => 'data_deletion',
            'tableName' => 'tbl_data_deletion'
        ];
        
        $data['settings'] = $this->settings;
         $keyword = $this->request->getGet('keyword') ?? '';
        if (!empty($keyword)) {
            $data['result'] = $this->details->orderBy('id', 'DESC')->like('user_email', $keyword)->findAll();
        } else {
            $data['result'] = $this->details->orderBy('id', 'DESC')->findAll();
        }
        
        return view('manage_data_deletion', $data);
    }
    
    public function delete($id = null){
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return $this->response->setJSON(['status' => 0, 'message' => 'Unauthorized']);
        }
        
        // Check if the user is authorized to perform the action
        if (!CIAuth::check()) {
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
    
    public function create(){
        $data = [
            'pageTitle' => 'Data Deletion Policy',
            'createType' => 'create'
        ];
        $data['settings'] = $this->settings;
        return view('delete_request', $data);
    }
    
    public function create_done(){
        $data = [
            'pageTitle' => 'Data Deletion Policy',
            'createType' => 'done'
        ];
        $data['settings'] = $this->settings;
        return view('delete_request', $data);
    }
    
    public function createHandler(){
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('account-delete-request'));
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('account-delete-request'));
        }
        
        if ($this->request->getPost('submit_delete') !== null) {
            $email = trim($this->request->getPost('user_email'));
            $data = array(
                'policy_type'  =>  'Delete Account',
                'user_email'  =>  $email,
                'report_msg'  =>  '',
                'deletion_on'  =>  strtotime(date('d-m-Y h:i:s A')),
            );
            $this->details->insert($data);
        } else if ($this->request->getPost('submit_clear') !== null) {
            $email = trim($this->request->getPost('user_email'));
            $data = array(
                'policy_type'  =>  'Clear Data',
                'user_email'  =>  $email,
                'report_msg'  =>  '',
                'deletion_on'  =>  strtotime(date('d-m-Y h:i:s A')),
            );
            $this->details->insert($data);
        } else {
            return redirect()->to(base_url('account-delete-request'));
        }
        return redirect()->to(base_url('account-request-done'));
    }
}