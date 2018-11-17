<?php
session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])){

    header('Location: login.php');
    exit;
}

if (isset($_POST['Logout'])) {

    session_destroy();
    header('location: login.php');
}

$idm = $_SESSION['user_id'] ;
$user = $_SESSION['user_name'];

require 'conn.php' ;

$sql =("SELECT ideaText FROM idea WHERE '$idm' = member_id ORDER BY id DESC");

$sq =("SELECT * FROM idea WHERE '$idm' = member_id  ORDER BY session DESC LIMIT 1");

foreach($conn->query($sq) as $row) { 
   $ses = $row['session'] ;
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        label {
            padding: 12px 12px 12px 0;
            display: inline-block;
        }

        input[type=submit] {
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=reset] {
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            background-color: yellow;
            border: none;
        }

        input[type=submit]:hover {
            background-color: #45a049;
        }

        .container {
            border-radius: 10px;
            background-color: white;
            padding: 30px;
            margin: 2%;
        }

        .col-25 {
            float: left;
            width: 25%;
            margin-top: 6px;
        }

        .col-75 {
            float: left;
            width: 75%;
            margin-top: 6px;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        input[type=text] {
            width: 70%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        .vl {
            border-left: 1px solid #E6E6FA;
            height: 600px;
            position: absolute;
            left: 58%;
            top: 40%;
            margin-left: -3px;
        }

        .left {
            float:left;
            width:55%;
            background-color: #E6E6FA;
            padding: 10px;
            box-shadow: 5px 10px 8px #888888;
        }

        .right {
            float:left;
            width:40%;
            height:600px
        }

        .idea {
            border-radius: 2px;
            background-color: 	#87CEFA;
            padding: 20px;
            margin: 2%;
            width: 70%
        }

        .idea2 {
            border-radius: 2px;
            background-color:   #87CEFA;
            padding: 10px;
            margin: 2%;
            width: 80%;
        }

        button {
            padding: 5px 10px;
            border-radius: 2px;
            cursor: pointer;
            float: right;
            background-color: white;
            color: #008CBA;
            border: 2px solid #008CBA;
        }
        td {
                padding: 1px;
                text-align: left;
            }

    </style>

</head>
<body>
<div>
    <form method="post" action="">
        <button type='submit' name="Logout"  onclick="resetValues()">Logout</button>
    </form>
    <div>
        Welcome <?php echo $user ; ?>
    </div>
    <p>
        Your task is to come up with many ideas as you can to address the problem below. Be as specific as possible in your responses.
    </p>

    <p style="font-size:12px;">
        P.S: if you have any issues with the system, try refreching the page : it will maintain your ideas and the timer in the same place as before.
    </p>
</div>
<div>
    <h1>Challenge</h1>
    <p style="font-size:24px;">We are searching for innovative (technical) solution for the security of city building. In the first step think of possible dangerous events, wich might occur (e.g. fire). Then brainstorm innovative solutions, how people in the bulding could be protected from such a danger or rescued from the building.</p>

    <p id="clockdiv">
        Time left: <span class="minutes"></span>:<span class="seconds"></span>
    </p>
</div>

<div class="left" >
    <h3>
        Submit a new idea
    </h3>
    <div class="container" >
        <form method="post" action="insertion.php" id="myform">
            <input type="hidden" name="idm" value="<?php echo $idm; ?>">
            <div class="row">
   <textarea rows="10" cols="90" name="ideaText" id="ideaText" onkeyup='saveValue(this);' required></textarea>
            </div>
    </div>

    <input type="hidden" name="time" id="time" onkeyup='saveValue(this);'>
    <input type="hidden" name="session" id="session" value="<?php echo $ses ?>">

    <div class="row">
        <input type="reset" value="Reset">
        <input type="submit" value="Submit" style="background-color: #008CBA; color: white; border: none; " onclick="getTime()">
    </div>
    </form>
    <hr width="700px">
    <p>
        Your previous ideas
    </p>
    <p>
    <?php foreach($conn->query($sql) as $row) { ?>
    <div class="idea">
        <?php echo "{$row['ideaText']}";?>
    </div>
    <?php } ?>
    </p>
</div>
<div class="vl"></div>
<div class="right">
<center>
        <form action="" method="post">
            <input type="submit" value="NEED INSPIRATION ?" name="insp" style="background-color: white; color: #008CBA; border: 2px solid #008CBA; ">
        </form>
        <p style="margin-left : 20px;">
            Click the button above and you will be presented with a set of others'ideas.
        </p>
        <p style="margin-left : 20px;">
            Feel free to use them as inspiration: remix them with your own ideas, expand on them, or use them in any way you'd like!
        </p>
    <hr width="400px">
    <?php 
    if (isset($_POST['insp'])) {
    $sql2 = (" SELECT ideaText FROM idea WHERE '$idm' = member_id  ORDER BY RAND() LIMIT 3;");

    foreach($conn->query($sql2) as $row) { ?>
    <div class="idea2">
        <?php echo "{$row['ideaText']}"; ?>
    </div>
    <?php } }?>
</center>

</div>

<script type="text/javascript">

    // timer
    var timeInMinutes = 10;
    var currentTime = Date.parse(new Date());
    var deadline;
    var session;
    if(isNaN(document.getElementById('session').value)){ session = 0 ;}
    else{session = Number(document.getElementById('session').value);}

    if(localStorage.getItem("deadline") != 0) {
        deadline = new Date(localStorage.getItem("deadline"));
        document.getElementById('session').value = session;
    } else {
        deadline = new Date(currentTime + timeInMinutes*60*1000);
        session = session + 1 ;
        document.getElementById('session').value = session;
    }

    function getTimeRemaining(endtime){
        var t = Date.parse(endtime) - Date.parse(new Date());
        var seconds = Math.floor( (t/1000) % 60 );
        var minutes = Math.floor( (t/1000/60) % 60 );
        return {
            'total': t,
            'minutes': minutes,
            'seconds': seconds
        };
    }

    function initializeClock(id, endtime){
        var clock = document.getElementById(id);
        function updateClock(){
            var t = getTimeRemaining(endtime);
            var minutesSpan = clock.querySelector('.minutes');
            var secondsSpan = clock.querySelector('.seconds');
            minutesSpan.innerHTML = t.minutes;
            secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);
            if(t.total<=0){
                alert("time is over !");
                window.location.href="challenge.php";
                resetValues();
            }else{

                localStorage.setItem("deadline", deadline);
            }
        }
        updateClock();
        var timeinterval = setInterval(updateClock,1000);
    }

    initializeClock('clockdiv', deadline);

     function getTime(){
        var t = Date.parse(deadline) - Date.parse(new Date());
        var seconds = Math.floor( (t/1000) % 60 );
        var minutes = Math.floor( (t/1000/60) % 60 );

        if (minutes < 10) {
            minutes = "0" + minutes;
        }
        if (seconds < 10) {
            seconds = "0" + seconds;
        }
        document.getElementById("time").value = "00:" + minutes + ":" + seconds ;
    }

    // keeping inputs values after refresh
    function saveValue(e){
        var id = e.id;
        var val = e.value;
        localStorage.setItem(id, val);  }

    function getSavedValue  (v){
        if (localStorage.getItem(v) == null) {
            return "";
        }
        return localStorage.getItem(v);
    }

    function resetValues(){

        document.getElementById("ideaText").value = "";

        localStorage.setItem('ideaText', '');

        deadline = 0 ;
        localStorage.setItem("deadline", deadline);


    }

    document.getElementById("ideaText").value = getSavedValue("ideaText");

</script>

</body>
</html>