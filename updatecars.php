<?php
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

if(IS_AJAX) {
    //Request identified as ajax request

include('functions.php');
sqlconnect();
$username = mysql_real_escape_string($_GET[username]);
$id=mysql_real_escape_string($_GET[id]);
$action = mysql_real_escape_string($_GET[action]);
$closedate = mysql_real_escape_string($_GET[closedate]);
$opendate = mysql_real_escape_string($_GET[opendate]);
$msg = mysql_real_escape_string($_GET[msg]);
$locale = mysql_real_escape_string($_GET[locale]);


if ($action == "makeactive"){
	$result = mysql_query("UPDATE autox_classifications SET `active` = '' WHERE `username` = '$username' AND `active` = 'Y'");
	$result = mysql_query("UPDATE autox_classifications SET `active` = 'Y' WHERE `username` = '$username' AND `pk` = '$id'");
	writelog($username, "Set car id $id active");
}


if ($action == "delcar"){
	$result = mysql_query("UPDATE autox_classifications SET `active` = 'H' WHERE `username` = '$username' AND `pk` = '$id'");
	$result = mysql_query("SELECT * from autox_classifications WHERE `username` = '$username' AND `active` != 'H'");
	if (mysql_num_rows($result) == "1"){
		$result = mysql_query("UPDATE autox_classifications SET `active` = 'Y' WHERE `username` = '$username' AND `active` != 'H'");
	}
	writelog($username, "Deleted car id $id");
}


if ($action == "renumber"){
	$result = mysql_query("SELECT * from autox_numbers where `drivernumber` = '$id'");
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$assigned = $row[1];	
	}	
	
	if ($assigned == ""){
		$result = mysql_query("UPDATE autox_numbers SET `username` = '' WHERE `username` = '$username'");
		$result = mysql_query("UPDATE autox_numbers SET `username` = '$username' WHERE `drivernumber` = '$id'");
	}

	writelog($username, "Chose new number - $id");
}


if ($action == "copycar"){
	$result = mysql_query("UPDATE autox_classifications SET `active` = '' WHERE `username` = '$username' AND `active` = 'Y'");
	$result = mysql_query("SELECT * from autox_classifications where `pk` = '$id'") or die("Error: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	
		//$row = mysql_real_escape_string($row);
		
		$car = mysql_real_escape_string($row[6]);
		$mods = mysql_real_escape_string($row[7]);
		$engine = mysql_real_escape_string($row[8]);
		$bob = mysql_query("INSERT INTO autox_classifications VALUES('', '$username','$row[2]', '$row[3]','$row[4]','$row[5]','$car','$mods','$engine','$row[9]','$row[10]','Y','$row[12]')") or die("Error: " . mysql_error());
	}
	writelog($username, "Copied classification - duplicated car id $id");
	
}

if ($action == "adddate"){
	$result = mysql_query("INSERT INTO autox_dates VALUES('', '$id', '$locale')") or die("Error: " . mysql_error());	
	writelog($username, "Admin added autox date $id @ $locale");
}


if ($action == "deldate"){
	$result = mysql_query("DELETE FROM autox_dates WHERE `pk` = '$id'") or die("Error: " . mysql_error());	
	writelog($username, "Admin deleted autox date with id $id");
}


if ($action == "updatetimes"){
	//echo"UPDATE autox_close SET `close` = '$closedate',`open` = '$opendate',`message` = '$msg' WHERE `pk` = '$id'";
	$result = mysql_query("UPDATE autox_close SET `close` = '$closedate',`open` = '$opendate',`message` = '$msg' WHERE `pk` = '$id'") or die("Error: " . mysql_error());	
}


if ($action == 'closesystem'){
	$result = mysql_query("SELECT * from autox_closeoverride") or die("Error: " . mysql_error());	
	if (mysql_num_rows($result) > 0){
		$bob = mysql_query("TRUNCATE TABLE autox_closeoverride") or die("Error: " . mysql_error());	
	} else {
		$bob = mysql_query("INSERT INTO autox_closeoverride VALUES('close', '$id')") or die("Error: " . mysql_error());	
	}
	writelog($username, "Admin closed the system");
}



if ($action == 'opensystem'){
	$result = mysql_query("SELECT * from autox_closeoverride");
	if (mysql_num_rows($result) > 0){
		$bob = mysql_query("TRUNCATE TABLE autox_closeoverride");
	} else {
		$bob = mysql_query("INSERT INTO `autox_closeoverride` VALUES('open', '')") or die("Error: " . mysql_error());	
	}
	writelog($username, "Admin opened the system");
}


} else {
	echo"direct access prohibited";
}

	

?>