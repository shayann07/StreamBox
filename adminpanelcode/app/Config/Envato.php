<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Envato Verification Configuration
 * Controls whether to use real Envato verification or bypass mode for development
 */
class Envato extends BaseConfig
{
    /**
     * Enable/Disable Envato verification bypass
     * Set to true to bypass Envato authentication (development mode)
     * Set to false for production (real Envato verification required)
     * 
     * @var bool
     */
    public bool $bypassVerification = true;

    /**
     * Default dummy Envato credentials for bypass mode
     * Used when bypassVerification is true
     * 
     * @var array
     */
    public array $dummyCredentials = [
        'envato_buyer_name' => 'demo_buyer',
        'envato_purchase_code' => 'DEMO-LICENSE-CODE-12345',
        'envato_api_key' => 'demo_api_key_xyz123abc456def789',
        'envato_package_name' => 'com.example.streambox'
    ];

    /**
     * List of allowed test/demo purchase codes for bypass mode
     * Add any test codes that should be accepted
     * 
     * @var array
     */
    public array $testPurchaseCodes = [
        'DEMO-LICENSE-CODE-12345',
        'TEST-LICENSE-12345678',
        'DEV-LICENSE-87654321'
    ];

    /**
     * Allowed test package names for APK validation
     * These packages are always allowed regardless of verification status
     * 
     * @var array
     */
    public array $testPackageNames = [
        'com.example.streambox',
        'com.test.streambox',
        'com.demo.streambox'
    ];

    /**
     * Enable APK build from admin panel
     * Set to true to allow building APK files from admin dashboard
     * 
     * @var bool
     */
    public bool $enableApkBuilder = true;

    /**
     * APK build directory (relative to public folder)
     * Where generated APK files will be stored
     * 
     * @var string
     */
    public string $apkBuildDir = 'uploads/apk_builds/';

    /**
     * Require package name validation even in bypass mode
     * Set to true to enforce package name validation for APK builds
     * 
     * @var bool
     */
    public bool $enforcePackageValidation = true;

    /**
     * Enable logging of bypass verification attempts
     * Useful for monitoring development/test usage
     * 
     * @var bool
     */
    public bool $logBypassAttempts = true;
}
