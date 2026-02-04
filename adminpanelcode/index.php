<?php

/*
 * Check minimum requirements for NemosoftsPanel.
 */
 
// Minimum PHP version required
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    die("<a>Current PHP version is " . PHP_VERSION . "! Minimum PHP version required is PHP $minPhpVersion or higher. Please upgrade your PHP version.</a>");
}

// Required PHP extensions
if(!extension_loaded('intl')){
	die("<a>Intl PHP extension missing! NemosoftsPanel requires Intl PHP extension to run, Please check and enable the extension.</a>");
}
if(!extension_loaded('mbstring')){
	die("<a>Mbstring  PHP extension missing! NemosoftsPanel requires Mbstring PHP extension to run, Please check and enable the extension.</a>");
}
if(!extension_loaded('json')){
	die("<a>JSON PHP extension missing! NemosoftsPanel requires JSON PHP extension to run, Please check and enable the extension.</a>");
}
if(!extension_loaded('pdo')){
	die("<a>PDO PHP extension missing! NemosoftsPanel requires PDO PHP extension to run, Please check and enable the extension.</a>");
}
if(!extension_loaded('curl')){
	die("<a>cURL PHP extension missing! NemosoftsPanel requires cURL PHP extension to run, Please check and enable the extension.</a>");
}
if(!extension_loaded('mysqli')){
	die("<a>MySQL PHP extension missing! NemosoftsPanel requires MySQL PHP extension to run, Please check and enable the extension.</a>");
}


/*
 * Check if the installation file exists.
 */
$installFile = "install/.lic";
if (!file_exists($installFile)) {
	header('Location: install');
	exit();
}

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';