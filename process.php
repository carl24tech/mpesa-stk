<?php
require_once 'config.php';

header('Content-Type: application/json');

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Validate inputs
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING) ?: 'Payment';

if (!$phone || !$amount) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number or amount']);
    exit;
}

// Format phone number
$phone = preg_replace('/[^0-9]/', '', $phone);
if (strlen($phone) === 9 && substr($phone, 0, 1) === '7') {
    $phone = '254' . $phone;
} elseif (strlen($phone) === 10 && substr($phone, 0, 2) === '07') {
    $phone = '254' . substr($phone, 1);
} elseif (strlen($phone) !== 12 || substr($phone, 0, 3) !== '254') {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
    exit;
}

// Validate amount
if ($amount < 1 || $amount > 150000) {
    echo json_encode(['success' => false, 'message' => 'Amount must be between 1 and 150,000']);
    exit;
}

try {
    // Get access token
    $access_token = getAccessToken();
    
    // Generate password
    $timestamp = date('YmdHis');
    $password = base64_encode(SHORTCODE . PASSKEY . $timestamp);
    
    // Prepare STK Push request
    $stk_data = [
        'BusinessShortCode' => SHORTCODE,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => SHORTCODE,
        'PhoneNumber' => $phone,
        'CallBackURL' => CALLBACK_URL,
        'AccountReference' => ACCOUNT_REFERENCE,
        'TransactionDesc' => substr($description, 0, 20)
    ];
    
    // Initiate STK Push
    $response = stkPushRequest($access_token, $stk_data);
    
    // Log transaction
    logTransaction($phone, $amount, $description, $response);
    
    if (isset($response['ResponseCode']) && $response['ResponseCode'] === '0') {
        echo json_encode([
            'success' => true,
            'message' => 'STK Push initiated. Please check your phone and enter your M-Pesa PIN.',
            'data' => [
                'merchant_request_id' => $response['MerchantRequestID'],
                'checkout_request_id' => $response['CheckoutRequestID']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $response['ResponseDescription'] ?? 'Failed to initiate STK Push'
        ]);
    }
    
} catch (Exception $e) {
    error_log("STK Push Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}

/**
 * Get M-Pesa API Access Token
 */
function getAccessToken() {
    $url = BASE_URL . '/oauth/v1/generate?grant_type=client_credentials';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . base64_encode(CONSUMER_KEY . ':' . CONSUMER_SECRET)]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        throw new Exception('Failed to get access token');
    }
    
    $result = json_decode($response, true);
    return $result['access_token'] ?? '';
}

/**
 * Make STK Push Request
 */
function stkPushRequest($access_token, $data) {
    $url = BASE_URL . '/mpesa/stkpush/v1/processrequest';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Log transaction details
 */
function logTransaction($phone, $amount, $description, $response) {
    $log_file = __DIR__ . '/logs/transactions.log';
    $log_entry = date('Y-m-d H:i:s') . ' | Phone: ' . $phone . 
                 ' | Amount: ' . $amount . ' | Desc: ' . $description . 
                 ' | Response: ' . json_encode($response) . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}
?>
