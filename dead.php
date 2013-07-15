<?php
$pageTitle = "- About";
include ("includes/header.php");
$result = mysql_query("SELECT pool_worker.username, webUsers.id, shares.username as username FROM shares LEFT JOIN pool_worker ON (shares.username = pool_worker.username) LEFT JOIN webUsers ON (pool_worker.associatedUserId = webUsers.id) GROUP BY pool_worker.username, webUsers.id HAVING webUsers.id IS NULL");
if ($resultrow = mysql_fetch_object($result)) {
echo "$resultrow->username";
}
include("includes/footer.php");
?>
