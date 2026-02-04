<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use App\Models\PanelModel;

class EmailService{
   
    private $mail;

    public function __construct(){
        
        require_once FCPATH . 'vendor/autoload.php'; // Load PHPMailer
        
        $this->mail = new PHPMailer(true);
        
        try {
            // Load SMTP settings dynamically
            $smtpSettings = SmtpConfig::getSmtpSettings();
            
            // Fetch app settings
            $panelModel = new PanelModel();
            $settings = $panelModel->getSettings();
            
           // Configure PHPMailer
            $this->mail->isSMTP();
            $this->mail->SMTPAuth   = true;
            $this->mail->Host       = $smtpSettings['SMTP_HOST'];
            $this->mail->Username   = $smtpSettings['SMTP_EMAIL'];
            $this->mail->Password   = $smtpSettings['SMTP_PASSWORD'];
            $this->mail->SMTPSecure = $smtpSettings['SMTP_SECURE'];
            $this->mail->Port       = $smtpSettings['PORT_NO'];
            
            // Set sender email
            $fromEmail = $smtpSettings['SMTP_EMAIL'];
            $fromName  = $settings['app_name'] ?? 'Your App';
            $this->mail->setFrom($fromEmail, $fromName);
            $this->mail->addReplyTo($fromEmail, $fromName);
            
        } catch (Exception $e) {
            log_message('error', 'Mailer Error: ' . $e->getMessage());
        }
    }

    public function sendEmail($to, $toName, $subject, $message){
        try {
            $this->mail->addAddress($to, $toName);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $message;
            
            return $this->mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}