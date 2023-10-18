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

    