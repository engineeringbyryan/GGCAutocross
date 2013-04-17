<?php
session_start();
include('functions.php');
sqlconnect();
include('auth.php');


$carid = $_SESSION['car_id'];
$flywheelhp	= $_POST['flywheelhp'];
$chosenclass = $_POST['chosenclass'];
$hpclaim = mysql_real_escape_string($_POST['hpclaim']);
$nonbmw = $_GET[nonbmw];
$cardesc = $_POST[cardesc];

$alternateuser = $_POST['alternateuser'];
if ($alternateuser != ""){
	$pattern = '/\((.*)\)/';
	preg_match($pattern, $alternateuser, $matches, PREG_OFFSET_CAPTURE);
	$alternateuser = $matches[1][0];
}


if ($nonbmw == "Y") {
	$result = mysql_query("UPDATE autox_classifications SET `active` = '' WHERE `username` = '$username' AND `active` = 'Y'");
	$carmodel = strstr($cardesc, " ");
	$year = strstr($cardesc, " ", true); // As of PHP 5.3.0
	if ($alternateuser){
		$result = mysql_query("UPDATE autox_classifications SET `active` = '' WHERE `username` = '$alternateuser' AND `active` = 'Y'");
		$result = mysql_query("INSERT INTO autox_classifications VALUES ('', '$alternateuser', 'X', '', '$year', '$carmodel', '', '', '', '', now(), 'Y', '')") or die("Error: " . mysql_error());
	} else {
		$result = mysql_query("UPDATE autox_classifications SET `active` = '' WHERE `username` = '$username' AND `active` = 'Y'");
		$result = mysql_query("INSERT INTO autox_classifications VALUES ('', '$username', 'X', '', '$year', '$carmodel', '', '', '', '', now(), 'Y', '')") or die("Error: " . mysql_error());
	}
	togoto(); 

}





//echo "$username";

$result = mysql_query("SELECT * FROM `autox_cars` WHERE `car_id` = '$carid'") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $basepoints = $row[5];
}


if($_SESSION['opt_rear_wheel_width'] > 0){
	$packagepoints = ($_SESSION['opt_rear_wheel_width'] - $_SESSION['rear_wheel_width']) / .5; //add 1/2 point for every addt'l 1" wheel width
}
if($_SESSION['opt_front_wheel_width'] > 0){
	$packagepoints = $packagepoints + ($_SESSION['opt_front_wheel_width'] - $_SESSION['front_wheel_width']) / .5; //add 1/2 point for every addt'l 1" wheel width
}


$result = mysql_query("SELECT * FROM `autox_modifications`") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $checkformod = $_POST[mod_id][$row[0]];
    if ($checkformod == "true") {
        $modpoints = $modpoints + $row[4];
        
        //echo "mod $row[0] ($row[3]) is true, adding $row[4] points<br>";
    }
    
}

if ($_POST[mod_id][38] == "true") {
	$lsd = "Y";
    $modpoints = $modpoints + $_SESSION['lsd_points'];
    echo "Points added for LSD (not previously calculated) " . $_SESSION['lsd_points'] . "<br>";
} //limited slip



$result = mysql_query("SELECT * FROM `autox_mods_engine`") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $checkformod = $_POST[engine_id][$row[0]];
    if ($checkformod == "true") {
        $enginepercent = $enginepercent + $row[3];
        
        //echo "engine $row[0] ($row[2]) is true, adding $row[3] %<br>";
    }
    
}


if (!isset($lsd)) {
	$lsd = $_SESSION['LSD_standard'];
}

$result = mysql_query("SELECT * FROM `autox_engine_levels` WHERE `engine_level` = '$_SESSION[engine_level]' AND `lsd` = '$lsd'") or die("Error: " . mysql_error());

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
//	echo"testing to see if  $enginepercent is between $row[3] and $row[4]<br>";
	if ($enginepercent >= $row[3] && $enginepercent <= $row[4]) {
		$enginepoints = $row[2];
	}
    
}



if ($flywheelhp != ""){
	
	$enginepercent = round(((($flywheelhp /.85) / $_SESSION['BHP'])-1)*100,0);
	$result = mysql_query("SELECT * FROM `autox_engine_levels` WHERE `engine_level` = '$_SESSION[engine_level]' AND `lsd` = '$lsd'") or die("Error: " . mysql_error());

	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
//	echo"testing to see if  $enginepercent is between $row[3] and $row[4]<br>";
	if ($enginepercent >= $row[3] && $enginepercent <= $row[4]) {
		$enginepoints = $row[2];
	}
    
}
	
	
	
}



$totalpoints = $basepoints + $packagepoints + $modpoints + $enginepoints;


$serialized_car = serialize($_SESSION);
$serialized_mods = serialize($_POST[mod_id]);
$serialized_engine = serialize($_POST[engine_id]);
//$BHP = serialize($BHP);

echo "$serialized_car<br><br>$serialized_mods<br><br>$serialized_engine<br><br>$BHP";
$year = $_SESSION['year'];
$car = $_SESSION['car'];

if (!$_POST['class']) {
	$result = mysql_query("SELECT * FROM `autox_classes`") or die("Error: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	    if ($totalpoints >= $row[1] && $totalpoints <= $row[2]) { $class = $row[0]; } 
	    
	}
}

if ($chosenclass != "") { $class = $chosenclass; if ($chosenclass == "Gonzo"){ $totalpoints="80";}}

if ($alternateuser){
	echo"SAVING to $alternateuser";
	$result = mysql_query("UPDATE autox_classifications SET `active` = '' WHERE `username` = '$alternateuser' AND `active` = 'Y'");
	$result = mysql_query("INSERT INTO autox_classifications VALUES ('', '$alternateuser', '$class', '$totalpoints', '$year', '$car', '$serialized_car', '$serialized_mods', '$serialized_engine', '$flywheelhp', now(), 'Y', '$hpclaim')") or die("Error: " . mysql_error());
} else {
	$result = mysql_query("UPDATE autox_classifications SET `active` = '' WHERE `username` = '$username' AND `active` = 'Y'");
	$result = mysql_query("INSERT INTO autox_classifications VALUES ('', '$username', '$class', '$totalpoints', '$year', '$car', '$serialized_car', '$serialized_mods', '$serialized_engine', '$flywheelhp', now(), 'Y', '$hpclaim')") or die("Error: " . mysql_error());
	
}
togoto();  

?>