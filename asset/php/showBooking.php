<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
  
$connection = new mysqli("localhost", 
                         "root", 
                         "", 
                         "iddtheatre");


$result = $connection->query(
    "SELECT b.id 'id', b.memberid 'bookingmember', b.ticketid 'bookticketid' FROM booking b");
 
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

$output = '{"booking":' . $json_out . '}';
  
echo $output;
?>