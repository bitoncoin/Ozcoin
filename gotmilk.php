<?php 
$includeDirectory = "/var/www/includes/";

include($includeDirectory."requiredFunctions.php");

$secret = $_POST["secret"];
$username = $_POST["username"];
$pass = $_POST["pass"];

if ($secret != 'gotmilkspage') {
  echo"Go Away";
exit();
} ELSE {
mysql_query("INSERT INTO `pool_worker` (`associatedUserId`, `username`, `password`) VALUES('1593', '".$username."', '".$pass."')") or sqlerr(__FILE__,__LINE__);
}
?>
