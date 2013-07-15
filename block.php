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

$pageTitle = "- Block Info";
include ("includes/header.php");

echo "<table class=\"stats_table blocks_width bottom_spacing\">";
echo "<tr><th scope=\"col\" colspan=\"8\"><center>Blocks Found</center></th></tr>";
echo "<tr><th scope=\"col\">Block</th>";
echo "<th scope=\"col\">Amount</th>";
echo "<th scope=\"col\">Confirms</th>";
echo "<th scope=\"col\">Finder</th>";
echo "<th scope=\"col\">Time</th>";
echo '<th scope="col" class="align_right">Value of Share</th>'; //added - ryannathans (earnings per share)
echo "<th scope=\"col\" class=\"align_right\">Totals Shares</th></tr>";


$result = mysql_query("SELECT blockNumber, confirms, timestamp FROM winning_shares WHERE confirms > 0 ORDER BY blockNumber DESC");
$i = 0;

while($resultrow = mysql_fetch_object($result)) {
$amountq = mysql_query("SELECT amount FROM winning_shares WHERE blockNumber = $resultrow->blockNumber");
$amounta = mysql_fetch_object($amountq);


  echo "<tr class=\"d" . ($i & 1) . "\">";

	$resdss = mysql_query("SELECT username FROM winning_shares WHERE blockNumber = $resultrow->blockNumber");
	$resdss = mysql_fetch_object($resdss);

	$blockNo = $resultrow->blockNumber;

        $splitUsername = '';
        $realUsername = '';
        if ($resdss != null) {
            $splitUsername = explode(".", $resdss->username);
            $realUsername = $splitUsername[0];
        }

	$confirms = $resultrow->confirms;

	if ($confirms > 120) {
		$confirms = 'Completed';
	}

	$resulta1 = mysql_query("SELECT amount, shares FROM winning_shares WHERE blockNumber = '$blockNo'") or sqlerr(__FILE__, __LINE__);
	$resdssa1 = mysql_fetch_object($resulta1);

    if( $confirms < 121 )
    {
      if( $resdssa1 !== FALSE )
      {
        $est = "EST " . number_format($resdssa1->amount, 8);
//        $users = number_format($resdssa1->shares);
        $totals = number_format($resdssa1->shares);
      }
      elseif( $resrow === FALSE )
      {

      }
      else
      {
        $est = "Processing";
        $users = "Processing";
        $totals = "Processing";
      }
    }
    else
    {
      $est = "Missing Data";
      $users = "Missing Data";
      $totals = "Missing Data";
    }

        $blockvalue = ($amounta ? $amounta->amount : ''); //edited - ryannathans value needs to be reused without reprocessing - using var
        $numeric_totals = str_replace(',', '', $totals);

	echo "<td>" . number_format( $blockNo ) . "</td>";
	echo "<td>" .$blockvalue . "</td>"; //edited - ryannathans value needs to be reused without reprocessing - using var
	echo "<td>" . $confirms . "</td>";
	echo "<td>$realUsername</td>";
	echo "<td>".strftime("%B %d %Y %r",$resultrow->timestamp)."</td>";
        echo '<td class="align_right">';  //added - ryannathans (earnings per share)
        if($totals == 'Before Upgrade') {
                echo 'Before Upgrade';
                $failure = true;
        }
        elseif($totals == 'Missing Data') {
                echo 'Missing Data';
                $failure = true;
        }
        elseif($totals == 'Processing') {
                echo 'Processing';
                $failure = true;
        }
        else {
                echo sprintf("%.10f", $blockvalue / $numeric_totals);
        }
        echo '</td>';
        echo "<td class=\"align_right\">" . $totals . "</td>";


  $i++;
}

echo "</table>";
//echo "You will not get paid till Confirms have hit 100. EST = Estimated Earnings.";
echo "<br /><a class=\"fancy_button top_spacing\" href=\"stats.php\">";
echo "<span style=\"background-color: #070;\">Pool Stats</span></a>";

include("includes/footer.php");
