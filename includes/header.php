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

//Set page starter variables//

$cookieValid	= false;
$activeMiners = false;

include("requiredFunctions.php");

include('includes/stats.php');
$stats = new Stats();

include("universalChecklogin.php");

if (!isset($pageTitle)) $pageTitle = outputPageTitle();
else $pageTitle = outputPageTitle(). " ". $pageTitle;

?>
<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php echo $pageTitle;?></title>
		<!--This is the main style sheet-->
		<link rel="stylesheet" href="css/mainstyle.css" type="text/css" />
		<script type="text/javascript" src="/js/EnhanceJS/enhance.js"></script>
		<script type="text/javascript" src="/js/jquery-1.6.1.min.js"></script>
		<!-- Analytics -->
		<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-24942908-1']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>
		<!-- end -->
		<script type="text/javascript">
			// Run capabilities test
			enhance({
				loadScripts: [
					'js/excanvas.js',
					'js/jquery-1.6.1.min.js',
					'js/jquery-ui-1.8.15.custom.min.js',
					'js/jquery.dataTables.min.js',
					'js/visualize.jQuery.js',
					'js/ozcoin_graphs.js',
					'js/standard_functions.js'
				],
				loadStyles: [
					'css/visualize.css',
					'css/visualize-light.css',
					'css/sunny/jquery-ui-1.8.15.custom.css',
					'css/datatables.css'
				]
			});

    	</script>
		<link rel="shortcut icon" href="/images/favicon.png" />
		<?php
			//If user isn't logged in load the login.js
			if(!$cookieValid){
		?>
			<script src="/js/login.js"></script>
		<?php
			}
		?>
	</head>
	<body>
		<div id="header">
			<div id="logo">
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
					<td rowspan="2"><img src="images/logo.png"></td>
					<td align="right" valign="bottom" id="currentRates">
<?
echo "<table class=\"hashrate\">";
$result = apc_fetch("serverstats:common");
if ($result !== FALSE) {
    echo $result;
} else {
    $output = '';
    $ourHashrate = $settings->getsetting('currenthashrate');
    $output .= "<tr><td>Current Hashrate</td><td>".number_format($ourHashrate/1000,3)." MH/s</td></tr>";
    $bitcoinController = new BitcoinClient($rpcType, $rpcUsername, $rpcPassword, $rpcHost);
    $networkHashrate = (intval($bitcoinController->query('getnetworkhashps'))/1000);
    $output .= "<tr><td>Network Hashrate</td><td>".number_format($networkHashrate/1000, 3)." MH/s </td></tr>";
    $output .= "<tr><td>Info</td><td>We are ".number_format($ourHashrate/$networkHashrate*100, 2)."%</td></tr>";
    $output .= "<tr><td>Current Workers</td><td>".$settings->getsetting('currentworkers')."</td></tr>";
    echo $output;
    apc_store("serverstats:common", $output, 20);
}
?>
</table>

					</tr>
				</table>
			</div>
		</div>
		<?php include ("menu.php"); ?>
		<?php include ("leftsidebar.php"); ?>
		<div id="content">
