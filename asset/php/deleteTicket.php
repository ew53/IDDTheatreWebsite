<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
  
$connection = new mysqli("localhost", 
                         "root", 
                         "", 
                         "iddtheatre");

// $movieid = $data -> movieidNow;

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$ticketID = $request->ticketID;

// $ticketID = 23;
// $seatID = 35;
// $showtimeID = 1;

$result = $connection->query(
    "DELETE FROM ticket
    WHERE id = $ticketID");
 
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

$output = '{"seat":' . $json_out . '}';
  
echo $output;
?>