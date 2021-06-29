<?php 
date_default_timezone_set("Asia/Taipei");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finaltopic";
$conn = mysqli_connect($servername,$username,$password,$dbname );
$get="SELECT status FROM play_status;";
$select = mysqli_query($conn,$get);
$send=mysqli_fetch_assoc($select);
$Go=$send['status'];
echo $Go;
?>