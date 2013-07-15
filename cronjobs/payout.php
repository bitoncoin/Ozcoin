<?php
//    Copyright (C) 2011  Mike Allison <dj.mikeallison@gmail.com>
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.

// 	  BTC Donations: 163Pv9cUDJTNUbadV4HMRQSSj3ipwLURRc

$includeDirectory = "/var/www/includes/";

include($includeDirectory."requiredFunctions.php");

//Verify source of cron job request
if (isset($cronRemoteIP) && $_SERVER['REMOTE_ADDR'] !== $cronRemoteIP) {
 die(header("Location: /"));
}

lock("money.php");

//mysql_query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE');
//mysql_query('BEGIN');
	
/////////Pay users who have reached their threshold payout
$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);
$resultQ = mysql_query("select userId, cast(balance as decimal(16,8)) as balance, IFNULL(paid, 0) as paid, IFNULL(sendAddress, '') as sendAddress FROM accountBalance WHERE CAST(threshold AS decimal(16,8)) >= 0.1 AND CAST(balance AS decimal(16,8)) >= CAST(threshold AS decimal(16,8))");
while ($resultR = mysql_fetch_object($resultQ)) {
	$currentBalance = $resultR->balance;
	$paid = $resultR->paid;
	$paymentAddress = $resultR->sendAddress;
	$userId = $resultR->userId;
//echo "Payment: $paid , User: $userId , Current Balance: $currentBalance<br>";
	if ($paymentAddress != '')
	{
		$isValidAddress = $bitcoinController->validateaddress($paymentAddress);
		if($isValidAddress){
			//Subtract TX feee
			//$currentBalance = $currentBalance - 0.01;
			//Send money//
			if($bitcoinController->sendtoaddress($paymentAddress, $currentBalance)) {				
				//Reduce balance amount to zero
				mysql_query("UPDATE `accountBalance` SET balance = '0', paid = '".$paid."' WHERE `userId` = '".$userId."'");

$userInfoQ = mysql_query("SELECT id,username, email, sendemail, emailAuthPin, recivemail FROM webUsers WHERE id = '".$resultR->userId."'");
$userInfo = mysql_fetch_object($userInfoQ);

if ($userInfo->sendemail == 'yes' && $userInfo->recivemail == 'yes') {
$id = $userInfo->id;
$to = "$userInfo->email";
$pin = "$userInfo->emailAuthPin";
$subject = "OzCoin Payout";

$msg = "
<html>
<head>
<title>Ozcoin's Payout</title>
</head>
<body>
Hello, $userInfo->username<br />
<br />
A block payout has just finished and you have been paid<br />
Paid: $currentBalance LTC<br />
Litecoin Address: $paymentAddress<br />
<br />
Thanks for using Ozcoin<br />
<img src=\"https://ozco.in/images/ozcoin.png\" alt=\"ozcoin site\"/><br>
To Unsubscribe from getting emails please <a href=https://ozco.in/unsub.php?id=$id&pin=$pin>click here</a>
</body></html>";

$from_email = "noreply@ozco.in"; //site email
$headers = "From: $from_email\n";
$headers .= 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";

mail($to, $subject, $msg, $headers);
}
			}
		}
	}
}
//mysql_query('COMMIT);


?>
