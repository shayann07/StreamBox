<?php

namespace App\Libraries;

class CIAuth {
    
    public static function setCIAuth($result){
        $session = session();
        $array = ['logged_in' => TRUE];
        $userdata = $result;
        $session->set('userdata', $userdata);
        $session->set($array);
    }
    
    public static function id(){
        $session = session();
        if($session->has('logged_in')){
            if($session->has('userdata')){
                return $session->get('userdata')->id;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    
    public static function check(){
        $session = session();
        return $session->has('logged_in');
    }
    
    public static function forget(){
        $session = session();
        $session->remove('logged_in');
        $session->remove('userdata');
        
        $message = array('message' => "Logout successfully",'class' => 'success');
        $session->set('response_msg', $message);
    }
    
    public static function user(){
        $session = session();
        if($session->has('logged_in')){
            if($session->has('userdata')){
                return $session->get('userdata');
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    
    public static function adminType(){
        $session = session();
        if($session->has('logged_in')){
            if($session->has('userdata')){
                return $session->get('userdata')->admin_type;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}