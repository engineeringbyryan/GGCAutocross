<?php
include('functions.php');
sqlconnect();
include('auth.php');
if (($_GET['export'] == "Y") && ($usergroup == "admin")) {

	$file = "export/" . date("Ymd") . "-classificationExport.csv";
	//$file = "export/export.csv";
	
	  $result = mysql_query("SELECT autox_numbers.drivernumber,gy01d_users.name,autox_classifications.car_year,autox_classifications.car_model,autox_classifications.points,autox_classifications.class,autox_classifications.pk FROM autox_classifications,gy01d_users,autox_numbers WHERE gy01d_users.username = autox_classifications.username and gy01d_users.username = autox_numbers.username and autox_classifications.active = 'Y' ORDER BY autox_numbers.drivernumber") or die("Error: " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$capitalizedname = ucwords($row[1]);
		$export = $export . "\"$row[0]\", $capitalizedname, $row[2], $row[3], $row[4], $row[5]\n";
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
         	background-image: url('img/satinweave.png')  /*thanks SubtlePatterns.com */
        }
        
    </style>
</head>

<body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="/autocross">GGC BMW CCA Autocross</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="/autocross">Autocross Home</a></li>
              <li><a href="classify.php">Car Classifier</a></li>
              <li class="active"><a href="showcars.php">Show all classified cars</a></li>  
            </ul>
            <ul class="nav pull-right">  
              <li><a href="logout.php">Logout <?php echo "$fullname ";?></a></li>             
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

<div class="container">



<?php

  $result = mysql_query("SELECT autox_numbers.drivernumber,gy01d_users.name,autox_classifications.car_year,autox_classifications.car_model,autox_classifications.points,autox_classifications.class,autox_classifications.pk FROM autox_classifications,gy01d_users,autox_numbers WHERE gy01d_users.username = autox_classifications.username and gy01d_users.username = autox_numbers.username and autox_classifications.active = 'Y' ORDER BY autox_classifications.points desc, autox_classifications.class, gy01d_users.name") or die("Error: " . mysql_error());
  
  
  	echo"<h4>All Classified Cars</h4>
	
	<table class='table table-condensed table-striped sortable' id='classifytable'>
	<thead>
	<tr><th>Number</th><th>Name</th><Th>Year</th><th>Model</th><th>Points</th><th>Class</th><th>Actions</th></tr>
	</thead><tbody>";
	
	
  
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	
		$capitalizedname = ucwords($row[1]);
		
		echo"<tr><td>$row[0]</td><Td>$capitalizedname</td><Td>$row[2]</td><td>$row[3]</td><Td>$row[4]</td><Td>$row[5]</td><Td><a href='show.php?id=$row[6]&amp;popup=Y'  class='carinfoajax btn'>View Details</a></td></tr>";
  
	}
  
	echo"</table>";
  ?>

</div>  <!--container-->
<?php include('bottombar.html');?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
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