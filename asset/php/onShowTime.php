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
$motID = $request->motID;

$result = $connection->query(
    "SELECT s.id 'sid', s.motid 'motid', s.hallid 'hallid', s.time 'time', mot.id 'movieontheatreid', mv.name 'moviename', mv.posterurl 'posterurl'
    FROM movieshowtime s 
    INNER JOIN movieontheatre mot ON s.motid = mot.id
    INNER JOIN movie mv ON mot.movieid = mv.id

    WHERE s.motid = $motID ");

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

$output = '{"time":' . $json_out . '}';
  
echo $output;
?>