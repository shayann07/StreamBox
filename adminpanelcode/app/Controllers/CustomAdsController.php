<?php

namespace App\Controllers;

use App\Libraries\CIAuth;

use App\Models\PanelModel;
use App\Models\CustomAdsModel;

class CustomAdsController extends BaseController {
    
    public function __construct(){
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
        $this->details = new CustomAdsModel();
    }

    public function index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        if(CIAuth::adminType() == 2){
           return redirect()->to(base_url('ns-admin')); 
        }
        
        $data = [
            'pageTitle' => 'Manage Custom Ads',
            'currentFile' => 'manage_ads',
            'tableName' => 'tbl_custom_ads'
        ];
        
        $data['settings'] = $this->settings;

        $keyword = $this->request->getGet('keyword') ?? '';
        if (!empty($keyword)) {
            $data['result'] = $this->details->orderBy('id', 'DESC')->like('ads_title', $keyword)->findAll();
        } else {
            $data['result'] = $this->details->orderBy('id', 'DESC')->findAll();
        }
        return view('manage_ads', $data);
    }
    
    public function create(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        if(CIAuth::adminType() == 2){
           return redirect()->to(base_url('ns-admin')); 
        }
        
        $data = [
            'pageTitle' => 'Create Custom Ads',
            'currentFile' => 'create_ads',
            'pageSave' => 'Create'
        ];
        $data['settings'] = $this->settings;
        return view('create_ads', $data);
    }
    
    public function edit($id = null){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        if(CIAuth::adminType() == 2){
           return redirect()->to(base_url('ns-admin')); 
        }
        
        $dataDetails = $this->details->find($id);
        
        $data = [
            'pageTitle' => 'Edit Custom Ads',
            'currentFile' => 'create_ads',
            'pageSave' => 'Save',
            'ads_id' => $dataDetails['id'],
            'row' => $dataDetails,
        ];
        $data['settings'] = $this->settings;
        return view('create_ads', $data);
    }
    
    public function createHandler(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        if(CIAuth::adminType() == 2){
           return redirect()->to(base_url('ns-admin')); 
        }
        
        $editID = $this->request->getPost('ads_id');
        
        // Simple CSRF check with better error handling
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken)) {
            $message = array('message' => 'CSRF token is missing. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            if(empty($editID)){
                return redirect()->to(base_url('ns-admin/manage-ads'));
            } else {
                return redirect()->to(base_url('ns-admin/create-ads/'.$editID));
            }
        }
        if ($postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed. Please refresh the page and try again.', 'class' => 'error');
            session()->set('response_msg', $message);
            if(empty($editID)){
                return redirect()->to(base_url('ns-admin/manage-ads'));
            } else {
                return redirect()->to(base_url('ns-admin/create-ads/'.$editID));
            }
        }
        
        if(empty($editID)){
            
            $imageName = $this->handleImageUpload();
            $base_url = htmlentities(trim($this->request->getPost('ads_redirect_url')));
            
            $data = array( 
                'ads_type'  =>  $this->request->getPost('ads_type'),
                'ads_title'  =>  $this->request->getPost('ads_title'),
                'ads_redirect_type'  =>  $this->request->getPost('ads_redirect_type'),
                'ads_redirect_url'  =>  $base_url,
                'ads_image'  =>  $imageName,
                'status' => '1'
            );
            $this->details->insert($data);
            $message = array('message' => lang('Validation.added'),'class' => 'success');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/manage-ads'));
            
        } else {
            
            $dataDetails = $this->details->find($editID);
            if($dataDetails){
                $imageName = $this->handleImageUpload($dataDetails['ads_image']);
                $base_url = htmlentities(trim($this->request->getPost('ads_redirect_url')));
                
                $data = array( 
                    'ads_type'  =>  $this->request->getPost('ads_type'),
                    'ads_title'  =>  $this->request->getPost('ads_title'),
                    'ads_redirect_type'  =>  $this->request->getPost('ads_redirect_type'),
                    'ads_redirect_url'  =>  $base_url,
                    'ads_image'  =>  $imageName,
                );
                $this->details->update($editID, $data);
                $message = array('message' => lang('Validation.updated'),'class' => 'success');
            } else {
                $message = array('message' => lang('Validation.updated_failed'),'class' => 'success');
            }
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/create-ads/'.$editID));
        }
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
            return $this->response->setJSON(['status' => 0, 'message' => 'An error occurred while deleting']);
        }
    }
    
    public function status($id = null){
        // Ensure user is authenticated
        if (!CIAuth::check()) {
            return $this->response->setStatusCode(401)
                ->setJSON(['status' => 0, 'msg' => 'Unauthorized']);
        }
        
        if(CIAuth::adminType() == 2){
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

    private function handleImageUpload($existingImage = '') {
        $suffix = '_panel';
        $file = $this->request->getFile('ads_image');
        
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
