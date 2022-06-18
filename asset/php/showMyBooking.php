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

$result = $connection->query(
    "SELECT b.id 'id', b.memberid 'bookingmemberid', b.ticketid 'bookticketid', t.id 'ticketid', t.seatid 'ticketseatid',
    t.showtime 'ticketshowtimeid', st.id 'seatid', st.row 'seatrow', st.no 'seatno', sht.id 'showtimeid', sht.motid 'showtimemotid', sht.hallid 'showtimehallid', sht.time 'showtimetime', sht.price 'showtimeprice', mot.id 'movotid', 
    mot.movieid 'movotmovieid', mot.theatreid 'movotthreatreid', hl.id 'hallid',hl.hallname 'hallname', mv.id 'movieid', mv.name 'moviename', mv.posterurl 'movieposterurl', th.id 'theatreid', th.name 'theatrename', mem.firstname 'memberfirstname', mem.lastname 'memberlastname'
    FROM booking b
    INNER JOIN ticket t ON b.ticketid = t.id
    INNER JOIN seat st ON t.seatid = st.id
    INNER JOIN movieshowtime sht ON t.showtime = sht.id
    INNER JOIN movieontheatre mot ON sht.motid = mot.id
    INNER JOIN theatrehall hl ON sht.hallid = hl.id
    INNER JOIN movie mv ON mot.movieid = mv.id
    INNER JOIN theatre th ON mot.theatreid = th.id
    INNER JOIN member mem ON b.memberid = mem.id
    WHERE b.memberid = $memberID");
 
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

$output = '{"mybooking":' . $json_out . '}';
  
echo $output;
?>