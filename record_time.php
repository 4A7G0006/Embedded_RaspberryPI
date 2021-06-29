<?php
date_default_timezone_set("Asia/Taipei");
$record = @$_GET['record'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finaltopic";
$conn = mysqli_connect($servername,$username,$password,$dbname );
$all="UPDATE record_time SET playing=$record ";
mysqli_query($conn,$all);
mysqli_close($conn);
?>