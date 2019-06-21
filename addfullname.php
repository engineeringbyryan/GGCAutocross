<?php
include('functions.php');
sqlconnect();
$username = $_POST[username];
$fullname = $_POST[fullname];
$userid = $_POST[userid];
$query = mysql_query("UPDATE wp_users SET `display_name` = '$fullname' WHERE `user_login` = '$username' AND `ID` = '$userid'");
?>

