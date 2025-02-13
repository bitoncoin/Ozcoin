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
$pageTitle = "- Admin Panel";
include ("includes/header.php");

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);

$goodMessage = "";
$returnError = "";
//Scince this is the Admin panel we'll make sure the user is logged in and "isAdmin" enabled boolean; If this is not a logged in user that is enabled as admin, redirect to a 404 error page

if(!$cookieValid || $isAdmin != 1) {
	header('Location: /');
	exit;
}

if (isset($_POST["act"]) && isset($_POST["authPin"]))
{

if (isset($_POST["authPin"])) {
$inputAuthPin = hash("sha256", $_POST["authPin"].$salt);
} ELSE {
$inputAuthPin = NULL;
}
	//Make sure an authPin is set and valid when $act is active
	if($_POST["act"] && $authPin == $inputAuthPin){
		//Update information if needed
		if($_POST["act"] == "UpdateMainPageSettings"){
			try {
				$settings->setsetting("sitepayoutaddress", mysql_real_escape_string($_POST["paymentAddress"]));
				$settings->setsetting("sitepercent", mysql_real_escape_string($_POST["percentageFee"]));
				$settings->setsetting("websitename", mysql_real_escape_string($_POST["headerTitle"]));
				$settings->setsetting("pagetitle", mysql_real_escape_string($_POST["pageTitle"]));
				$settings->setsetting("slogan", mysql_real_escape_string($_POST["headerSlogan"]));
				$settings->setsetting("siterewardtype", mysql_real_escape_string($_POST["rewardType"]));
				$settings->loadsettings(); //refresh settings
				$goodMessage = "Successfully updated general settings";
			} catch (Exception $e) {
				$returnError = "Database Failed - General settings was not updated";
			}
		}
	} else if($_POST["act"] && $authPin != $inputAuthPin){
		$returnError = "Authorization Pin # - Invalid";
	}
}

//Display Error and Good Messages(If Any)
echo "<span class=\"goodMessage\">".antiXss($goodMessage)."</span><br/>";
echo "<span class=\"returnMessage\">".antiXss($returnError)."</span>";
?>
<div id="AdminContainer">
	<h1 style="text-decoration:underline;">Welcome back admin</h1><br/>
	<h3>General Settings</h3>
	<hr size="1" width="80%"></hr>
	<!--Begin main page edits-->
	<form action="/adminPanel.php" method="post">
		<input type="hidden" name="act" value="UpdateMainPageSettings">
		Page Title <input type="text" name="pageTitle" value="<?php echo antiXss($settings->getsetting("pagetitle"));?>"><br/>
		Header Title <input type="text" name="headerTitle" value="<?php echo antiXss($settings->getsetting("websitename"));?>"><br/>
		Header Slogan <input type="text" name="headerSlogan" value="<?php echo antiXss($settings->getsetting("slogan"));?>"><br/>
		Percentage Fee <input type="text" name="percentageFee" size="10" maxlength="10" value="<?php echo antiXss($settings->getsetting("sitepercent")); ?>">%<br/>
		Fee Address <input type="text" name="paymentAddress" size="60" value="<?php echo antiXss($settings->getsetting("sitepayoutaddress"));?>"><br/>
		Default Reward Type <select name="rewardType">
		<option value="0" <?php if ($settings->getsetting("siterewardtype") == 0) echo "selected"; ?>>Cheat Proof Score</option>
		<option value="1" <?php if ($settings->getsetting("siterewardtype") == 1) echo "selected"; ?>>Proportional</option>
		</select>
		<br/><br/>
		Authorization Pin <input type="password" size="4" maxlength="4" name="authPin"><br/>
		<input type="submit" value="Update Main Page Settings">
	</form>
	<br/><br/>
	<h3>Backend</h3>
	<hr size="1" width="80%"></hr>
<?

$cronjobq = mysql_query("SELECT locked from locks WHERE name = 'shares'")or sqlerr(__FILE__, __LINE__);
$cronjoba = mysql_fetch_object($cronjobq);
$cronjob = $cronjoba->locked;
if($cronjob == '0') {
	echo "<a href=/cronjobs/cronjob.php>Run Cronjob</a><br/>";
} ELSE {
	echo "Cronjob is running<br/>";
}

if($cronjob == '0') {
	echo "<a href=/cronjobs/payout.php>Run Payout</a><br/>";
} ELSE {
	echo "Payout is running<br/>";
}

$hashrateq = mysql_query("SELECT locked from locks WHERE name = 'hashrate.php'")or sqlerr(__FILE__, __LINE__);
$hashratea = mysql_fetch_object($hashrateq);
$hashrate = $hashratea->locked;
if($hashrate == '0') {
	echo "<a href=/cronjobs/hashrate.php>Run Hashrate</a><br/>";
} ELSE {
	echo "Hashrate is running<br/>";
}

$workersq = mysql_query("SELECT locked from locks WHERE name = 'workers.php'")or sqlerr(__FILE__, __LINE__);
$workersa = mysql_fetch_object($workersq);
$workers = $workersa->locked;
if($workers == '0') {
	echo "<a href=/cronjobs/workers.php>Run Workers</a><br/>";
} ELSE {
	echo "Workers is running<br/>";
}

$workersq = mysql_query("SELECT locked from locks WHERE name = 'shares'")or sqlerr(__FILE__, __LINE__);
$workersa = mysql_fetch_object($workersq);
$workers = $workersa->locked;
if($workers == '0') {
	echo "<a href=/cronjobs/shares.php>Run Shares</a><br/>";
} ELSE {
	echo "Shares is running<br/>";
}

$idleq = mysql_query("SELECT locked from locks WHERE name = 'idleworkers.php'")or sqlerr(__FILE__, __LINE__);
$idlea = mysql_fetch_object($idleq);
$idle = $idlea->locked;
if($idle == '0') {
	echo "<a href=/cronjobs/idleworkers.php>Run Idle Workers</a><br/>";
} ELSE {
	echo "Idle Workers is running<br/>";
}
	

?>
	<br/>
	<h3>Info</h3>
	<hr size="1" width="80%"></hr>

	<?

	$sitewallet = mysql_query("SELECT sum(balance) FROM `accountBalance` WHERE `balance` > 0") or sqlerr(__FILE__, __LINE__);
	$sitewalletq = mysql_fetch_row($sitewallet);
	$usersbalance = $sitewalletq[0];
	$balance = $bitcoinController->query("getbalance");
	$total = $balance - $usersbalance;

	echo "Block Number: ".$bitcoinController->getblocknumber()."<br>";
	echo "Difficulty: ".$bitcoinController->query("getdifficulty")."<br>";
	echo "Wallet Balance: ".$balance."<br>";
	echo "UnPaid: ".$usersbalance."<br>";
	echo "Total Left: <font color=red>$total</font><br>";

	echo "<a href=/wallet.php>Everything Wallet</a><br/>";

?>
	<br><h3>News Control</h3>
	<hr size="1" width="80%"></hr>
<?
$getnews = mysql_query("SELECT id, `timestamp`, `title`, `message` FROM `news` ORDER BY `timestamp` DESC LIMIT 3") or sqlerr(__FILE__, __LINE__);
while($news = mysql_fetch_array($getnews)){
	echo"$news[title] <form action=\"/news.php\" method=\"post\"><input type=\"hidden\" name=\"id\" value=\"$news[id]\"><input type=\"hidden\" name=\"action\" value=\"update\"><input type=\"submit\" value=\"Update News\"></form><br>";
}
?>
	<form action="/news.php" method="post"><input type="hidden" name="action" value="new"><input type="submit" value="New News"></form>
	<br/><br/>
	<h3>Users Control</h3>
	<hr size="1" width="80%"></hr>
	<a href=users.php style="color: blue">Show Users</a>
	<br /><br />
	<h3>Onion List - User list by Stales (Debug tool)</h3>
	<hr size="1" width="80%"></hr>
	<a href="onion_tool.php">Onion Tool</a>
</div>

<?include ("includes/footer.php");?>