<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
 
$routes->set404Override(function() {
    $data = ['pageTitle' => '404'];
    $data['settings'] = [
        'site_description' => 'Oops! The requested URL was not found on this server.',
        'site_keywords' => 'Oops, 404',
        'app_name' => 'Page Not Found :('
    ];
    echo view('404', $data);
});

$routes->get('/', 'AuthController::index');
$routes->get('/info', 'Home::index');

// API -------------------------------------------------------------------------
$routes->add('/api', 'ApiController::index');
$routes->add('/api-web', 'ApiWebController::index');
$routes->add('/sbox_api', 'SBoxController::index');
$routes->add('/sbox_api.php', 'SBoxController::index');

$routes->get('/privacy-policy', 'PanelController::policy_index');
$routes->get('/terms', 'PanelController::terms_index');

$routes->get('/account-delete-request', 'DataDeletioController::create');
$routes->get('/account-request-done', 'DataDeletioController::create_done');
$routes->add('/create_extream_handler', 'DataDeletioController::createHandler');

$routes->get('/reseller', 'ResellerController::index');
$routes->group('reseller', static function($routes){
    
    // Login -------------------------------------------------------------------
    $routes->get('login', 'ResellerController::loginForm');
    $routes->get('logout', 'ResellerController::logout');
    $routes->add('login_handler', 'ResellerController::loginHandler');
    
    // Dashboard----------------------------------------------------------------
    $routes->get('create', 'ResellerController::index');
    $routes->get('done', 'ResellerController::done');
    
    $routes->add('create_handler', 'ResellerController::createHandler');
});

$routes->get('/ns-admin', 'AuthController::index');
$routes->group('ns-admin', static function($routes){
    
    // Login -------------------------------------------------------------------
    $routes->get('login', 'AuthController::loginForm');
    $routes->get('logout', 'AuthController::logout');
    $routes->add('login_handler', 'AuthController::loginHandler');
    
    // Register----------------------------------------------------------
    $routes->get('register', 'AuthController::registerForm');
    $routes->add('register_handler', 'AuthController::registerHandler');
    
    // Forgot password----------------------------------------------------------
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->add('forgot_handler', 'AuthController::forgotPasswordHandler');
    
    // Profile----------------------------------------------------------
    $routes->get('profile', 'AuthController::profileForm');
    $routes->add('profile_handler', 'AuthController::profileHandler');
    
    // Dashboard----------------------------------------------------------------
    $routes->get('dashboard', 'PanelController::index');
    
    // Notification-------------------------------------------------------------
    $routes->get('manage-notification', 'NotificationController::index');
    $routes->get('notification-onesignal', 'NotificationController::onesignal_index');
    $routes->add('onesignal_handler', 'NotificationController::onesignalHandler');
    $routes->add('delete-notification/(:num)', 'NotificationController::delete/$1');
    
    // CustomAds ---------------------------------------------------------------
    $routes->get('manage-ads', 'CustomAdsController::index');
    $routes->get('create-ads', 'CustomAdsController::create');
    $routes->get('create-ads/(:num)', 'CustomAdsController::edit/$1');
    $routes->add('create_ads_handler', 'CustomAdsController::createHandler');
    $routes->add('delete-ads/(:num)', 'CustomAdsController::delete/$1');
    $routes->add('status-ads/(:num)', 'CustomAdsController::status/$1');
    
    // DNS Extream -------------------------------------------------------------
    $routes->get('manage-extream', 'ExtreamController::index');
    $routes->get('create-extream', 'ExtreamController::create');
    $routes->get('create-extream/(:num)', 'ExtreamController::edit/$1');
    $routes->add('create_extream_handler', 'ExtreamController::createHandler');
    $routes->add('delete-extream/(:num)', 'ExtreamController::delete/$1');
    $routes->add('status-extream/(:num)', 'ExtreamController::status/$1');
    
    // DNS 1-Stream Extream ----------------------------------------------------
    $routes->get('manage-stream', 'StreamController::index');
    $routes->get('create-stream', 'StreamController::create');
    $routes->get('create-stream/(:num)', 'StreamController::edit/$1');
    $routes->add('create_stream_handler', 'StreamController::createHandler');
    $routes->add('delete-stream/(:num)', 'StreamController::delete/$1');
    $routes->add('status-stream/(:num)', 'StreamController::status/$1');
    
    // DNS Blocklist -----------------------------------------------------------
    $routes->get('manage-blocklist', 'BlocklistController::index');
    $routes->get('create-blocklist', 'BlocklistController::create');
    $routes->get('create-blocklist/(:num)', 'BlocklistController::edit/$1');
    $routes->add('create_blocklist_handler', 'BlocklistController::createHandler');
    $routes->add('delete-blocklist/(:num)', 'BlocklistController::delete/$1');
    $routes->add('status-blocklist/(:num)', 'BlocklistController::status/$1');
    
    // Device ID ---------------------------------------------------------------
    $routes->get('manage-device-id', 'DeviceController::index');
    $routes->get('create-device-id', 'DeviceController::create');
    $routes->get('create-device-id/(:num)', 'DeviceController::edit/$1');
    $routes->add('create_device_handler', 'DeviceController::createHandler');
    $routes->add('delete-device-id/(:num)', 'DeviceController::delete/$1');
    $routes->add('status-device-id/(:num)', 'DeviceController::status/$1');
    
    // Activation Code ---------------------------------------------------------
    $routes->get('manage-activation', 'ActivationController::index');
    $routes->get('create-activation', 'ActivationController::create');
    $routes->get('create-activation/(:num)', 'ActivationController::edit/$1');
    $routes->add('create_activation_handler', 'ActivationController::createHandler');
    $routes->add('delete-activation/(:num)', 'ActivationController::delete/$1');
    $routes->add('status-activation/(:num)', 'ActivationController::status/$1');
    
    // Reports -----------------------------------------------------------------
    $routes->get('manage-report', 'ReportsController::index');
    $routes->get('create-report/(:num)', 'ReportsController::edit/$1');
    $routes->add('create_report_handler', 'ReportsController::createHandler');
    $routes->add('delete-report/(:num)', 'ReportsController::delete/$1');
    
    // Admin -------------------------------------------------------------------
    $routes->get('manage-admin', 'AuthController::admin_index');
    $routes->get('create-admin', 'AuthController::create');
    $routes->get('create-admin/(:num)', 'AuthController::edit/$1');
    $routes->add('create_admin_handler', 'AuthController::createAdminHandler');
    $routes->add('delete-admin/(:num)', 'AuthController::delete/$1');
    $routes->add('status-admin/(:num)', 'AuthController::status/$1');
    
    // Data Deletion Policy ----------------------------------------------------
    $routes->get('manage-data-deletion', 'DataDeletioController::index');
    $routes->add('delete-data-deletion/(:num)', 'DataDeletioController::delete/$1');
    
    // Token -------------------------------------------------------------------
    $routes->get('create-token', 'TokenController::create');
    $routes->get('create-token/(:num)', 'TokenController::edit/$1');
    $routes->add('create_token_handler', 'TokenController::createHandler');
    $routes->add('delete-token/(:num)', 'TokenController::delete/$1');
    
    // Select Page -------------------------------------------------------------
    $routes->get('create-select', 'SelectPageController::create');
    $routes->get('create-select/(:num)', 'SelectPageController::edit/$1');
    $routes->add('create_select_handler', 'SelectPageController::createHandler');
    $routes->add('delete-select/(:num)', 'SelectPageController::delete/$1');
    $routes->add('status-select/(:num)', 'SelectPageController::status/$1');
    
    // Settings ----------------------------------------------------------------
    $routes->get('settings-panel', 'SettingsController::panel_index');
    $routes->get('settings-app', 'SettingsController::app_index');
    $routes->get('settings-app-ui', 'SettingsController::app_ui_index');
    $routes->get('settings-api', 'SettingsController::api_index');
    $routes->get('settings-web', 'SettingsController::web_index');
    $routes->get('settings-ads', 'SettingsController::ads_index');

    $routes->add('panel_handler', 'SettingsController::panelHandler');
    $routes->add('app_handler', 'SettingsController::appHandler');
    $routes->add('app_ui_handler', 'SettingsController::appUiHandler');
    $routes->add('api_handler', 'SettingsController::apiHandler');
    $routes->add('web_handler', 'SettingsController::webHandler');
    $routes->add('ads_handler', 'SettingsController::advertisementHandler');
    
    $routes->get('create-gallery', 'SettingsController::createGallery');
    $routes->add('create_gallery_handler', 'SettingsController::createGalleryHandler');
    $routes->add('delete-gallery/(:num)', 'SettingsController::deleteGallery/$1');
    $routes->add('status-gallery/(:num)', 'SettingsController::statusGallery/$1');
    
    // Verification ------------------------------------------------------------
    $routes->get('verification', 'AuthController::verification_index');
    $routes->add('verification_handler', 'AuthController::verificationHandler');
    
    // APK Builder ---------------------------------------------------------------
    $routes->get('apk-builder', 'ApkBuilderController::index');
    $routes->add('apk_builder_handler', 'ApkBuilderController::buildHandler');
    $routes->get('apk-download/(:any)', 'ApkBuilderController::downloadApk/$1');
    
    // Urls --------------------------------------------------------------------
    $routes->get('urls', 'PanelController::urls_index');
});