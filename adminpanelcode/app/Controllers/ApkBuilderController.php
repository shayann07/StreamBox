<?php

namespace App\Controllers;

use App\Libraries\CIAuth;
use App\Models\PanelModel;

class ApkBuilderController extends BaseController {
    
    protected $settings;

    public function __construct(){
        $panelModel = new PanelModel();
        $this->settings = $panelModel->getSettings();
    }

    /**
     * Show APK builder page
     */
    public function index() {
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        $data = [
            'pageTitle' => 'APK Builder',
            'currentFile' => 'apk_builder'
        ];
        $data['settings'] = $this->settings;
        return view('apk_builder', $data);
    }

    /**
     * Build APK with validation
     */
    public function buildHandler() {
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $allowedAdminTypes = [1, 3];
        if(!in_array(CIAuth::adminType(), $allowedAdminTypes)){
            return redirect()->to(base_url('ns-admin'));
        }
        
        // CSRF validation
        $postedToken = $this->request->getPost('csrf_test_name');
        if (empty($postedToken) || $postedToken !== csrf_hash()) {
            $message = array('message' => 'Security token validation failed.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/apk-builder'));
        }
        
        // Get form inputs
        $app_name = trim($this->request->getPost('app_name'));
        $package_name = trim($this->request->getPost('package_name'));
        $version_code = trim($this->request->getPost('version_code'));
        $version_name = trim($this->request->getPost('version_name'));
        
        // Validate inputs
        $validation_errors = $this->validateBuildInputs($app_name, $package_name, $version_code, $version_name);
        
        if (!empty($validation_errors)) {
            $message = array('message' => implode(', ', $validation_errors), 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/apk-builder'));
        }
        
        // Validate package name against stored settings
        if ($package_name !== $this->settings['envato_package_name']) {
            $message = array(
                'message' => 'Package name does not match verified Envato package. Expected: ' . $this->settings['envato_package_name'],
                'class' => 'error'
            );
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/apk-builder'));
        }
        
        // Validate license/Envato credentials exist
        if (!$this->isLicenseValid()) {
            $message = array(
                'message' => 'Valid Envato license required. Please complete verification first.',
                'class' => 'error'
            );
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/apk-builder'));
        }
        
        // Generate APK file
        $apk_filename = $this->generateApkFile($app_name, $package_name, $version_code, $version_name);
        
        if (empty($apk_filename)) {
            $message = array('message' => 'Failed to generate APK file.', 'class' => 'error');
            session()->set('response_msg', $message);
            return redirect()->to(base_url('ns-admin/apk-builder'));
        }
        
        // Log APK build
        $this->logApkBuild($app_name, $package_name, $version_name, CIAuth::id());
        
        $message = array(
            'message' => 'APK generated successfully: ' . $apk_filename,
            'class' => 'success'
        );
        session()->set('response_msg', $message);
        return redirect()->to(base_url('ns-admin/apk-builder'));
    }

    /**
     * Validate build inputs
     */
    private function validateBuildInputs($app_name, $package_name, $version_code, $version_name) {
        $errors = [];
        
        if (empty($app_name)) {
            $errors[] = 'App name is required';
        }
        
        if (empty($package_name)) {
            $errors[] = 'Package name is required';
        } elseif (!preg_match('/^[a-z][a-z0-9_]*(\.[a-z0-9_]+)*$/', $package_name)) {
            $errors[] = 'Invalid package name format (e.g., com.example.app)';
        }
        
        if (empty($version_code) || !is_numeric($version_code)) {
            $errors[] = 'Valid version code (numeric) is required';
        }
        
        if (empty($version_name)) {
            $errors[] = 'Version name is required';
        } elseif (!preg_match('/^\d+\.\d+(\.\d+)?$/', $version_name)) {
            $errors[] = 'Invalid version name format (e.g., 1.0.0)';
        }
        
        return $errors;
    }

    /**
     * Check if license is valid
     */
    private function isLicenseValid() {
        return !empty($this->settings['envato_buyer_name']) 
            && !empty($this->settings['envato_purchase_code']) 
            && !empty($this->settings['envato_api_key'])
            && !empty($this->settings['envato_package_name']);
    }

    /**
     * Generate APK file (Simulated)
     * In production, this would call actual APK build tool
     */
    private function generateApkFile($app_name, $package_name, $version_code, $version_name) {
        $envatoConfig = config('Envato');
        
        // Create APK build directory if it doesn't exist
        $apk_dir = FCPATH . $envatoConfig->apkBuildDir;
        if (!is_dir($apk_dir)) {
            @mkdir($apk_dir, 0755, true);
        }
        
        // Generate APK filename
        $timestamp = date('YmdHis');
        $apk_filename = strtolower(str_replace(' ', '_', $app_name)) . '_' . $version_name . '_' . $timestamp . '.apk';
        $apk_file_path = $apk_dir . $apk_filename;
        
        // Create APK build manifest
        $manifest = [
            'app_name' => $app_name,
            'package_name' => $package_name,
            'version_code' => $version_code,
            'version_name' => $version_name,
            'envato_buyer_name' => $this->settings['envato_buyer_name'],
            'envato_package_name' => $this->settings['envato_package_name'],
            'envato_api_key' => $this->settings['envato_api_key'],
            'built_on' => date('Y-m-d H:i:s'),
            'built_by' => CIAuth::id(),
            'server_url' => base_url(),
            'api_endpoint' => base_url('api')
        ];
        
        // Create manifest file
        $manifest_file = $apk_dir . str_replace('.apk', '_manifest.json', $apk_filename);
        file_put_contents($manifest_file, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        
        // Create dummy APK file (in production, this would be generated by build tools)
        // For now, create a minimal APK structure
        $apk_content = json_encode([
            'type' => 'apk_build',
            'manifest' => $manifest,
            'timestamp' => time()
        ]);
        
        file_put_contents($apk_file_path, $apk_content);
        
        return $apk_filename;
    }

    /**
     * Log APK build action
     */
    private function logApkBuild($app_name, $package_name, $version_name, $admin_id) {
        $log_file = WRITEPATH . 'logs/apk_builds_' . date('Y-m-d') . '.log';
        $log_message = "[" . date('Y-m-d H:i:s') . "] "
            . "App: {$app_name}, "
            . "Package: {$package_name}, "
            . "Version: {$version_name}, "
            . "Builder ID: {$admin_id}\n";
        @file_put_contents($log_file, $log_message, FILE_APPEND);
    }

    /**
     * Download APK file
     */
    public function downloadApk($filename) {
        if(!CIAuth::check()){
            return redirect()->to(base_url('ns-admin/login'));
        }
        
        $envatoConfig = config('Envato');
        $apk_dir = FCPATH . $envatoConfig->apkBuildDir;
        $file_path = $apk_dir . $filename;
        
        // Security: prevent directory traversal
        if (strpos(realpath($file_path), realpath($apk_dir)) !== 0) {
            return $this->response->setStatusCode(403)->setBody('Access denied');
        }
        
        if (!file_exists($file_path)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }
        
        return $this->response->download($file_path, null);
    }
}
