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
$newpassword = $request->mPassword;

$result = $connection->query(
    "UPDATE member
    SET mpassword = '$newpassword'
    WHERE id = $memberID");
    // "SELECT m.id 'id', m.username 'username', m.mpassword 'password', m.firstname 'firstname', m.lastname 'lastname', m.email 'email'
    // FROM member m
    // WHERE m.id = $memberID");
 
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