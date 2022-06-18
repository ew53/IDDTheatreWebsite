<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
  
$connection = new mysqli("localhost", 
                         "root", 
                         "", 
                         "iddtheatre");

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$bookingID = $request->bookingID;

$result = $connection->query(
    "UPDATE seat st 
    INNER JOIN ticket t
    ON st.id = t.seatid
    INNER JOIN booking b
    ON t.id = b.ticketid
    SET st.available = 0
    WHERE b.id = $bookingID");
    // "UPDATE st
    // SET st.available = 0
    // FROM seat st 
    // INNER JOIN ticket t ON st.id = t.id
    // INNER JOIN booking b ON b.ticketid = t.id
    // WHERE b.id = 1");

$connection->close();

?>