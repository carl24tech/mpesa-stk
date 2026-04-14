## WITH ITENTIONAL BUGS MY BRO!! 😏😭😂

# M-Pesa STK Push Payment System

A professional PHP implementation of Safaricom's M-Pesa STK Push API.

## Quick Setup

1. Edit config.php with your credentials:
   - CONSUMER_KEY
   - CONSUMER_SECRET  
   - PASSKEY
   - CALLBACK_URL

2. Create logs folder:
   mkdir logs
   chmod 755 logs

3. Upload to server and you're done.

## Requirements

- PHP 7.4+
- cURL extension
- M-Pesa API credentials

## Environment URLs

Sandbox: https://sandbox.safaricom.co.ke
Production: https://api.safaricom.co.ke

## Files

index.php - Payment form
config.php - Your credentials here
process.php - Handles STK push
callback.php - M-Pesa responses
assets/ - CSS and JS files
logs/ - Transaction logs

## License

MIT - Use freely
