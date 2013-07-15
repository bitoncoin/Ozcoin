<?php
$pageTitle = "- Account Details";

include ("includes/header.php");

if(!$cookieValid) {
	header('Location: /');
	exit;
}
//Execute the following based on what $_POST["act"] is set to
$returnError = "";
$goodMessage = "";

$act = NULL;
if (isset($_POST["act"])) {
	$act = $_POST["act"];

if (isset($_POST["authPin"])) {
$inputAuthPin = hash("sha256", $_POST["authPin"].$salt);
} ELSE {
$inputAuthPin = NULL;
}

	//Check if authorization pin has been inputted correctly
	if($inputAuthPin == $authPin && $act){
		if($act == "cashOut"){
			$txfee = 0;
			if ($settings->getsetting("sitetxfee") > 0)
				$txfee = $settings->getsetting("sitetxfee");
			//Get user's balance and send it to set address;
			//Does user have any money in their balance
			if($currentBalance > $txfee) {				
				//Send $currentBalance to $paymentAddress
				//Validate that a $paymentAddress has been set & is valid before sending
				$isValidAddress = $bitcoinController->validateaddress($paymentAddress);
				if($isValidAddress) {
					if (!islocked("money")) {
						//Subtract TX fee, site percentage and donation percentage.
						$sitepercent = $settings->getsetting("sitepercent");
						$currentBalance = ($currentBalance*(1-$sitepercent/100)*(1-$donatePercent/100)) - $txfee;
						//Send money//
						try {
							$paid = 0;
							$result = mysql_query("SELECT IFNULL(paid,'0') as paid FROM accountBalance WHERE userId=".$userId);
							if ($resultrow = mysql_fetch_object($result)) $paid = $resultrow->paid + $currentBalance;
							
							lock("money");
							mysql_query("BEGIN");
							//Reduce balance amount to zero
							mysql_query("UPDATE accountBalance SET balance = '0', paid = '$paid' WHERE userId = $userId");
							if ($bitcoinController->sendtoaddress($paymentAddress, $currentBalance)) {																									
							$goodMessage = "You have successfully sent ".$currentBalance." to the following address:".$paymentAddress;
								mail("$userEmail", "Ozcoin Manual Payout Notification", "Hello,\n\nYour requested manual payout of ". $currentBalance." LTC has been sent to your payment address ".$paymentAddress.".", "From: Ozcoin Notifications <admin@ozco.in>");
							//Set new variables so it appears on the page flawlessly
							$currentBalance = 0;						
								mysql_query("COMMIT");
						}else{
								mysql_query("ROLLBACK");
							$returnError = "Commodity failed to send.";
						}
						} catch (Exception $e) {
							mail("locos@westnet.com.au", "Error","$e $_GET $_POST", "From: Ozcoin Notifications <admin@ozco.in>");
							mysql_query("ROLLBACK");
					}
						unlock("money");
					}
					else
					{
						$returnError = "Automatic payouts currently in progress, try again later.";
					}
				}else{
					$returnError = "That isn't a valid Bitcoin address";
				}
			}else{
				$returnError = "You have no money in your account!";
			}
		}


		if($act == "updateDetails"){
			//Update user's details
			$newSendAddress = mysql_real_escape_string($_POST["paymentAddress"]);
			$newPayoutThreshold = mysql_real_escape_string($_POST["payoutThreshold"]);
			$nickname = mysql_real_escape_string($_POST["nickname"]);

			if (isset($_POST["ipcheck"])) {
				$ipcheck = mysql_real_escape_string($_POST["ipcheck"]);
			} ELSE {
				$ipcheck = 'no';
			}

			if (isset($_POST["livestats"])) {
				$livestats = mysql_real_escape_string($_POST["livestats"]);
			} ELSE {
				$livestats = 'no';
			}

			if (isset($_POST["update_interval"])) {
				$update_interval = mysql_real_escape_string($_POST["update_interval"]);
				if($update_interval < 20)
				{
					$update_interval = 20;
				}
			} ELSE {
				$update_interval = '60';
			}

			if ($newPayoutThreshold > 50)
				$newPayoutThreshold = 50;
			if ($newPayoutThreshold < 0.0)
				$newPayoutThreshold = 0;
			$updateSuccess1 = mysql_query("UPDATE accountBalance SET sendAddress = '".$newSendAddress."', threshold = '".$newPayoutThreshold."' WHERE userId = ".$userId) or sqlerr(__FILE__, __LINE__);

			if (isset($ipcheck)) {
				mysql_query("UPDATE webUsers SET ipcheck = '".$ipcheck."' WHERE id = '".$userId."'") or sqlerr(__FILE__, __LINE__);
			}

			if (isset($livestats)) {
				mysql_query("UPDATE webUsers SET livestats = '".$livestats."' WHERE id = '".$userId."'") or sqlerr(__FILE__, __LINE__);
			}

			if (isset($update_interval)) {
				mysql_query("UPDATE webUsers SET update_interval = '".$update_interval."' WHERE id = '".$userId."'") or sqlerr(__FILE__, __LINE__);
			}

			if (isset($nickname)) {
				mysql_query("UPDATE webUsers SET nickname = '".$nickname."' WHERE id = '".$userId."'") or sqlerr(__FILE__, __LINE__);
			}

			if($updateSuccess1){
				$goodMessage = "Account details are now updated.";
				$paymentAddress = $newSendAddress;
				$payoutThreshold = $newPayoutThreshold;
				?><meta http-equiv="refresh" content="0; url=/accountdetails.php"><?
			}
		}

		if($act == "sendemail") {
			//Update user's details
			if (isset($_POST["checkemail"])) {
				$checkemail = $_POST["checkemail"];
			} ELSE {
				$checkemail = 'no';
			}

			if (isset($_POST["recivemail"])) {
				$recivemail = $_POST["recivemail"];
			} ELSE {
				$recivemail = 'no';
				$checkemail = 'no';
			}

			$updateSuccess1 = mysql_query("UPDATE webUsers SET sendemail = '$checkemail', recivemail = '$recivemail' WHERE id = $userId") or sqlerr(__FILE__, __LINE__);

			if($updateSuccess1){
			?><meta http-equiv="refresh" content="0; url=/accountdetails.php"><?
		}
	}

		if($act == "deluser") {

			if (isset($_POST["chckuser"])) {
				$chckuser = $_POST["chckuser"];
			} ELSE {
				$chckuser = 'no';
			}

			if($chckuser == 'yes') {
			$updateSuccess1 = mysql_query("UPDATE webUsers SET ad = 'yes', sendemail = 'no' WHERE id = $userId") or sqlerr(__FILE__, __LINE__);
			} ELSE {
			echo "You must tick the box";
			}

			if($updateSuccess1){
			setcookie($cookieName, 0, time()-3600, $cookiePath, $cookieDomain);
//			?><meta http-equiv="refresh" content="0; url=/"><?
		}
	}

		if ($act == "deadworker") {
			if (isset($_POST["deadworker"])) {
				$deadworker = $_POST["deadworker"];
			} else {
				$deadworker = 'no';
			}
			$updateSuccess1 = mysql_query("UPDATE webUsers SET deadworker = '$deadworker' WHERE id = $userId") or sqlerr(__FILE__, __LINE__);

			if ($updateSuccess1){
                        	?><meta http-equiv="refresh" content="0; url=/accountdetails.php"><?
                        	$goodMessage = "Dead Worker Notification Set";

			}
		}

		if($act == "donor") {

			//Update user's details
			/*if (isset($_POST["donorselect"])) {
				$donorselect = $_POST["donorselect"];
			} ELSE {
				$donorselect = 0;
			}*/

			if (isset($_POST["donatePercent"])) {
			$newDonatePercent = $_POST["donatePercent"];
			} ELSE {
				$newDonatePercent = 0;
			}

			/*if(empty($newDonatePercent)) {
			echo "You need to set a value higher then 0";
			exit;
			 }*/

			if ($newDonatePercent < 0) {
				echo "Donation % must be numeric.";
			exit;
			}

			if (!is_numeric($newDonatePercent)) {
				echo "Donation % must be numeric.";
			exit;
			}

			/*if (isset($_POST["agreement"])) {
				$agreement = $_POST["agreement"];
			} ELSE {
				$agreement = 'no';
			}

			if($agreement == 'no' && $newDonatePercent > 0) {
			echo "You need to agree to this";
			exit;
			}

			if (isset($_POST["donorend"])) {
				$donorend = $_POST["donorend"];
			} ELSE {
				$donorend = 'no';
			}
			if ($agreement== 'yes') {
			echo "you have set this wait till your subscription has finished";
			exit;
			}*/
/*
			if($newDonatePercent == 0) {
			$deadworker = 'no';
			}
*/

			//$updateSuccess1 = mysql_query("UPDATE webUsers SET donorselect = '$donorselect', donorend = '$donorend', donate_percent = '$newDonatePercent',donoragree = '$agreement' WHERE id = $userId") or sqlerr(__FILE__, __LINE__);
			$updateSuccess1 = mysql_query("UPDATE webUsers SET donate_percent = '$newDonatePercent', deadworker = '$deadworker' WHERE id = $userId") or sqlerr(__FILE__, __LINE__);

			if($updateSuccess1){
			?><meta http-equiv="refresh" content="0; url=/accountdetails.php"><?
			$goodMessage = "Donation Set";
		}
	}

		if($act == "updatePassword"){
			//Update password
			$oldPass = hash("sha256", $_POST["currentPassword"].$salt);
			$newPass = $_POST["newPassword"];
			$newPassConfirm = $_POST["newPassword2"];

			$userQ = mysql_query("SELECT pass FROM webUsers WHERE id = '".$userId."'");
			$userA = mysql_fetch_object($userQ);
			$hashedPass = $userA->pass;

if( strlen($newPass) < 8 ) {
$validRegister = 0;
	$returnError .= "Password too short! <br />";
}

if( strlen($newPass) > 200 ) {
$validRegister = 0;
	$returnError .= "Password too long! <br />";
}

if( strlen($newPass) < 8 ) {
$validRegister = 0;
	$returnError .= "Password too short! <br />";
}

if( !preg_match("#[0-9]+#", $newPass) ) {
$validRegister = 0;
	$returnError .= "Password must include at least one number! <br />";
}


if( !preg_match("#[a-z]+#", $newPass) ) {
$validRegister = 0;
	$returnError .= "Password must include at least one letter! <br />";
}


if( !preg_match("#[A-Z]+#", $newPass) ) {
$validRegister = 0;
	$returnError .= "Password must include at least one CAPS! <br />";
}


			//If hash $oldPass is the same as the DB already hashed password continue you with the password change
			if($oldPass == $hashedPass){
				//Check if new password is valid
				if($newPass != "" && strlen($newPass) > 7 && strlen($newPass) <= 200){
					//Change the password only if $newPass == $newPassConfirm
					if($newPass == $newPassConfirm){
						//Update hashed password
						$newHashedPass = hash("sha256", $newPass.$salt);
						$passchangeSuccess = mysql_query("UPDATE `webUsers` SET `pass` = '".$newHashedPass."' WHERE `id` = '".$userId."'");
						if($passchangeSuccess){
							?><meta http-equiv="refresh" content="0; url=/done.php"><?
						}else{
							$returnError = "Database Failure - Unable to change password";
						}
					}else if($newPass != $newPassConfirm){
						$returnError = "The \"New Password\" and \"New Password Repeat\" fields must match";
					}
				}else{
					$returnError = "Your new password is not valid, Must be longer then 8 characters, and no more than 200.";
				}

			}else if($oldPass != $hashedPass){
				//Typed in password dosent match DB password
				$returnError = "You must type in the correct current password before you can set a new password.";
			}
		}


}else if($inputAuthPin != $authPin && $act != "addWorker" && $act != "Update Worker" && $act != "Delete Worker"){
		$returnError = "Authorization Pin is Invalid!";
	}

	if($act == "addWorker"){
		//Add worker
		$prefixUsername = $userInfo->username;
		$inputUser = $prefixUsername.".".mysql_real_escape_string($_POST["username"]);
		$inputPass = mysql_real_escape_string($_POST["pass"]);

		//Check if username already exists
		$usernameExistsQ = mysql_query("SELECT id,username FROM `pool_worker` WHERE `associatedUserId` = ".$userId." AND `username` = '".$inputUser."'");
		$usernameExists = mysql_num_rows($usernameExistsQ);
		$usernameExists1 = mysql_fetch_object($usernameExistsQ);

		$workerId = $usernameExists1->username;

		if($usernameExists == 0){
			$addWorkerQ = mysql_query("INSERT INTO `pool_worker` (`associatedUserId`, `username`, `password`) VALUES('".$userId."', '".$inputUser."', '".$inputPass."')")or sqlerr(__FILE__,__LINE__);
			if($addWorkerQ){
				$goodMessage = "Worker successfully added!";
			}else if(!$addWorkerQ){
				$returnError = "Database Error - Worker was not added :(";
			}
		}else if($usernameExists == 1){
				mysql_query("UPDATE `pool_worker` SET disabled = 'no' WHERE username = '".$workerId."' AND `associatedUserId` = '".$userId."'")or sqlerr(__FILE__,__LINE__);
			$returnError = "Reinstated Worker";
		}


	}
}

		if($act == "Update Worker"){

			//Mysql Injection Protection
				$workerId = mysql_real_escape_string($_POST["workerId"]);
				$workernum = mysql_real_escape_string($_POST["workernum"]);
				$password = mysql_real_escape_string($_POST["password"]);

			if (isset($_POST["monitor"])) {
				$monitor = $_POST["monitor"];
			} ELSE {
				$monitor = 'no';
			}

		$prefixUsername = $userInfo->username;
		$inputUser = $prefixUsername.".".mysql_real_escape_string($_POST["workernum"]);
			//update worker
				mysql_query("UPDATE `pool_worker` SET `username` = '".$inputUser."', `password` = '".$password."', monitor = '".$monitor."' WHERE `id` = '".$workerId."' AND `associatedUserId` = '".$userId."'")or sqlerr(__FILE__,__LINE__);
		}


		if($act == "Delete Worker"){

			//Mysql Injection Protection
				$workerId = mysql_real_escape_string($_POST["workerId"]);

			//Delete worker OH NOES!
				mysql_query("UPDATE `pool_worker` SET `disabled` = 'yes' WHERE `id` = '".$workerId."' AND `associatedUserId` = '".$userId."'")or sqlerr(__FILE__,__LINE__);
		}
if (isset($_POST["act1"])) {
if($_POST["act1"] == "header") {

	if (isset($_POST["tradehillusd"])) {
		$h0 = $_POST["tradehillusd"];
	} ELSE {
		$h0 = '0';
	}

	if (isset($_POST["tradehillaud"])) {
		$h1 = $_POST["tradehillaud"];
	} ELSE {
		$h1 = '0';
	}
	if (isset($_POST["mtgoxusd"])) {
		$h2 = $_POST["mtgoxusd"];
	} ELSE {
		$h2 = '0';
	}
	if (isset($_POST["mtgoxaud"])) {
		$h3 = $_POST["mtgoxaud"];
	} ELSE {
		$h3 = '0';
	}

$header = "$h0.$h1.$h2.$h3";
mysql_query("UPDATE webUsers SET header = '".$header."' WHERE id = $userId")or sqlerr(__FILE__,__LINE__);
}

if($_POST["act1"] == "sendemail") {

	if (isset($_POST["checkemail"])) {
		$checkemail = $_POST["checkemail"];
	} ELSE {
		$checkemail = 'no';
	}

	if (isset($_POST["recivemail"])) {
		$recivemail = $_POST["recivemail"];
	} ELSE {
		$recivemail = 'no';
		$checkemail = 'no';
	}

	$updateSuccess1 = mysql_query("UPDATE webUsers SET sendemail = '$checkemail', recivemail = '$recivemail' WHERE id = $userId") or sqlerr(__FILE__, __LINE__);

		if($updateSuccess1){
		?><meta http-equiv="refresh" content="0; url=/accountdetails.php"><?
		}
	}

}

//Display Error and Good Messages(If Any)
echo "<span class=\"goodMessage\">".$goodMessage."</span>";
echo "<span class=\"returnMessage\">".$returnError."</span>";

echo"<h2>Account Details</h2>";
echo"<form action=accountdetails.php method=post id=updateDetails2>";
echo"<input type=\"hidden\" name=\"act\" value=\"updateDetails\">";
echo"<input type=\"hidden\" name=\"submit\" value=\"Update Account Settings\"/>";
echo"<table class=\"accounts_table\">";

if ($userInfo->ipcheck == "yes") {
$ipcheck = 'checked';
} ELSE {
$ipcheck = 'unchecked';
}

	echo "<tr><td>Username</td><td>$userInfo->username</td></tr>";
	echo "<tr><td>Nickname</td><td><input type=\"text\" name=\"nickname\" value=\"$userInfo->nickname\" size=\"10\" maxlength=\"10\"> Max length 10<br><font size =1>Abusing this feature can get your account banned. eg improper use or vulgar language </font></td></tr>";
	echo "<tr><td><a href=\"api.php?api_key=$userApiKey\" style=\"color: blue\" target=\"_blank\">API</a> Key: </td><td>$userApiKey</td></tr>";
	echo "<tr><td>Payment Address</td><td><input type=\"text\" name=\"paymentAddress\" value=\"$paymentAddress\" size=\"50\"></td></tr>";
	echo "<tr><td>Automatic Payout<br /></td><td valign=\"top\"><input type=\"text\" name=\"payoutThreshold\" value=\"$payoutThreshold\" size=\"3\" maxlength=\"3\" /><span class=\"small\">(0.1-50 LTC, 0 for manual)</span></td></tr>";
	echo "<tr><td>IP Check </td><td><input type=checkbox name=ipcheck value=yes $ipcheck> Tick this if <font color=red>YOU DONT</font> want your ipchecked on login. This can be a secuirty risk.</td></tr>";
	echo "<tr><td>Authorize Pin</td><td><input type=\"password\" name=\"authPin\" size=\"4\" maxlength=\"4\" /></td></tr>";
  echo "<tr><td colspan=\"2\"><input type=\"submit\" value=\"Update Account Settings\" /></td></tr>";
echo "</table></form>";
?>
<h2>Donor</h2>
<form action="/accountdetails.php" method="post">
<input type="hidden" name="act" value="donor">
<table class="accounts_table">
<?
if ($userInfo->donoragree == "yes") {
$donoragree = 'checked';
} ELSE {
$donoragree = 'unchecked';
}
if ($userInfo->donorend == "yes") {
$donorend = 'checked';
} ELSE {
$donorend = 'unchecked';
}

if ($userInfo->deadworker == "yes") {
$deadworker = 'checked';
} ELSE {
$deadworker = 'unchecked';
}

if($userInfo->donorselect > 0){
	echo"<tr><td>Donation Info</td><td>Block Time: $userInfo->donorselect Percentage: $donatePercent</td></tr>";
}
?>
	<tr><td>Donation Info</td><td><a href=donateinfo.php target=_blank>Donor info</a></td></tr>
	<tr><td>Donation %</td><td><?/*Period for <select name="donorselect"><option value="1" selected>1 Block</option><option value="2">2 Blocks</option><option value="5">5 Blocks</option><option value="10">Perm</option></select>*/?> How Much <input type="text" name="donatePercent" value="<?php echo $donatePercent;?>" size="4">%</td></tr>
<?/*	<tr><td>Agreement: </td><td><input type="checkbox" name="agreement" value="yes" <?PHP echo $donoragree ?> > Tick this is you agree to the <a href=/donor.php>Donation Policy</a></td></tr>
	<tr><td>Email End: </td><td><input type="checkbox" name="donorend" value="yes" <?PHP echo $donorend ?> > Tick this is you want a email at the end of donation peroid</td></tr>*/
	echo"<tr><td>Authorize Pin</td><td><input type=password name=authPin size=4 maxlength=4></td></tr>";
	echo"<tr><td colspan=2><input type=submit value=\"Donor Submit\"></td></tr>";

echo"</table>";
echo"</form>";

//echo"<h2>Exchange Rate Display</h2>";
//echo"<form action=/accountdetails.php method=post>";
//echo"<input type=hidden name=act1 value=header>";
//echo"<table class=accounts_table>";

//$headerQ = mysql_query("SELECT header FROM webUsers WHERE id = $userId");
//$headerA = mysql_fetch_array($headerQ);

//$split = explode(".", $headerA["header"]);
//$split0 = $split[0];
//$split1 = $split[1];
//$split2 = $split[2];
//$split3 = $split[3];

//if ($split0 == "1") {
//$header0 = 'checked';
//} ELSE {
//$header0 = 'unchecked';
//}
//if ($split1 == "2") {
//$header1 = 'checked';
//} ELSE {
//$header1 = 'unchecked';
//}
//if ($split2 == "3") {
//$header2 = 'checked';
//} ELSE {
//$header2 = 'unchecked';
//}
//if ($split3 == "4") {
//$header3 = 'checked';
//} ELSE {
//$header3 = 'unchecked';
//}

//echo"<tr><td colspan=2>Tick these boxs to show them in the header</td></tr>";
//echo"<tr><td>Tradehill (USD)</td><td><input type=checkbox name=tradehillusd value=1 $header0></td></tr>";
//echo"<tr><td>Tradehill (AUD)</td><td><input type=checkbox name=tradehillaud value=2 $header1></td></tr>";
//echo"<tr><td>MtGox (USD)</td><td><input type=checkbox name=mtgoxusd value=3 $header2></td></tr>";
//echo"<tr><td>MtGox (AUD)</td><td><input type=checkbox name=mtgoxaud value=4 $header3></td></tr>";
//echo"<tr><td colspan=2><input type=submit value=\"Submit\"></td></tr>";


echo"</table>";
echo"</form>";
?>
<h2>Cash Out</h2>
<form action="/accountdetails.php" method="post">
<input type="hidden" name="act" value="cashOut">
<table class="accounts_table">
	<tr><td colspan="2"><i>(Please note: there's a 2% LTC transaction fee for manual payouts.)</i><br/>
<i>(Auto Payouts are free and roll every hour)</i></td></tr>
	<tr><td>Account Balance</td><td><?php echo number_format( $currentBalance, 8 ); ?> LTC</td></tr>
	<tr><td>Payout To</td><td><?php echo $paymentAddress; ?></td></tr>
	<tr><td>Authorize Pin</td><td><input type="password" name="authPin" size="4" maxlength="4"></td></tr>
	<tr><td colspan="2"><input type="submit" value="Cash Out" /></td></tr>
</table>
</form>

<h2>Receive Emails</h2>
<form action="/accountdetails.php" method="post">
<input type="hidden" name="act1" value="sendemail">
<table class="accounts_table">
	<tr><td>Current Email: </td><td><?PHP echo $userEmail ?></td></tr>
	<tr><td>Receiving Emails: </td><td><?PHP echo $userInfo->sendemail ?></td></tr>
<?
if ($userInfo->sendemail == "yes") {
$checked1 = 'checked';
} ELSE {
$checked1 = 'unchecked';
}
if ($userInfo->recivemail == "yes") {
$recivemail = 'checked';
} ELSE {
$recivemail = 'unchecked';
}
?>
<tr><td>Receive Emails: </td><td><input type="checkbox" name="recivemail" value="yes" <?PHP echo $recivemail ?> > Tick this if you want to receive ANY emails from us eg announcements, payouts, updates</td></tr>
<tr><td>Payout Emails: </td><td><input type="checkbox" name="checkemail" value="yes" <?PHP echo $checked1 ?> > Tick this is you want to receive emails </td></tr>
<tr><td colspan="2"><input type="submit" value="Update Settings"></td></tr>
</form>
</table>

<h2>Change Password</h2>
<form action="/accountdetails.php" method="post"><input type="hidden" name="act" value="updatePassword">
<table class="accounts_table">
<tr><td>Current Password: </td><td><input type="password" name="currentPassword" maxlength="200"></td></tr>
<tr><td>New Password: </td><td><input type="password" name="newPassword" maxlength="200"></td></tr>
<tr><td>New Password Repeat: </td><td><input type="password" name="newPassword2" maxlength="200"></td></tr>
<tr><td>Authorize Pin: </td><td><input type="password" name="authPin" size="4"	maxlength="4"></td></tr>
<tr><td>Info</td><td>Passwords must be min 8 chars 200 max and have 1 Cap and 1 Number</td></tr>
<tr><td colspan="2"><span style="text-decoration: underline;">(You will be redirected to the login screen upon success)</span><br>
<input type="submit" value="Update Password Settings"></td></tr>
</form>
</table>
<?
echo "<h2>Delete Account</h2>";
echo "<form action=\"/accountdetails.php\" method=\"post\"><input type=\"hidden\" name=\"act\" value=\"deluser\">";
echo "<table class=\"accounts_table\">";
echo "<tr><td>Delete Me: </td><td><input type=\"checkbox\" name=\"chckuser\" value=\"yes\"> Tick this to make sure you want to delete your account</td></tr>";
echo "<tr><td>Authorize Pin: </td><td><input type=password name=authPin size=4 maxlength=4></td></tr>";
echo "<tr><td colspan=2><span style=\"text-decoration: underline;\">(Once the account has been deleted it cannot be restored.)</tr></td>";
echo "<tr><td colspan=2><input type=\"submit\" value=\"Delete Me\"></td></tr>";
echo "</form>";
echo "</table>";

//echo"<h2>Blocks Found By Your Workers</h2>";
//echo"<tr><td>Block Number</td><td>Worker Found It</td><td>Time</td></tr>";
//$getblocks = mysql_query("SELECT blockNumber, username, time FROM shares_history WHERE userId = $userId AND upstream_result = 'Y'");
//while($block = mysql_fetch_object($getblocks)){
//echo"<table class=\"accounts_table\" width=40%>";
//echo"<tr><td>$block->blockNumber</td><td>$block->username</td><td>$block->time</td></tr>";
//echo"</table>";
//}
//?>
<?php
	echo "<h2>Workers</h2>";
	echo "<table class=\"accounts_table\">";
        echo"<tr><td>Dead Worker </td><td colspan=4><input type=checkbox name=deadworker value=yes $deadworker> Tick this if you want to receive emails on dead workers</td></tr>";
	echo "<tr><td>Authorize Pin</td><td><input type=\"password\" name=\"authPin\" size=\"4\" maxlength=\"4\"/></td>";

	echo "<td><input type=\"submit\" value=\"Update Account Settings\" /></td><td><input type=\"hidden\" name=\"act\" value=\"deadworker\"></tr>";

        echo"<tr><td colspan=7>Make sure you have the workers you want monitored ticked.</td></tr>";

	echo "<tr><td>Worker Name</td><td>Worker Password</td><td>Active</td><td>Hashrate (Mhash/s)</td><td>Monitor</td><td>Update</td><td>Delete</td></tr>";
//Get list of workers from the associatedUserId
$getWorkers = mysql_query("SELECT `id`, `username`, `password`, active, hashrate, monitor, disabled FROM `pool_worker` WHERE `associatedUserId` = '".$userId."' AND disabled = 'no'");
while($worker = mysql_fetch_array($getWorkers)){
?><form action="/accountdetails.php" method="post">
<input type="hidden" name="workerId" value="<?=$worker["id"]?>"><?

if ($worker["monitor"] == "yes") {
$monitor = 'checked';
} ELSE {
$monitor = 'unchecked';
}

	//Display worker information and the forms to edit or update them

	$splitUsername = explode(".", $worker["username"]);
	$realUsername = $splitUsername[1];
	?>
	<tr>
	 <td <?php if ($worker["active"] == 0) { ?>style="color: red"<?php } ?>><?php echo $userInfo->username; ?>.<input type="text" name="workernum" value="<?php echo $realUsername; ?>" size="10"></td>
	    <td><input type="text" name="password" value="<?php echo $worker["password"]?>" size="10"></td>
	    <td><?php if ($worker["active"] == 1) echo "Y"; else echo "N"; ?>
	    <td><?php echo $worker["hashrate"]?></td>
	    <td><input type="checkbox" name="monitor" value="yes" <?PHP echo $monitor ?> ></td>
	   <td><input type="submit" name="act" value="Update Worker"><td><input type="submit" name="act" value="Delete Worker"/></td>
</td></tr></tr>

</form>
	<?php
}
?>
</table>
<br>
<table class="accounts_table">
<form action="/accountdetails.php" method="post"><input type="hidden"name="act" value="addWorker">
	<tr><td><?php echo $userInfo->username;?>.<input type="text" name="username"value="user" size="10" maxlength="20"></td>
	<td><input type="text"name="pass" value="pass" maxlength="20"></td>
	<td><input type="submit" value="Add worker"></tr></td>
</form>
</table>
<br />
<br />

<?php include ("includes/footer.php");?>
