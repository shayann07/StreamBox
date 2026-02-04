<?php

namespace App\Controllers;

use App\Libraries\TimeHelper;
use App\Libraries\XtreamCodes;

use App\Models\PanelModel;
use App\Models\AdvertisementModel;
use App\Models\ExtreamModel;
use App\Models\StreamModel;
use App\Models\NotificationModel;
use App\Models\CustomAdsModel;
use App\Models\ReportsModel;
use App\Models\GalleryModel;
use App\Models\DeviceModel;
use App\Models\ActivationModel;
use App\Models\TrialModel;
use App\Models\SelectPageModel;

class ApiController extends BaseController {
    
    protected $apiHeader;

    public function __construct(){
        $this->apiHeader = getenv('API_HEADER_APP');
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
    }
    
    public function index() {
        
        // Purchase code verification
        if (!$this->isPurchaseCodeValid()) { 
            $set[$this->apiHeader][]=array('MSG'=> 'Purchase code verification failed!','success'=>'0');
            header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            die();
        }
        
        // Check if 'data' is provided in the POST request
        $data = $this->request->getPost('data');
        if (!$data) {
            $set[$this->apiHeader][]=array('MSG'=> 'Missing data parameter!','success'=>'0');
            header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            die();
        }
        
        $get_helper = $this->getApiData($data);
        if ($get_helper['helper_name'] == "app_details") {
            
            // Details ---------------------------------------------------------
            $jsonObj= array();
            $data_arr= array();
            
            if($this->settings){
                $data_arr['app_email'] = $this->settings['app_email'];
                $data_arr['app_author'] = $this->settings['app_author'];
                $data_arr['app_contact'] = $this->settings['app_contact'];
                $data_arr['app_website'] = $this->settings['app_website'];
                $data_arr['app_description'] = $this->settings['app_description'];
                $data_arr['app_developed_by'] = $this->settings['app_developed_by'];
                
                // Envato
                $data_arr['envato_api_key'] = $this->settings['envato_api_key'];
                
                // is_
                $data_arr['is_rtl'] = $this->settings['is_rtl'];
                $data_arr['is_maintenance'] = $this->settings['is_maintenance'];
                $data_arr['is_screenshot'] = $this->settings['is_screenshot'];
                $data_arr['is_apk'] = $this->settings['is_apk'];
                $data_arr['is_vpn'] = $this->settings['is_vpn'];
                $data_arr['is_xui_dns'] = $this->settings['is_xui_dns'];
                $data_arr['is_xui_radio'] = $this->settings['is_xui_radio'];
                $data_arr['is_stream_dns'] = $this->settings['is_stream_dns'];
                $data_arr['is_stream_radio'] = $this->settings['is_stream_radio'];
                $data_arr['is_local_storage'] = $this->settings['is_local_storage'];
                $data_arr['is_select_activation_code'] = $this->settings['is_select_activation_code'];
                
                // is_select
                $data_arr['is_select_xui'] = $this->settings['is_select_xui'];
                $data_arr['is_select_stream'] = $this->settings['is_select_stream'];
                $data_arr['is_select_playlist'] = $this->settings['is_select_playlist'];
                $data_arr['is_select_device_id'] = $this->settings['is_select_device_id'];
                $data_arr['is_select_single'] = $this->settings['is_select_single'];
                $data_arr['is_trial_xui'] = $this->settings['is_trial_xui'];
                $data_arr['is_ovpn'] = $this->settings['is_ovpn'];
                
                $data_arr['admin_trial_note'] = $this->settings['admin_trial_note'];
                
                // Ads Network
                $data_arr['ad_network'] = $this->settings['ad_network'];
                
                $data_arr['publisher_id'] = ($this->settings['ad_network'] == 'admob') ? $this->settings['publisher_id'] : "";
                
                // BannerAds
                $data_arr['banner_ad'] = $this->settings['banner_ad'];
                if ($this->settings['ad_network'] == 'admob') {
                    $data_arr['banner_ad_id'] = $this->settings['banner_ad_id'];
                } else {
                    $data_arr['banner_ad_id'] = '';
                }
               
                // InterstitalAds
                $data_arr['interstital_ad'] = $this->settings['interstital_ad'];
                $data_arr['interstital_ad_click'] = $this->settings['interstital_ad_click'];
                if ($this->settings['ad_network'] == 'admob') {
                    $data_arr['interstital_ad_id'] = $this->settings['interstital_ad_id'];
                } else {
                    $data_arr['interstital_ad_id'] = '';
                }
                
                // AppUpdate
                $data_arr['app_update_status'] = $this->settings['app_update_status'];
                $data_arr['app_new_version'] = $this->settings['app_new_version'];
                $data_arr['app_update_desc'] = $this->settings['app_update_desc'];
                $data_arr['app_redirect_url'] = $this->settings['app_redirect_url'];
                
                // Custom Ads
                $data_arr['custom_ads'] = $this->settings['custom_ads'];
                $data_arr['custom_ads_clicks'] = $this->settings['custom_ads_clicks'];
                
                // App Themes
                $data_arr['app_orientation'] = $this->settings['app_orientation'];
                $data_arr['is_theme'] = $this->settings['is_theme'];
                $data_arr['is_epg'] = $this->settings['is_epg'];
                
                // Dowload
                $data_arr['is_download'] = $this->settings['is_dowload'];
               
                // TMDb Key
                $data_arr['tmdb_key'] = $this->settings['account_delete_intruction'];
                
                array_push($jsonObj,$data_arr);
            }
            $row['details'] = $jsonObj;
            
            // Advertisement Details -------------------------------------------
            $jsonObj = array();
            $data_arr = array();
            
            $advertisementModel = new AdvertisementModel();
            $dataAdvertisement = $advertisementModel->where('id', 1)->first();
            
            if($dataAdvertisement){
                $data_arr['ad_status'] = $dataAdvertisement['ad_status'];
                
                // Ads Network
                $data_arr['ad_network'] = $dataAdvertisement['ad_network'];
                
                // Publisher ID
                $data_arr['publisher_id'] = ($dataAdvertisement['ad_network'] == 'admob') ? $dataAdvertisement['admob_publisher_id'] : "";
                
                $data_arr['banner_movie'] = $dataAdvertisement['banner_movie'];
                $data_arr['banner_series'] = $dataAdvertisement['banner_series'];
                $data_arr['banner_epg'] = $dataAdvertisement['banner_epg'];
                
                $data_arr['interstital_ad'] = $dataAdvertisement['interstitial_post_list'];
                
                $data_arr['reward_ad_on_movie'] = $dataAdvertisement['reward_ad_on_movie'];
                $data_arr['reward_ad_on_episodes'] = $dataAdvertisement['reward_ad_on_episodes'];
                $data_arr['reward_ad_on_live'] = $dataAdvertisement['reward_ad_on_live'];
                $data_arr['reward_ad_on_single'] = $dataAdvertisement['reward_ad_on_single'];
                $data_arr['reward_ad_on_local'] = $dataAdvertisement['reward_ad_on_local'];
                
                // BannerAds
                if ($dataAdvertisement['ad_network'] == 'admob') {
                    $data_arr['banner_ad_id'] = $dataAdvertisement['admob_banner_unit_id'];
                } else {
                    $data_arr['banner_ad_id'] = '';
                }
               
                // InterstitalAds
                $data_arr['interstital_ad_click'] = $dataAdvertisement['interstital_ad_click'];
                if ($dataAdvertisement['ad_network'] == 'admob') {
                    $data_arr['interstital_ad_id'] = $dataAdvertisement['admob_interstitial_unit_id'];
                } else {
                    $data_arr['interstital_ad_id'] = '';
                }
                
                // RewardAds
                $data_arr['reward_minutes'] = $dataAdvertisement['reward_minutes'];
                if ($dataAdvertisement['ad_network'] == 'admob') {
                    $data_arr['reward_ad_id'] = $dataAdvertisement['admob_reward_ad_unit_id'];
                } else {
                    $data_arr['reward_ad_id'] = '';
                }
                
                array_push($jsonObj,$data_arr);
            }
            $row['ads_details'] = $jsonObj;
            
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
            
            // CustomAds Details -----------------------------------------------
            $jsonObj = array();
            $data_arr = array();
            
            $customAdsModel = new CustomAdsModel();
            $dataCustomAds = $customAdsModel->orderBy('id', 'DESC')->where('status', 1)->findAll();
            
            if (!empty($dataCustomAds)) {
                foreach ($dataCustomAds as $data4) {
                    $data_arr['ads_type'] = $data4['ads_type'];
                    $data_arr['ads_title'] = $data4['ads_title'];
                    $data_arr['ads_image'] =  base_url('images/'.$data4['ads_image']);
                    $data_arr['ads_redirect_type'] = $data4['ads_redirect_type'];
                    $data_arr['ads_redirect_url'] = $data4['ads_redirect_url'];
                    
                    array_push($jsonObj, $data_arr);
                }
            }
            $row['popup_ads'] = $jsonObj;
            
            // Notification Details --------------------------------------------
            $jsonObj = array();
            $data_arr = array();
            
            $notificationAdsModel = new NotificationModel();
            $dataNotification = $notificationAdsModel->orderBy('id', 'DESC')->findAll();
            
            if (!empty($dataNotification)) {
                foreach ($dataNotification as $data5) {
                    $data_arr['id'] = $data5['id'];
                  	$data_arr['notification_title'] = $data5['notification_title'];
                  	$data_arr['notification_msg'] = $data5['notification_msg']; 
                  	$data_arr['notification_description'] = $data5['notification_description']; 
                    $data_arr['notification_on'] = TimeHelper::calculateTimeSpan($data5['notification_on'],true);
                    
                    array_push($jsonObj, $data_arr);
                }
            }
            $row['notification_data'] = $jsonObj;
            
            // Select Page -----------------------------------------------------
            $jsonObj = array();
            $data_arr = array();
            
            $selectModel = new SelectPageModel();
            $dataSelect = $selectModel->orderBy('id', 'DESC')->where('status', 1)->findAll();
            if (!empty($dataSelect)) {
                foreach ($dataSelect as $data6) {
                    $data_arr['id'] = $data6['id'];
                  	$data_arr['title'] = $data6['title'];
                  	$data_arr['redirect_type'] = $data6['redirect_type']; 
                  	$data_arr['page_data'] = $data6['page_data']; 
                    array_push($jsonObj, $data_arr);
                }
            }
            $row['select_data'] = $jsonObj;
            
            $set[$this->apiHeader] = $row;
            header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            die();
        } else if($get_helper['helper_name']=="post_report"){ 
            $jsonObj= array();
            
            $user_name = $get_helper['user_name'];
            $user_pass = $get_helper['user_pass'];
            $report_title = $get_helper['report_title'];
            $report_msg = $get_helper['report_msg'];
            
            $data = array(
                'user_name' => $user_name,
                'user_pass'  =>  $user_pass,
                'report_title'  =>  $report_title,
                'report_msg'  =>  $report_msg,
                'report_on'  =>  strtotime(date('d-m-Y h:i:s A'))
            );
            
            $reports = new ReportsModel();
            $reports->insert($data);
            
            $set[$this->apiHeader][]=array('MSG'=> "Report Success",'success'=> '1');
          	header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        	die();
        } else if($get_helper['helper_name']=="get_interstitial"){
            
            $jsonObj= array();	
            
            $customAdsModel = new CustomAdsModel();
            $result = $customAdsModel->orderBy('id', 'DESC')->where('status', 1)->findAll();
            if (!empty($result)) {
                foreach ($result as $data) {
                    $row['ads_image'] = base_url('images/'.$data['ads_image']);
                  	$row['ads_redirect_type'] = $data['ads_redirect_type'];
                  	$row['ads_redirect_url'] = $data['ads_redirect_url'];
                  	
                  	array_push($jsonObj,$row);
                }
            }
            
            $set[$this->apiHeader] = $jsonObj;
            header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            die();
            
        } else if($get_helper['helper_name']=="get_poster"){
            
            $jsonObj= array();	
            
            $type = isset($get_helper['poster_type']) ? $get_helper['poster_type'] : "movie_ui";
            
            $posterGalleryModel = new GalleryModel();
            $result = $posterGalleryModel->select('*')->where('status', 1)->where('poster_type', $type)->orderBy('RAND()', '', false)->findAll();
            if (!empty($result)) {
                foreach ($result as $data) {
                    $row['poster_image'] = base_url('images/'.$data['poster_image']);
                  	array_push($jsonObj,$row);
                }
            }
            
            $set[$this->apiHeader] = $jsonObj;
            header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            die();
            
        } else if($get_helper['helper_name']=="get_device_user"){
            
            $jsonObj= array();	
            
            $device_id = !empty($get_helper['device_id']) ? trim($get_helper['device_id']) : '0';
            
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
            
            $set[$this->apiHeader] = $jsonObj;
            header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            die();
            
        } else if($get_helper['helper_name']=="get_activation_code"){
            
            $jsonObj= array();
            
            $activation_code = !empty($get_helper['activation_code']) ? trim($get_helper['activation_code']) : '0';
            
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
            
            $set[$this->apiHeader] = $jsonObj;
            header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            die();
         
        } else if($get_helper['helper_name']=="get_trial"){  
            
            $jsonObj= array();
            
            $device_id = !empty($get_helper['device_id']) ? trim($get_helper['device_id']) : '0';
            
            $trialModel = new TrialModel();
            $result = $trialModel->where('user_name', $device_id)
                    ->where('status', 1)
                    ->orderBy('id', 'DESC')
                    ->findAll();
            if (!empty($result) && isset($result[0])) {
                
                $IP = $this->settings['login_access_code'];
                $username = $result[0]['user_name'];
                $password = $result[0]['user_password'];
                
                $xTreamCodes = new XtreamCodes();
                $status = $xTreamCodes->getStatus($IP, $username, $password);
                
                if ($status === "1") {
                    $set[$this->apiHeader][] = array('MSG' => 'Trial account is valid.', 'success' => '1');
                } elseif ($status === "2") {
                    $set[$this->apiHeader][] = array('MSG' => 'Trial account has expired.', 'success' => '0');
                } else {
                    $set[$this->apiHeader][] = array('MSG' => 'Trial account is invalid.', 'success' => '0');
                }
                header( 'Content-Type: application/json; charset=utf-8' );
                echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                die();
            } else {
                $set[$this->apiHeader][] = array('MSG' => 'Trial account is valid.', 'success' => '2');
            }
            
            header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            die();
            
        } else if($get_helper['helper_name']=="add_trial"){  
            

            $device_id = !empty($get_helper['device_id']) ? trim($get_helper['device_id']) : '0';
            
            $trialModel = new TrialModel();
            $result = $trialModel->where('user_name', $device_id)
                      ->where('status', 1)
                      ->orderBy('id', 'DESC')
                      ->findAll();
            if (!empty($result)) {
                
                $jsonObj = array(
                    'user_type'=> 'xui',
                    'user_name'  => $result[0]['user_name'],
                    'user_password'  => $result[0]['user_password'],
                    'dns_base'  =>  $result[0]['dns_base']
                );
                
                $set[$this->apiHeader][] = $jsonObj;
                header( 'Content-Type: application/json; charset=utf-8' );
                echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                die();
            } else {
                
                $base_url = $this->settings['login_access_code'];
                
                $IP = $this->settings['reseller_access_code'];
                $apiKey = $this->settings['reseller_api_key'];
                $packageID = $this->settings['admin_packages'];
                
                $xTreamCodes = new XtreamCodes();
                $dataAPI = $xTreamCodes->createLine($IP, $apiKey, $device_id, $packageID);
                
                if($dataAPI){
                    if($dataAPI['status'] == "STATUS_SUCCESS"){
                        $dataAdd = array(
                            'user_type'=> 'xui',
                            'user_name'  => $dataAPI['data']['username'],
                            'user_password'  => $dataAPI['data']['password'],
                            'dns_base'  =>  $base_url,
                            'exp_date'  => $dataAPI['data']['exp_date'],
                            'registered_on'  =>  strtotime(date('d-m-Y h:i:s A')),
                            'status'  =>  '1'
                        );
                        
                        $trialModel->insert($dataAdd);
                        
                      	$jsonObj = array(
                            'user_type'=> 'xui',
                            'user_name'  => $dataAPI['data']['username'],
                            'user_password'  => $dataAPI['data']['password'],
                            'dns_base'  =>  $base_url
                        );
                        
                        $set[$this->apiHeader][] = $jsonObj;
                        header( 'Content-Type: application/json; charset=utf-8' );
                        echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                        die();
                      	
                    } else {
                        $set[$this->apiHeader][]=array('MSG'=> 'Trial failed!','success'=>'0');
                        header( 'Content-Type: application/json; charset=utf-8' );
                        echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                        die();
                    }
                } else {
                    $set[$this->apiHeader][]=array('MSG'=> 'Trial failed!','success'=>'0');
                    header( 'Content-Type: application/json; charset=utf-8' );
                    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                    die();
                }
            }
        } else {
            $data = $this->request->getPost('data');
            $get_helper = $this->getApiData($data); 
        }
    }
    
    // Helper method to check purchase code validity
    private function isPurchaseCodeValid() {
        $envatoConfig = config('Envato');
        
        // If bypass mode is enabled, accept any non-empty values
        if ($envatoConfig->bypassVerification === true) {
            return !empty($this->settings['envato_buyer_name']) 
                && !empty($this->settings['envato_purchase_code']) 
                && !empty($this->settings['envato_api_key']);
        }
        
        // Production mode: strict validation
        return !empty($this->settings['envato_buyer_name']) 
            && !empty($this->settings['envato_purchase_code']) 
            && !empty($this->settings['envato_api_key']);
    }

    // Separate method for handling the API data validation
    private function getApiData($data_info) {
        // Decode and check validity
        $data_json = $data_info;
        $data_arr = json_decode(urldecode(base64_decode($data_json)), true);
        
        // Validate package name - must match stored package name
        if ($data_arr['application_id'] != $this->settings['envato_package_name']) {
            $set[$this->apiHeader][] = array('success' => '-1', "MSG" => 'Invalid Package Name');

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit();
        }
        return $data_arr;
    }
}