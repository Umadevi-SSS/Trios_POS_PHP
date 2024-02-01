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

if (isset($data1->fromdate)) {
  $fromdate = $data1->fromdate;
} else {
  echo "no";
  return;
}

if (isset($data1->todate)) {
 $todate = $data1->todate;
} else {
 return;
}



$sql_amount = "SELECT COUNT(billno) as billno,CONCAT('Rs. ', FORMAT(sum(amount), 0, 'en_IN'))  as amount FROM tbl_pos_date_sales
WHERE client_id=".$data1->clientid." AND bill_date between  DATE_FORMAT(STR_TO_DATE('" . $fromdate . "', '%Y-%m-%d'), '%Y-%m-%d') and DATE_FORMAT(STR_TO_DATE('" . $todate . "', '%Y-%m-%d'), '%Y-%m-%d')
ORDER BY bill_date DESC";

$result_amt = mysqli_query($con,$sql_amount);
$value = mysqli_fetch_array($result_amt);

$sql="SELECT billno,DATE_FORMAT(bill_date,'%d/%m/%Y') AS bill_date,CONCAT('Rs. ', FORMAT(amount, 2, 'en_IN')) as amount  FROM tbl_pos_date_sales
WHERE client_id=".$data1->clientid." AND bill_date between  DATE_FORMAT(STR_TO_DATE('".$fromdate."', '%Y-%m-%d'), '%Y-%m-%d') and DATE_FORMAT(STR_TO_DATE('".$todate."', '%Y-%m-%d'), '%Y-%m-%d')
ORDER BY transactionno DESC";

  
    $result = mysqli_query($con,$sql);  
	
    if (!empty($result)) {
        // check for empty result
        if (mysqli_num_rows($result) > 0) {
      // user node
            $response["data"] = array();
 
            while ($row = mysqli_fetch_array($result)) {       
              $report = array();
              
              $report["bill_date"] = $row["bill_date"];
              $report["bill_no"] = $row["billno"];

              $report["amount"] = $row["amount"];
              array_push($response["data"], $report);
            }
            // success
            $response["success"] = 1;
            $response["total"] = $value["amount"];
            $response["totalbills"] = $value["billno"];

        } else {
            // no user found
            $response["success"] = 0;
            $response["message"] = "No Report found";
            $response["total"] = 0;
             $response["totalbills"] = 0;
 
            // echo no users JSON
            echo json_encode($response);
        }
    } else {
        // no user found
        $response["success"] = 0;
        $response["message"] = "No found";
        $response["total"] = 0;
         $response["totalbills"] = 0;
 
        // echo no users JSON
        echo json_encode($response);
    }

    echo  json_encode($response,JSON_UNESCAPED_UNICODE);
    mysqli_close($con);
?>
