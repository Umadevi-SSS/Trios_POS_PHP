

<?php
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *');
// header('Content-Type: application/json, charset=utf-8');

$response = array();
require_once('vendor/autoload.php');
use Razorpay\Api\Api;

$api_key = 'rzp_test_odilvJ4uBrslqs'; // Replace with your Razorpay Key ID
$api_secret = 'XiNTCZkuN1J2Re8sd2aN4lv3'; 
 
 require_once __DIR__ . '/catchexception.php';
// include db connect class
require_once __DIR__ . '/db_config.php';
 


// connecting to db
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE) or die(mysqli_error($con));
 mysqli_set_charset($con,"utf8");
$con->set_charset('utf8');

$json = file_get_contents('php://input');

$data1 =  json_decode($json);

 // $data1 = json_decode('{"device_id":"1212","passcode":"1234","email_id":"umsssdssssssssfassssdddssshrxxxsamzxa@gamil.com","customer_id":"9","plan_type":"2","plan_id":"1","plan_amount":"200","razorpay_response":"","razorpay_status":""}');


$keyval = "";
$keymethod = "";
//get key and method
$aesdetails = "select * from tbl_controls";

$pass = mysqli_query($con, $aesdetails);
if (!empty($pass)) {
 while ($row = mysqli_fetch_array($pass)) {
  $keyval = $row["keyval"];

  $keymethod = $row["method"];
 }
}

//passcode encrypt function
$key = substr(hash('sha256', $keyval, true), 0, 32); // Must be exact 32 chars (256 bit)// You must store this secret random key in a safe place of your system.
$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0); // IV must be exact 16 chars (128 bit)
$output = base64_encode(openssl_encrypt(urldecode( $data1->passcode), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv));



$sql = "SELECT a.status,b.plan_type FROM tbl_user_profile AS a INNER JOIN tbl_pos_mobile AS b ON a.mobile_id=b.id
    WHERE a.user_id='" . $data1->email_id . "'";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);

    if (!empty($result)) {
        if (mysqli_num_rows($result) == 0) {

            $insertplandetails = "INSERT INTO tbl_pos_mobile (email_id,customer_id,plan_type,plan_id,plan_amount,razorpay_response,
            razorpay_status,subscription_date,created_at)
            VALUES ('" . $data1->email_id . "','" . $data1->customer_id . "','" . $data1->plan_type . "','" . $data1->plan_id . "' ,'" . $data1->plan_amount . "',
            '" . $data1->razorpay_response . "','" . $data1->razorpay_status . "',NOW(),NOW())";
            
            $result_plan = mysqli_query($con, $insertplandetails);
            $lastInsertId = mysqli_insert_id($con);
            if ($data1->plan_type == "1") {
                $status = "1";
            } else {
                $status = "0";
                
            }
            $insertuser = "INSERT INTO tbl_user_profile (mobile_id,client_id,user_id,user_name,passcode,status,machine_id,address,mobile_no,created_at)
            values ('" . $lastInsertId . "','" . $data1->customer_id . "','" . $data1->email_id . "','','" . $output . "' ,'".$status. "','" . $data1->device_id . "','',
            '',NOW())";
    
            $result_user = mysqli_query($con, $insertuser);

            if ($data1->plan_type == "2") {
            }
            $response["msg"] = 'Successfully registered.';
            
            $response["receipt_id"] = $lastInsertId;
        }
        else if ($data1->plan_type == "1") {
            $response["msg"] = 'Already register. Please renewal the subscription.';
            $response["success"] = 3;
            $response["receipt_id"] = 0;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return;

        }
        else if ($row['status'] == "1" && $row['plan_type'] != "1") {
            $response["msg"] = 'Already register for this email id.';
            $response["success"] = 2;
            $response["receipt_id"] = 0;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return;

        }
        else {

            $insertplandetails = "INSERT INTO tbl_pos_mobile (email_id,customer_id,plan_type,plan_id,plan_amount,razorpay_response,
            razorpay_status,subscription_date,created_at)
            VALUES ('" . $data1->email_id . "','" . $data1->customer_id . "','" . $data1->plan_type . "','" . $data1->plan_id . "' ,'" . $data1->plan_amount . "',
            '" . $data1->razorpay_response . "','" . $data1->razorpay_status . "',NOW(),NOW())";
            
            $result_plan = mysqli_query($con, $insertplandetails);
            $lastInsertId = mysqli_insert_id($con);
            $response["receipt_id"] = $lastInsertId;
            if ($data1->plan_type == "1") {
                $status = "1";
            } else {
                $status = "0";
                
            }

            // $insertuser = "UPDATE tbl_user_profile SET status='0',mobile_id = '" . $lastInsertId . "' WHERE 
            // user_id='" . $data1->email_id . "'";
    
            // $result_user = mysqli_query($con, $insertuser);
        }
    }


$response["success"] = 1;
echo json_encode($response,JSON_UNESCAPED_UNICODE);


mysqli_close($con);
?>
