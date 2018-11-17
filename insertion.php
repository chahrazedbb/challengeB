<?php
require 'conn.php';


$idm = $_POST['idm'];
$ideaText = $_POST['ideaText'];
$time = $_POST["time"];
$session = $_POST["session"];
$sql = "INSERT INTO idea (ideaText,member_id,time,session)
    VALUES ('$ideaText','$idm','$time','$session')";

$result = $conn->exec($sql);

if($result){
    header('location:challenge.php');
}

