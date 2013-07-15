<?PHP
include ("includes/header.php");
$winningAccountQ = mysql_query("SELECT id, blockNumber FROM winning_shares WHERE type = 'orphan'");
	while ($winningAccountR = mysql_fetch_object($winningAccountQ)) {
	mysql_query("DELETE FROM unconfirmed_rewards WHERE blockNumber == $winningAccountR->blockNumber");
}

?>