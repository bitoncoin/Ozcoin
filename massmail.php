<?php

$pageTitle = "- Admin Panel";
include ("includes/header.php");

$goodMessage = "";
$returnError = "";

if(!$cookieValid || $isAdmin != 1) {
	header('Location: /');
	exit;
}

if (isset($_POST["action"])) {

$subject = $_POST["subject"];
$res = mysql_query("SELECT id, username, email, sendemail, recivemail, emailAuthPin FROM webUsers") or sqlerr(__FILE__, __LINE__);

$from_email = "noreply@ozco.in"; //site email

$subject = substr(trim($_POST["subject"]), 0, 80);
if ($subject == "") $subject = "(no subject)";
$subject = "$subject";

$message1 = trim($_POST["message"]);
if ($message1 == "") { 
echo" Email is blank";
exit;
}

while($arr=mysql_fetch_array($res)){

if ($arr['recivemail'] == 'yes' && $arr['sendemail'] == 'yes') {
$id = $arr['id'];
$pin = $arr['emailAuthPin'];
$to = $arr["email"];

$message = " ".nl2br($message1)."
<br>
<br>
To Unsubscribe from getting emails please <a href=https://lc.ozco.in/unsub.php?id=$id&pin=$pin>click here</a>";

$headers = "From: $from_email\n";
$headers .= 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";

$success = mail($to, $subject, $message, $headers);

}

if ($success)
$goodMessage = "Emails Sent";
else
$returnError = "Emails Failed";

}
}


echo "<table>";
echo "<h2>Mass Email</h2>";
echo "<form action=massmail.php method=post>";
echo "<input type=hidden name=action value=massmail>";
echo "<tr><td>Subject: <input type=text name=subject></td></tr>";
echo "<tr><td><br></td></tr>";
echo "<tr><td><textarea name=message rows=10 cols=80></textarea></td></tr>";
echo "<tr><td><br><input type=submit value=Submit></td></tr>";
echo "</form>";
echo "</table>";

?>