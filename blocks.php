<?php
// Copyright (C) 2011 Mike Allison <dj.mikeallison@gmail.com>
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.

// BTC Donations: 163Pv9cUDJTNUbadV4HMRQSSj3ipwLURRc

error_reporting(E_ALL);
ini_set('display_errors', '1'); 

$pageTitle = "- Block Info";
include ("includes/header.php");

if(!$cookieValid) {
	header('Location: /block.php');
	exit;
}

echo "<table class=\"stats_table blocks_width bottom_spacing\">";
echo "<tr><th scope=\"col\" colspan=\"8\">Last 100 Blocks Found</th></tr>";
echo "<tr><th scope=\"col\">Block</th>";
echo "<th scope=\"col\">Amount</th>";
echo "<th scope=\"col\">Confirms</th>";
echo "<th scope=\"col\">Finder</th>";
echo "<th scope=\"col\">Time</th>";
echo "<th scope=\"col\" class=\"align_right\">Earnings</th>";
echo "<th scope=\"col\" class=\"align_right\">Shares</th>";
echo "<th scope=\"col\" class=\"align_right\">Total Shares</th></tr>";


$result = mysql_query("SELECT username, blockNumber, timestamp, confirms, shares, amount FROM winning_shares WHERE confirms > 0 ORDER BY blockNumber DESC LIMIT 100") or sqlerr(__FILE__, __LINE__);
$i = 0;

while($resultrow = mysql_fetch_object($result)) {
echo "<tr class=\"d" . ($i & 1) . "\">";

$resulta = mysql_query("SELECT userId, sum(amount) as amount, sum(shares) as shares FROM rounddetails WHERE blockNumber = $resultrow->blockNumber AND userId = $userId") or sqlerr(__FILE__, __LINE__);
// $resulta = mysql_query("SELECT r.userid,r.amount,s.count - s.invalid AS shares FROM rounddetails r, shares_counted s where r.userid=s.userid and r.blocknumber = s.blocknumber and s.blockNumber = $resultrow->blockNumber AND r.userid=$userId") or sqlerr(__FILE__, __LINE__);
if ($resdssa = mysql_fetch_object($resulta)) {
	$shares = $resdssa->shares;
//    $shares = "TBD";
	$earnings = $resdssa->amount;
} else {
	$shares = "0";
	$earnings = "0";
}

$blockNo = $resultrow->blockNumber;

$splitUsername = explode(".", $resultrow->username);
$realUsername = $splitUsername[0];

//$earnings = $resdssa->amount;

$confirms = $resultrow->confirms;

if ($confirms > 120) {
	$confirms = 'Completed';
}


echo "<td><a href=\"http://blockexplorer.com/b/" . $blockNo . "\">" . number_format( $blockNo ) . "</a></td>";
echo "<td>" . $resultrow->amount . "</td>";
echo "<td>" . $confirms . "</td>";
echo "<td>".$realUsername."</td>";
echo "<td>".strftime("%B %d %Y %r",$resultrow->timestamp)."</td>";
echo "<td class=\"align_right\">$earnings</td>";
//echo "<td class=\"align_right\">Under Construction</td>";
echo "<td class=\"align_right\">$shares</td>";
// echo "<td class=\"align_right\">TBD</td>";
echo "<td class=\"align_right\">" . $resultrow->shares . "</td>";
$i++;
}

echo "</table>";
echo "You will not get paid till Confirms have hit 120";
echo "<br /><a class=\"fancy_button top_spacing\" href=\"stats.php\">";
echo "<span style=\"background-color: #070;\">Stats</span></a>";

include("includes/footer.php");
