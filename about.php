<?php
include('functions.php');
sqlconnect();
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
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
<?php include('navbar.html');?>
<div class="container">
<p>The GGC autocross system has been created as a method to classify & equalize BMW and Mini brand automobiles.  It uses a formula to calculate base points which takes into account horsepower, torque, gear ratios, differential ratios, and weight.  On top of the base points we add points for wheel widths, suspension stiffness, and owner-added modifications.</p>
<p>To equalize a time, use the following formula:  equalizedTime = ((60/59.901) ^points) * actualTime * 0.85</p>
<p>The system has been primarily developed by Matt Visser, Jason Sams, Jeff Roberts, Kris Linquist, and Ryan Rich.</p>
<p>The online system has been coded by Kris Linquist @klinquist (http://www.linquist.com) using php, javascript, and jquery.  It is available open-source: <a href="http://www.github.com/klinquist/GGCAutocross">http://www.github.com/klinquist/GGCAutocross</a></p>
<p>The system is revised periodically by the Autocross Team. As revisions are made, they will be noted in the list below.</p>
<table width="100%">
  <col width="15%">
  <col width="85%">
  <tr>
    <td><b>Date</b></td><td><b>Description</b></td> 
  </tr>
    <tr>
    <td>March 22nd, 2017</td><td>The E90 M3 base points have been adjusted from 55 points to 58 points.<br></td> 
  </tr>
  <tr>
    <td>March 22nd, 2017</td><td>The E92 M3 base points have been adjusted from 56 points to 59 points.</td> 
  </tr>
  <tr>
    <td>March 22nd, 2017</td><td>The E93 M3 base points have been adjusted from 52 points to 55 points.</td> 
  </tr>
  <tr>
    <td>March 22nd, 2017</td><td>The F80 M3 base points have been adjusted from 61 points to 63 points.</td> 
  </tr>
  <tr>
    <td>March 22nd, 2017</td><td>The F82 M4 base points have been adjusted from 60 points to 62 points.</td> 
  </tr>
  <tr>
    <td>March 22nd, 2017</td><td>The F83 M3 base points have been adjusted from 59 points to 61 points.</td> 
  </tr>
  <tr>
    <td>March 22nd, 2017</td><td>A car was added the to system: 2001-2006 M3 (Without Sunroof). This car has 55 base points.</td>
  </tr>
  <tr>
    <td>March 22nd, 2017</td><td>The description for camber modifications have been updated to include the removing of alignment pins to achieve more camber.</td>
  </tr>
  <tr>
    <td>March 22nd, 2017</td><td>Added the "W" class, where everyone is a winner!</td>
  </tr>
</table>
<BR>
<p><a href="https://bmwautocross.com/wp-content/uploads/2017/03/2017_GGC_AutocrossClassificationFAQ.pdf">Click here to view the full classification system documentation and FAQ.</a></p>

<p>The point to class table can be found below.</p>

<table class="table table-condensed">
<thead>

<th>
  <tr><td>Class</td><td>From</td><td>To</td><td>Adjusted</td><td>Adjustment formula</td></tr>
</th>
</thead>
<tbody>

<?php
$result = mysql_query("SELECT * FROM autox_classes") or die("Error: " . mysql_error());
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
     echo"<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td></tr>";
}
?>

</tbody>
</div>  <!--container-->
<?php include ('bottombar.html'); ?>
<script src="js/bootstrap.min.js"></script>
</html>
