<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');

$pageTitle = "- Lost Password?";
include ("includes/header.php");
//Execute the following based on what $_POST["act"] is set to
$returnError = "";
$goodMessage = "";

$act = NULL;

if (isset($_POST["act"])) {
	$act = $_POST["act"];
}

if($act){
	if($act == "find"){
		if (isset($_POST["emailto"])) {
			$emailto = mysql_real_escape_string($_POST["emailto"]);
		} ELSE {
			$emailto = NULL;
		}

		$usernameExistsQ = mysql_query("SELECT id, passreset FROM webUsers WHERE email = '".$emailto."'");
		$usernameExist = mysql_fetch_object($usernameExistsQ);

		if($usernameExist == NULL){
			echo "Email Not Found";
			include ("includes/footer.php");
			exit;
		}

		$passreset = $usernameExist->passreset;
		$emaile = $usernameExist->id;

		if($passreset >= '3'){
			echo "Your account has been reset 3 times, you cannot reset it anymore times please contact a admin on irc.";
			include ("includes/footer.php");
			exit;
		}

		if($emaile == 0){
			echo "Email Not Found";
			include ("includes/footer.php");
			exit;
		}

		//To Pull 7 Unique Random Values Out Of AlphaNumeric

		//removed number 0, capital o, number 1 and small L
		//Total: keys = 32, elements = 33
		$characters = array(
		"A","B","C","D","E","F","G","H","J","K","L","M",
		"N","P","Q","R","S","T","U","V","W","X","Y","Z",
		"1","2","3","4","5","6","7","8","9");

		//make an "empty container" or array for our keys
		$keys = array();

		//first count of $keys is empty so "1", remaining count is 1-6 = total 7 times
		while(count($keys) < 32) {
			//"0" because we use this to FIND ARRAY KEYS which has a 0 value
			//"-1" because were only concerned of number of keys which is 32 not 33
			//count($characters) = 33
			$x = mt_rand(0, count($characters)-1);
			if(!in_array($x, $keys)) {
			   $keys[] = $x;
			}
		}
$random_chars = '';
		foreach($keys as $key){
		   $random_chars .= $characters[$key];
		}

		$lock = $passreset + 1;
		$passchangeSuccess = mysql_query("UPDATE `webUsers` SET `emailAuthPin` = ".sqlesc($random_chars).", passreset = '".$lock."' WHERE `email` = '".$emailto."'") or sqlerr(__FILE__, __LINE__);
		if($passchangeSuccess){

$to = "$emailto";
$subject = "OzCoin Password Recovery";
$from_email = "noreply@ozco.in"; //site email

$headers = "From: $from_email\n";
$headers .= 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";

$msg = "
<html>
<head>
<title>Ozcoin's Password Recovery System</title>
</head>
<body>
Hello, Your have requested a password reset.<br />
If you requested this please click this link to allow a password reset.<br />
<a href=https://$_SERVER[SERVER_NAME]/lostpassword.php?act=updatepassword&emailto=$emailto&emailAuthPin=$random_chars>link</a>
</body></html>";

		mail($to, $subject, $msg, $headers);

			echo "Email has been sent.";
		}else{
			echo "Database Failure - Unable to set varibles";
		}


	}

	if($act == "updatePassword"){

		if (isset($_POST["user"])) {
			$username = $_POST["user"];
		} ELSE {
			$username = NULL;
		}

		if (isset($_POST["authPin"])) {
			$inputAuthPin = hash("sha256", $_POST["authPin"].$salt);
		} ELSE {
			$inputAuthPin = NULL;
		}

		$usernameExistsQ = mysql_query("SELECT `id`, pin, passreset FROM `webUsers` WHERE `username` = ".sqlesc($username)."");
		$usernameExists = mysql_fetch_object($usernameExistsQ);

		//Update password
		$newPass = mysql_real_escape_string($_POST["newPassword"]);
		$newPassConfirm = mysql_real_escape_string($_POST["newPassword2"]);

		//If hash $oldPass is the same as the DB already hashed password continue you with the password change
		if($inputAuthPin == $usernameExists->pin){
			//Check if new password is valid
			if($newPass != "" && strlen($newPass) > 6){
				//Change the password only if $newPass == $newPassConfirm
				if($newPass == $newPassConfirm){
					//Update hashed password
					$newHashedPass = hash("sha256", $newPass.$salt);
					$passchangeSuccess = mysql_query("UPDATE `webUsers` SET `pass` = '".$newHashedPass."', passreset = '0' WHERE `id` = ".sqlesc($usernameExists->id)."");
					
					if($passchangeSuccess){
						echo "Password successfully changed.";
					}else{
						echo "Database Failure - Unable to change password";
					}
				}else if($newPass != $newPassConfirm){
					echo "The \"New Password\" and \"New Password Repeat\" fields must match";
				}
			}else{
				echo "Your new password is not valid, Must be longer then 6 characters";
			}
		} else if($inputAuthPin != $usernameExists->pin) {
			//Typed in password dosent match DB password
			echo "You must type in the correct current authpin before you can set a new password.";
		}
	}
}

if (isset($_GET["act"])) {
	$act = $_GET["act"];
	$emailto = $_GET["emailto"];
	$emailAuthPin = mysql_real_escape_string($_GET["emailAuthPin"]);
}

if($act == "updatepassword"){

	$usernameExistsQ = mysql_query("SELECT `id`, emailAuthPin, username FROM `webUsers` WHERE `email` = ".sqlesc($emailto)."");
	$usernameExists = mysql_fetch_object($usernameExistsQ);
	$username = $usernameExists->username;
	$authpin = $usernameExists->emailAuthPin;

	if(!$username) {
		echo"Username doesnt exist";
		exit;
	}

	if($emailAuthPin != $usernameExists->emailAuthPin) {
		echo "Inncorrect Details.";
		exit;
	} ELSE {
		echo "<h2>Change Password</h2>";
		echo "<form action=\"lostpassword.php\" method=\"post\"><input type=\"hidden\" name=\"act\" value=\"updatePassword\"><input type=\"hidden\" name=\"user\" value=\"$username\">";
		echo "<table>";
		echo "<tr><td>Account Name: </td><td>$username</tr>";
		echo "<tr><td>Account Name: </td><td>$emailto</tr>";
		echo "<tr><td>New Password: </td><td><input type=\"password\" name=\"newPassword\"></td></tr>";
		echo "<tr><td>New Password Repeat: </td><td><input type=\"password\" name=\"newPassword2\"></td></tr>";
		echo "<tr><td>Authorize Pin: </td><td><input type=\"password\" name=\"authPin\" size=\"4\" maxlength=\"4\"></td></tr>";
		echo "</table>";
		echo "<span style=\"text-decoration: underline;\">(Min Password length is 6)</span> <br />";
		echo "<input type=\"submit\" value=\"Update Password Settings\"></form>";
	}
}

if($act == NULL){
	echo "<h2>Lost Password</h2>";
	echo "<table>";
	echo "<form action=\"lostpassword.php\" method=\"post\"><input type=\"hidden\" name=\"act\" value=\"find\">";
	echo "<tr><td>Account email: </td><td><input type=\"text\" name=\"emailto\"></tr>";
	echo "<td><input type=\"submit\" value=\"Find Account\"></tr></td></form>";
	echo "</table>";
}

include ("includes/footer.php");
?>