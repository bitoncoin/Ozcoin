<?PHP
$pageTitle = "- Donor Information";

include ("includes/header.php");

if(!$cookieValid) {
	header('Location: /');
	exit;
}

echo "<h2>Donation Details</h2><br>";
echo "You must set this in donor section to see the extra options.";
echo "1% <br>";
echo "Idle miner emails sent to your Ozcoin account email address. Script scans for inactive miners over 5 minutes<br><br>";

echo "<h2>Donatoin Policy.</h2><br>";
//echo "Once you have set the donation you cannot undo it until after the donation period set expires.<br>";
//echo "If you do set this by mistake you will need to send a email to graet@ozco.in to reset it. You have 1 hour to do this or the donation period is locked in.<br>";
echo "Donations cannot be reversed once they have been donated.<br>";


?>