<?php


function sqlconnect()
{
    $dbhost="db.bmwautocross.com";
    $dbuser="GGCAutocross";
    $dbpassword="FGT2M6q3[yh3vepd";
    $dbname="GGC_Autocross";
    $db = mysql_connect($dbhost,$dbuser,$dbpassword) or die("Couldn't connect to the database.");
    mysql_select_db($dbname) or die("Couldn't select the database");
}



function sendemail($emailto, $emailsubject, $emailbody) {
    $mailfromname="GGC Autocross";
    $contactemail="autocross@ggcbmwcca.org";
    global $password;
    $eol="\n";
    $mime_boundary=md5(time());
    $headers = 'From: ' . $mailfromname . ' <'. $contactemail . '>'.$eol . 'Reply-To: ' . $mailfromname . ' <'. $contactemail . '>'.$eol.'Return-Path: ' . $mailfromname . ' <'. $contactemail . '>'.$eol."X-Mailer: PHP v".phpversion().$eol.'MIME-Version: 1.0'.$eol;
    $msg .= $emailbody . $eol;
    mail($emailto, $emailsubject, $msg, $headers);
}



function generatepassword()
{
// *************************
// Random Password Generator
// *************************
    $totalChar = 32; // number of chars in the password
    $salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";  // salt to select chars from
    srand((double)microtime()*1000000); // start the random generator
    $password=""; // set the inital variable
    for ($i=0;$i<$totalChar;$i++)  // loop and create password
    $password = $password . substr ($salt, rand() % strlen($salt), 1);
    return $password;
}


function diedie($errmsg)

{
    echo"$errmsg</div></div>";
    include "/data/webroot/autox.ggcbmwcca.org/html/bottom.php";
}


function togoto ($loca) 
{
	$host = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	header("Location: http://$host$uri/$loca");
	exit;
}


function writelog($username, $logdata){
        sqlconnect();
#		$handle=fopen("/data/webroot/autox.ggcbmwcca.org/html/axlogs/axlog.txt", "a");
        $datenow = date("Y-m-d");
        $timenow = date("G:i:s");
        $result = mysql_query("INSERT INTO autox_logs VALUES ('$datenow', '$timenow', '$_SERVER[REMOTE_ADDR]', '$username', '$logdata')") or die("Error: " . mysql_error());
#        fwrite($handle, $now . "$_SERVER[REMOTE_ADDR] - " . $result . "\n");
#		fclose($handle);

}


function echoActiveClassIfRequestMatches($requestUri)
{
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    if ($current_file_name == $requestUri) 
        echo 'class="active"';
}



?>
