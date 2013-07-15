<?php
include ("includes/header.php");

if (isset($_GET["id"])) {
	$id = $_GET["id"];
}

if (isset($_GET["pin"])) {
	$emailpin = $_GET["pin"];
} ELSE {
echo "Email Pin Not Present";
exit;
}

$userQ = mysql_query("SELECT emailAuthPin FROM webUsers WHERE id = '".$id."'");
$userA = mysql_fetch_object($userQ);
$emailAuthPin = "$userA->emailAuthPin";


if($emailAuthPin != $emailpin){
Echo "Invlaid Pin";
} ELSE {
$unsubscribe = mysql_query("UPDATE webUsers SET recivemail = 'yes' WHERE id = '$id'");
if($unsubscribe){
echo "You have unsubscribed from ozcoin emails.";
} ELSE {
echo "Failed to unsubscribe you";
}
}

include("includes/footer.php");			
?>