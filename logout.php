<?php 
include("includes/requiredFunctions.php");

if (!isset($timeoutStamp)) {
	$timeoutStamp = null;
}
setcookie($cookieName, 0, $timeoutStamp, $cookiePath, $cookieDomain);
?>
<html>
  <head>
	<title><?php echo antiXss(outputPageTitle());?> </title>
	<link rel="stylesheet" href="/css/mainstyle.css" type="text/css" />
	<? header('Location: /'); ?>
  </head>
  <body>
	<div id="pagecontent">
		<h1>You have been logged out<br/>
		<a href="/">Click here if you continue to see this message</a></h1>
	</div>
  </body>
</html>