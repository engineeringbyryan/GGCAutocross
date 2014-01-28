<?php
include('functions.php');
session_destroy();
setcookie ("GGCAutoXAuthType", "", time() - 3600);
setcookie ("GGCAutoXCreds", "", time() - 3600);
//togoto();
header ("Location: http://ggcbmwcca.org/new_html/wp-login.php?action=logout");
?>
