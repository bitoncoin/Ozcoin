<?php
//DELETE
error_reporting(E_ALL);
ini_set('display_errors', '1');

if(!$cookieValid){
//No valid cookie show login//
?>
	<!--Login Input Field-->
	<div id="leftsidebar">
	<form action="/login.php" method="post" id="loginForm">
	Login:<br>
	<a class="fancy_button login_button" href="javascript:$('#loginForm').submit();"><span style="background-color: #070;">Login</span></a>
	<input type="text" name="username" id="input_username" onclick="javascript:LoginUsernameFocus();" onkeypress="javascript:CheckSubmit();" onfocus="this.select()" onblur="this.value=!this.value?'Username':this.value;" value="Username" size="15" maxlength="20">
	<input type="password" name="password" id="input_password" onclick="javascript:LoginPasswordFocus();" onkeypress="javascript:CheckSubmit(event);" onfocus="this.select()" onblur="this.value=!this.value?'password':this.value;" value="password" size="15" maxlength="200">
	</form>
	<br>
	<a class="fancy_button" href="lostpassword.php"><span style="background-color: #070;">Lost Password</span></a>
	</div>
<?php
}else if($cookieValid){
//Valid cookie YES! Show this user stats//
?>
<div id="leftsidebar">
<?php
echo "<div id=\"sidebardata\">";
if($cookieValid){

	echo "Welcome Back, <i><b>".$userInfo->username."</b></i><br/><hr size='1' width='100%'>";
	echo "<table class=\"left_stats_table\">";
	echo "<tr><td>Current Hashrate</td><td>" . number_format( $currentUserHashrate ) . "&nbsp;KH/s</td></tr>";
	echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
	echo "<tr><td>Lifetime Shares</td><td>" . number_format( $lifetimeUserShares ) . "</td></tr>";
	echo "<tr><td>Lifetime Stales</td><td>" . number_format( $lifetimeUserInvalidShares ) . "</td></tr>";

	$total_stale_rate = 0;
	if( $lifetimeUserInvalidShares > 0 && $lifetimeUserShares > 0 )
	{
		$total_stale_rate = ($lifetimeUserInvalidShares / $lifetimeUserShares) * 100;
	}

	echo "<tr><td>Total Stale Rate</td><td>" . number_format( $total_stale_rate, 3 ) . "%</td></tr>";
	echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
	echo "<tr><td>Round Shares</td><td>" . number_format( $totalOverallShares ) . "&nbsp;shares</td></tr>";
	echo "<tr><td>Valid This Round</td><td>" . number_format( $totalUserShares ) . "&nbsp;shares</td></tr>";

	$shares_in_round = 0;
	if( $totalOverallShares > 0 && $totalUserShares > 0 )
	{
		$shares_in_round = ($totalUserShares / $totalOverallShares) * 100;
	}

	echo "<tr><td>Share Of Round</td><td>" . number_format( $shares_in_round, 3 ) . "%</td></tr>";
	echo "<tr><td>Est. Earnings</td><td>" . sprintf("%.8f", ($totalUserShares/$totalOverallShares)*50) . "&nbsp;LTC</td></tr>";
	echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
	echo "<tr><td>Current Balance</td><td>" .number_format( $currentBalance, 8 ) . "&nbsp;LTC</td></tr>";

//	$result = mysql_query( "SELECT sum(balance) as amount_earned  FROM accountBalanceHistory WHERE userid = '" . $userInfo->id . "'" );
	$result = mysql_query( "SELECT sum(amount) as amount_earned  FROM rounddetails WHERE rewarded = 'Y' and userid = '" . $userInfo->id . "'" );
	if ($resultrow = mysql_fetch_object($result))
	{
		echo "<tr><td>Total Earned</td><td>" . number_format( $resultrow->amount_earned, 8 ) . "&nbsp;LTC</td></tr>";
	}

	echo "</table>";
	//edit by Ryan Shaw (ryannathans)
	echo "<table class=\"left_stats_table\">";
	if(is_numeric($settings->getsetting('statstime'))) {
		$lastupdatedtime = time() - $settings->getsetting('statstime');
		$lastupdatedtimei = date("i", $lastupdatedtime);
		$lastupdatedtimei = $lastupdatedtimei - 0;
		$lastupdatedtimes = date("s", $lastupdatedtime) - 0;

		echo "<tr>";

		if($lastupdatedtimei == '01') {
			echo '<td>Last Updated</td><td>1 minute ago</td>';
		}
		elseif($lastupdatedtimei == '00') {
			echo '<td>Last Updated</td><td>' . $lastupdatedtimes . ' seconds ago</td>';
		}
		else {
			echo '<td>Last Updated</td><td>' . $lastupdatedtimei . ' minutes ago</td>';
		}

		echo "</tr>";

		$nextupdatetime = 5 - $lastupdatedtimei;

		echo "<tr>";

		if($nextupdatetime == '1') {
			echo '<td>Next Update In</td><td>' . $nextupdatetime . ' minute</td>';
		}
		elseif($nextupdatetime < 0) {

			$nextupdatetime = abs($nextupdatetime);

			if($nextupdatetime < 1) {
				echo '<td colspan="2">Server is ' . date("s", mktime(0, $nextupdatetime)) . ' seconds overdue refreshing</td>';
			}
			if($nextupdatetime == 1) {
				echo '<td colspan="2">Server is ' . $nextupdatetime . ' minute overdue refreshing</td>';
			}
			if($nextupdatetime > 1) {
				echo '<td colspan="2">Server is ' . $nextupdatetime . ' minutes overdue refreshing</td>';
			}
		}
		else {
			echo '<td>Next Update In</td><td>' . $nextupdatetime . ' minutes</td>';
		}

		echo "</tr>";
	}
	else {
		echo '<tr><td>EPIC FAIL<br>statstime entry in database was not numeric<br>update times can not function without it</td></tr>';
	}

	echo "</table>";
	echo"<a class=\"fancy_button top_spacing\" href=\"my_stats.php\">
	<span style=\"background-color: #070;\">My Stats</span>
	</a>
	<a class=\"fancy_button top_spacing left_spacing\" href=\"logout.php\">
	<span style=\"background-color: #070;\">Logout</span>
	</a>";
mysql_query("UPDATE webUsers SET lastseen = '" . get_date_time() . "' WHERE id = $userId") or sqlerr(__FILE__, __LINE__);
}
else
{
	include('login.php');
}
echo "</div>";
?>
</div>
<?php
}
?>

