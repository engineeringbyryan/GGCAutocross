<?php
date_default_timezone_set("America/Los_Angeles"); 


$result = mysql_query("SELECT * FROM autox_close") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	$close = $row[1];
	$open = $row[2];
	$msg = $row[3];
}


$result = mysql_query("SELECT * FROM autox_dates") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	$autocross = strtotime($row[1]);
	if ((time() >= strtotime("$close", $autocross)) && (time() <= strtotime("$open", $autocross))) 	{
		$closemsg = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>$msg</div>";
	}
}


$result = mysql_query("SELECT * FROM autox_closeoverride") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	if (($closemsg) && ($row[0] == "open")) { unset($closemsg);}
	if ((!$closemsg) && ($row[0] == "close")) { 
		if ($row[1] == "") {  	
			$closemsg = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>The autocross system is currently closed.</div>"; 
		} else {
			$closemsg = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>" . $row[1] . "</div>";
		}
	}	
}



if ($_COOKIE[GGCAutoXAuthType] == "local"){
	$creds = explode(":", $_COOKIE[GGCAutoXCreds]);
	$result = mysql_query("SELECT name,username,password,id FROM gy01d_users WHERE `username` = '$creds[0]'") or die("Error: " . mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$breakpass = split(":", $row[2]);
		$id = $row[3];
		$fullname = $row[0];
	}
	if ($creds[1] ==  $breakpass[0]){
		if (!$closemsg) {$username = $creds[0];}
		$result = mysql_query("SELECT * FROM gy01d_user_usergroup_map WHERE `user_id` = '$id' AND `group_id` = '11'") or die("Error: " . mysql_error());
		if (mysql_num_rows($result) != "0") { $usergroup = "admin";}
	} else {
		unset($usergroup);
		unset($username);
		unset($fullname);
		$warning = "<div class='alert alert-error'>Incorrect login.  <a href='http://www.ggcbmwcca.org/component/comprofiler/lostpassword' target='_blank'>Lost username or password?</a></div>";
		writelog($creds[0], "Incorrect login attempt");
	}
		
}


















$loginname = $_POST[loginname];
$loginpassword = $_POST[loginpassword];
if ($loginname){
	$result = mysql_query("SELECT name,username,password,id FROM gy01d_users WHERE `username` = '$loginname'") or die("Error: " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$fullname = $row[0];
		$breakpass = explode(":", $row[2]);
		$id = $row[3];
	}
	
	if (md5($loginpassword . $breakpass[1]) == $breakpass[0]){
		setcookie("GGCAutoXAuthType", "local", time()+60*60*24*365);
		setcookie("GGCAutoXCreds", $loginname . ":" . md5($loginpassword . $breakpass[1]), time()+60*60*24*365);	
		if (!$closemsg) {$username = $loginname;}
		$result = mysql_query("SELECT * FROM gy01d_user_usergroup_map WHERE `user_id` = '$id' AND `group_id` = '11'") or die("Error: " . mysql_error());
		if (mysql_num_rows($result) != "0") { $usergroup = "admin";}
	} else {
		unset($username);
		unset($fullname);
		unset($usergroup);
		$warning = "<div class='alert alert-error'>Incorrect login.  <a href='http://www.ggcbmwcca.org/component/comprofiler/lostpassword' target='_blank'>Lost username or password?</a></div>";
		writelog($loginname, "Incorrect login attempt");
	}
} 

if (!$closemsg) { $displaymsg = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>In order to save a classification or choose a number, you must be logged in.<Br><Br>
<form class='form-inline' action='$_SERVER[PHP_SELF]' method='post'><input type='text' class='input-medium' placeholder='Login name' name='loginname'> <input type='password' class='input-medium' placeholder='Password' name='loginpassword'> <button type='submit' class='btn btn-primary'>Login</button> <a href='http://www.ggcbmwcca.org/component/comprofiler/registers' class='btn btn-link' target='_blank'>Create Account</a></form>
</div>"; } else { $displaymsg = $closemsg; }


function loginform() {
	
		global $warning;
		global $displaymsg;
		echo "$warning";
		echo "$displaymsg";
		

}

?>