<?php 
$pageTitle = "- Password Change";
include ("includes/header.php");

//Execute the following based on what $_POST["act"] is set to
$returnError = "";
$goodMessage = "";

$act = NULL;
if (isset($_POST["act"])) {
	$act = $_POST["act"];

if($act == "password"){
//Update password
$oldPass = hash("sha256", $_POST["currentPassword"]);
#$pin = hash("sha256", $_POST["pin"]);
$username = $_POST["username"];
$email = $_POST["email"];
$newPass = $_POST["newPassword"];
$newPassConfirm = $_POST["newPassword2"];

$userQ = mysql_query("SELECT pass,email,pin FROM webUsers WHERE username = ".sqlesc($username)."") or sqlerr(__FILE__, __LINE__);
$userA = mysql_fetch_object($userQ);

if(!$username) {
echo "Invalid Username <br />";
exit;
}

$hashedPass = $userA->pass;

#if($pin != $userA->pin) {
#echo "Pin incorrect <br />";
#exit;
#}

if($oldPass != $userA->pass) {
echo "Old Password incorrect <br />";
exit;
}

if($email != $userA->email) {
echo "email incorrect <br />";
exit;
}

if( strlen($newPass) < 8 ) {
echo "Password too short! <br />";
exit;
}

if( strlen($newPass) > 200 ) {
echo "Password too long! <br />";
exit;
}

if( strlen($newPass) < 8 ) {
echo "Password too short! <br />";
exit;
}

if( !preg_match("#[0-9]+#", $newPass) ) {
	echo "Password must include at least one number! <br />";
exit;
}


if( !preg_match("#[a-z]+#", $newPass) ) {
	echo "Password must include at least one letter! <br />";
exit;
}


if( !preg_match("#[A-Z]+#", $newPass) ) {
	echo "Password must include at least one CAPS! <br />";
exit;
}


			//If hash $oldPass is the same as the DB already hashed password continue you with the password change
			if($oldPass == $hashedPass){
				//Check if new password is valid
				if($newPass != "" && strlen($newPass) > 7 && strlen($newPass) <= 200){
					//Change the password only if $newPass == $newPassConfirm
					if($newPass == $newPassConfirm){
						//Update hashed password
						$newHashedPass = hash("sha256", $newPass.$salt);
						$passchangeSuccess = mysql_query("UPDATE `webUsers` SET `pass` = ".sqlesc($newHashedPass)." WHERE username = ".sqlesc($username)."") or sqlerr(__FILE__, __LINE__);
						if($passchangeSuccess){
							echo "Password successfully changed.";
							exit;
						}else{
							echo "Database Failure - Unable to change password";
							exit;
						}
					}else if($newPass != $newPassConfirm){
						echo "The \"New Password\" and \"New Password Repeat\" fields must match";
						exit;
					}
				}else{
					echo "Your new password is not valid, Must be longer then 8 characters, and no more than 200.";
					exit;
				}

			}else if($oldPass != $hashedPass){
				//Typed in password dosent match DB password
				echo "You must type in the correct current password before you can set a new password.";
				exit;
			}
		}
}	



echo "You must change your password for new security policy<br>";
echo "<font color=red>This is a once off use. if u have had a admin or you have changed it this will not work.</font><br>";
echo "Min 8 Chars Max 200<br>";
echo "Must have 1 Cap and 1 Number<br>";
echo"<table class=\"accounts_table\">";
echo"<form action=password.php method=post>";
echo"<input type=\"hidden\" name=\"act\" value=\"password\">";
echo "<tr><td>Username</td><td> <input type=\"text\" name=\"username\"><br></td></tr>";
echo "<tr><td>Email</td><td> <input type=\"text\" name=\"email\"><br></td></tr>";
echo "<tr><td>Old Password</td><td> <input type=\"password\" name=\"currentPassword\"><br></td></tr>";
echo "<tr><td colspan=2><br></td></tr>";
echo "<tr><td>New Password</td><td> <input type=\"password\" name=\"newPassword\"><br></td></tr>";
echo "<tr><td>Confirm</td><td> <input type=\"password\" name=\"newPassword2\"><br></td></tr>";
echo"<tr><td colspan=\"2\"><input type=\"submit\" value=\"Update\" /></td></tr>";
echo"</table></form>";

include ("includes/footer.php"); 
?>