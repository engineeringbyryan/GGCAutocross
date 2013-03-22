<?php
include('functions.php');
sqlconnect();
include('auth.php');
?>
<!DOCTYPE html>
<html>

<head>
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/colorbox.css" rel="stylesheet" media="screen">
    <link href="css/selectboxit.css" rel="stylesheet" media="screen">
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
        
        .bottombar {
	      
	      border-width:1px; border-style: solid; border-color: black; padding: 0px; height: 20px;margin-left: auto; margin-right: auto; text-align: center; background-color: #dcdcdc; color:#000000; font-size: 15px;
	   
      }


      /* Select Box */
		#copycar {
  		width: 800px;
  		cursor:pointer;
  		white-space:nowrap;
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
              <li class="active"><a href="/autocross">Autocross Home</a></li>
              <li><a href="classify.php">Car Classifier</a></li>
              <li><a href="showcars.php">Show all classified cars</a></li>  
              <?php if ($usergroup == "admin") { echo"<li><a href='admin.php'>Admin</a></li>";} ?>
            </ul>
            <?php if ($username) { echo"<ul class='nav pull-right'>  
              <li><a href='logout.php'>Logout $fullname</a></li>             
            </ul>"; } ?>
            ?>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

<div class="container">
	<div class="row-fluid">
		<div class="span10">
<?php


if (!$username){
loginform();
?>


<h3>Welcome to GGC Autocross!</h3>
<p>Our autocrosses are a competitive environment where a cone track is setup in a big parking lot (Candlestick Park) or airfield (Marina Airport). We put cars into classes based on a formula (considering weight, torque, modifications, and several other factors), and drivers compete against other cars within their class. Though there is an element of risk, hazards to participants and property are not expected to exceed those encountered in normal, legal highway driving. Due to the length of our courses we allow only one to two cars on the course at any one time (opposite sides). Cars race against the clock, not side-by-side.
</p>
<p>For more information, go <a href="http://www.ggcbmwcca.org/driving-events/autocross">here</a>.</p>

A typical signup process for a <u>first timer</u> is:
<ul>
<li>Login to this page (create an account if you have not done so)
<li>Classify your car & save it
<li>Pick a driver number
<li>Visit http://www.motorsportreg.com to sign up for & pay for an event
</ul>

<p>As long as you drive the same car for each event, you should only have to classify your car once per season.  Simply sign up @ motorsportreg.com for each event- registration typically opens up the Monday after the previous event.
</p>
<?php
} else {

$result = mysql_query("SELECT drivernumber FROM autox_numbers WHERE `username` = '$username' ORDER BY `drivernumber` ASC") or die("Error: " . mysql_error());
if (mysql_num_rows($result) == "0"){
	echo"<span class='badge badge-important'>You have not chosen a number.  You must choose a number to compete!</span>";
	
} else {
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$mynumber = $row[0];
	}

	echo"<h4>Your autocross number is $mynumber. Want a new number? "; 

}

echo"Choose a number here: ";

	echo"<select name='drivernumber' class='span1' id='numberform'><option value=''></option>";
	$result = mysql_query("SELECT drivernumber FROM autox_numbers WHERE `username` = '' ORDER BY `drivernumber` ASC") or die("Error: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo"<option value='$row[0]'>$row[0]</option>";
	}


echo"</select></h4>";





$result = mysql_query("SELECT * FROM autox_classifications WHERE `username` = '$username' AND `active` != 'H'") or die("Error: " . mysql_error());
if (mysql_num_rows($result) != "0") {
	echo"<h4>Your Classified Cars</h4>";
	if (mysql_num_rows($result) > 1) { echo"<h5>Click on a classification to make it active for the next autocross</h5>";}
	
	echo"
	<table class='table table-condensed' id='classifytable'>
	<thead>
	<tr><th>Car</th><th>Points</th><th>Class</th><th>Actions</th></tr>
	</thead><tbody>";



	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	if ($row[11] == "Y"){
		$rowclass = "success";
		$activebutton = $row[0];
	} else {
		$rowclass = "";
	}
	
		echo"<tr class='$rowclass' id='$row[0]'><td class='activecell'>$row[4] $row[5]</td><td class='activecell'>$row[3]</td><Td class='activecell'>$row[2]</td><Td><a href='show.php?id=$row[0]&popup=Y' class='btn carinfoajax'>View details</a> <a href='#' class='btn btn-danger delcar'>Delete</a></td></tr>";
	
	
	}
	
	echo"</tbody></table>";

}

if (!$activebutton){ echo"<span class='badge badge-important' id='classwarning'>Warning: You do not have an active car classification</span><br>"; }
	echo"<br><a href='classify.php' class='btn btn-info'>Classify your own car</a> ";
	echo" <a href='#' class='btn btn-info' id='otherpickerbutton'>Copy someone else's classification if you will be driving their car</a>";
	echo"<div id='otherpicker'>
	<br><Br>
	<select name='other' id='copycar'>
	<option value=''>Select a driver to copy</option>";
	$result = mysql_query("SELECT autox_numbers.drivernumber,gy01d_users.name,autox_classifications.car_year,autox_classifications.car_model,autox_classifications.points,autox_classifications.class,autox_classifications.pk FROM autox_classifications,gy01d_users,autox_numbers WHERE gy01d_users.username = autox_classifications.username and gy01d_users.username = autox_numbers.username and autox_classifications.active = 'Y' ORDER BY autox_numbers.drivernumber") or die("Error: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo"<option value='$row[6]'>#$row[0] - $row[1] - $row[2] $row[3] - $row[4] pts $row[5] class</option>";
	}
	echo"</select></div>";
}
?>

</div> <!--span10-->

<div class="span2">
		<div class="well sidebar-nav">
		<ul class="nav nav-list">
			<li class="nav-header">Upcoming autocrosses</li>
			
<?php

		$result = mysql_query("SELECT * FROM autox_dates ORDER BY `autoxdate` ASC") or die("Error: " . mysql_error());	
		$open = 0;
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			$open++;
			if (time() > strtotime("$row[2] 10:00AM")) {
				$thedate = date("D, M j", strtotime($row[1]));
				echo"<li><h6>$thedate<br>&nbsp;&nbsp;$row[2]";
				if ((!$closemsg) && ($open == 1)){
					echo"<br>&nbsp;&nbsp;&nbsp; <a href='http://ggcbmwcca.motorsportreg.com/' target='_blank'>Reg open</a>";
				}
				echo"</h6></li>";

			}
		}


?>			
		</ul>
		</div> <!-- well sidebar-nav-->

</div> <!-- span2 -->
</div> <!--row-fluid-->
</div>  <!--container-->


<div class="navbar navbar-fixed-bottom bottombar">GGC Autocross System (c)GGC BMW CCA and Kris Linquist</div>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<script src="js/selectboxit.js"></script>

<!--	<script type="text/javascript" src="http://www.fancyapps.com/fancybox/source/jquery.fancybox.js?v=2.1.4"></script>
	<link rel="stylesheet" type="text/css" href="http://www.fancyapps.com/fancybox/source/jquery.fancybox.css?v=2.1.4" media="screen" />-->

<script>
<?php if (!$activebutton) { $activebutton = "0";}?>
var activebutton = <?php echo $activebutton;?>;
$(document).ready(function() {
		$(".carinfoajax").colorbox();
		$("select").selectBoxIt({});
		$("#otherpicker").hide();
		
}); 

$("#numberform").change(function(event){
	var numma = $("#numberform option:selected").text();
	$.get('updatecars.php?action=renumber&username=<?php echo $username;?>' + '&id='+numma, function(data) {
		location.reload();
	});
});



$("#copycar").change(function(event){
	var numma = $("#copycar option:selected").val();
	$.get('updatecars.php?action=copycar&username=<?php echo $username;?>' + '&id='+numma, function(data) {
		location.reload();
	});
});



$('#otherpickerbutton').on('click', function(event) {
	$('#otherpicker').show();	
});


$('.activecell').on('click', function(event) {

	var trid = $(this).closest('tr').attr('id');
	//alert(trid);
	$.get('updatecars.php?action=makeactive&username=<?php echo $username;?>' + '&id='+trid, function(data) {});
    if ($(this).closest('tr').hasClass('success')) {
        $(this).closest('tr').removeClass('');
        $("#classwarning").hide();

    } else {
        $(this).closest('tr').addClass('success').siblings().removeClass('success'); 
        $("#classwarning").hide();
    }
});


$('.delcar').click(function(event){
	var trid = $(this).closest('tr').attr('id');	
	//alert ("del car where id = " + trid);
	$.get('updatecars.php?action=delcar&username=<?php echo $username;?>' + '&id='+trid, function(data) {
		location.reload();
		
	});

});
</script>

</body></html>