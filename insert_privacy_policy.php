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


// file_get_contents('php://input');
$data1 =  json_decode($json);


$insertprivacy = "INSERT INTO tbl_privacy_policy (user_id,machine_id,privacy_policy,created_at) values ($data1->userid,'". $data1->machineid . "','".$data1->privacypolicy."',NOW())";

$result_privacy = mysqli_query($con, $insertprivacy);
$response["success"] = 1;
echo json_encode($response,JSON_UNESCAPED_UNICODE);


mysqli_close($con);
?>
