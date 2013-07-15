<?php 
$pageTitle = "- Pin Change";
include ("includes/header.php");

if(!$cookieValid) {
	header('Location: /');
	exit;
}

error_reporting(0); 

//Execute the following based on what $_POST["act"] is set to
$returnError = "";
$goodMessage = "";

$act = NULL;
if (isset($_POST["act"])) {
	$act = $_POST["act"];

if($act == "password"){
//Update password
$pass = hash("sha256", $_POST["currentPassword"].$salt);
$username = $_POST["username"];
$newPass = $_POST["newPassword"];
$newPassConfirm = $_POST["newPassword2"];

$userQ = mysql_query("SELECT pass,email,pin FROM webUsers WHERE username = ".sqlesc($username)."") or sqlerr(__FILE__, __LINE__);
$userA = mysql_fetch_object($userQ);

if(!$username) {
echo "Invalid Username <br />";
exit;
}

if(!$userA->pass) {
echo "Invalid Username";
exit;
}

$hashedPass = $userA->pass;

if(!is_numeric($_POST["currentPassword"])){
echo "Pin is not all numbers";
}

//If hash $oldPass is the same as the DB already hashed password continue you with the password change
if($pass == $hashedPass){
//Check if new password is valid
if($newPass != "" && strlen($newPass) > 3){
//Change the password only if $newPass == $newPassConfirm
if($newPass == $newPassConfirm){
//Update hashed password
$newHashedPass = hash("sha256", $newPass.$salt);
$passchangeSuccess = mysql_query("UPDATE `webUsers` SET `pin` = ".sqlesc($newHashedPass)." WHERE username = ".sqlesc($username)."") or sqlerr(__FILE__, __LINE__);
if($passchangeSuccess){
echo "authpin successfully changed.";
exit;
}else{
echo "Database Failure - Unable to change pin";
exit;
}
}else if($newPass != $newPassConfirm){
echo "The \"New Pin\" and \"New Pin Repeat\" fields must match";
exit;
}
}else{
echo "Your new pin is not valid, Must be 4 numbers.";
exit;
}

}else if($pass != $hashedPass){
//Typed in password dosent match DB password
echo "You must type in the correct current password before you can set a new password.";
exit;
}
}
}	



echo "You must change your pin aswell to suit the new password policy<br>";
echo "<font color=red>This is a once off use. if u have had a admin or you have changed it this will not work.</font><br>";
echo"<table class=\"accounts_table\">";
echo"<form action=passpin.php method=post>";
echo"<input type=\"hidden\" name=\"act\" value=\"password\">";
echo "<tr><td>Username</td><td> <input type=\"text\" name=\"username\"><br></td></tr>";
echo "<tr><td>Password</td><td> <input type=\"password\" name=\"currentPassword\"><br></td></tr>";
echo "<tr><td colspan=2><br></td></tr>";
echo "<tr><td>New Pin</td><td> <input type=\"password\" name=\"newPassword\"><br></td></tr>";
echo "<tr><td>Confirm</td><td> <input type=\"password\" name=\"newPassword2\"><br></td></tr>";
echo"<tr><td colspan=\"2\"><input type=\"submit\" value=\"Update\" /></td></tr>";
echo"</table></form>";

include ("includes/footer.php"); 
?>