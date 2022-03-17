<?php


function sqlconnect()
{
    $dbhost="";
    $dbuser="";
    $dbpassword="";
    $dbname="";

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli($dbhost, $dbuser, $dbpassword, $dbname);

    if ($db -> connect_errno) {
        echo "Failed to connect to MySQL: " . $db -> connect_error;
        exit();
    }

    return $db;
}

function sqldisconnect($db)
{
    mysqli_close($db);
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
        $db = sqlconnect();
        $datenow = date("Y-m-d");
        $timenow = date("G:i:s");
        mysqli_query($db, "INSERT INTO autox_logs VALUES ('$datenow', '$timenow', '$_SERVER[REMOTE_ADDR]', '$username', '$logdata')") or die("Error: " . mysqli_error());
}


function echoActiveClassIfRequestMatches($requestUri)
{
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    if ($current_file_name == $requestUri) 
        echo 'class="active"';
}

function mysqli_field_name($result, $field_offset)
{
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}

?>
