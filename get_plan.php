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



$sql="SELECT id,plan_name,amount FROM tbl_plan WHERE status=1";

  
    $result = mysqli_query($con,$sql);  
	
    if (!empty($result)) {
        // check for empty result
        if (mysqli_num_rows($result) > 0) {
      // user node
            $response= array();
 
            while ($row = mysqli_fetch_array($result)) {       
              $report = array();
              
              $report["plan_name"] = $row["plan_name"];

              $report["id"] = $row["id"];

              $report["amount"] = $row["amount"];
              array_push($response, $report);
            }
            // success
            // $response["success"] = 1;

        } else {
            // no user found
            $response["success"] = 0;
            $response["message"] = "No Report found";
 
            // echo no users JSON
            echo json_encode($response);
        }
    } else {
        // no user found
        $response["success"] = 0;
        $response["message"] = "No found";
 
        // echo no users JSON
        echo json_encode($response);
    }

    echo  json_encode($response,JSON_UNESCAPED_UNICODE);
    mysqli_close($con);
?>
