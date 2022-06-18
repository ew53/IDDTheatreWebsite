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
$showtimeID = $request->showtimeID;

$result = $connection->query(
    "SELECT st.id 'seatid', st.hallid 'seat_hallid', st.row 'row', st.no 'no', st.available 'available', sh.id 'showtimeid', sh.motid 'motid', sh.hallid 'showtime_hallid', mot.id 'showtime_motid', mv.name 'moviename', mv.posterurl 'posterurl' 
    FROM seat st 
    INNER JOIN movieshowtime sh ON st.hallid = sh.hallid 
    INNER JOIN movieontheatre mot ON sh.motid = mot.id
    INNER JOIN movie mv ON mot.movieid = mv.id
    WHERE sh.id = $showtimeID");

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