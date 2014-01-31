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


define('WP_USE_THEMES', false);
require("../new_html/wp-load.php");


$loginname = $_POST[loginname];
$loginpassword = $_POST[loginpassword];

if ($loginname){
	$creds = array();
	$creds['user_login'] = $loginname;
	$creds['user_password'] = $loginpassword;
	$creds['remember'] = true;
	$user = wp_signon( $creds, false );
	$userID = $user->ID;
	wp_set_current_user( $userID, $user_login );
	wp_set_auth_cookie( $userID, true, false );
	do_action( 'wp_login', $user_login );


	if (is_user_logged_in()) {
			$username = $user->user_login;

			
			foreach ( $user->roles as $role ) {
				if ($role == "autox") { $usergroup = "admin";}
				if ($role == "administrator") { $usergroup = "admin"; }
			}

			if ( isset($user->groups[ $site_admin_group ] ) ) {
				$usergroup = "admin";
			}

			if ( isset($user->groups[ $autox_coord_group ] ) ) {
				$usergroup = "admin";
			}
			
			
/*
			if ($username == "klinquist") { $usergroup = "admin";}
			if ($username == "jeffroberts") { $usergroup = "admin";}
			if ($username == "TheCarousel") { $usergroup = "admin";}
			if ($username == "MattV") { $usergroup = "admin";}	
*/				

	} else {
			

			unset($username);
			unset($fullname);
			unset($usergroup);
			echo "<div class='alert alert-error'>Incorrect login.   You may be locked out for too many attempts. <a href='http://ggcbmwcca.org/new_html/wp-login.php' target='_blank'>Go here</a> to see a more detailed error or to reset your password.</div>";
			writelog($loginname, "Incorrect login attempt");
	} 

}


if (is_user_logged_in()) {
		global $current_user;
		global $username;
		get_currentuserinfo();
		$username = $current_user->user_login;

			foreach ( $current_user->roles as $role ) {
				if ($role == "autox") { $usergroup = "admin";}
				if ($role == "administrator") { $usergroup = "admin"; }
				
			}
	
}


function loginform() {
	if (!is_user_logged_in()) {
		global $closemsg;
		if (!$closemsg) { echo "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>In order to save a classification or choose a number, you must be logged in.<Br><Br>
		<form class='form-inline' action='$_SERVER[PHP_SELF]' method='post'><input type='text' class='input-medium' placeholder='Login name' name='loginname'> <input type='password' class='input-medium' placeholder='Password' name='loginpassword'> <button type='submit' class='btn btn-primary'>Login</button> <a href='http://ggcbmwcca.org/new_html/wp-login.php?action=register' class='btn btn-link' target='_blank'>Create Account</a></form>
		</div>"; } else { echo "$closemsg"; }
}

	

}

?>