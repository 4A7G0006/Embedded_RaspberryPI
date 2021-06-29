<?php
date_default_timezone_set("Asia/Taipei");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "finaltopic";
$conn = mysqli_connect($servername,$username,$password,$dbname );
$all="SELECT COUNT(*) FROM playlist;";
$nn = mysqli_query($conn,$all);
$qq=mysqli_fetch_assoc($nn);
$plnum=$qq['COUNT(*)'];
$sql="SELECT loadsong FROM now_song";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result);
$num=$row['loadsong'];
$add=intval($num)-1;
if($add<1){
    $add=$plnum;
}                        
$num=strval($add) ;
$upp="UPDATE now_song SET loadsong = $num ";
mysqli_query($conn,$upp);
$select="SELECT * FROM playlist WHERE list = $num";
$str = mysqli_query($conn,$select);
$np=mysqli_fetch_assoc($str);
$song=$np['name'];
echo $song;
mysqli_close($conn);
?>