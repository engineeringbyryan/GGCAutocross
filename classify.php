<?php
session_start();
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
    <meta charset="UTF-8">
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/bootstrap-responsive.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="css/add2home.css">
    <script type="application/javascript" src="js/add2home.js"></script>
    
     <style>
        table { cursor:pointer; }
        .table tbody tr.selected td{
            background-image: url('img/check.png');
            background-repeat:no-repeat;
            border: 1px solid #007c1e; 
            background-color: #d0e9c6;
        }
        body {
            background-image: url('img/satinweave.png');  /*thanks SubtlePatterns.com */
            padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
        }
      .bottombar {
	      border-width:1px; border-style: solid; border-color: black; padding: 5px; height: 25px;margin-left: auto; margin-right: auto; text-align: center; background-color: #d9edf7; color:#3a87ad; font-size: 19px;
      }
      .tablehead {
        background-color: #cccccc;
      }
      @media (max-width: 979px) {
        .navbar-fixed-top,
        .navbar-fixed-bottom {
            position: fixed;
            margin-left: 0px;
            margin-right: 0px;
      }

        }
    </style>
    <title>Classify</title>
    
    
    <link href="css/selectboxit.css" rel="stylesheet" media="screen">
</head>
<body>
<?php include('navbar.html'); ?>
<div class="container">
    <div id="floatDiv"></div>
<?php
if (!$username or ($closemsg and $usergroup <> "admin")){
    if($username) {
        echo "$closemsg";
    }
    loginform();
}
$year    = $_POST[year];
$carid   = $_POST[carid];
$wheelid = $_POST[wheelid];
$suspvalue = 0;
if ($carid == "") {
	$_SESSION = array();
    echo "
    <span class='label label-warning'>Note: If your vehicle is missing from the classification system, please contact the autocross team at 'autocross@ggcbmwcca.org'.</span>
    <br><br>
    <form id='step1' action='$_SERVER[PHP_SELF]' method='post' name='year'>
    <p>Have a BMW or Mini?</p>
    <div class='input-append'>
     <SELECT name='year' onchange='this.form.submit()'>
    <option value=''>Select Year</option>";
    if ($year != "") {
        echo "<option selected='$year'>$year</option>";
    } //if year has already been slected, make it the default value
    $currentyear = date("Y");
    $currentyear++; //always show current year + 1 to account for the next MY
    for ($i = $currentyear; $i >= 1960; $i--) {
        echo "<option value='$i'>$i</option>";
    }
    echo "</SELECT></div></form>";
}
if (($carid == "") && ($year == "") && ($username != "" && ($closemsg == "" || $usergroup == "admin"))){
	    echo "<p>Don't have a BMW or Mini?  Type in the year, make, and model of your car:</p><form id='step1' action='calc.php?nonbmw=Y' method='post'> <input name='cardesc' class='input-large' id='xclassinput' placeholder='2000 Mazda Miata'>";
        if ($usergroup == "admin"){
            echo"<script>
            var peoplelist = [";
            $result = mysqli_query($db, "SELECT * FROM `wp_users`") or die("Error: " . mysqli_error());
            while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                $escaped = str_replace("'", "", $row[9]);
                echo"'$escaped ($row[1])',";
            }
            echo"'Fake User (fakeuser)'];
            </script>";
            echo"<div class='nonbmwsubmit'>Admin: Save this classification in someone else's profile: <input type='text' class='input-large' id='users' name='alternateuser'  placeholder='Start typing a name...'></div>";
        }
        echo"<button type='submit' class='btn btn-primary nonbmwsubmit' style='display:inline;'>Submit</button></form><div id='nonbmwhelper' class='badge badge-important'></div>";

}
if ($year != "" && $carid == "") {
    $db = sqlconnect();
    echo "<form id='step2' action='$_SERVER[PHP_SELF]' method='post' name='model'><input type='hidden' name='year' value='$year'>
    <div class='input-append'>
    <SELECT name='carid' class='span5' onchange='this.form.submit()'><option value=''>Select your car</option>";
    $result = mysqli_query($db, "SELECT * FROM `autox_cars` WHERE `year_start` <=$year AND `year_end` >=$year ORDER BY `autox_cars`.`car` ASC") or die("Error: " . mysqli_error());
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    mysqli_free_result($result);
    echo "</SELECT></div></form>";
}

// NOTE: We are disabling the portion of the system that determines vehicle package. With BMWs most recent line up, there are too many variants to make this sustainable. If we need to add this back, please look at the previous commit history. 

if ($year != "" && $carid != "") {
    $readytoclassify="Y";
    $db = sqlconnect();
    $result = mysqli_query($db, "SELECT * FROM `autox_cars` WHERE `car_id` = '$carid'") or die("Error: " . mysqli_error());
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $_SESSION['year'] = $year; //Push the year into a session cookie
        foreach ($row as $key => $value) { //Push the entire car information row into a session cookie
            $_SESSION[mysqli_field_name($result, $key)] = $value;
        }
    }
    
    $_SESSION['opt_package_desc']	   = "";
    $_SESSION['opt_package_name']      = "";
    $_SESSION['opt_rear_wheel_width']  = ""; //destroy the cookie... otherwise if you go back, you'll have the wrong # of points!            
    $_SESSION['opt_front_wheel_width'] = ""; //destroy the cookie... otherwise if you go back, you'll have the wrong # of points!

    echo"<span class='label label-warning'>Note: Please review all classification descriptions as we have made changes to the classification system for the 2022 season.</span>";
    echo"<br>";
    echo "<h3>" . $year . " " . $_SESSION['car'] . "</h3>";
	echo "<h5>" . $_SESSION['points'] . " base points</h5>";
    echo"<span class='label label-info'>Click on a modification to select/unselect</span><form id='options' action='calc.php' method='post'>";
    echo"<br>"; //rrich 3/19/2016
    echo"<span class='label label-info'>If you have a question about a particular modification or if your car has a modification<br> that this system does not handle (Ex: Increasing wheel sizes more than 3\"),<br> please contact us at autocross@ggcbmwcca.org so we can assist you in getting your car correctly classified.</span>"; //rrich 3/19/2016
    $result = mysqli_query($db, "SELECT * FROM `autox_mod_categories` ORDER BY `mandatory_selection` DESC, `category_id`") or die("Error: " . mysqli_error());
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "<h4>$row[5]";
        if ($row[2] == "Y") {
            echo "    <span class='badge badge-important'>Selection required</span>";
        }
        echo "</h4><h5>$row[6]</h5>";
        if ($row[1] == "Front Wheels"){
	        if ($_SESSION['opt_front_wheel_width']) { echo"<h5>We think your front wheels are ". $_SESSION['opt_front_wheel_width'] . " inches wide. Calculate increased width from this value.</h5>"; } else { echo"<h5>We think your front wheels are ". $_SESSION['front_wheel_width'] . " inches wide. Calculate increased width from this value.</h5>"; }
        }
		
		//if ($row[1] == "Weight Reduction"){
			//echo "<h5>We think your vehicles curb weight is " . $_SESSION['weight'] . " pounds.</h5>"; //rrich 3/20/2016
		//}


        if ($row[1] == "Rear Wheels"){
	        if ($_SESSION['opt_rear_wheel_width']) { echo"<h5>We think your rear wheels are ". $_SESSION['opt_rear_wheel_width'] . " inches wide. Calculate increased width from this value.</h5>"; } else { echo"<h5>We think your rear wheels are ". $_SESSION['rear_wheel_width'] . " inches wide. Calculate increased width from this value.</h5>"; }
        }
        $showlsd = "";
        if ($row[1] == "LSD"){
         
            if ($_SESSION['LSD_standard'] == "N") { 
                    echo "<h5>We don't think your car came from the factory with a limited slip differential.  If we are incorrect OR if you added an LSD, select this option.</h5>"; 
                    
            } else { 
                    echo "<h5>We think your car came with a limited slip differential.  These points are included in your base point calculation.</h5>"; 
                    $showlsd = "N";
                    
            }
        }
        

        if ($showlsd != "N") {
    
        $query = "SELECT * FROM `autox_modifications` WHERE `category_id` = '$row[0]'";
        $anotherresult = mysqli_query($db, $query) or die("Error: " . mysqli_error());
        if ($row[7] == "Y") { //allow a multiple selections on certain categories
            echo "<table class='table table-condensed table-bordered modstablemulti'>";
        } else {
            echo "<table class='table table-condensed table-bordered modstable'>";
        }
        echo "<thead><tr class='tablehead'><th style='padding-left:30px;'>Name</th><th>Point value</th></tr></thead><tbody>";
        while ($anotherrow = mysqli_fetch_array($anotherresult, MYSQLI_NUM)) {
            if ($anotherrow[0] == "38") {
                $modpoints = $_SESSION['lsd_points'];
            } else {
                $modpoints = $anotherrow[4];
            } //for limited slip
            $default = $anotherrow[1];
            if  ($_SESSION['suspension_code'] == $anotherrow[5]) {
	            $default = "Y"; 
	            if ($anotherrow[5] != "B") {
			        $addtlinfo = "(<i>Auto selected due to car</i>)";
			    }
	            $suspvalue = $suspvalue + $anotherrow[4];
            }
            if ($default == "Y") { //Check to see if this needs to be default
                echo "<tr class='selected'><Td class='span6' style='padding-left:30px;'>$anotherrow[3] $addtlinfo</td><td class='span2 pointvalue' style='padding-left:30px;'>$modpoints</td><input type='hidden' name='mod_id[$anotherrow[0]]' value='true'></tr>";
            } else {
                echo "<tr><Td class='span6' style='padding-left:30px;'>$anotherrow[3]</td><td class='span2 pointvalue' style='padding-left:30px;'>$modpoints</td><input type='hidden' name='mod_id[$anotherrow[0]]' value='false'></tr>";
            }
        }
        echo "</tbody></table>";
 
        }


    }
    echo "<div id='enginemodificationsheader'><h4>Engine modifications</h4>
	      <h5>Below is a list of engine modifications as well as an average percent gain that modification provides.  Click your modifications OR enter a rear wheel (not flywheel) horsepower number below that you believe is true either from a dyno chart or modification manufacturer claims. Please select carefully as once you choose a calculation method, you will have to create a new classification to choose a different method.</h5></div>
	      <center><p><button class='btn btn-info' id='showenginetable'>I wish to select my mods from a table and will assume GGC's engine gains are correct</button></p>
          <p><button class='btn btn-info' id='showrwhptable'>I wish to enter a rear wheel HP number</button></p>
          <p><button class='btn btn-info' id='hidethebuttons'>I have no engine modifications</button></p>
          <table class='table table-condensed table-bordered enginetablemulti'><thead><tr class='tablehead'><th style='padding-left:30px;'>Name</th><th style='padding-left:30px;'>% addt'l horsepower</th></tr></thead><tbody>";
    
    $result = mysqli_query($db, "SELECT * FROM `autox_mods_engine` ORDER BY `percent` ASC") or die("Error: " . mysqli_error());
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "<tr><Td class='span6' style='padding-left:30px;'>$row[2]</td><td class='span2 pointvalue' style='padding-left:30px;'>$row[3] </td><input type='hidden' name='engine_id[$row[0]]' value='false'></tr>";
    }
    echo"</table><table class='table table-condensed table-bordered enginetablemultiresults'>
        <Tr><td class='span6' style='padding-left:30px;'>Total additional hp</td><td class='span2' style='padding-left:30px;'><div class='percent' style='display: inline;'>0</div>%</td></tr>
        <Tr><td class='span6' style='padding-left:30px;'>Points from engine mods</td><Td class='span2' style='padding-left:30px;'><div class='enginemodpoints' style='display: inline;'>0</div></td></tr></table>";
    $rwhp = round($_SESSION[BHP] * .85);
    $total = round($rwhp + 10);
    echo "<table class='table table-condensed table-bordered' id='rwhptable'>
        <tr id='dynorow'><td class='span6' style='padding-left:30px;'>Your TOTAL rear wheel (not flywheel) horsepower based on dyno or modification manufacturer claims. We think your car has approx <B>$rwhp</b> rwhp, if you added a mod that adds a claimed 10hp, enter <B>$total</b> in this box.</td><td class='span2' style='padding-left:30px;'><input type='text' name='flywheelhp' id='dyno' class='input-small'>hp</td></tr>";
       if ($username and (!$closemsg or $usergroup == "admin")) { echo "<tr id='claimrow'><td class='span6' style='padding-left:30px;'>If you entered a RWHP number based on manufacturers claims, please list your engine mods in this box. If you entered a RWHP number based on a dyno, simply type 'dyno' into this box.</td><Td class='span2' style='padding-left:30px;'><textarea name='hpclaim' id='explainhp'></textarea></td></tr>"; }

    echo"<Tr><td class='span6' style='padding-left:30px;'>Total additional hp</td><td class='span2' style='padding-left:30px;'><div class='percent' style='display: inline;'>0</div>%</td></tr>
         <Tr><td class='span6' style='padding-left:30px;'>Points from engine mods</td><Td class='span2' style='padding-left:30px;'><div class='enginemodpoints' style='display: inline;'>0</div></td></tr></table>";
    if ($username and (!$closemsg or $usergroup == "admin")){
      echo"<div id='differentclass'><br><br><table class='table'><Tr><Td>Want to run your car in a higher or non-competitive class?  Select it here</td><Td><SELECT name='chosenclass' class='span2' id='chosenclass'></SELECT></td></tr></table>";
		if ($usergroup == "admin"){
			echo"<script>
			var peoplelist = [";
	   		$result = mysqli_query($db, "SELECT * FROM `wp_users`") or die("Error: " . mysqli_error());
	   	   	while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		   	   	$escaped = str_replace("'", "", $row[9]);
	   	   		echo"'$escaped ($row[1])',";
	   	   	}
	   		echo"'Fake User (fakeuser)'];
	   		</script>";
			echo"<table class='table'><Tr><Td class='span8'>";
	   		echo"Admin: Save this classification in someone else's profile</td><td class='span4'><input type='text' class='input-large' id='users' name='alternateuser'  placeholder='Start typing a name...'>";
	   		echo"</td></tr></table>";
   		}
        echo "</div><button class='btn btn-success' type='submit' id='submitclassification'>Save this classification to my user profile</button>";
    }
    echo"</form>";
}
        

?>
<Br><Br><br>
</div>  <!--container-->
<div class="navbar navbar-fixed-bottom bottombar" id='finalpoints'></div>
<script src="js/jquery191min.js"></script>
<script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/selectboxit.js"></script>
<script>
var closemsg = "<?php echo $closemsg ?>";
var usergroup = "<?php echo $usergroup ?>";

function updatefloater(currentvalue,cumulativepoints)
{
    if (!cumulativepoints) {cumulativepoints = 0;}
    var carclass;
    var currentcarclass;
    var chosencarclasstext = $('#chosenclass').find(":selected").text();
    var chosencarclassval = $('#chosenclass').find(":selected").val();
    var texttodisplay = currentvalue + cumulativepoints;
    <?php
    $db = sqlconnect();
    $result = mysqli_query($db, "SELECT * FROM `autox_classes`") or die("Error: " . mysqli_error());
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "if (texttodisplay >= $row[1] && texttodisplay <= $row[2]) { carclass = '$row[0]'; } ";
    }
    ?>

    // rrich 1/27/2017: If classification has 0 points, that means no car has been selected and we need to wait for a car to be selected to preform this piece.
    if(texttodisplay != 0) {
        if (chosencarclasstext != "") {currentcarclass = chosencarclasstext;}
            else {currentcarclass = carclass;}
        var update = "Total points: " + texttodisplay + " - Class " + currentcarclass;
        $("#finalpoints").text(update);

        if (carclass === 'C') { $("#chosenclass").html("<option value=''></option><option value='N'>N (Non-compete)</option><option value='B'>B</option><option value='A'>A</option><option value='AA'>AA</option><option value='AAA'>AAA</option><option value='Gonzo'>Gonzo</option>") ;} 
        if (carclass === 'B') { $("#chosenclass").html("<option value=''></option><option value='N'>N (Non-compete)</option><option value='A'>A</option><option value='AA'>AA</option><option value='AAA'>AAA</option><option value='Gonzo'>Gonzo</option>") ;} 
        if (carclass === 'A') { $('#chosenclass').html("<option value=''></option><option value='N'>N (Non-compete)</option><option value='AA'>AA</option><option value='AAA'>AAA</option><option value='Gonzo'>Gonzo</option>") ;} 
        if (carclass === 'AA') { $('#chosenclass').html("<option value=''></option><option value='N'>N (Non-compete)</option><option value='AAA'>AAA</option><option value='Gonzo'>Gonzo</option>") ;} 
        if (carclass === 'AAA') { $('#chosenclass').html("<option value=''></option><option value='N'>N (Non-compete)</option><option value='Gonzo'>Gonzo</option>") ;} 
        if (carclass === 'Gonzo') { $('#chosenclass').html("<option value=''></option><option value='N'>N (Non-compete)</option>") ;}

        var username = "<?php echo $username  ?>"; 
        if (username && (!closemsg || usergroup == "admin")) {
            $('select#chosenclass').selectBoxIt();
            $('select#chosenclass').data("selectBox-selectBoxIt").refresh();
        }

        if (chosencarclasstext != '' && chosencarclassval != carclass){
            $("#chosenclass").val(chosencarclassval).attr('selected','selected');
            $('select#chosenclass').data("selectBox-selectBoxIt").refresh();
        }
    }

}
<?php
echo "var enginemods = [";
$result = mysqli_query($db, "SELECT * FROM `autox_engine_levels` WHERE engine_level = \"" . $_SESSION['engine_level'] . "\" AND `lsd` = 'N'") or die("Error: " . mysqli_error());
while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	echo "['" . $row[0] . "',". $row[3] . "," . $row[4] . "," . $row[2] . "],";
}
echo "['Z',0,0,0]];
var enginemodslsd = [";
$result = mysqli_query($db, "SELECT * FROM `autox_engine_levels` WHERE engine_level = \"" . $_SESSION['engine_level'] . "\" AND `lsd` = 'Y'") or die("Error: " . mysqli_error());
while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
	echo "['" . $row[0] . "',". $row[3] . "," . $row[4] . "," . $row[2] . "],";
}
echo "['Z',0,0,0]];";
?>
var cumulativepoints = 0;
var lsd = '<?php echo $_SESSION['LSD_standard'];?>';
var cumulativepercent = 0;
var dynopoints = 0;
var suspvalue = <?php echo $suspvalue; ?>;
var pointvalue = 0;
pointvalue = pointvalue * 1;
var basepoints = <?php if ($_SESSION['points']) {echo $_SESSION['points'];} else { echo "0";} ?>;
var pkgpoints =  <?php if ($_SESSION['pkg_points']) {echo $_SESSION['pkg_points'];} else { echo "0";} ?>;
basepoints = basepoints * 1;
var currentvalue = 0;
currentvalue = currentvalue * 1;
currentvalue = currentvalue + basepoints + pkgpoints + suspvalue;
updatefloater(currentvalue,cumulativepoints); //klinquist 10/18



$(document).ready(function(){
    $('#xclassinput').keyup(function() {
        var nonbmwyear = $(this).val().substr(0,2);
        if ((nonbmwyear == '19') || (nonbmwyear == '20'))  {
           $('.nonbmwsubmit').show();
        }   else {
            if ($(this).val().length > 2) {
                $('#nonbmwhelper').text('4 digit year required');
            } else {
                $('#nonbmwhelper').text('');
            }
        }
    });
    $('.nonbmwsubmit').hide();
	$('.enginetablemulti').hide();
    $('.enginetablemultiresults').hide();
	$('#rwhptable').hide();
	$('#percenttable').hide();
	$('#submitclassification').hide();
	<?php if ($readytoclassify != "Y") { echo"$('#finalpoints').hide();"; }?>
	$("select").selectBoxIt({});
    $('#differentclass').hide();
<?php 
if ($usergroup == "admin"){ echo "
	$('#users').typeahead({
		source: peoplelist
	});
";
}
?>
});


// rrich 1/27/2017: If it is a "Gonzo" class car, but has less than the minimum gonzo class points, display the total points as the minimum value.
$('#chosenclass').change(function(event) {
    <?php 
        // rrich 1/27/2017: Get the number of points where "Gonzo" class starts.
        $query = mysqli_query($db, "SELECT * FROM `autox_classes` WHERE class = 'Gonzo' LIMIT 1") or die("Error: " . mysqli_error());
        $gonzo = mysqli_fetch_assoc($query);
        echo "var gonzostartpoints = " . $gonzo['start_points'] . ";";
    ?>
    finalpoints = currentvalue + cumulativepoints;
    if ($('#chosenclass').find(":selected").text() === "Gonzo" && finalpoints < gonzostartpoints){
        $("#finalpoints").text("Total points: " + gonzostartpoints + " - Class Gonzo");
    } else {
        updatefloater(currentvalue,cumulativepoints);
    }
    });      


$('#showenginetable').on('click', function(event) {
	event.preventDefault();
	$('.enginetablemulti').show();
    $('.enginetablemultiresults').show();
	$('#showrwhptable').hide();
	$('#showenginetable').hide();
	$('#percenttable').show();
	$('#hidethebuttons').hide();
    $('#differentclass').show();
    $('#submitclassification').show();
});      
$('#showrwhptable').on('click', function(event) {
	event.preventDefault();
	$('#rwhptable').show();
	$('#showenginetable').hide();
	$('#showrwhptable').hide();
	$('#percenttable').show();
	$('#hidethebuttons').hide();
    $('#differentclass').show();
    $('#submitclassification').show();  
}); 
$('#hidethebuttons').on('click', function(event) {
	event.preventDefault();
	$('#showenginetable').hide();
	$('#showrwhptable').hide();
	$('#hidethebuttons').hide();
	$('#enginemodificationsheader').hide();
    $('#noenginemodificationsheader').show();
    $('#differentclass').show();
    $('#submitclassification').show();
});  
                   
$('.modstable tbody tr').on('click', function(event) {
    if ($(this).hasClass('selected')) {
        $(this).find('input').attr('value','false');
        $(this).removeClass('selected');
        pointvalue = $(this).find(".pointvalue").html(); 
        pointvalue = pointvalue * 1;
        currentvalue = currentvalue - pointvalue;
        islsdselected = $(this).find('input').attr('name');
        if (islsdselected == 'mod_id[38]') { lsd = 'N';} else { lsd = 'Y'; }  //klinquist 10/17 
    } else {
        $(this).find('input').attr('value','true');           
         pointvalue = $(this).closest("table").find(".selected").find(".pointvalue").html(); 
        pointvalue = pointvalue * 1
        if (isNaN(pointvalue) === false) { currentvalue = currentvalue - pointvalue; }
        $(this).addClass('selected').siblings().removeClass('selected'); 
        $(this).addClass('selected').siblings().find('input').attr('value','false');  //testing this, worked!
        pointvalue = $(this).find(".pointvalue").html(); 
        pointvalue = pointvalue * 1
        currentvalue = currentvalue + pointvalue;
        islsdselected = $(this).find('input').attr('name');
        if (islsdselected == 'mod_id[38]') { lsd = 'Y';}        
    }

	updatefloater(currentvalue,cumulativepoints); //klinquist 10/18
});
$('.modstablemulti tbody tr').on('click', function(event) {
    if ($(this).hasClass('selected')) {
        $(this).find('input').attr('value','false');
        $(this).removeClass('selected');
        pointvalue = $(this).find(".pointvalue").html(); 
        pointvalue = pointvalue * 1
        currentvalue = currentvalue - pointvalue;
    } else {
        $(this).find('input').attr('value','true');           
        $(this).addClass('selected');
        pointvalue = $(this).find(".pointvalue").html(); 
        pointvalue = pointvalue * 1
        currentvalue = currentvalue + pointvalue;

    }

	updatefloater(currentvalue,cumulativepoints); //klinquist 10/18
});
$('.enginetablemulti tbody tr').on('click', function(event) {
    if ($(this).hasClass('selected')) {
        $(this).find('input').attr('value','false');
        $(this).removeClass('selected');
         pointvalue = $(this).find(".pointvalue").html(); 
        pointvalue = pointvalue * 1;
        cumulativepercent = cumulativepercent - pointvalue;
        if (lsd == 'N'){
		    for (var i=0;i<parseInt(enginemods.length);i++) {
		        if (cumulativepercent >= enginemods[i][1] && cumulativepercent <= enginemods[i][2]) { engineresult = enginemods[i][3]; }
		    }
	    } else {
		    for (var i=0;i<parseInt(enginemodslsd.length);i++) {
		        if (cumulativepercent >= enginemodslsd[i][1] && cumulativepercent <= enginemodslsd[i][2]) { engineresult = enginemodslsd[i][3]; }
		    }
	    }
	    cumulativepoints = cumulativepoints - (cumulativepoints - engineresult); 
    } else {
        $(this).find('input').attr('value','true');           
        $(this).addClass('selected');
        pointvalue = $(this).find(".pointvalue").html(); 
        pointvalue = pointvalue * 1
        cumulativepercent = cumulativepercent + pointvalue;
        if (lsd == 'N'){
		    for (var i=0;i<parseInt(enginemods.length);i++) {
		        if (cumulativepercent >= enginemods[i][1] && cumulativepercent <= enginemods[i][2]) { engineresult = enginemods[i][3]; }
		    }     
		 } else {
		    for (var i=0;i<parseInt(enginemodslsd.length);i++) {
		        if (cumulativepercent >= enginemodslsd[i][1] && cumulativepercent <= enginemodslsd[i][2]) { engineresult = enginemodslsd[i][3]; }
		    }     
		 }
	    cumulativepoints = (cumulativepoints + engineresult) - cumulativepoints;        
        
    }
    $('.percent').empty();
    $('.percent').append(cumulativepercent);
    $('.enginemodpoints').empty();
    $('.enginemodpoints').append(cumulativepoints);        
    updatefloater(currentvalue,cumulativepoints);
});
$("#dyno").keyup(function() {  
	var hp= $("#dyno").val()
	<?php
	$bhp = $_SESSION['BHP'];
	if (isset($_SESSION['BHP'])){ echo "var increase = Math.round((((hp/.85) / $bhp)-1) * 100);";} ?>

	if (increase > 0) {
			if (lsd == 'N'){
			    for (var i=0;i<parseInt(enginemods.length);i++) {
		        	if (increase >= enginemods[i][1] && increase <= enginemods[i][2]) { 
		        		engineresult = enginemods[i][3];
		        	}
		        } 
		    } else {
			    for (var i=0;i<parseInt(enginemodslsd.length);i++) {
		        	if (increase >= enginemodslsd[i][1] && increase <= enginemodslsd[i][2]) { 
		        		engineresult = enginemodslsd[i][3];
		        	}
		        } 
		    }
	        $('.percent').empty();
	        $('.percent').append(increase);
	        $('.enginemodpoints').empty();
	        $('.enginemodpoints').append(engineresult);  
            if ($('#explainhp').val() == '') { 
                $('#explainhp').css('border', 'solid 1px red'); 
                $('#explainhp').attr("placeholder", "Required");
            } 
            cumulativepoints = (cumulativepoints + engineresult) - cumulativepoints; 
	        updatefloater(currentvalue, cumulativepoints);	
            $('#dynorow').addClass('selected');      
	} else {
            $('.percent').empty();
	        $('.percent').append("0");
	        $('.enginemodpoints').empty();
	        $('.enginemodpoints').append("0"); 
            $('#dynorow').removeClass('selected');  
            $('#explainhp').css('border', ''); 
            $('#explainhp').attr("placeholder", "");
            updatefloater(currentvalue);  
	}
});
</script>
</body></html>