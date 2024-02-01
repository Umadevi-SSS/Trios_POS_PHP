<?php

header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json, charset=utf-8');

// array for JSON response
$response = array();

require_once __DIR__ . '/catchexception.php';
// include db connect class
require_once __DIR__ . '/db_config.php';

// connecting to db
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE) or die(mysqli_error($con));
mysqli_set_charset($con, "utf8");
$con->set_charset('utf8');

$passcode = "";
$userid = "";

$json = file_get_contents('php://input');
$data1 = json_decode($json);

if (isset($data1->passcode)) {
 $passcode = $data1->passcode;
} else {
 echo "no";
 return;
}

if (isset($data1->userid)) {
 $userid = $data1->userid;
} else {
 return;
}


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
$output = base64_encode(openssl_encrypt(urldecode($passcode), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv));

// echo $output;

$sql = "SELECT a.id,a.user_name,CAST(a.client_id AS CHAR(50)) as client_id,COALESCE(b.privacy_policy,'') AS privacy_policy
 FROM tbl_user_profile AS a LEFT  JOIN tbl_privacy_policy AS b ON a.id=b.user_id 
WHERE a.passcode='" . $output . "' AND a.user_id='" . $userid . "' AND a.status=1 ORDER BY b.id DESC LIMIT 1";

$salecode = "";
//echo $sql;
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);

if (!empty($result)) {
 // check for empty result
 if (mysqli_num_rows($result) > 0) {

  $response["userid"] = $row["id"];
  $response["privacy_policy"] = $row["privacy_policy"];

  $response["username"] = $row["user_name"];
  $response["clientid"] = $row["client_id"];
  // success
  $response["success"] = 1;

  // echoing JSON response

 } else {
  // no user found
  $response["userid"] = "";
  $response["username"] = "";
  $response["privacy_policy"] = "";
  $response["clientid"] = "";
  $response["success"] = 0;
  $response["message"] = "No Report found";

  // echo no users JSON

 }
} else {
 // no user found
 $response["userid"] = "";
 $response["privacy_policy"] = "";

 $response["username"] = "";
 $response["clientid"] = "";

 $response["success"] = 0;
 $response["message"] = "No found";

 // echo no users JSON

}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
mysqli_close($con);
