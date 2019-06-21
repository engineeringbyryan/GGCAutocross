<?php
// Import the wordpress config file for the database information.
require_once('../new_html/wp-config.php');

// Create connection.
$Con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection.
if ($Con->connect_error) 
{
    die("DB connection failed");
} 

// Get button clicks log results joined with logins from.
$QueryButtonLog = "SELECT DISTINCT rbl.pk AS Visitor_ID, rbl.date AS Date, rbl.ip_address AS IP_Address, al.user AS Username FROM autox_reg_button_log AS rbl LEFT OUTER JOIN autox_logs AS al ON rbl.ip_address = al.ip ORDER BY rbl.pk DESC";
$ButtonLog = $Con->query($QueryButtonLog);

// Build the website.
echo "<br>";
echo "<center>";
echo "<h2><font color='red'>" . "Those who didn't follow instructions." . "</font></h2>";
echo "<h3>" . "This log uses the classification system's IP address log to identify who clicked the button." . "</h3>";
echo "<h3>" . "If visitor ID has multiple log entries, then multiple users have logged into the classification<br>system from that IP address. The table will reflect everyone who has logged in<br>from that IP address." . "</h3>";
echo "<br>";
if ($ButtonLog->num_rows > 0) 
{
    echo "<table border='1'><tr><th>Visitor ID</th><th>Date</th><th>IP Address</th><th>Username</th></tr>";
    while($Row = $ButtonLog->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $Row["Visitor_ID"] . "</td>";
        echo "<td>" . $Row["Date"] . "</td>";
        echo "<td>" . $Row["IP_Address"] . "</td>";
        echo "<td>" . $Row["Username"] . "</td></tr>";
    }
    echo "</table>";
} 
else 
{
    echo "No log entries found.";
}

// Disconnect.
$Con->close();
?>