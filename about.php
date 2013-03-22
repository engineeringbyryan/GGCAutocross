<?php
include('functions.php');
?>
<!DOCTYPE html>
<html>

<head>
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
        

      /* Select Box */
		#copycar {
  		width: 800px;
  		cursor:pointer;
  		white-space:nowrap;
  	}


    </style>
</head>

<body>
<?php include('navbar.html');?>
<div class="container">

<p>The GGC autocross system has been created as a method to classify & equalize BMW and Mini brand automobiles.  It uses a formula to calculate base points which takes into account horsepower, torque, gear ratios, differential ratios, and weight.  On top of the base points we add points for wheel widths, suspension stiffness, and owner-added modifications.</p>

<p>To equalize a time, use the following formula:  equalizedTime = ((60/59.901) ^points) * actualTime * 0.85</p>
<p>The system has been primarily developed by Matt Visser, Jason Sams, Jeff Roberts, and Kris Linquist.</p>

<p>The online system has been coded by Kris Linquist @klinquist (http://www.linquist.com) using php, javascript, and jquery.  It is available open-source: <a href="http://www.github.com/klinquist/GGCAutocross">http://www.github.com/klinquist/GGCAutocross</a></p>

<p>The system is revised periodically based on autocross results with known drivers.</p>


</div>  <!--container-->


<?php include ('bottombar.html'); ?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>
