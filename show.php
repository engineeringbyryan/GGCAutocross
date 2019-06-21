<?php
include('functions.php');
sqlconnect();

$popup = $_GET[popup];

if ($popup != 'Y'){
	
?>
<!DOCTYPE html>
<html>
<head>
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/colorbox.css" rel="stylesheet" media="screen">
    <meta charset="UTF-8">
     <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <title>GGC BMW CCA Autocross page</title>
    <link href="css/bootstrap-responsive.css" rel="stylesheet" media="screen">
        <style>
        body {
         	background-image: url(img/satinweave.png)  /*thanks SubtlePatterns.com */
        }
    </style>
</head>

<body>

<?php include('navbar.html');?>

<div class="container">

<?php
	
	
}


$id = $_GET[id];

$result = mysql_query("SELECT * FROM autox_classifications WHERE `pk` = '$id'") or die("Error: " . mysql_error());


while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

	$car_id = unserialize($row[6]);
	$mod_id = unserialize($row[7]);
	$engine_id = unserialize($row[8]);
	$flywheelhp = $row[9];
	$hpclaim = $row[12];
	$class = $row[2];
}




//$flywheelhp	= $_POST['flywheelhp'];

echo"<h3>$car_id[year] $car_id[car]</h3>";


echo"<table class='table table-condensed table-striped'>";
    $basepoints = $car_id[points];
echo "<Tr><Td>Base points</td><td>$basepoints</td></tr>";


if($car_id['opt_rear_wheel_width'] > 0){
	$packagepoints = ($car_id['opt_rear_wheel_width'] - $car_id['rear_wheel_width']) / .5; //add 1/2 point for every addt'l 1" wheel width
}
if($car_id['opt_front_wheel_width'] > 0){
	$packagepoints = $packagepoints + ($car_id['opt_front_wheel_width'] - $car_id['front_wheel_width']) / .5; //add 1/2 point for every addt'l 1" wheel width
}



if ($packagepoints == "") {$packagepoints = "0";}
echo "<Tr><Td>package wheel/tire points</td><td> $packagepoints</td></tr>";


$result = mysql_query("SELECT * FROM `autox_modifications`") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $checkformod = $mod_id[$row[0]];
    if ($checkformod == "true") {
        $modpoints = $modpoints + $row[4];
        
        echo "<tr><Td>$row[3]</td><Td>$row[4]</td></tr>";
    }
    
}

if ($mod_id[38] == "true") {
	$lsd = "Y";
    $modpoints = $modpoints + $car_id['lsd_points'];
    echo "<Td>Points added for LSD (not previously calculated)</td><td>" . $car_id['lsd_points'] . "</td></tr>";
} //limited slip



$result = mysql_query("SELECT * FROM `autox_mods_engine`") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $checkformod = $engine_id[$row[0]];
    if ($checkformod == "true") {
        $enginepercent = $enginepercent + $row[3];
        
        echo "<tr><Td>$row[2]</td><Td>(+$row[3] %)</td></tr>";
    }
    
}


if (!isset($lsd)) {
	$lsd = $car_id['LSD_standard'];
}

$result = mysql_query("SELECT * FROM `autox_engine_levels` WHERE `engine_level` = '$car_id[engine_level]' AND `lsd` = '$lsd'") or die("Error: " . mysql_error());

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

	if ($enginepercent >= $row[3] && $enginepercent <= $row[4]) {
		$enginepoints = $row[2];
	}
    
}



if ($flywheelhp != ""){
	echo"<tr><td>Declared rear wheel hp</td><Td>$flywheelhp hp</td></tr>";
	$enginepercent = round(((($flywheelhp / .85) / $car_id['BHP'])-1)*100,0);
	$result = mysql_query("SELECT * FROM `autox_engine_levels` WHERE `engine_level` = '$car_id[engine_level]' AND `lsd` = '$lsd'") or die("Error: " . mysql_error());

	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	if ($enginepercent >= $row[3] && $enginepercent <= $row[4]) {
		$enginepoints = $row[2];
	}
    
}
	
	
	
}


if ($modpoints == "") {$modpoints = "0";}
if ($enginepoints == "") {$enginepoints = "0";}

$totalpoints = $basepoints + $packagepoints + $modpoints + $enginepoints;


if ($enginepercent) {	
	if (isset($_GET['showsource'])) { echo"<tr><td>Horsepower claim source</td><Td>$hpclaim</td></tr>"; }
	echo "<tr><Td>Total engine percent increase</td><Td>(+$enginepercent %)</td></tr>";
	echo "<Tr><Td>Total points from engine</td><Td>$enginepoints</td></tr>";
}


// rrich 1/27/2017: Get the number of points where "Gonzo" class starts.
$query = mysql_query("SELECT * FROM `autox_classes` WHERE class = 'Gonzo' LIMIT 1") or die("Error: " . mysql_error());
$gonzo = mysql_fetch_assoc($query);
$gonzostartpoints = $gonzo['start_points'];

// rrich 1/27/2017: If it is a "Gonzo" class car, but has less than the minimum gonzo class points, define how many points were added to make it a "Gonzo" class car and make the total points the minimum value.
if ($class == "Gonzo" && $totalpoints < $gonzostartpoints) {
	$adjustmentpoints = $gonzostartpoints - $totalpoints;
	echo "<Tr><Td>Gonzo Adjustment</td><td>$adjustmentpoints</td></tr>";
	echo "<tr><Td><h3>Total</h3></td><td><h3>$gonzostartpoints</h3></td></tr></table>";
} else if ($class == "W"){ 
	$adjustmentpoints = 999 - $totalpoints;
	echo "<Tr><Td>Winner Adjustment</td><td>$adjustmentpoints</td></tr>";
	echo "<tr><Td><h3>Total</h3></td><td><h3>999</h3></td></tr></table>";
} else {
	echo "<tr><Td><h3>Total</h3></td><td><h3>$totalpoints</h3></td></tr></table>";
}


if ($popup != 'Y'){
	
?>

</div>  <!--container-->
<?php include('bottombar.html');?>
<script src="https://code.jquery.com/jquery-latest.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.colorbox.js"></script>

<?php

} else {
	
	echo"<a href='show.php?id=" . $id . "' target='_blank'>Link to send to others</a>";
	
}


?>