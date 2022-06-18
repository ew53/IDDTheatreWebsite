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
$seatID = $request->seatID;
$showtimeID = $request->showtimeID;
// $ticketID = 23;
// $seatID = 35;
// $showtimeID = 1;

$result = $connection->query(
    "INSERT INTO ticket (id, seatid, showtime)
    VALUES ($ticketID, $seatID, $showtimeID)");
    // "SELECT m.id 'id', m.name 'name', m.description 'description', m.posterurl 'posterurl', m.releasedate 'releasedate', m.trailerurl 'trailerurl', d.name 'distributor' FROM movie m INNER JOIN distributor d ON m.distributorid = d.id WHERE m.shownow = '1'");
 
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