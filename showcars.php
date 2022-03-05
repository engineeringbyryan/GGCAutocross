<?php
include('functions.php');
sqlconnect();
include('auth.php');
sqlconnect();

// rrich 1/27/2017: Get the number of points where "Gonzo" class starts.
$query = mysql_query("SELECT * FROM `autox_classes` WHERE class = 'Gonzo' LIMIT 1") or die("Error: " . mysql_error());
$gonzo = mysql_fetch_assoc($query);
$gonzostartpoints = $gonzo['start_points'];

if (($_GET['export'] == "Y") && ($usergroup == "admin")) {
	$file = "export/" . date("Ymd") . "-classificationExport.csv";
	$result = mysql_query("SELECT wp_users.user_login, wp_users.user_email, autox_numbers.drivernumber,
wp_users.display_name, CONCAT(
(select wp_usermeta.meta_value from wp_usermeta where wp_usermeta.user_id = wp_users.id and wp_usermeta.meta_key = 'first_name'),' ',(select wp_usermeta.meta_value from wp_usermeta where wp_usermeta.user_id = wp_users.id and wp_usermeta.meta_key = 'last_name')) full_name,
autox_classifications.car_year,
autox_classifications.car_model,
autox_classifications.points,
autox_classifications.class,
autox_classifications.pk
FROM autox_classifications,
wp_users,
autox_numbers
WHERE wp_users.user_login = autox_classifications.username
and wp_users.user_login = autox_numbers.username
and autox_classifications.active = 'Y'
ORDER BY autox_numbers.drivernumber") or die("Error: " . mysql_error());
	$tds = $_GET[tds];
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$capitalizedname = ucwords($row[3]);
		$lastname = trim(strstr($capitalizedname, " "));
		$firstname = trim(strstr($capitalizedname, " ", true)); // As of PHP 5.3.0
		$fullname = $row[4] = trim(str_replace(",", "", $row[4]));
		
		if (trim($fullname) == "") { 
			$fullname = $firstname . " " . $lastname;
		}
		
		$row[5] = trim(str_replace(",", "", $row[5]));
		$row[6] = trim(str_replace(",", "", $row[6]));
		$row[7] = trim(str_replace(",", "", $row[7]));						
		//if ($row[2] == "") { $car = explode(" ", $row[3]);} else { $car[0] = $row[2]; $car[1] = $row[3];}
		if ($firstname == "") {$firstname = $row[3];}
		if ($tds == "Y") {
			$export = $export . "$row[0],$row[1],$row[2],$firstname,$lastname,$fullname,$row[5],$row[6],$row[7],TDS / $row[8]\n";
		} else {
			if (($row[8] == "Gonzo") && ($row[7] < $gonzostartpoints)) { 
				$points = $gonzostartpoints; 
			} else { $points = $row[7]; }
			$export = $export . "$row[0],$row[1],$row[2],$firstname,$lastname,$fullname,$row[5],$row[6],$points,$row[8]\n";
		}
	}
	file_put_contents($file, $export);
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
}

if (($_GET['msrexport'] == "Y") && ($usergroup == "admin")) {
	$file = "export/" . date("Ymd") . "-DriverNumberExport.csv";
	$result = mysql_query("SELECT autox_numbers.drivernumber,
wp_users.display_name,
wp_users.user_email,
CASE
WHEN (SELECT autox_classifications.active FROM autox_classifications where wp_users.user_login = autox_classifications.username and autox_classifications.active = 'Y' LIMIT 1) IS NOT NULL THEN 'Yes' ELSE 'No' END AS active_classification
FROM
wp_users,
autox_numbers
WHERE wp_users.user_login = autox_numbers.username
ORDER BY autox_numbers.drivernumber") or die("Error: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$row[0] = trim(str_replace(",", "", $row[0]));
		$row[1] = trim(str_replace(",", "", $row[1]));
		$row[2] = trim(str_replace(",", "", $row[2]));
		$row[3] = trim(str_replace(",", "", $row[3]));					
		$export = $export . "$row[0],$row[1],$row[2],$row[3]\n";
	}
	file_put_contents($file, $export);
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="initial-scale=1.0"> 
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <link rel="apple-touch-icon" href="autoxicon.png" />
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/colorbox.css" rel="stylesheet" media="screen">
    <meta charset="UTF-8">
     <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <title>Show Cars</title>
    <link href="css/bootstrap-responsive.css" rel="stylesheet" media="screen">
        <style>
        body {
         	background-image: url('img/satinweave.png')  /*thanks SubtlePatterns.com */
        }
         @media (max-width: 979px) {
        	.navbar-fixed-top,
        	.navbar-fixed-bottom {
        		position: fixed;
       			margin-left: 0px;
        		margin-right: 0px;
      		}
      		.firstelement { padding-top: 60px; }
      	}
    </style>
</head>
<body>
<?php include('navbar.html');?>
<div class="container firstelement">
<?php
  $result = mysql_query("SELECT 
	autox_numbers.drivernumber,
	wp_users.display_name, 
	CONCAT((select wp_usermeta.meta_value from wp_usermeta where wp_usermeta.user_id = wp_users.id and wp_usermeta.meta_key = 'first_name'),' ',(select wp_usermeta.meta_value from wp_usermeta where wp_usermeta.user_id = wp_users.id and wp_usermeta.meta_key = 'last_name')) full_name,
	autox_classifications.car_year,
	autox_classifications.car_model,
	autox_classifications.points,
	autox_classifications.class,
	autox_classifications.pk
FROM 
	autox_classifications,
	wp_users,
	autox_numbers
WHERE 
	wp_users.user_login = autox_classifications.username
	and wp_users.user_login = autox_numbers.username
	and autox_classifications.active = 'Y'
ORDER BY 
	case 
	when autox_classifications.class = 'Gonzo' then 1
    when autox_classifications.class = 'AAA' then 2
    when autox_classifications.class = 'AA' then 3
    when autox_classifications.class = 'A' then 4
    when autox_classifications.class = 'B' then 5
    when autox_classifications.class = 'C' then 6
    when autox_classifications.class = 'N' then 7
    when autox_classifications.class = 'X' then 8
    else 10
    end asc, autox_classifications.points desc") or die("Error: " . mysql_error());

  	echo"<h4>All Classified Cars</h4>
	<table class='table table-condensed table-striped sortable' id='classifytable'>
	<thead>
	<tr><th>Number</th><th>Name</th><Th>Year</th><th>Model</th><th>Points</th><th>Class</th><th>Actions</th></tr>
	</thead><tbody>";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$capitalizedname = ucwords($row[2]);
		if (trim($capitalizedname) == "") { $capitalizedname = ucwords($row[1]); }
		// rrich 1/27/2017: If it is a "Gonzo" class car, but has less than the minimum gonzo class points, make the total points the minimum value.
		if ($row[6] == "Gonzo" && $row[5] < $gonzostartpoints) {
			$points = $gonzostartpoints;
		} else {
			$points = $row[5];
		}
		echo"<tr><td>$row[0]</td><Td>$capitalizedname</td><Td>$row[3]</td><td>$row[4]</td><Td>$points</td><Td>$row[6]</td><Td><a href='show.php?id=$row[7]&amp;popup=Y'  class='carinfoajax btn'>View Details</a></td></tr>";
	}
	echo"</table>";
  ?>
</div>  <!--container-->
<?php include('bottombar.html');?>
<script src="https://code.jquery.com/jquery-latest.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<script src="js/sorttable.js"></script>
<script>
$(document).ready(function() {
//  $('#button'+activebutton).hide();
		$(".carinfoajax").colorbox();
}); 
</script>
</body></html>