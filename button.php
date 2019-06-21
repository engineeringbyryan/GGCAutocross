<?php
// Import the wordpress config file for the database information.
require_once('../new_html/wp-config.php');

// Function to get users IP.
function GetUserIp()
{
    $Client  = @$_SERVER['HTTP_CLIENT_IP'];
    $Forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $Remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($Client, FILTER_VALIDATE_IP))
    {
        $Ip = $Client;
    }
    elseif(filter_var($Forward, FILTER_VALIDATE_IP))
    {
        $Ip = $Forward;
    }
    else
    {
        $Ip = $Remote;
    }

    return $Ip;
}

// Create connection.
$Con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection.
if ($Con->connect_error) 
{
    die("DB connection failed");
} 

// Get users IP.
$UserIp = GetUserIp();

// Log users visit.
$QueryLogVisit = "INSERT INTO autox_reg_button_log (ip_address, date) VALUES ('$UserIp', now())";

if ($Con->query($QueryLogVisit) === FALSE) 
{
    echo "Log Error";
    echo "<br>";
}

// Get button clicks this week.
$QueryWeekCount = "SELECT COUNT(*) FROM autox_reg_button_log WHERE YEARWEEK(date) = YEARWEEK(NOW())";
$WeekCount = $Con->query($QueryWeekCount);

// Get button clicks for all time.
$QueryAllTimeCount = "SELECT COUNT(*) FROM autox_reg_button_log";
$AllTimeCount = $Con->query($QueryAllTimeCount);

// Build the website.
echo "<br>";
echo "<center>";
echo "<h2><font color='red'>" . "You didn't follow instructions." . "</font></h2>";
echo "<img src='img/meme1.jpg'>";
echo "<br>";
echo "<h3>" . "Please go back and review the registration page thoroughly." . "</h3>";
echo "<br>";
echo "<img src='img/meme2.jpg'>";
echo "<br>";
while($Result = mysqli_fetch_assoc($WeekCount)) 
{
    echo "<h3>" . "This Week: " . $Result["COUNT(*)"] . "</h3>";
}
while($Result = mysqli_fetch_assoc($AllTimeCount)) 
{
    echo "<h3>" . "All Time: " . $Result["COUNT(*)"] . "</h3>";
}
echo "<br>";
echo "</center>";

// Close the database connection.
$Con->close();
?>