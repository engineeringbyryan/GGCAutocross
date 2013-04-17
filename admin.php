<?php
include('functions.php');
sqlconnect();
include('auth.php');
?>
<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="initial-scale=1.0"> 
    <script src="js/stay_standalone.js" type="text/javascript"></script>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <link rel="apple-touch-icon" href="autoxicon.png" />
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/bootstrap-responsive.css" rel="stylesheet" media="screen">
    <link href="css/colorbox.css" rel="stylesheet" media="screen">
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

<?php include('navbar.html'); ?>

<div class="container">
<?php 
if ($usergroup == "admin"){
	echo"<a href='showcars.php?export=Y' class='btn btn-primary'>Download autocross export</a><br>";
	echo"<h3>Autocross dates</h3>
	<Table class='table table-condensed table-striped'>
	<thead><th>Date</th><th>Location</th><th>Action</th></thead>
	<tbody>";
	$result = mysql_query("SELECT * FROM `autox_dates` ORDER BY `autoxdate` ASC") or die("Error: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo"<tr id='$row[0]'><Td>$row[1]</td><Td>$row[2]</td><td><a href='#' class='btn btn-error deldate'>Delete</a></td></tr>";
	}
	echo"<tr><td><input class='input-small'id='datetoadd' placeholder='YYYY-MM-DD'></td><td><input class='input-medium' id='localetoadd'></td><td><a href='#' class='btn adddate'>Add</a></td></tr>";
	echo"</table>";
	
	
	
	echo"<h3>When to close the classification system</h3>
	<h6>Relative to an autocross date - this uses php's strtotime function</h6>
	<Table class='table table-condensed'>
	<thead><th>Close date/time</th><th>Open date/time</th><th>Message</th><th>Actions</th></thead>
	<tbody>";	
	$result = mysql_query("SELECT * FROM `autox_close`") or die("Error: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo"<tr id='$row[0]'><Td><input id='closedate' class='input-large' value='$row[1]'></td><Td><input id='opendate' class='input-large' value='$row[2]'></td><Td><textarea id='msg' rows='3'>$row[3]</textarea></td><td><a href='#' class='btn updatetimes'>Update</a></td></tr>";
	}
	echo"</table>";
	
	if ($username){
		echo"<a href='#' class='btn btn-danger closesystem'>Manually Close System</a> <textarea id='closemessage' placeholder='closed message' rows='3'></textarea>";
	} else {
		echo"<a href='#' class='btn btn-success opensystem'>Manually Open System</a>";
	}
	
	echo"
	<Br><Br>

	Go to main page as someone else: <select id='alternateuser'>";

	$result = mysql_query("SELECT * FROM `gy01d_users` ORDER BY `name` ASC") or die("Error: " . mysql_error());
	   	   	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {	
		   	   	$escaped = str_replace("'", "", $row[1]);
	   	   		echo"<option value='$row[2]'>$escaped ($row[2])</option>";

	}
	
	echo"</select><br><Br><br>


	<script src='http://code.jquery.com/jquery-latest.js'></script>
<script src='js/bootstrap.min.js'></script>
<script src='js/bootstrap-typeahead.js'></script>
<script src='js/jquery.colorbox.js'></script>";
		


} else {
	
	echo"You're not an admin!";
}		
?>
<Br><Br>
</div>  <!--container-->

<script>


$('.deldate').click(function(event){
	var trid = $(this).closest('tr').attr('id');	
	//alert ("del car where id = " + trid);
	$.get('updatecars.php?action=deldate&id='+trid, function(data) {
		location.reload();
		
	});

});

$('.adddate').click(function(event){
	value = $("#datetoadd").val(); 
	locale = $("#localetoadd").val();
	$.get('updatecars.php?action=adddate&id='+value+'&username='+locale, function(data) {
		location.reload();
	});
});


$('.updatetimes').click(function(event){
	var trid = $(this).closest('tr').attr('id');	
	closedate = $("#closedate").val();  
	opendate = $("#opendate").val();
	msg = $("#msg").val();
	$.get('updatecars.php?action=updatetimes&id='+trid+'&closedate='+closedate+'&opendate='+opendate+'&msg='+msg, function(data) {
		location.reload();
	});
});


$('.closesystem').click(function(event){
	closemsg = $("#closemessage").val(); 
	$.get('updatecars.php?action=closesystem&id='+closemsg, function(data) {
		location.reload();
	});
});

$('.opensystem').click(function(event){
	closemsg = $("#closemessage").val(); 
	$.get('updatecars.php?action=opensystem', function(data) {
		location.reload();
	});
});


$('#alternateuser').change(function(event){
	var theuser = $(this).val();
	//alert(theuser);
	window.location = "index.php?user=" + theuser;
});



</script>
<Br><Br><br>
</body>
</html>