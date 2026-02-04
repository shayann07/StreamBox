<?php

namespace App\Libraries;

class CIAuthReseller {
    
    public static function setCIAuth($result){
        $session = session();
        $array = ['reseller_logged_in' => TRUE];
        $userdata = $result;
        $session->set('reseller_user_data', $userdata);
        $session->set($array);
    }
    
    public static function id(){
        $session = session();
        if($session->has('reseller_logged_in')){
            if($session->has('reseller_user_data')){
                return $session->get('reseller_user_data')->id;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    
    public static function check(){
        $session = session();
        return $session->has('reseller_logged_in');
    }
    
    public static function forget(){
        $session = session();
        $session->remove('reseller_logged_in');
        $session->remove('reseller_user_data');
        
        $message = array('message' => "Logout successfully",'class' => 'success');
        $session->set('response_msg', $message);
    }
    
    public static function user(){
        $session = session();
        if($session->has('reseller_logged_in')){
            if($session->has('reseller_user_data')){
                return $session->get('reseller_user_data');
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    
    public static function adminType(){
        $session = session();
        if($session->has('reseller_logged_in')){
            if($session->has('reseller_user_data')){
                return $session->get('reseller_user_data')->admin_type;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}