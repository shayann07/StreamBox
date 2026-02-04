<?php

namespace App\Libraries;

use Config\Database;

class SmtpConfig{
    
    public static function getSmtpSettings(){
        
        $db = Database::connect();
        $query = $db->query("SELECT * FROM tbl_smtp_settings WHERE id = 1");
        $smtp_row = $query->getRowArray();
        
        if (!$smtp_row) {
            throw new \Exception('SMTP settings not found.');
        }
        
        // Determine SMTP type
        if ($smtp_row['smtp_type'] === 'server') {
            return [
                'SMTP_HOST'     => $smtp_row['smtp_host'],
                'SMTP_EMAIL'    => $smtp_row['smtp_email'],
                'SMTP_PASSWORD' => $smtp_row['smtp_password'],
                'SMTP_SECURE'   => $smtp_row['smtp_secure'],
                'PORT_NO'       => $smtp_row['port_no'],
            ];
        } elseif ($smtp_row['smtp_type'] === 'gmail') {
            return [
                'SMTP_HOST'     => $smtp_row['smtp_ghost'],
                'SMTP_EMAIL'    => $smtp_row['smtp_gemail'],
                'SMTP_PASSWORD' => $smtp_row['smtp_gpassword'],
                'SMTP_SECURE'   => $smtp_row['smtp_gsecure'],
                'PORT_NO'       => $smtp_row['gport_no'],
            ];
        } else {
            return [
                'SMTP_HOST'     => $smtp_row['smtp_host'],
                'SMTP_EMAIL'    => $smtp_row['smtp_email'],
                'SMTP_PASSWORD' => $smtp_row['smtp_password'],
                'SMTP_SECURE'   => $smtp_row['smtp_secure'],
                'PORT_NO'       => $smtp_row['port_no'],
            ];
        }
    }
    
    public static function getSettings(){
        $db = Database::connect();
        $query = $db->query("SELECT * FROM tbl_smtp_settings WHERE id = 1");
        return $query->getRowArray();
    }
}
