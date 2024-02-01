<?php
 
$response = array();
 
 require_once __DIR__ . '/catchexception.php';
// include db connect class
require_once __DIR__ . '/db_config.php';
 

// $serverName = "172.16.1.200"; //serverName\instanceName
// $connectionInfo = array("Database" => "dbSSSPOS_ERNAKULAM_BRANCH", "UID" => "sa", "PWD" => "Sivaavis11");
// $conn = sqlsrv_connect($serverName, $connectionInfo);

// if ($conn) {
//    // echo "Conexión establecida.<br />";
// } else {
//    // echo "Conexión no se pudo establecer.<br />";
//     die(print_r(sqlsrv_errors(), true));
// }

// $sql = "SELECT Convert(nvarchar(50), Billingdate,103) as billdate,sum(TotalAmount) as Amount,Companycode
//  FROM tblPosAmount Group by Billingdate,Companycode";
// $params = array();
// $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
// $stmt = sqlsrv_query($conn, $sql);

// $row_count = sqlsrv_num_rows($stmt);

// if ($row_count === false) {
//     //echo "Error in Connection";
// } else {
//   //  echo "Connection Established";
// }
// $res = [];
// $data = array();

// while ($row = sqlsrv_fetch_array($stmt,SQLSRV_FETCH_NUMERIC)) {
//      $res= json_encode($row,JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
//      print $res;
//      $data[] = "($row[0], '$row[1]')";
// }
// print json_encode($data,JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
// sqlsrv_close($conn);



// connecting to db
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD,DB_DATABASE) or die(mysqli_error($con));
 mysqli_set_charset($con,"utf8");
$con->set_charset('utf8');

$json = file_get_contents('php://input');
$data1 =  json_decode($json);
//$data1 = json_decode($data);

 
foreach ($data1->deletedata as $item) {
   $sql_con = "DELETE FROM  tbl_pos_date_sales WHERE transactionno=".$item->transactionno." AND client_id=".$item->clientid."";
  $resultDelete = mysqli_query($con, $sql_con);
}

foreach ($data1->salesdata as $item) {
  $sql_con = "SELECT * FROM  tbl_pos_date_sales WHERE transactionno=".$item->transactionno." AND client_id=".$item->clientid."";
  $result = mysqli_query($con, $sql_con);
  if (empty($result) || mysqli_num_rows($result) <= 0) {
    if ($item->status == 'Active' || $item->status == '1') {
      $insertsales = "INSERT INTO tbl_pos_date_sales (bill_date,amount,company_code,billno,transactionno,created_at,client_id) values (DATE_FORMAT(STR_TO_DATE('" . $item->billdate . "', '%d/%m/%Y'), '%Y-%m-%d'),$item->amount,$item->companycode,'" . $item->billno ."',$item->transactionno,NOW(),$item->clientid)";
      $result_insert = mysqli_query($con, $insertsales);
    }
  } else {
    $updatesales = "UPDATE tbl_pos_date_sales SET bill_date=DATE_FORMAT(STR_TO_DATE('".$item->billdate."', '%d/%m/%Y'), '%Y-%m-%d'), amount=".$item->amount." WHERE transactionno=".$item->transactionno." AND client_id=".$item->clientid." ";
    $result_update = mysqli_query($con, $updatesales);
  }
}

mysqli_close($con);
?>
