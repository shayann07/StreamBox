<?php

namespace App\Controllers;

use App\Libraries\CIAuth;
use App\Libraries\OneSignal;

use App\Models\PanelModel;

class PanelController extends BaseController {
    
    public function __construct(){
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
    }
    
    public function index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $adminID = CIAuth::id();
        $adminType = CIAuth::adminType();
        
        $notificationModel = new \App\Models\NotificationModel();
        $notificationTotalCount = $notificationModel->countAll();
        
        $customAdsModel = new \App\Models\CustomAdsModel();
        $customAdsTotalCount = $customAdsModel->countAll();
        
        $extreamModel = new \App\Models\ExtreamModel();
        $extreamTotalCount = $extreamModel->countAll();
        
        $streamModel = new \App\Models\StreamModel();
        $streamTotalCount = $streamModel->countAll();
        
        $blocklistModel = new \App\Models\BlocklistModel();
        $blocklistTotalCount = $blocklistModel->countAll();
        
        $deviceIdModel = new \App\Models\DeviceModel();
        if (!in_array($adminType, [1, 3, "1", "3"])) {
            $deviceIdTotalCount = $deviceIdModel->where('admin_id', $adminID)->countAll();
        } else {
            $deviceIdTotalCount = $deviceIdModel->countAll();
        }
        
        $activationCodeModel = new \App\Models\ActivationModel();
        if (!in_array($adminType, [1, 3, "1", "3"])) {
            $activationCodeTotalCount = $activationCodeModel->where('admin_id', $adminID)->countAll();
        } else {
            $activationCodeTotalCount = $activationCodeModel->countAll();
        }
        
        $reportsModel = new \App\Models\ReportsModel();
        $reportsTotalCount = $reportsModel->countAll();
        
        $storePolicyModel = new \App\Models\DataDeletioModel();
        $storePolicyTotalCount = $storePolicyModel->countAll();
        
        $adminModel = new \App\Models\AdminModel();
        $adminTotalCount = $adminModel->countAll();
        
        $tokenModel = new \App\Models\TokenCodeModel();
        $tokenTotalCount = $tokenModel->countAll();
        
        $data = [
            'pageTitle' => 'Dashboard',
            'currentFile' => 'dashboard',
            
            'notificationTotalCount' => $notificationTotalCount,
            'customAdsTotalCount' => $customAdsTotalCount,
            'extreamTotalCount' => $extreamTotalCount,
            'streamTotalCount' => $streamTotalCount,
            'blocklistTotalCount' => $blocklistTotalCount,
            'deviceIdTotalCount' => $deviceIdTotalCount,
            'activationCodeTotalCount' => $activationCodeTotalCount,
            'reportsTotalCount' => $reportsTotalCount,
            'storePolicyTotalCount' => $storePolicyTotalCount,
            'tokenTotalCount' => $tokenTotalCount,
            'adminTotalCount' => $adminTotalCount,
            
        ];
        $data['settings'] = $this->settings;
        $data['result'] = $reportsModel->orderBy('id', 'DESC')->limit(8)->findAll();
        
        return view('dashboard', $data);
    }
    
    public function urls_index(){
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $data = [
            'pageTitle' => 'Urls',
            'currentFile' => 'urls'
        ];
        $data['settings'] = $this->settings;
        return view('urls', $data);
    }

    public function policy_index(){
        $data = [
            'pageTitle' => 'Privacy Policy',
            'currentFile' => 'policy'
        ];
        $data['settings'] = $this->settings;
        return view('policy', $data);
    }
    
    public function terms_index(){
        $data = [
            'pageTitle' => 'Terms & Conditions',
            'currentFile' => 'terms'
        ];
        $data['settings'] = $this->settings;
        return view('policy', $data);
    }
}