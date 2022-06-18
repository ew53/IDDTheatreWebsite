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
$movieID = $request->movieID;
$testing = 1;

$result = $connection->query(
    "SELECT mt.id 'motid', mt.theatreid 'theatreid', th.name 'theatrename', m.name 'moviename', m.posterurl 'posterurl' 
    FROM movieontheatre mt 
    INNER JOIN theatre th ON mt.theatreid = th.id 
    INNER JOIN movie m ON mt.movieid = m.id
    WHERE mt.movieid = $movieID ");
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

$output = '{"ontheatre":' . $json_out . '}';
  
echo $output;
?>