<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
  
$connection = new mysqli("localhost", 
                         "root", 
                         "", 
                         "iddtheatre");


$result = $connection->query(
    "SELECT c.id 'id', c.memberid 'cardmember', c.cardno 'cardno', c.cvv 'cvvno', c.expmonth 'expmonth', c.expyear 'expyear' FROM card c");
 
$output = "";
$arr = array();

while ($rs = $result->fetch_assoc()) {
    if ($output != "") {
        $output = ", ";
    }
    $arr[] = $rs;

    $json_out = json_encode($arr);
}

$connection->close();

$output = '{"card":' . $json_out . '}';
  
echo $output;
?>