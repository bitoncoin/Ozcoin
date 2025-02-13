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
//
//    Improved Stats written by Tom Lightspeed (tomlightspeed@gmail.com + http://facebook.com/tomlightspeed)
//    Developed Socially for http://ozco.in
//    If you liked my work, want changes/etc please contact me or donate 16p56JHwLna29dFhTRcTAurj4Zc2eScxTD.
//    Special thanks to Wayno, Graet & Ycros from #ozcoin on freenode.net for their help :-)
//    Additional thanks to Krany from #ozcoin on freenode.net.
//    May the force be with you.

$pageTitle = "- Stats";
include ("includes/header.php");

$numberResults = 30;
$last_no_blocks_found = 5;

$onion_winners = 10;

$bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);

$difficulty = $bitcoinController->query("getdifficulty");
//time = difficulty * 2**32 / hashrate
// hashrate is in Mhash/s

function CalculateTimePerBlock($btc_difficulty, $_hashrate){
	if ($btc_difficulty > 0 && $_hashrate > 0) {
		$find_time_hours = ((($btc_difficulty * bcpow(2,32)) / ($_hashrate * bcpow(10,6))) / 3600);
	} else {
		$find_time_hours = 0;
	}
	return $find_time_hours;
}

function CoinsPerDay ($time_per_block, $btc_block) {
	if($time_per_block > 0 && $btc_block > 0) {
		$coins_per_day = (24 / $time_per_block) * $btc_block;
	} else {
		$coins_per_day = 0;
	}
	return $coins_per_day;
}
?>

<div id="stats_wrap">
<?php
if (!$cookieValid){
	echo "<div id=\"new_user_message\"><p>Welcome to <a href=\"/\">Simplecoin.us</a>! Please login or <a href=\"register.php\">join us</a> to get detailed stats and graphs relating to your hashing!</p></div>";
}
?>
<div id="stats_members">
	<table class="stats_table member_width">
		<tr><th colspan="4" scope="col">Top <?php echo $numberResults;?> Hashrates</th></tr>
		<tr><th scope="col">Rank</th><th scope="col">User Name</th><th scope="col">KH/s</th><th scope="col">LTC/Day</th></tr>
<?php

// TOP 30 CURRENT HASHRATES  *************************************************************************************************************************

$result = $stats->userhashrates();
$rank = 1;
$user_found = false;

foreach ($result as $username => $user_hash_rate) {
	//$username = $resultrow->username;
	if ($cookieValid && $username == $userInfo->username) {
		echo "<tr class=\"user_position\">";
		$user_found = true;
	} else {
	echo "<tr class=\"d" . ($rank & 1) . "\">";
	}
	echo "<td>".$rank;

	if ($rank == 1) {
		echo "&nbsp;<img src=\"/images/crown.png\" />";
	}

	//$user_hash_rate = $resultrow->hashrate;
	echo "</td><td>".$username."</td><td>".number_format($user_hash_rate)."</td><td>&nbsp;";
	$time_per_block = CalculateTimePerBlock($difficulty, $user_hash_rate);
	$coins_day = CoinsPerDay($time_per_block, $bonusCoins)/1000;
	echo number_format( $coins_day, 3 );
	echo "</td></tr>";
	if ($rank == 30)
		break;
	$rank++;
}

if ($cookieValid && $user_found == false) {
	$rank = $stats->userrankhash($userInfo->id);	
	$user_hashrate = $stats->userhashrate($userInfo->username);
	echo "<tr class=\"user_position\"><td>" . $rank . "</td><td>" . $userInfo->username . "</td><td>" . number_format( $user_hashrate ) . "</td><td>";
	//$time_per_block = CalculateTimePerBlock($difficulty, $user_hashrate);
	//$coins_day = CoinsPerDay($time_per_block, $bonusCoins);
	$time_per_block = CalculateTimePerBlock($difficulty, $user_hash_rate);
	$coins_day = CoinsPerDay($time_per_block, $bonusCoins)/1000;
	echo number_format($coins_day, 3) . "</td></tr>";
}
echo "</table><br/><br/>";

echo "</div>";
?>
<div id="stats_lifetime">
	<table class="stats_table member_width">
		<tr><th colspan="3" scope="col">Top <?php echo $numberResults;?> Lifetime Shares</th></tr>
		<tr><th scope="col">Rank</th><th scope="col">User Name</th><th scope="col">Shares</th></tr>
<?php

// TOP 30 LIFETIME SHARES  *************************************************************************************************************************

$result = $stats->userssharecount($numberResults);
$rank = 1;
$user_found = false;

foreach ($result as $username => $shares) {
	if ($cookieValid && $username == $userInfo->username) {
		echo "<tr class=\"user_position\">";
		$user_found = true;
	} else {
		echo "<tr class=\"d" . ($rank & 1) . "\">";
	}

	echo "<td>" . $rank;
	
	if ($rank == 1) 
		echo "&nbsp;<img src=\"/images/crown.png\" />";
	
	echo "</td><td>".$username."</td><td>" . number_format($shares) . "</td></tr>";
	$rank++;
}

if ($cookieValid && $user_found == false) {
	$rank_shares = $stats->userrankshares($userInfo->id);
	if (count($rank_shares) > 0)  
		echo "<tr class=\"user_position\"><td>".$rank_shares[0]."</td><td>" . $userInfo->username . "</td><td>".number_format($rank_shares[1])."</td></tr>";	
}
?>
	</table>
</div>
<div id="stats_server">

<?php
// START SERVER STATS *************************************************************************************************************************

$show_hashrate = $settings->getsetting('currenthashrate');
$current_block_no = $bitcoinController->getblocknumber();
$show_difficulty = round($difficulty, 2);

echo "<table class=\"stats_table server_width\">";
echo "<tr><th colspan=\"2\" scope=\"col\">Server Stats</td></tr>";
echo "<tr class=\"d0\"><td class=\"leftheader\">Pool Hash Rate</td><td>". number_format($show_hashrate/1000, 3) . " MHashes/s</td></tr>";
echo "<tr class=\"d1\"><td class=\"leftheader\">Pool Efficiency</td><td><span class=\"green\">". number_format($stats->poolefficiency(), 2) . "%</span></td></tr>";

$res = $stats->userhashrates();
$hashcount = 0;
foreach ($res as $hash)
	if ($hash > 0) 
		$hashcount++;

echo "<tr class=\"d0\"><td class=\"leftheader\">Current Users Mining</td><td>" . number_format($hashcount) . "</td></tr>";
echo "<tr class=\"d1\"><td class=\"leftheader\">Current Total Miners</td><td>" . number_format($stats->currentworkers()) . "</td></tr>";
echo "<tr class=\"d0\"><td class=\"leftheader\">Current Block</td><td><a href=\"http://blockexplorer.com/b/" . $current_block_no . "\">";
echo number_format($current_block_no) . "</a></td></tr>";
echo "<tr class=\"d1\"><td class=\"leftheader\">Current Difficulty</th><td>$difficulty</a></td></tr>";

$lastblocks = $stats->lastwinningblocks($last_no_blocks_found);

$show_time_since_found = false;
$time_last_found;

if (count($lastblocks) > 0) {
	$found_block_no = $lastblocks[0][1];
	$confirm_no = $lastblocks[0][2];

	echo "<tr class=\"d0\"><td class=\"leftheader\">Last Block Found</td><td><a href=\"http://blockexplorer.com/b/" . $found_block_no . "\">" . number_format($found_block_no) . "</a></td></tr>";

	$time_last_found = $lastblocks[0][3];

	$show_time_since_found = true;
}

$time_to_find = CalculateTimePerBlock($difficulty, $stats->currenthashrate());
// change 25.75 hours to 25:45 hours
$intpart = floor( $time_to_find );
$fraction = $time_to_find - $intpart; // results in 0.75
$minutes = number_format(($fraction * 60 ),0);

echo "<tr class=\"d1\"><td class=\"leftheader\">Est. Time To Find Block</td><td>" . number_format($time_to_find,0) . " Hours " . $minutes . " Minutes</td></tr>";

$now = new DateTime( "now" );
if (isset($time_last_found))
	$hours_diff = ($now->getTimestamp() - $time_last_found) / 3600;
else 
	$hours_diff = 0;
	
if( $hours_diff < $time_to_find )
{
	$time_last_found_out = "<span class=\"green\">";
}
elseif( ( $hours_diff * 2 ) > $time_to_find )
{
	$time_last_found_out = "<span class=\"red\">";
}
else
{
	$time_last_found_out = "<span class=\"orange\">";
}

$time_last_found_out = $time_last_found_out . floor( $hours_diff ). " Hours " . $hours_diff*60%60 . " Minutes</span>";

echo "<tr class=\"d0\"><td class=\"leftheader\">Time Since Last Block</td><td>" . $time_last_found_out . "</td></tr>";

echo "</table>";

// SHOW LAST (=$last_no_blocks_found) BLOCKS  *************************************************************************************************************************

echo "<table class=\"stats_table server_width top_spacing\">";
echo "<tr><th scope=\"col\" colspan=\"4\">Last $last_no_blocks_found Blocks Found - <a href=\"blocks.php\">All Blocks Found</a></th></tr>";
echo "<tr><th scope=\"col\">Block</th><th scope=\"col\">Confirms</th><th scope=\"col\">Finder</th><th scope=\"col\">Time</th></tr>";
$i = 0;
foreach ($lastblocks as $resultrow) {
  echo "<tr class=\"d" . ($i & 1) . "\">";
	$splitUsername = explode(".", $resultrow[0]);
	$realUsername = $splitUsername[0];

	
	$confirms = $resultrow[2];

	if ($confirms > 119) {
		$confirms = "Done";
	}

	$block_no = $resultrow[1];

	echo "<td><a href=http://abe.liteco.in/chain/Litecoin/b/$block_no>" . number_format($block_no) . "</a></td>";
	echo "<td>" . $confirms . "</td>";
	echo "<td>$realUsername</td>";
	echo "<td>".strftime("%F %r",$resultrow[3])."</td>";
	echo "</tr>";
  $i++;
}

echo "</table>";

// SERVER BLOCKS/TIME GRAPH *************************************************************************************************************************
// http://www.filamentgroup.com/lab/update_to_jquery_visualize_accessible_charts_with_html5_from_designing_with/
// table is hidden, graph follows

echo "<table id=\"blocks_over_week\" class=\"hide\">";
echo "<caption>Blocks Found Over Last Week</caption>";
echo "<thead><tr><td></td>";

// get last 7 days of blocks, confirms over 0
$query = "SELECT sum(no_blocks) as blocks_found, DATE_FORMAT(date, '%b %e') as date from 
		(SELECT COUNT(n.blockNumber) as no_blocks, CAST(FROM_UNIXTIME(n.timestamp) as date) as date
		FROM networkBlocks n, winning_shares w
		WHERE n.blockNumber = w. blockNumber AND w.confirms > 0
			AND CAST(FROM_UNIXTIME(n.timestamp) as DATE) > DATE_SUB(now(), INTERVAL 6 DAY)        	
		GROUP BY DAY(FROM_UNIXTIME(n.timestamp))
		UNION
		SELECT 0, CAST(FROM_UNIXTIME(timestamp) as DATE) as date
		FROM networkBlocks
		WHERE CAST(FROM_UNIXTIME(timestamp) as DATE) > DATE_SUB(CURDATE(), INTERVAL 6 DAY)        	
		GROUP BY DAY(FROM_UNIXTIME(timestamp))
		) as blah group by date order by blah.date ASC";
$result = mysql_query_cache($query);

foreach ($result as $resultrow) {
	echo "<th scope=\"col\">" . $resultrow->date . "</th>";
}

echo "</thead><tbody><tr><th scope=\"row\">Ozocin LTC POOL</th>";

// re-iterate through results
//mysql_data_seek($result, 0);

foreach ($result as $resultrow) {
	echo "<td>" . $resultrow->blocks_found . "</td>";
}

echo "</tbody></table>";

echo "</div>";


echo "<div class=\"clear\"></div></div>";

include("includes/footer.php");

?>