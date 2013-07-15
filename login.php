<?php
/*
Copyright (C)  41a240b48fb7c10c68ae4820ac54c0f32a214056bfcfe1c2e7ab4d3fb53187a0 Name Year (sha256)
a
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
Website Reference:http://www.gnu.org/licenses/gpl-2.0.html

Note From Author: Keep the original donate address in the source files when transferring or redistrubuting this code.
Please donate at the following address: 1Fc2ScswXAHPUgj3qzmbRmwWJSLL2yv8Q
*/
//This page will attempt to take informtion from the user and create an ecrypted session inside of a cookie
//Set page starter variables//
$cookieValid = 0;
$login_attempts_allowed = 10; // clock: increased login attempts allowed

//Include hashing functions
include("includes/requiredFunctions.php");

if (isset($_POST["username"]) && isset($_POST["password"])) {
	//Filter input results before querying them into database
	$user = mysql_real_escape_string(substr($_POST["username"], 0, 20));
	$pass = mysql_real_escape_string(substr($_POST["password"], 0, 200)); // clock: increased password max length to 200
	//Check the supplied username & password with the saved username & password
	connectToDb();
	$checkPassQ = mysql_query("SELECT id, secret, pass, accountLocked, accountFailedAttempts, ad FROM webUsers WHERE username = '".$user."' LIMIT 0,1");
	$checkPass = mysql_fetch_object($checkPassQ);
	$userExists = $checkPass->id;
	$ad = $checkPass->ad;


	if($ad == 'yes'){
		header('Location: /error.php');
		exit;
	}

	if($checkPass->accountFailedAttempts >= $login_attempts_allowed){
	echo "Account has been banned";
	die();
	}
} else {
	$userExists = 0;
}

//Check if user exists before checking login data
if($userExists > 0){
	//Check to see if this user has an `accountLocked`
	if($checkPass->accountLocked < time()){
		//Check to see if this user has attempted to login more then the maximum allowed failed attempts
		if($checkPass->accountFailedAttempts < $login_attempts_allowed){
			$dbHash = $checkPass->pass;
			$inputHash = hash("sha256", $pass.$salt);
			//Do Check
			if($dbHash == $inputHash){
				//Give out the secrect SHHH!! be quite too!
				//Get ip address so we can hash with the cookie so no one can steal the password
				$ip = $_SERVER['REMOTE_ADDR'];
				//$ip = getRealIpAddr();
				$timeoutStamp = time()+60*60*24*7; //1 week session
				//Update logged in ip address so no one can steal this cookie hash unless
				mysql_query("UPDATE `webUsers` SET `sessionTimeoutStamp` = ".$timeoutStamp.", `loggedIp` = '".$ip."' WHERE `id` = ".$userExists);

				//Set cookie in browser for session
				$hash		= $checkPass->secret.$dbHash.$ip.$timeoutStamp;
				$cookieHash = hash("sha256", $hash.$salt);
				setcookie($cookieName, $checkPass->id."-".$cookieHash, $timeoutStamp, $cookiePath, $cookieDomain);
				$cookieValid = 1;

				//Display output message
				$outputMessage = "Welcome back, we'll be returning to the main page shortly";
				$lock = 0;
				mysql_query("UPDATE `webUsers` SET accountFailedAttempts = ".$lock.", lastseen = '" . get_date_time() . "' WHERE `id` = ".$userExists);
			}else{
				$lock = $checkPass->accountFailedAttempts + 1;
				mysql_query("UPDATE `webUsers` SET accountFailedAttempts = ".$lock." WHERE `id` = ".$userExists);
				header('Location: /error.php');
				exit;
			}
		}
	}
}else{
 	header('Location: /error.php');
}
if($userExists == 0){
				header('Location: /error.php');
				exit;
}
?>
<html>
  <head>
	<title><?php echo antiXss(outputPageTitle());?> </title>
	<link rel="stylesheet" href="/css/mainstyle.css" type="text/css" />
 	<? header('Location: /'); ?>
	<meta http-equiv="refresh" content="5; url=/">
  </head>
  <body>
	<div id="pagecontent">
		<h1><?php echo antiXss($outputMessage); ?><br/>
		<a href="/">Click here if you continue to see this message</a></h1>
	</div>
  </body>
</html>

