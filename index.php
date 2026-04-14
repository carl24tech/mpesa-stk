<?php
require_once 'config.php';

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa STK Push - <?php echo BUSINESS_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="logo">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                        <rect width="40" height="40" rx="8" fill="#00B140"/>
                        <path d="M12 20L18 14L22 18L28 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 28L14 22L18 18L12 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h1>M-Pesa Payment</h1>
                <p class="subtitle">Secure STK Push Payment</p>
            </div>

            <div id="message-box" class="message-box hidden"></div>

            <form id="stkForm" class="payment-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-wrapper">
                        <span class="country-code">+254</span>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               class="form-control" 
                               placeholder="712345678"
                               maxlength="9"
                               pattern="[0-9]{9}"
                               required>
                    </div>
                    <small class="form-text">Enter your M-Pesa registered phone number</small>
                </div>

                <div class="form-group">
                    <label for="amount">Amount (KES)</label>
                    <div class="input-wrapper">
                        <span class="currency">KSh</span>
                        <input type="number" 
                               id="amount" 
                               name="amount" 
                               class="form-control" 
                               placeholder="Enter amount"
                               min="1"
                               max="150000"
                               step="1"
                               required>
                    </div>
                    <small class="form-text">Minimum: KSh 1 | Maximum: KSh 150,000</small>
                </div>

                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <input type="text" 
                           id="description" 
                           name="description" 
                           class="form-control" 
                           placeholder="Payment for services"
                           maxlength="50">
                </div>

                <button type="submit" id="submitBtn" class="btn btn-primary">
                    <span class="btn-text">Initiate Payment</span>
                    <span class="spinner hidden"></span>
                </button>
            </form>

            <div class="card-footer">
                <div class="security-badge">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 0L2 3v5c0 4 6 8 6 8s6-4 6-8V3L8 0z"/>
                    </svg>
                    <span>256-bit SSL Secured</span>
                </div>
                <p class="footer-text">Powered by Safaricom M-Pesa</p>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
