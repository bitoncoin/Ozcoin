<?php

include ("includes/header.php");

$results = mysql_query("SELECT min(id) as id from userHashrates") or die(mysql_error());
$min = mysql_fetch_object($results);

$id = $min->id;

while ($id < 56780063) {
	echo $id . "\r";
	mysql_query("DELETE FROM userHashrates WHERE id= $id");
	$id++;
}

?>
