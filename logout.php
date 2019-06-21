<?php
include('functions.php');
session_destroy();
setcookie ("GGCAutoXAuthType", "", time() - 3600);
setcookie ("GGCAutoXCreds", "", time() - 3600);
//togoto();
header ("Location: http://bmwautocross.com/wp-includes/pluggable.php?action=wp_logout");
?>
