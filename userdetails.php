<?PHP
$pageTitle = "- User Details";
include ("includes/header.php");
	
$goodMessage = "";
$returnError = "";
//Scince this is the Admin panel we'll make sure the user is logged in and "isAdmin" enabled boolean; If this is not a logged in user that is enabled as admin, redirect to a 404 error page

if(!$cookieValid || $isAdmin != 1) {
	header('Location: /');
	exit;
}

if (isset($_GET["id"])) {
$id = $_GET['id'];
} ELSE {
$id = NULL;
}

if ($id == NULL) {
echo "User not found";
}

$act = NULL;
if (isset($_POST["act"])) {
	$act = $_POST["act"];

if($act == "updatePassword"){
//Update password
$password = $_POST["password"];

//Update hashed password
$password = hash("sha256", $password.$salt);
mysql_query("UPDATE `webUsers` SET `pass` = '".$password."' WHERE id = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
}

if($act == "updatepin"){
//Update password
$authPin = $_POST["authPin"];

//Update hashed authpin
$authPin = hash("sha256", $authPin.$salt);
mysql_query("UPDATE `webUsers` SET `pin` = '".$authPin."' WHERE `id` = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
}

if($act == "unban"){
mysql_query("UPDATE `webUsers` SET `accountFailedAttempts` = '0' WHERE `id` = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
}

if($act == "deluser"){
mysql_query("UPDATE webUsers SET ad = 'yes', sendemail = 'no' WHERE `id` = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
}

}

$search = mysql_query("SELECT * FROM webUsers WHERE id = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_object($search);

if ($user->nickname == NULL) {
$usernamet = $user->username;
} ELSE {
$usernamet = $user->nickname;
}


echo "<br><h2>User details for $usernamet</h2>";
echo "<table class=accounts_table>";
if($user->admin == '1') {
$admin = "<font color=green>Yes</font>";
} ELSE {
$admin = "<font color=red>No</font>";
}
echo "<tr><td>Admin: </td><td>$admin</td></tr>";
echo "<tr><td>Show Name: </td><td>$usernamet</td></tr>";
if($isAdmin == '1') {
echo "<tr><td>Username: </td><td>$user->username</td></tr>";
echo "<tr><td>Nickname: </td><td>$user->nickname</td></tr>";
echo "<tr><td>Email: </td><td>$user->email</td></tr>";
echo "<tr><td>Logged IP: </td><td>$user->loggedIp</td></tr>";
}
echo "<tr><td>Join Date: </td><td>$user->joindate</td></tr>";
echo "<tr><td>Last Access: </td><td>$user->lastseen</td></tr>";
echo "<tr><td>Share Count: </td><td>$user->share_count</td></tr>";
echo "<tr><td>Stale Shares: </td><td>$user->stale_share_count</td></tr>";
echo "<tr><td>Round Shares: </td><td>$user->shares_this_round</td></tr>";
if($isAdmin == '1') {
echo "<tr><td colspan=2><hr size=\"1\" width=\"100%\"></hr></td></tr>";
echo "<form action=\"/userdetails.php?id=$id\" method=\"post\"><input type=\"hidden\" name=\"act\" value=\"updatePassword\">";
echo "<tr><td>Password: </td><td><input type=\"text\" name=\"password\" size=\"40\"></td></tr>";
echo "<tr><td colspan=2><input type=\"submit\" value=\"Update Password\"></td></tr>";
echo "<tr><td colspan=2><hr size=\"1\" width=\"100%\"></hr></td></tr>";
echo "</form>";
echo "<form action=\"/userdetails.php?id=$id\" method=\"post\"><input type=\"hidden\" name=\"act\" value=\"updatepin\">";
echo "<tr><td>Authorize Pin: </td><td><input type=\"text\" name=\"authPin\" size=\"4\" maxlength=\"4\"></td></tr>";
echo "<tr><td colspan=2><input type=\"submit\" value=\"Update Pin\"></td></tr>";
echo "<tr><td colspan=2><hr size=\"1\" width=\"100%\"></hr></td></tr>";
echo "</form>";
echo "<form action=\"/userdetails.php?id=$id\" method=\"post\"><input type=\"hidden\" name=\"act\" value=\"unban\">";
echo "<tr><td>Failure: </td><td>$user->accountFailedAttempts</td></tr>";
echo "<tr><td colspan=2><input type=\"submit\" value=\"Unban\"></td></tr>";
echo "<tr><td colspan=2><hr size=\"1\" width=\"100%\"></hr></td></tr>";
echo "</form>";
echo "<form action=\"/userdetails.php?id=$id\" method=\"post\"><input type=\"hidden\" name=\"act\" value=\"deluser\">";
echo "<tr><td colspan=2><input type=\"submit\" value=\"Delete User\"></td></tr>";
echo "<tr><td colspan=2><hr size=\"1\" width=\"100%\"></hr></td></tr>";
echo "</form>";
echo "</table>";

$accountBalanceQ = mysql_query("SELECT * FROM accountBalance WHERE userId = ".sqlesc($id)."") or die(mysql_error());
$accountBalanceHistoryQ = mysql_query("SELECT * FROM accountBalanceHistory WHERE userId = ".sqlesc($id)." ORDER BY timestamp DESC") or die(mysql_error());

$accountBalance = mysql_fetch_object($accountBalanceQ);
$accountBalanceHistory = array();

$addresses = array();
if (!empty($accountBalance->sendAddress))
	$addresses[] = $accountBalance->sendAddress;

while ($r = mysql_fetch_object($accountBalanceHistoryQ)) {
	$accountBalanceHistory[] = $r;
	
	if (!in_array($r->sendAddress, $addresses) && !empty($r->sendAddress))
		$addresses[] = $r->sendAddress;
}
}
if($isAdmin == '1') {
echo"<br>";
echo"<h3>Account Info</h3>";
echo"<br>";
echo"<h4>Current Account</h4>";
echo"<dl class=datalist>";
	echo"<dt>Balance</dt>";
	echo"<dd>$accountBalance->balance</dd>";
	echo"<dt>Current Send Address:</dt>";
	echo"<dd>$accountBalance->sendAddress</dd>";
	echo"<dt>Previous Send Addresses:</dt>";
	echo"<dd>";
		foreach ($addresses as $a):
			if ($a != $accountBalance->sendAddress);
			echo"$a <br>";
		endforeach;
	echo"</dd>";
	echo"<dt>Threshold:</dt>";
	echo"<dd>$accountBalance->threshold</dd>";
	echo"<dt>Wallet Transactions:</dt>";
	echo"<dd><a href=/wallet.php?filter='".implode('+', $addresses)."' target=_blank>Search</a></dd>";
echo"</dl>";
echo"<br>";
echo"<h4>History</h4>";
echo"<table class=datatable>";
	echo"<thead>";
		echo"<tr>";
			echo"<th>Date</th>";
			echo"<th>Balance</th>";
			echo"<th>Threshold</th>";
			echo"<th>Send Address</th>";
		echo"</tr>";
	echo"</thead>";
	echo"<tbody>";
		foreach ($accountBalanceHistory as $abh):
		echo"<tr>";
			echo"<td>$abh->timestamp</td>";
			echo"<td>$abh->balance</td>";
			echo"<td>$abh->threshold</td>";
			echo"<td>$abh->sendAddress</td>";
		echo"</tr>";
		endforeach;
	echo"</tbody>";
echo"</table>";
}
echo"</table><br>";
include ("includes/footer.php");
?>