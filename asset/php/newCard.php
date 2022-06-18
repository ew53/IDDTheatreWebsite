<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
  
$connection = new mysqli("localhost", 
                         "root", 
                         "", 
                         "iddtheatre");

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$cardid = $request->cardID;
$memberid = $request->memberID;
$cardno = $request->cardNo;
$cvvno = $request->cvvNo;
$expmonth = $request->expMonth;
$expyear = $request->expYear;
$cardtype = $request->cardType;
$cardurl = $request->cardUrl;

// $cardid = 5;
// $memberid = 2;
// $cardno = 1222344456667888;
// $cvvno = 213;
// $expmonth = 8;
// $expyear = 2022;
// $cardtype = "VisaCard";
// $cardurl = "https://i.ibb.co/wBbVt8c/maestro.png";

$result = $connection->query(
    "INSERT INTO card (id, memberid, cardno, cvv, expmonth, expyear, type, cardurl)
    VALUES ($cardid , $memberid ,'$cardno', md5('$cvvno'), md5('$expmonth'), md5('$expyear'), '$cardtype', '$cardurl')");
 
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