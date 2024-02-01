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

// $data1 = json_decode('{"customer_id":"9","customer_name":"uma","mobile_no":"9999999","email_id":"uma@gmail.com","address":"","application_type":"server","registration_key":"9999","host_address":""}');

$lastInsertId = $data1->customer_id;

if ($data1->application_type == 'Server') {

    $sql = "SELECT * FROM tbl_customer AS a INNER JOIN tbl_customer_system AS b ON a.id=b.customer_id 
    WHERE a.customer_name='".$data1->customer_name."' AND a.mobile_no='" . $data1->mobile_no . "' AND b.application_type = '".$data1->application_type."'";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);

    if (!empty($result)) {
        if (mysqli_num_rows($result) == 0) {
            
        $insertcustomer = "INSERT INTO tbl_customer (customer_name,mobile_no,email_id,address,created_at)
            values ('" . $data1->customer_name . "','" . $data1->mobile_no . "','" . $data1->email_id . "' ,'" . $data1->address . "',NOW())";

            $result_customer = mysqli_query($con, $insertcustomer);

            $lastInsertId = mysqli_insert_id($con);

            $updatecustomer = "UPDATE tbl_customer SET customer_id=CONCAT('CI-','".$lastInsertId."') WHERE customer_name = '" . $data1->customer_name . "'";

            $result_update_customer = mysqli_query($con, $updatecustomer);

            $response["msg"] = 'Registered Successfully';
            $response["client_id"] = $lastInsertId;

        } else {
            $lastInsertId = "";
            $response["msg"] = 'Customer Name already exists';
            $response["success"] = 2;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return;
        }
    }
}

$insertcustomersystem = "INSERT INTO tbl_customer_system (application_type,registration_key,customer_id,created_at,host_address)
            values ('" . $data1->application_type . "','" . $data1->registration_key . "','" . $lastInsertId . "' ,NOW(),'" . $data1->host_address . "')";

$result_customer_system = mysqli_query($con, $insertcustomersystem);

$response["success"] = 1;
echo json_encode($response,JSON_UNESCAPED_UNICODE);


mysqli_close($con);
?>
