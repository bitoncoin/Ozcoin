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
$pageTitle = "- Getting Started";
include ("includes/header.php");
	
?>

<b><u>Get a Bitcoin Address</u></b><br/>
Sign-up for an online wallet. For example: <a href="https://www.mybitcoin.com/" target="_blank"  style="color: blue">mybitcoin.com</a><br/>
Or download the client to your PC from: <a href="http://www.bitcoin.org//" target="_blank"  style="color: blue">bitcoin.org</a><br/><br/>

<b><u>Setup a bitcoin miner</u></b><br/><br/>

<b><u>ATI/AMD Users</u></b><br/>
You need to have the latest drivers installed to start minning. Current is 11.5<br><br>

	<b><u>Windows</u></b><br/>
	GUIMiner: <a href="/files/guiminer-20110609.exe" style="color: blue">HERE</a><br>
	Phoenix: <a href=http://forum.bitcoin.org/index.php?topic=6458.0 style="color: blue">HERE</a><br><br>
	<hr size="1" width="50%"></hr><br>
	<img src="/images/guiminer.png"><br/><br>
	<b><u>GUIMiner options</b></u><br/>
	Server: Other</br>
	Host: ozco.in</br>
	Port: 8332</br>
	Username: &lt;your user name&gt;.&lt;miner name (default is 1)&gt;<br/>
	Password: &lt;your miner password (default is 'x')&gt;<br/>
	Device: Select the graphics card/cpu you would like to use<br/>
	Extra Flags: Can be blank, but I find "-v -w128 -f 60" to work well<br/><br/>
	
	<b><u>Linux</u></b><br/>
	Download Phoenix: <a href="http://svn3.xp-dev.com/svn/phoenix-miner/files/phoenix-1.4.tar.bz2" style="color: blue">Download</a><br/>
	<a href=http://forum.bitcoin.org/index.php?topic=6458.0>Howto Setup and Info</a>
	
	
<?php include("includes/footer.php"); ?>

