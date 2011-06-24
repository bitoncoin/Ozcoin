<?php
/*
Copyright (C) Copyright (C) 41a240b48fb7c10c68ae4820ac54c0f32a214056bfcfe1c2e7ab4d3fb53187a0 Name Year (sha256)

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

*/

//RPC Bitcoind Client Information
$rpcType = "http"; // http or https
$rpcUsername = "changeme"; // username
$rpcPassword = "changeme11"; // password
$rpcHost = "localhost";


//Login to Mysql with the following
$dbHost = "localhost";
$dbUsername = "changeme";
$dbPassword = "changeme11";
$dbPort = "3306";
$dbDatabasename = "pushpool";

//Cookie settings | More Info @ http://us.php.net/manual/en/function.setcookie.php
$cookieName = "ozcoin"; //Set this to what ever you want "Cheesin?"
$cookiePath = "/";	//Choose your path!
$cookieDomain = "ozco.in"; //Set this to your domain

// Who to show errors for
$developers = array(
	'127.0.0.1' // set this to your ip to see errors
);
$developerPassword = ''; // set this password for ppl to show errors using password 

$isDeveloper = false;
if (in_array($_SERVER['REMOTE_ADDR'], $developers) or isset($_GET['dev']) and $_GET['dev'] == $developerPassword) {
	ini_set('display_errors', '1');
	ini_set('html_errors', '1');
	ini_set('xdebug.default_enable', '1');
	ini_set('xdebug.collect_vars', 'on');
	ini_set('xdebug.collect_params', '4');
	ini_set('xdebug.dump_globals', 'on');
	ini_set('xdebug.dump.SERVER', 'REQUEST_URI');
	ini_set('xdebug.show_local_vars', 'on');
	$isDeveloper = true;
}

include("bitcoinController/bitcoin.inc.php");

//Encrypt settings
$salt = ""; //Just type a random series of numbers and letters; set it to anything or any length you want. "You can never have enough salt."
$cookieValid	= false; //Don't touch leave as: false

connectToDb();
include('settings.php');

$settings = new Settings();

/////////////////////////////////////////////////////////////////////NO NEED TO MESS WITH THE FOLLOWING | FOR DEVELOPERS ONLY///////////////////////////////////////////////////////////////////
function connectToDb(){
	//Set variables to global retireve outside of the scope
	global $dbHost, $dbUsername, $dbPassword, $dbDatabasename;
	
	//Connect to database
	mysql_connect($dbHost, $dbUsername, $dbPassword)or die(mysql_error());
	mysql_select_db($dbDatabasename);
}

class checkLogin
{
	function checkCookie($input, $ipaddress){	
		global $salt;		
		connectToDb();		
		/*$input comes in the following format userId-passwordhash
		
		/*Validate that the cookie hash meets the following criteria:
			Cookie Ip: matches $ipaddres;
			Cookie Timeout: Is still greater then the current time();
			Cookie Secret: matches the mysql database secret;
		*/
			
		//Split cookie into 2 mmmmm!
		$cookieInfo = explode("-", $input);
		
		$validCookie = false;
		
		//Get "secret" from MySql database
		$getSecretQ	= mysql_query("SELECT secret, pass, sessionTimeoutStamp FROM webUsers WHERE id = ".mysql_real_escape_string($cookieInfo[0])." LIMIT 0,1");
		if ($getSecret = mysql_fetch_object($getSecretQ)) {
			$password	= $getSecret->pass;
			$secret	= $getSecret->secret;
			$timeoutStamp	= $getSecret->sessionTimeoutStamp;
				
			//Create a variable to test the cookie hash against
			$hashTest = hash("sha256", $secret.$password.$ipaddress.$timeoutStamp.$salt);
				
			//Test if $hashTest = $cookieInfo[1] hash value; return results
			
			if($hashTest == $cookieInfo[1]){		
				$validCookie = true;
			}				
		}
		
		return $validCookie;
	}
	
	function returnUserId($input){
		//Just split the cookie to get the userId
		$cookieInfo = explode("-", $input);
			
		return $cookieInfo[0];			
	}
}



function outputPageTitle(){
	if (!isset($settings))
	{
		connectToDb();	
		$settings = new Settings();
	}
	//Get page title
	return $settings->getsetting("pagetitle");;
}

function outputHeaderTitle(){	
	if (!isset($settings))
	{
		connectToDb();	
		$settings = new Settings();
	}
	return $settings->getsetting("websitename");
}

//Helpfull functions
function genRandomString($length=10) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $string = "";    

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}

function antiXss($input) {
	//strip HTML tags from input data
	return htmlentities(strip_tags($input), ENT_QUOTES);
}

function sqlerr($file = '', $line = '')
{
  print("<table border=0 bgcolor=blue align=left cellspacing=0 cellpadding=10 style='background: blue'>" .
    "<tr><td class=embedded><font color=white><h1>SQL Error</h1>\n" .
  "<b>" . mysql_error() . ($file != '' && $line != '' ? "<p>in $file, line $line</p>" : "") . "</b></font></td></tr></table>");
  die;
}

function sqlesc($x) {
    return "'".mysql_real_escape_string($x)."'";
}

$_current_lock = null;

function unlock() {
	global $_current_lock;
	$sql = "UPDATE locks SET locked = 0 WHERE name = '" . mysql_real_escape_string($_current_lock) . "'";
	mysql_query($sql);
	//echo("unlocked.\n");
}

function lock($name) {
	global $_current_lock;
	mysql_query("LOCK TABLES locks WRITE");
	$q = mysql_query("SELECT locked FROM locks WHERE name = '" . mysql_real_escape_string($name) . "'");

	$lock = mysql_fetch_object($q);
	if ($lock === false) {
		mysql_query("INSERT INTO locks (name, locked) VALUES ('".mysql_real_escape_string($name)."', 1)");
		//echo("New lock.\n");
	} elseif ($lock->locked) {
		echo("Lock already held, exiting. (".$name.")");
		mysql_query("UNLOCK TABLES");
		exit();
		return;
	} else {		
		mysql_query("UPDATE locks SET locked = 1 WHERE name = '" . mysql_real_escape_string($name) . "'");
		//echo("Taking lock.\n");
	}
	
	mysql_query("UNLOCK TABLES");
	$_current_lock = $name;
	register_shutdown_function('unlock');
}


?>
