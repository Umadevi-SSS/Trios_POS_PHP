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

$sql_sync = "SELECT DATE_FORMAT(created_at,'%d/%m/%Y %H:%i:%s') AS sync_date FROM  tbl_pos_date_sales WHERE client_id=".$data1->clientid." ORDER BY id DESC LIMIT 1";

$result_sync = mysqli_query($con,$sql_sync);
$value = mysqli_fetch_array($result_sync);

	
    if (!empty($result_sync)) {

            $response["success"] = 1;
            $response["sync_date"] = $value["sync_date"];

        
    } else {
        // no user found
        $response["success"] = 0;
        $response["sync_date"] = "";
        // echo no users JSON
        echo json_encode($response);
    }

    echo  json_encode($response,JSON_UNESCAPED_UNICODE);
    mysqli_close($con);
?>
