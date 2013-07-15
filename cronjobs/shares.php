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

error_reporting(E_ALL);
ini_set('display_errors', '1'); 

$includeDirectory = "/var/www/includes/";
include($includeDirectory."requiredFunctions.php");
	
//Check that script is run locally
ScriptIsRunLocally();

try {
	$lastBlockQ = mysql_query("SELECT IFNULL(MAX(blockNumber), 0) AS blockNumber FROM networkBlocks WHERE confirms > 0");
	$lastBlockR = mysql_fetch_object($lastBlockQ);

	$sql = "" .
		"SELECT    wu.id                AS userId, " .
		"          IFNULL(SUM(a.id), 0) AS shares " .
		"FROM      webUsers wu " .
		"LEFT JOIN " .
		"          (SELECT  COUNT(*) AS id, " .
		"                   p.associatedUserId " .
		"          FROM     shares s " .
		"          JOIN     pool_worker p ON s.username = p.username " .
		"          WHERE    s.our_result                = 'Y' " .
		"          GROUP BY p.associatedUserId " .
		"           " .
		"          UNION " .
		"           " .
		"          SELECT   COUNT(*) AS id, " .
		"                   sh.userId " .
		"          FROM     shares_history sh " .
		"          WHERE    sh.our_result      = 'Y' " .
		"                   AND sh.blockNumber > " . $lastBlockR->blockNumber .
		"          GROUP BY sh.userId " .
		"          ) a ON wu.id = a.associatedUserId " .
		"GROUP BY  wu.id";
//echo "$sql\n";
	$result = mysql_query($sql);
	$totalsharesthisround = 0;
	$associated_users = array();
	while ($row = mysql_fetch_array($result)) {
		$associated_users[] = $row['userId'];
		if ($row["shares"] > 0) {
			mysql_query("UPDATE webUsers SET shares_this_round=".$row["shares"]." WHERE id=".$row["userId"]);
			$totalsharesthisround += $row["shares"];
		}
	}

    mysql_query("COMMIT");
} catch (Exception $ex)  {
    mysql_query("ROLLBACK");
}

mysql_query("UPDATE settings SET value='".$totalsharesthisround."' WHERE setting='currentroundshares'");

////Update share counts
/*
////Update current round shares
$sql = "UPDATE webUsers u, ".
	   "	(SELECT IFNULL(count(s.id),0) AS id, p.associatedUserId FROM pool_worker p ".
	   "	LEFT JOIN shares s ON p.username=s.username ".
	   "	WHERE s.our_result='Y' GROUP BY p.associatedUserId) a ". 
	   "SET shares_this_round = a.id WHERE u.id = a.associatedUserId ";
mysql_query($sql) or sqlerr(__FILE__, __LINE__);
*/

//Update past shares
$sql = "UPDATE webUsers u, ".
	   	"	(SELECT DISTINCT userId, sum(count) AS valid, sum(invalid) AS invalid, id FROM shares_counted GROUP BY userId) s ".
		"SET u.share_count = s.valid, u.stale_share_count = s.invalid WHERE u.id = s.userId";
//echo "$sql\n";
mysql_query ($sql) or sqlerr(__FILE__, __LINE__);

//
?>
