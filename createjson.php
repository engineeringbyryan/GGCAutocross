<?php
function sqlconnect()
{
    $dbhost="db.ggcbmwcca.org";
    $dbuser="ggcjom";
    $dbpassword="bombe";
    $dbname="ggcjom";
    $db = mysql_connect($dbhost,$dbuser,$dbpassword) or die("Couldn't connect to the database.");
    mysql_select_db($dbname) or die("Couldn't select the database");
}


sqlconnect();

$result = mysql_query("SELECT * FROM `autox_cars`") or die("Error: " . mysql_error());
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		for ($i=$row[2];$i<$row[3];$i++){
			$result = mysql_query("SELECT * FROM `autox_optional_car_wheels` WHERE `car_id` = '$carid'") or die("Error: " . mysql_error());
			    if (mysql_num_rows($result) == 0) {
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
			        while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			            echo "<option value='$row[0]'>$row[2]</option>";
			        }
			        mysql_free_result($result);
			        echo "</SELECT></div></form>";
			    }



		}
    }
mysql_free_result($result);

