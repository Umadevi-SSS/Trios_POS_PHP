<?php
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json, charset=utf-8');

$response = array();
 
 require_once __DIR__ . '/catchexception.php';
// include db connect class
require_once __DIR__ . '/db_config.php';
 


// connecting to db
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE) or die(mysqli_error($con));
 mysqli_set_charset($con,"utf8");
$con->set_charset('utf8');

$json = file_get_contents('php://input');

$data1 =  json_decode($json);


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

$sql = "SELECT * FROM tbl_user_profile WHERE user_id='".$data1->userid."'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);
if (!empty($result)) {
    if (mysqli_num_rows($result) == 0) {
        
       $insertprivacy = "INSERT INTO tbl_user_profile (user_id,user_name,passcode,status,machine_id,address,mobile_no,created_at)
        values ('" . $data1->userid . "','" . $data1->user_name . "','" . $output . "' ,0,'" . $data1->machineid . "','" . $data1->address . "',
        '" . $data1->mobile_no . "',NOW())";

        $result_privacy = mysqli_query($con, $insertprivacy);
        $response["msg"] = 'Registered Successfully';

    } else {
        $response["msg"] = 'User ID already exists';
    }
}

$response["success"] = 1;
echo json_encode($response,JSON_UNESCAPED_UNICODE);


mysqli_close($con);
?>
