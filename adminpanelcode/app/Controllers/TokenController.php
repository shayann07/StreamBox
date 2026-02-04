<?php

namespace App\Controllers;

use App\Libraries\CIAuth;
use App\Libraries\PasswordGenerator;

use App\Models\PanelModel;
use App\Models\TokenCodeModel;

class TokenController extends BaseController {
    
    public function __construct(){
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
        $this->details = new TokenCodeModel();
    }
    
    public function create(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $password = PasswordGenerator::make(10,"d");
        
        $data = [
            'pageTitle' => 'Create Token',
            'currentFile' => 'settings_api',
            'pageSave' => 'Create',
            'activationCode' => $password
        ];
        $data['settings'] = $this->settings;
        return view('create_token', $data);
    }
    
    public function edit($id = null){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $dataDetails = $this->details->find($id);
        
        $data = [
            'pageTitle' => 'Edit Token',
            'currentFile' => 'settings_api',
            'pageSave' => 'Save',
            'token_id' => $dataDetails['id'],
            'row' => $dataDetails,
        ];
        $data['settings'] = $this->settings;
        return view('create_token', $data);
    }
    
    public function createHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $editID = $this->request->getPost('token_id');
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            if(empty($editID)){
                return redirect()->to(base_url('ns-admin/settings-api'));
            } else {
                return redirect()->to(base_url('ns-admin/create-token/'.$editID));
            }
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            if(empty($editID)){
                return redirect()->to(base_url('ns-admin/settings-api'));
            } else {
                return redirect()->to(base_url('ns-admin/create-token/'.$editID));
            }
        }
        
        if(empty($editID)){
            
            $data = array(
                'token_name'  => $this->request->getPost('token_name'),
                'token_code'  =>  $this->request->getPost('token_code'),
                'status'  =>  '1'
            );
            
            $this->details->insert($data);
            $message = array('message' => lang('Validation.added'),'class' => 'success');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/settings-api'));
            
        } else {
            
            $dataDetails = $this->details->find($editID);
            if($dataDetails){
                $data = array(
                    'token_name'  => $this->request->getPost('token_name')
                );
                $this->details->update($editID, $data);
                $message = array('message' => lang('Validation.updated'),'class' => 'success');
            } else {
                $message = array('message' => lang('Validation.updated_failed'),'class' => 'success');
            }
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/create-token/'.$editID));
        }
    }
    
    public function delete($id = null){
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
    
    public function status($id = null){
        // Ensure user is authenticated
        if (!CIAuth::check()) {
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
            $message = ($for_action === 'enable') ?  lang('Validation.enabled') : lang('Validation.disabled');
            
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
}