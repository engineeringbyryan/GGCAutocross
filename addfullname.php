<?php
include('functions.php');
$db = sqlconnect();
$username = $_POST[username];
$fullname = $_POST[fullname];
$userid = $_POST[userid];
$query = mysqli_query($db,"UPDATE wp_users SET `display_name` = '$fullname' WHERE `user_login` = '$username' AND `ID` = '$userid'");
?>

