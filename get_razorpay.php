<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razorpay Checkout</title>
</head>
<body>

<?php 
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *');


require_once 'vendor/autoload.php'; // Include Razorpay PHP SDK

use Razorpay\Api\Api;

$api_key = 'rzp_test_odilvJ4uBrslqs';
$api_secret = 'XiNTCZkuN1J2Re8sd2aN4lv3';

$api = new Api($api_key, $api_secret);

require_once __DIR__ . '/catchexception.php';
// include db connect class
require_once __DIR__ . '/db_config.php';
 


// connecting to db
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE) or die(mysqli_error($con));
 mysqli_set_charset($con,"utf8");
$con->set_charset('utf8');


// $json = file_get_contents('php://input');

// $data1 =  json_decode($json);

//$data1 = json_decode('{"device_id":"1212","passcode":"1234","email_id":"umsssssssssdddddssssadsssrxdsamzxa@gamil.com","customer_id":"9","plan_type":"2","plan_id":"1","plan_amount":50000,"razorpay_response":"","razorpay_status":""}');
if (isset($_GET['plan_amount'])) {
    // Access the 'name' parameter value
    $plan_amount = $_GET['plan_amount'];
   
} else {
    $plan_amount=0;
}

if (isset($_GET['receipt_id'])) {
    // Access the 'name' parameter value
    $receipt_id = $_GET['receipt_id'];
   
} else {
    $receipt_id=0;
}


if (isset($_GET['customer_name'])) {
    // Access the 'name' parameter value
    $customer_name = "'" . $_GET['customer_name'] . "'"; 
   
} else {
    $customer_name='';
}

if (isset($_GET['customer_email'])) {
    // Access the 'name' parameter value
    $customer_email = "'" . $_GET['customer_email'] . "'";
   
} else {
    $customer_email='';
}

if (isset($_GET['customer_mobileno'])) {
    // Access the 'name' parameter value
    $customer_mobileno = $_GET['customer_mobileno'];
   
} else {
    $customer_mobileno=0;
}

// Create a Razorpay order
$orderOptions = array(
    'amount' => $plan_amount, // Amount in paise (equals 500 INR)
    'currency' => 'INR',
    'receipt' => $receipt_id,
    'payment_capture' => 1 // Auto-capture
);

$order = $api->order->create($orderOptions);
$updateorderid = "UPDATE tbl_pos_mobile SET order_id= '" .$order->id . "' WHERE id = '" . $receipt_id . "'";

$result_update_orderid = mysqli_query($con, $updateorderid);


?>

<!-- <button id="rzp-button">Pay Now</button> -->

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    alert('df')
    var options = {
        key: 'rzp_test_odilvJ4uBrslqs',
        amount: 1.00,
        currency: 'INR',
        name: 'Trio-S Software Consultancy Pvt Ltd',
        description: 'Payment for your product or service',
        image: 'path/to/your/logo.png',
        order_id: '123',
        // prefill: {
        //     name: <?php echo $customer_name; ?>,
        //     email: <?php echo $customer_email; ?>,
        //     contact: <?php echo $customer_mobileno; ?>
        // },
        theme: {
            color: '#3399cc'
        }
    };

    var rzp = new Razorpay(options);

    // document.getElementById('rzp-button').onclick = function (e) {
        rzp.open();
        e.preventDefault();
    // }
</script>

</body>
</html>
