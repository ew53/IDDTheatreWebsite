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
$ticketID = $request->currentTicketID;

$result = $connection->query(
    "SELECT t.id 'ticketid', st.row 'seatrow', st.no 'seatno', ms.time 'time', ms.price 'price', mv.name 'moviename', th.name 'theatrename', h.hallname 'hallname' FROM ticket t 
    INNER JOIN movieshowtime ms ON t.showtime = ms.id 
    INNER JOIN seat st ON t.seatid = st.id
    INNER JOIN movieontheatre mt  ON ms.motid = mt.id
    INNER JOIN movie mv ON mt.movieid = mv.id
    INNER JOIN theatre th ON mt.theatreid = th.id
    INNER JOIN theatrehall h ON ms.hallid = h.id
    WHERE t.id = $ticketID");
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

$output = '{"ticket":' . $json_out . '}';
  
echo $output;
?>