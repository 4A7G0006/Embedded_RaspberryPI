<?php
date_default_timezone_set("Asia/Taipei");
$get_play = @$_GET['play_status'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finaltopic";
$conn = mysqli_connect($servername,$username,$password,$dbname );
$update_status="UPDATE play_status SET status = $get_play";
mysqli_query($conn,$update_status);
mysqli_close($conn);
?>