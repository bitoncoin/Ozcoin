<?php

/**
 * left side bar ajax get page
 *
 * @version $Id$
 * @copyright 2011
 */

//DELETE
error_reporting(E_ALL);
ini_set('display_errors', '1');

include("requiredFunctions.php");
include("universalChecklogin.php");

if($cookieValid){
<?php
	// START SERVER STATS *************************************************************************************************************************

	echo "<table class=\"stats_table server_width\">";

	echo "<tr><th colspan=\"2\" scope=\"col\">Server Stats</td></tr>";

	$hashrate = $settings->getsetting('currenthashrate');
	$show_hashrate = round($hashrate / 1000,3);

	echo "<tr class=\"d0\"><th class=\"leftheader server_col_width\">Pool Hash Rate</th><td>". number_format($show_hashrate, 3) . " Ghashes/s</td></tr>";

	$results = mysql_query("SELECT (1 - (SUM(stale_share_count)/SUM(share_count))) * 100 AS efficiency FROM webUsers") or sqlerr(__FILE__, __LINE__);
	$row = mysql_fetch_object($results);

	echo "<tr class=\"d1\"><th class=\"leftheader\">Pool Efficiency</th><td><span class=\"green\">". number_format($row->efficiency, 2) . "%</span></td></tr>";

	$res = mysql_query("SELECT count(webUsers.id) FROM webUsers WHERE hashrate > 0") or sqlerr(__FILE__, __LINE__);
	$row = mysql_fetch_array($res);
	$users = $row[0];

	echo "<tr class=\"d0\"><th class=\"leftheader\">Current Users Mining</th><td>" . number_format($users) . "</td></tr>";
	echo "<tr class=\"d1\"><th class=\"leftheader\">Current Total Miners</th><td>" . number_format($settings->getsetting('currentworkers')) . "</td></tr>";

	$current_block_no = $bitcoinController->query("getblocknumber");

	echo "<tr class=\"d0\"><th class=\"leftheader\">Current Block</th><td><a href=\"http://blockexplorer.com/b/" . $current_block_no . "\">";
	echo number_format($current_block_no) . "</a></td></tr>";

	$show_difficulty = round($difficulty, 2);

	echo "<tr class=\"d1\"><th class=\"leftheader\">Current Difficulty</th><td><a href=\"http://dot-bit.org/tools/nextDifficulty.php\">" . number_format($show_difficulty) . "</a></td></tr>";

	$result = mysql_query("SELECT blockNumber, confirms, timestamp FROM networkBlocks WHERE confirms > 0 ORDER BY blockNumber DESC LIMIT 1");

	$show_time_since_found = false;
	$time_last_found;

if ($resultrow = mysql_fetch_object($result)) {

	$found_block_no = $resultrow->blockNumber;
	$confirm_no = $resultrow->confirms;

	echo "<tr class=\"d0\"><th class=\"leftheader\">Last Block Found</th><td><a href=\"http://blockexplorer.com/b/" . $found_block_no . "\">" . number_format($found_block_no) . "</a></td></tr>";

	$time_last_found = $resultrow->timestamp;

	$show_time_since_found = true;
}

	$time_to_find = CalculateTimePerBlock($difficulty, $hashrate);
	// change 25.75 hours to 25:45 hours
	$intpart = floor( $time_to_find );
	$fraction = $time_to_find - $intpart; // results in 0.75
	$minutes = number_format(($fraction * 60 ),0);

	echo "<tr class=\"d1\"><th class=\"leftheader\">Est. Time To Find Block</th><td>" . number_format($time_to_find,0) . " Hours " . $minutes . " Minutes</td></tr>";

	$now = new DateTime( "now" );
	$hours_diff = ($now->getTimestamp() - $time_last_found) / 3600;

if( $hours_diff < ( $time_to_find * 1.5 ) ) // $hours_diff < $time_to_find = original, pool having bad luck so changed this a bit
{
	$time_last_found_out = "<span class=\"green\">";
}
else
{
	$time_last_found_out = "<span>"; //delete this when uncommenting below
}
/* UNCOMMENT THIS CODE WHEN POOL IS BIGGER
   elseif( ( $hours_diff * 2 ) > $time_to_find )
   {
   $time_last_found_out = "<span class=\"red\">";
   }
   else
   {
   $time_last_found_out = "<span class=\"orange\">";
   }
*/
	$time_last_found_out = $time_last_found_out . floor( $hours_diff ). " Hours " . $hours_diff*60%60 . " Minutes</span>";

	echo "<tr class=\"d0\"><th class=\"leftheader\">Time Since Last Block</th><td>" . $time_last_found_out . "</td></tr>";

	echo "</table>";

	// SHOW LAST (=$last_no_blocks_found) BLOCKS  *************************************************************************************************************************

	echo "<table class=\"stats_table server_width top_spacing\">";
	echo "<tr><th scope=\"col\" colspan=\"4\">Last $last_no_blocks_found Blocks Found - <a href=\"blocks.php\">All Blocks Found</a></th></tr>";
	echo "<tr><th scope=\"col\">Block</th><th scope=\"col\">Confirms</th><th scope=\"col\">Finder</th><th scope=\"col\">Time</th></tr>";

	$result = mysql_query("SELECT blockNumber, confirms, timestamp, accountAddress FROM networkBlocks WHERE confirms > 0 ORDER BY blockNumber DESC LIMIT " . $last_no_blocks_found);
	$i = 0;



while($resultrow = mysql_fetch_object($result)) {



	echo "<tr class=\"d" . ($i & 1) . "\">";

	$resdss = mysql_query("SELECT username, solution FROM shares_history WHERE upstream_result = 'Y' AND blockNumber = $resultrow->blockNumber");
	$resdss = mysql_fetch_object($resdss);

	$splitUsername = explode(".", $resdss->username);
	$realUsername = $splitUsername[0];
	$block_hash = $resdss->solution;

	$resdss1 = mysql_query("SELECT nickname,username FROM webUsers WHERE username='".$realUsername."'");
	$resdss1 = mysql_fetch_object($resdss1);

if ($resdss1->nickname == NULL) {
	$usernamet = $resdss1->username;
} ELSE {
	$usernamet = $resdss1->nickname;
}

	$confirms = $resultrow->confirms;

	if ($confirms > 100) {
		$confirms = "Done";
	}
	$block_no = $resultrow->blockNumber;

	echo "<td><a href=\"http://blockexplorer.com/b/$block_no\">" . number_format($block_no) . "</a></td>";
	echo "<td>" . $confirms . "</td>";
	echo "<td>$usernamet</td>";
	echo "<td>".strftime("%m-%d %r",$resultrow->timestamp)."</td>";
	echo "</tr>";
	$i++;
}

	echo "</table>";
}
else
{
	include('login.php');
}

//end edit by Ryan Shaw (ryannathans)
?>