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
$memberid = $request->memberID;
$username = $request->musername;
$password = $request->mpassword;
$firstname = $request->mfirstname;
$lastname = $request->mlastname;
$email = $request->memail;
// $memberid = 75;
// $username = 34;
// $password = 12;
// $firstname = 44;
// $lastname = 66;
// $email = "hello";

$result = $connection->query(
    "INSERT INTO member (id, username, mpassword, firstname, lastname, email)
    VALUES ($memberid , '$username' , md5('$password'), '$firstname', '$lastname', '$email')");
    // "INSERT INTO member (id, username, mpassword, firstname, lastname, email)
    // VALUES (2, 'dsdsdsd', 'dsdsds', 'dsdsds', 'dsdss', 'dsdsd')");
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

$output = '{"member":' . $json_out . '}';
  
echo $output;
?>