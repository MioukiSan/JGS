<?php
$servername="localhost";
$dbusername="root";
$dbpassword="";
$dbname="jgs";

$conn = mysqli_connect($servername,$dbusername,$dbpassword,$dbname);

// Check connection
if (!$conn){
    die("Maintenance Mode.");
}
define("CURRENCY","Php ");
require_once ("sql_utilities.php"); 
require_once ("function.php");
session_start();


if(isset($_GET['logout'])){
    $user_id = $_SESSION['user_id'];

    $updateStatusSql = "UPDATE users SET online_offline = 'offline' WHERE user_id = $user_id";
    mysqli_query($conn, $updateStatusSql);

    session_destroy();
    
    header("location: ./");
    exit();
}

?>