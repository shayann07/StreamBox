<?php

namespace App\Controllers;

use App\Libraries\CIAuth;

use App\Models\PanelModel;
use App\Models\ReportsModel;

class ReportsController extends BaseController {
    
    public function __construct(){
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
        $this->details = new ReportsModel();
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
            'pageTitle' => 'Manage Reports',
            'currentFile' => 'manage_report',
            'tableName' => 'tbl_xui_dns_block'
        ];
        
        $data['settings'] = $this->settings;
        $data['result'] = $this->details->orderBy('id', 'DESC')->findAll();
        
        return view('manage_report', $data);
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
            'pageTitle' => 'View Report',
            'currentFile' => 'manage_report',
            'pageSave' => 'Save',
            'report_id' => $dataDetails['id'],
            'row' => $dataDetails,
        ];
        $data['settings'] = $this->settings;
        return view('create_report', $data);
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
}