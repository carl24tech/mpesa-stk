<?php

define('CONSUMER_KEY', 'YOUR_CONSUMER_KEY');
define('CONSUMER_SECRET', 'YOUR_CONSUMER_SECRET');
define('PASSKEY', 'YOUR_PASSKEY');
define('SHORTCODE', '174379'); // Sandbox shortcode
define('BASE_URL', 'https://sandbox.safaricom.co.ke'); 


define('CALLBACK_URL', 'https://yourdomain.com/callback.php');

// Business details
define('BUSINESS_NAME', 'Your Business Name');
define('ACCOUNT_REFERENCE', 'CompanyXLTD');

// Environment settings
define('ENVIRONMENT', 'sandbox'); // Change to 'production' for live
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Africa/Nairobi');

// Session start for CSRF protection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
