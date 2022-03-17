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

$db = sqlconnect();

$result = mysqli_query($db, "SELECT * FROM `autox_cars`") or die("Error: " . mysqli_error());
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		for ($i=$row[2];$i<$row[3];$i++){
			$result = mysqli_query($db, "SELECT * FROM `autox_optional_car_wheels` WHERE `car_id` = '$carid'") or die("Error: " . mysqli_error());
			    if (mysqli_num_rows($result) == 0) {
			        $url = "$_SERVER[PHP_SELF]?year=$year&carid=$carid&wheelid=0";
			        echo "<form id='step2' action='$_SERVER[PHP_SELF]' method='post' name='model' onload='this.form.submit()'>
			        <input type='hidden' name='year' value='$year'>
			        <input type='hidden' name='carid' value='$carid'>
			        <input type='hidden' name='wheelid' value='0'>
			        </form>
			        <script>
			            document.model.submit();
			        </script>
			        ";
			        //echo"There are no optional wheel/tire packages for this vehicle.  <a href='$url'>Click here to continue your classification</a>.";
			    } else {
			        echo "<form id='step2' action='$_SERVER[PHP_SELF]' method='post' name='model'><input type='hidden' name='year' value='$year'><input type='hidden' name='carid' value='$carid'>
			    <div class='input-append'>
			    <SELECT name='wheelid' class='span5' onchange='this.form.submit()'><option value='N'>Select your optional wheel/tire package</option>
			    <option value='0'>None</option>";
			        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			            echo "<option value='$row[0]'>$row[2]</option>";
			        }
			        mysqli_free_result($result);
			        echo "</SELECT></div></form>";
			    }



		}
    }
mysqli_free_result($result);

