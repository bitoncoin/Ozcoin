<?PHP
$pageTitle = "- Donor Information";

include ("includes/header.php");

if(!$cookieValid) {
	header('Location: /');
	exit;
}

echo "<h2>Donation Details</h2><br>";
echo "1% <br>";
echo "Idle miner emails sent to your Ozcoin account email adress. Polled every 5 minutes.<br>";
echo "<br>";
echo "2% <br>";
echo "Free manual Payouts.<br>";
echo "Payouts on new blocks after 10 confirms.<br>";
echo "Idle miner emails.<br>";
echo "<br>";
echo "Setting up donations<br>";
echo "Go to Account Details page<br>";
echo "Donation %<br>";
echo "Choose Donation Period and set the % you would like to donate.<br>";
echo "Agreement:<br>";
echo "You must tick the  box if agree<br>";
echo "Email End: <br>";
echo "You can select this if you want to be remined that you donation time is ending.<br>";
echo "Authorize Pin<br>";
echo "Enter PIN<br>";
echo "Click Donor Submit<br>";
echo "<br>";

echo "<h2>Donatoin Policy.</h2><br>";
echo "Once you have set the donation you cannot undo it until after the donation period set expires.<br>";
echo "If you do set this by mistake you will need to send a email to graet@ozco.in to reset it. You have 1 hour to do this or the donation period is locked in.<br>";
echo "Donations cannot be reversed once they have been debited.<br>";


?>