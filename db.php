<?php

/*

$user = 'root';
$password = 'root';
$db = 'user';
$host = 'localhost';
$port = 3307;

$con = mysqli_connect($host,$user,$password,$db,$port);

*/

$con = new mysqli("localhost","root","","interview");

if($con->connect_error) {
    die("Connection failed");
}

function row_count($result) {
    return mysqli_num_rows($result);;
}


function query($query) {
    global $con;
    return mysqli_query($con,$query);
}


function confirm($result) {
    global $con;
    if(!$result){
        die("QUERY FAILED".mysqli_error($con));
        
    }
}
                      
                      
function fetch_array($result) {
    global $con;
    return $result->fetch_assoc();
}


?>