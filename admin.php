<?php
include('functions.php');
$db = sqlconnect();
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
	echo"<a href='showcars.php?export=Y' class='btn btn-primary'>Download autocross export</a> <a href='showcars.php?export=Y&tds=Y' class='btn btn-primary'>Download autocross export for TDS</a> <a href='showcars.php?msrexport=Y' class='btn btn-primary'>Download driver number export</a><br>";
	echo"<h3>Autocross dates</h3>
	<Table class='table table-condensed table-striped'>
	<thead><th>Date</th><th>Location</th><th>Action</th></thead>
	<tbody>";
	$result = mysqli_query($db, "SELECT * FROM `autox_dates` ORDER BY `autoxdate` ASC") or die("Error: " . mysqli_error());
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		echo"<tr id='$row[0]'><Td>$row[1]</td><Td>$row[2]</td><td><a href='#' class='btn btn-error deldate'>Delete</a></td></tr>";
	}
	echo"<tr><td><input class='input-small'id='datetoadd' placeholder='YYYY-MM-DD'></td><td><input class='input-medium' id='localetoadd'></td><td><a href='#' class='btn adddate'>Add</a></td></tr>";
	echo"</table>";
	
	
	echo"<h3>When to close the classification system</h3>
	<h6>Relative to an autocross date - this uses php's strtotime function</h6>
	<Table class='table table-condensed'>
	<thead><th>Close date/time</th><th>Open date/time</th><th>Message</th><th>Actions</th></thead>
	<tbody>";	
	$result = mysqli_query($db, "SELECT * FROM `autox_close`") or die("Error: " . mysqli_error());
	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		echo"<tr id='$row[0]'><Td><input id='closedate' class='input-large' value='$row[1]'></td><Td><input id='opendate' class='input-large' value='$row[2]'></td><Td><textarea id='msg' rows='3'>$row[3]</textarea></td><td><a href='#' class='btn updatetimes'>Update</a></td></tr>";
	}
	echo"</table>";
	

	$result = mysqli_query($db, "SELECT * FROM autox_closeoverride") or die("Error: " . mysqli_error());
	$close_override = mysqli_fetch_row($result)[0];

	if ($closemsg && $close_override == ""){
		echo "<a href='#' class='btn btn-success opensystem'>Disable Automatic System Close</a><br><br>";
		echo "<a href='#' class='btn btn-danger closesystem'>Manually Close System</a> <textarea id='closemessage' placeholder='Automatic system re-open will be disabled. Please enter your close message here.)' rows='3'></textarea>";
	} 
	elseif ($closemsg && $close_override == "close") {
		echo "<a href='#' class='btn btn-success opensystem'>Remove Manual Close and Resume Automatic System Close</a>";

	}
	elseif ($close_override == "open") {
		echo"<a href='#' class='btn btn-danger closesystem'>Resume Automatic System Close</a>";

	}
	else {
		echo"<a href='#' class='btn btn-danger closesystem'>Manually Close System</a> <textarea id='closemessage' placeholder='closed message' rows='3'></textarea>";
	}
	
	echo"
	<Br><Br><br><br>

	Go to main page as someone else: <select id='alternateuser'>";

	$result = mysqli_query($db, "SELECT * FROM `wp_users` ORDER BY `display_name` ASC") or die("Error: " . mysqli_error());
	   	   	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		   	   	$escaped = str_replace("'", "", $row[9]);
	   	   		echo"<option value='$row[1]'>$escaped ($row[1])</option>";

	}
	
	echo"</select><br><br><br>";

	$avalible_numbers = "<option value='0'></option>";
	$result = mysqli_query($db, "SELECT drivernumber FROM `autox_numbers` WHERE username = '' ORDER BY drivernumber ASC") or die("Error: " . mysqli_error());
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			$avalible_numbers .= "<option value='$row[0]'>$row[0]</option>";
		}
	echo"<h3>Assigned Driver Numbers</h3>
	<Table class='table table-condensed table-striped'>
	<thead><th>Name</th><th>Current Number</th><th>New Number</th><th>Unassign Number</th></thead>
	<tbody>";


	$result = mysqli_query($db, "SELECT u.user_login, u.display_name, an.drivernumber FROM `autox_numbers` an JOIN `wp_users` u ON an.username = u.user_login ORDER BY an.drivernumber ASC") or die("Error: " . mysqli_error());
	   	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			echo"<tr id='$row[0]'><Td>$row[1]</td><Td>$row[2]</td><td><select class='updatenumber'>$avalible_numbers</select></td><td><a href='#' class='btn unassignnumber'>Unassign</a></td></tr>";
	}

	echo"</table>";

	echo"<script src='https://code.jquery.com/jquery-latest.js'></script>
<script src='js/bootstrap.min.js'></script>
<script src='js/bootstrap-typeahead.js'></script>
<script src='js/jquery.colorbox.js'></script>";
?>
<script>

var username = "<?php echo $creds[0];?>";

$('.updatenumber').change(function(event){
	var newnumber = $(this).val();
	var driver = $(this).closest('tr').attr('id');
	//alert ("del car where id = " + trid);
	$.get('updatecars.php?action=renumber&id='+newnumber+'&username='+driver, function(data) {
		location.reload();
	});
});

$('.unassignnumber').click(function(event){
	var driver = $(this).closest('tr').attr('id');	
	//alert ("del car where id = " + trid);
	$.get('updatecars.php?action=unnumber&username='+driver, function(data) {
		location.reload();
	});

});

$('.deldate').click(function(event){
	var trid = $(this).closest('tr').attr('id');	
	//alert ("del car where id = " + trid);
	$.get('updatecars.php?action=deldate&id='+trid+'&username='+username, function(data) {
		location.reload();
	});

});

$('.adddate').click(function(event){
	value = $("#datetoadd").val(); 
	locale = $("#localetoadd").val();
	$.get('updatecars.php?action=adddate&id='+value+'&locale='+locale+'&username='+username, function(data) {
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
	$.get('updatecars.php?action=closesystem&id='+closemsg+'&username='+username, function(data) {
		location.reload();
	});
});

$('.opensystem').click(function(event){
	closemsg = $("#closemessage").val(); 
	$.get('updatecars.php?action=opensystem&username='+username, function(data) {
		location.reload();
	});
});


$('#alternateuser').change(function(event){
	var theuser = $(this).val();
	//alert(theuser);
	window.location = "index.php?user=" + theuser;
});


</script>
<?php		


} else {
	
	echo"You're not an admin!";
}		
?>
<Br><Br>
</div>  <!--container-->


<Br><Br><br>
</body>
</html>