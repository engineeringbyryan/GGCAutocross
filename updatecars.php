<?php
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

if(IS_AJAX) {
    //Request identified as ajax request

include('functions.php');
$db = sqlconnect();
$username = mysqli_real_escape_string($db, $_GET[username]);
$id=mysqli_real_escape_string($db, $_GET[id]);
$action = mysqli_real_escape_string($db, $_GET[action]);
$closedate = mysqli_real_escape_string($db, $_GET[closedate]);
$opendate = mysqli_real_escape_string($db, $_GET[opendate]);
$msg = mysqli_real_escape_string($db, $_GET[msg]);
$locale = mysqli_real_escape_string($db, $_GET[locale]);


if ($action == "makeactive"){
	$result = mysqli_query($db, "UPDATE autox_classifications SET `active` = '' WHERE `username` = '$username' AND `active` = 'Y'");
	$result = mysqli_query($db, "UPDATE autox_classifications SET `active` = 'Y' WHERE `username` = '$username' AND `pk` = '$id'");
	writelog($username, "Set car id $id active");
}


if ($action == "delcar"){
	$result = mysqli_query($db, "UPDATE autox_classifications SET `active` = 'H' WHERE `username` = '$username' AND `pk` = '$id'");
	$result = mysqli_query($db, "SELECT * from autox_classifications WHERE `username` = '$username' AND `active` != 'H'");
	if (mysqli_num_rows($result) == "1"){
		$result = mysqli_query($db, "UPDATE autox_classifications SET `active` = 'Y' WHERE `username` = '$username' AND `active` != 'H'");
	}
	writelog($username, "Deleted car id $id");
}


if ($action == "unnumber"){
	$result = mysqli_query($db, "UPDATE autox_numbers SET `username` = '' WHERE `username` = '$username'");

	writelog($username, "Unassigned number - $id");
}


if ($action == "renumber"){
	$result = mysqli_query($db, "SELECT * from autox_numbers where `drivernumber` = '$id'");
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		$assigned = $row[1];	
	}	
	
	if ($assigned == ""){
		$result = mysqli_query($db, "UPDATE autox_numbers SET `username` = '' WHERE `username` = '$username'");
		$result = mysqli_query($db, "UPDATE autox_numbers SET `username` = '$username' WHERE `drivernumber` = '$id'");
	}

	writelog($username, "Chose new number - $id");
}


if ($action == "copycar"){
	$result = mysqli_query($db, "UPDATE autox_classifications SET `active` = '' WHERE `username` = '$username' AND `active` = 'Y'");
	$result = mysqli_query($db, "SELECT * from autox_classifications where `pk` = '$id'") or die("Error: " . mysqli_error());
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	
		//$row = mysqli_real_escape_string($row);
		
		$car = mysqli_real_escape_string($db, $row[6]);
		$mods = mysqli_real_escape_string($db, $row[7]);
		$engine = mysqli_real_escape_string($db, $row[8]);
		$bob = mysqli_query($db, "INSERT INTO autox_classifications VALUES('', '$username','$row[2]', '$row[3]','$row[4]','$row[5]','$car','$mods','$engine','$row[9]','$row[10]','Y','$row[12]')") or die("Error: " . mysqli_error());
	}
	writelog($username, "Copied classification - duplicated car id $id");
	
}

if ($action == "adddate"){
	$result = mysqli_query($db, "INSERT INTO autox_dates VALUES('', '$id', '$locale')") or die("Error: " . mysqli_error());
	writelog($username, "Admin added autox date $id @ $locale");
}


if ($action == "deldate"){
	$result = mysqli_query($db, "DELETE FROM autox_dates WHERE `pk` = '$id'") or die("Error: " . mysqli_error());
	writelog($username, "Admin deleted autox date with id $id");
}


if ($action == "updatetimes"){
	//echo"UPDATE autox_close SET `close` = '$closedate',`open` = '$opendate',`message` = '$msg' WHERE `pk` = '$id'";
	$result = mysqli_query($db, "UPDATE autox_close SET `close` = '$closedate',`open` = '$opendate',`message` = '$msg' WHERE `pk` = '$id'") or die("Error: " . mysqli_error());
}


if ($action == 'closesystem'){
	$result = mysqli_query($db, "SELECT * from autox_closeoverride") or die("Error: " . mysqli_error());
	if (mysqli_num_rows($result) > 0){
		$bob = mysqli_query($db, "TRUNCATE TABLE autox_closeoverride") or die("Error: " . mysqli_error());
	} else {
		$bob = mysqli_query($db, "INSERT INTO autox_closeoverride VALUES('close', '$id')") or die("Error: " . mysqli_error());
	}
	writelog($username, "Admin closed the system");
}



if ($action == 'opensystem'){
	$result = mysqli_query($db, "SELECT * from autox_closeoverride");
	if (mysqli_num_rows($result) > 0){
		$bob = mysqli_query($db, "TRUNCATE TABLE autox_closeoverride");
	} else {
		$bob = mysqli_query($db, "INSERT INTO `autox_closeoverride` VALUES('open', '')") or die("Error: " . mysqli_error());
	}
	writelog($username, "Admin opened the system");
}


} else {
	echo"direct access prohibited";
}

	

?>