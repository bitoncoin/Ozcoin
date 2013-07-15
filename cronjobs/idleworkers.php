<?PHP

error_reporting(E_ALL);
ini_set('display_errors', '1'); 

$includeDirectory = "/var/www/includes/";
include($includeDirectory."requiredFunctions.php");

//Verify source of cron job request
if (isset($cronRemoteIP) && $_SERVER['REMOTE_ADDR'] !== $cronRemoteIP) {
	die(header("Location: /"));
}

lock("idleworkers.php");

$dt = time()-3600;

//$sql="SELECT donate_percent, id,email,username,deadworker,iwarn FROM webUsers WHERE deadworker='yes' AND iwarn < $dt";
$sql="SELECT donate_percent, id,email,username,deadworker,iwarn FROM webUsers WHERE deadworker='yes' AND iwarn is NULL";
//echo "$sql\n\n";
$resultQ = mysql_query($sql);
while ($resultR = mysql_fetch_object($resultQ)) {

	$sql = "SELECT username,id FROM pool_worker WHERE active = 0 AND monitor = 'yes' AND associatedUserId = $resultR->id";
// echo $sql . "\n\n";
	$idleC=0;
	$idleworker = "<br>";
	$idleQ = mysql_query($sql);
	while($idleW = mysql_fetch_object($idleQ)) {
		$check = $idleW->username;
		if ($check != NULL) {
			$idleC++;
			$idleworker .= "$check<br>";
		}
	}
	echo "$idleworker";
	$id = $resultR->id;
	$to = "$resultR->email";
	$subject = "OzCoin LTC Idle Miner";
	if ($idleC > 0) {
		$msg = "
<html>
<head>
<title>Ozcoin's LTC Dead Miners Email</title>
</head>
<body>
Hello, $resultR->username<br />
<br />
This is a automatic email telling you your miner(s)
$idleworker 
have stopped working.
<br>
You will not receive another email for 1hr (or not at all if we can fix it before then)
<br>
Thanks for using Ozcoin<br />
<br>
<img src=\"https://ozco.in/images/ozcoin.png\" alt=\"ozcoin pic\"/><br>
To Unsubscribe from getting emails please <a href=https://ozco.in/unsub.php?id=$id>click here</a>
</body></html>";

		$from_email = "noreply@ozco.in"; //site email

		$headers = "From: $from_email\n";
		$headers .= 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";

		mail($to, $subject, $msg, $headers);

		$time = time();
		mysql_query("UPDATE webUsers SET iwarn='".$time."' WHERE id=$resultR->id");
	}
}
?>
