<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> <!--使用jquery的ajax做不更新網頁讀取資料庫-->
    <script type="text/javascript">
        var all;
        var now_playing;
        var playlist;
        var player = new Audio();
        var stat;
        window.setInterval("back_reload();",300); 
        function loading(){
            <?php
                date_default_timezone_set("Asia/Taipei");
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "finaltopic";
                $conn = mysqli_connect($servername,$username,$password,$dbname );

                $sql="SELECT loadsong FROM now_song";
                $result = mysqli_query($conn,$sql);
                $row = mysqli_fetch_assoc($result);
                $num=$row['loadsong'];

                $select="SELECT * FROM playlist WHERE list = $num";
                $str = mysqli_query($conn,$select);
                $np=mysqli_fetch_assoc($str);
                $song=$np['name'];

                $timeupdate="SELECT playing FROM record_time";
                $str = mysqli_query($conn,$timeupdate);
                $now_time=mysqli_fetch_assoc($str);
                $playing_time=$now_time['playing'];

                $all="SELECT COUNT(*) FROM playlist;";
                $nn = mysqli_query($conn,$all);
                $qq=mysqli_fetch_assoc($nn);
                $plnum=$qq['COUNT(*)'];
                mysqli_close($conn);
            ?>
            now_playing=<?php echo intval($num)?>;
            all=<?php echo $plnum ?>;
            playlist = "<?php echo $song ?>";
            player = new Audio(playlist);
            player.currentTime=<?php echo $playing_time ?>;
        }
        function back_reload() {
            $.ajax({
                url:'play_status_go.php',
                type:"GET",
                data : {},
                async : false,
                dataType:"text",
                success:function(status){
                    stat=status;
                }     
            })  
            switch (stat) {
                case '1':
                    Pause();
                    break;
                case '2':
                    Stop();
                    break;
                case '3':
                    Up();
                    break;
                case '4':
                    Next();
                    break;
                case '5':
                    Play();
                    break;
                case '6':
                    Volume_plus();
                    break;
                case '7':
                    Volume_min();
                    break;                
                default:
            } 
        }
        function Stop() {
            player.pause();
            document.getElementById("now_play").innerHTML= '<font color="crimson">'+"目前播放狀態 : 停止"+'</font>';
            player.currentTime = 0;
        }
        function Next() {
            var data2;
            $.ajax({
                url:'next_song.php',
                type:"GET",
                data : {},
                async : false,
                dataType:"text",
                success:function(data){
                    data2=data;   
                }     
            })
            playlist=data2;
            change_title();
            Stop();
            player.remove();
            player = new Audio(playlist);
            ChangeString();
            new_song();
            document.getElementById("now_play").innerHTML='<font color="mediumblue">'+"目前播放狀態 : 播放中"+'</font>';
            player.play();
        }
        function Up(){
            var data1;
            $.ajax({
                    url:'back_song.php',
                    type:"GET",
                    data : {},
                    async : false,
                    dataType:"text",
                    success:function(data){
                        data1=data;   
                    }     
                })
            playlist=data1;
            change_title();
            Stop();
            player.remove();
            player = new Audio(playlist);
            ChangeString();
            new_song();
            document.getElementById("now_play").innerHTML='<font color="mediumblue">'+"目前播放狀態 : 播放中"+'</font>';
            player.play();
        }
        function Pause() {
            player.pause();
            document.getElementById("now_play").innerHTML='<font color="red">'+"目前播放狀態 : 暫停中"+'</font>';//"目前播放狀態 : 暫停中";
        }
        function Play() {
            document.title = playlist;
            document.getElementById("now_play").innerHTML='<font color="mediumblue">'+"目前播放狀態 : 播放中"+'</font>';
            new_song();
            ChangeString();
            player.play();
        }
        function Volume_plus(){
            player.volume+=0.1;
            document.getElementById("Volumee_set").innerHTML="音量 : "+Math.round(player.volume*100)+" % ";
        }
        function Volume_min(){
            player.volume-=0.1;
            if(player.volume<=0){
                player.volume=0.;
            }
            document.getElementById("Volumee_set").innerHTML="音量 : "+Math.round(player.volume*100)+" % ";
        }
        function change_title() {
            all++;
            if (now_playing > all - 1) {
                now_playing = 0;
            }
            if(now_playing < 0){
                now_playing = all - 1;
            }
            document.title = playlist;
        }
        function ChangeString() {
            document.getElementById("changesong").innerHTML ='<font color="black">'+ playlist+'</font>';
        }
        function new_song() {
            player.addEventListener("timeupdate", function () {

                //得到目前撥放百分比 當前進度/總進度
                var percent = player.currentTime / player.duration
                //計算進度條的因子 百分比需要*因子 最後得到100%
                var screen=document.getElementById("progressBar").clientWidth;
                var sp = screen / 100;
                //接續進度條的width
                var swidth = (percent * 100 * sp) + "px";
                console.log(percent * 100, swidth)
                //設置進度條的撥放進度
                document.getElementById("playProgressBar").style.width = swidth;
                //留兩位百分比
                document.getElementById("ptxt").innerText = "   "+ ((percent * 100).toFixed(2)) + " %"
                //結束進行下一首
                if (player.ended) {
                    Next();
                }
                $.ajax({
                    url:'record_time.php',
                    type:"GET",
                    data : {record:player.currentTime},
                    async : false,
                    dataType:"text",
                    success:function(){}     
                })
            })
        }
    </script>

    <style>
        .postion {
            display: flex;
            white-space:pre;
            align-items: center;          
        }
        .progress {
            height: 25px;
        }
        .progress_color {
            background-color:blue;
        }
        .Volumee{
            float: right;
        }
        .btn{
            width:10%;
            text-align:center;
        }
      </style>
</head>
<body onload="loading();">
    <div class="container">
        <div class="alert alert-primary" role="alert">
            <p>目前播放的歌曲是:</p><div id="Volumee_set" class="Volumee">音量 : 100 % </div><p id="changesong"><br></p><p id="now_play">目前播放狀態 :</p>
            <div class="progress " id="progressBar">
                <div class="progress_color" id="playProgressBar"></div>
            </div>
            <div class="postion" id="ptxt" aria-valuemin="0" aria-valuemax="100">&nbsp;&nbsp;0 %</div>
            <br>
            <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="Toolbar with button groups">
                <input type="button" value="播放" class="btn btn-secondary" onclick="Play()"><br>
                <input type="button" value="暫停" class="btn btn-secondary" onclick="Pause()"><br>
                <input type="button" value="停止" class="btn btn-secondary" onclick="Stop()"><br>
                <input type="button" value="音量 +" class="btn btn-secondary" onclick="Volume_plus()"><br>
                <input type="button" value="音量 -" class="btn btn-secondary" onclick="Volume_min()"><br>
                <input type="button" value="上一首" class="btn btn-secondary" onclick="Up()"><br>
                <input type="button" value="下一首" class="btn btn-secondary" onclick="Next()">
            </div>
        </div>
    </div>
</body>
</html>