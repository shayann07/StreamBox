<?php

namespace App\Libraries;

class PasswordGenerator {
    
    public static function make($length = 4, $available_sets = 'ld'){
        
        $sets = [];
        
        if (strpos($available_sets, 'l') !== false) {
            $sets[] = 'ABCDEFGHJKLMNPRSTWYZ';
        }
        
        if (strpos($available_sets, 'd') !== false) {
            $sets[] = '23456789';
        }
        
        $all = '';
        $password = '';
        
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        
        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }
        
        return str_shuffle($password);
    }
    
    public static function generateStrongPassword(){
    	$key = PasswordGenerator::make(8)."-".PasswordGenerator::make(4,"d")."-".PasswordGenerator::make()."-".PasswordGenerator::make()."-".PasswordGenerator::make(12);
    	return $key;
    }
    
}