<?php
require_once 'config.php';

header('Content-Type: application/json');

// Get callback data
$callback_data = file_get_contents('php://input');
$data = json_decode($callback_data, true);

// Log callback
$log_file = __DIR__ . '/logs/callback.log';
$log_entry = date('Y-m-d H:i:s') . ' | Callback: ' . $callback_data . PHP_EOL;
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Process callback
if (isset($data['Body']['stkCallback'])) {
    $callback = $data['Body']['stkCallback'];
    $result_code = $callback['ResultCode'];
    $result_desc = $callback['ResultDesc'];
    $merchant_request_id = $callback['MerchantRequestID'];
    $checkout_request_id = $callback['CheckoutRequestID'];
    
    if ($result_code === 0) {
        // Payment successful
        $metadata = $callback['CallbackMetadata']['Item'];
        $amount = $phone = $transaction_id = '';
        
        foreach ($metadata as $item) {
            if ($item['Name'] === 'Amount') $amount = $item['Value'];
            if ($item['Name'] === 'PhoneNumber') $phone = $item['Value'];
            if ($item['Name'] === 'MpesaReceiptNumber') $transaction_id = $item['Value'];
        }
        
        // Process successful payment (update database, send confirmation, etc.)
        // This is where you'd handle your business logic
        
        $success_log = date('Y-m-d H:i:s') . 
                      ' | SUCCESS | Phone: ' . $phone . 
                      ' | Amount: ' . $amount . 
                      ' | Transaction: ' . $transaction_id . PHP_EOL;
        file_put_contents($log_file, $success_log, FILE_APPEND);
    } else {
        // Payment failed
        $failed_log = date('Y-m-d H:i:s') . 
                     ' | FAILED | ' . $result_desc . 
                     ' | Request ID: ' . $merchant_request_id . PHP_EOL;
        file_put_contents($log_file, $failed_log, FILE_APPEND);
    }
}

// Acknowledge receipt
echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Success']);
?>
