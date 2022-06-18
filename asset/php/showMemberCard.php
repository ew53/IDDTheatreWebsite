<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
  
$connection = new mysqli("localhost", 
                         "root", 
                         "", 
                         "iddtheatre");

                         $postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$memberID = $request->memberID;

$result = $connection->query(
    "SELECT c.id, c.cardno, c.type, c.cardurl
    FROM card c
    WHERE c.memberid=$memberID");
    
 
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