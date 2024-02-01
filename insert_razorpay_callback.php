

<?php
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *');

// webhook.php

// Include Razorpay PHP SDK
require_once 'vendor/autoload.php';

use Razorpay\Api\Api;


$api_key = 'rzp_test_odilvJ4uBrslqs'; // Replace with your Razorpay Key ID
$api_secret = 'XiNTCZkuN1J2Re8sd2aN4lv3';



require_once __DIR__ . '/catchexception.php';
// include db connect class
require_once __DIR__ . '/db_config.php';

// connecting to db
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE) or die(mysqli_error($con));
mysqli_set_charset($con, "utf8");
$con->set_charset('utf8');



// Initialize Razorpay API
$api = new Api($api_key, $api_secret);

// Get the JSON input
$input = file_get_contents('php://input');

// Verify the signature
$webhookSecret = 'Shiva$123';
$webhookSignature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'];

try {
    $api->utility->verifyWebhookSignature($input, $webhookSignature, $webhookSecret);
} catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
    // Invalid signature
    http_response_code(400);
    die('Invalid signature');
}

// Process the webhook data
$attributes = json_decode($input, true);
$event = $attributes['event'];
$data = $attributes['payload']['payment']['entity'];

// Handle different events
switch ($event) {
    case 'payment.authorized':
        // Payment authorized
        // Update your database, send confirmation email, etc.
        $updaterazorpay = "UPDATE tbl_pos_mobile SET razorpay_response='" . $data . "',razorpay_status='authorized'
        WHERE order_id = '" . $orderId . "'";

        $result_update_razorpay = mysqli_query($con, $updaterazorpay);

        break;

    case 'payment.captured':
        // Payment captured
        // Update your database, send confirmation email, etc.
        $updaterazorpay = "UPDATE tbl_pos_mobile SET razorpay_response='" . $data . "',razorpay_status='captured'
        WHERE order_id = '" . $orderId . "'";

        $result_update_razorpay = mysqli_query($con, $updaterazorpay);

        break;

    case 'payment.failed':
        // Payment failed
        // Update your database, send failure notification, etc.
        $updaterazorpay = "UPDATE tbl_pos_mobile SET razorpay_response='" . $data . "',razorpay_status='failed'
        WHERE order_id = '" . $orderId . "'";

        $result_update_razorpay = mysqli_query($con, $updaterazorpay);

        break;

    // Add more cases for other events as needed

    default:
        // Unknown event
        break;
}

// Respond to Razorpay with a 200 OK status
http_response_code(200);
?>