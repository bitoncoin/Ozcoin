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
	<style>

		#content div.start_step_frame
		{
			padding: 10px;
			float: left;
		}

		#content div.start_step_small
		{
			width: 380px;
			height: 200px;
			background-color: #FEFCE0;
			border: 3px solid #FEF286;
			padding: 10px;
		}
		#content div.start_step_wide
		{
			width: 806px;
			background-color: #FEFCE0;
			border: 3px solid #FEF286;
			padding: 10px;
		}
		#content div.start_step_frame p
		{
			position:relative;
			margin-top: 5px;
			z-index:2;
		}

		#content div.start_step_frame h3
		{
			font-weight: bold;
			font-size: 1.1em;
			margin: 0 0 10px 0;
		}

		#content div.start_step_frame tr
		{
			margin: 3px;
			background-color: #FFFFFF;
		}

		#content div.start_step_frame li a
		{
			line-height: 24px;
		}


		#content div.start_step_frame div.codebox
		{
			background-color: #FFFFFF;
			border: 2px solid #000000;
			padding: 10px;
			font-family:"Courier New", monospace;
		}

		#content div.start_step_frame A:link{}
		#content div.start_step_frame A:visited{}
		#content div.start_step_frame A:active{}
		#content div.start_step_frame A:hover{}


		#content div.start_step_frame .number_anchor
		{
			position:relative;
		}

		#content div.start_step_frame .number
		{
			position:absolute;
			left: 10px;
			font-size: 160px;
			color: #FFFFFF;
			-webkit-user-select: none;
			-khtml-user-select: none;
			-moz-user-select: none;
			-o-user-select: none;
			user-select: none;
			z-index:1;
		}

	
	</style>


	<div class="start_step_frame">
		<div class="start_step_small">
			<h3>Create a Litecoin Address!</h3>
			<p>You will need a Litecoin wallet to store your newly mined coins, download the Litecoin client from the official Litecoin website.</p>
			<p>Your Litecoin wallet contains all of the private keys necessary for spending your received transactions.</p>

			<div style="text-align: center;margin-top: 15px;"><a href="http://litecoin.org/" target="_blank">Download Litecoin client</a></div>

		</div>
	</div>

	<div class="start_step_frame">
		<div class="start_step_small">
			<h3>Create an account on ozco.in!</h3>
			<p>This is so you can keep track of your share of the mined Litecoins.</p>
			<p>You will need this to set up the mining program too.</p>
			<div style="text-align: center;margin-top: 50px;"><a href="/register.php" target="_blank">Register with ozco.in</a></div>

		</div>
	</div>

	<div class="start_step_frame">
		<div class="start_step_wide">
			<h3>Download a Miner!</h3>
			<p>There are many different mining programs on the internet and choosing one depends on your hardware and your level of skill.</p>
			<p>If you're new to Litecoin mining, try the GUI miner. It will simplify the process and get you mining sooner!</p>

			<div style="height:20px;">&nbsp;</div>
			<div style="position:relative;z-index:1;float:left; width:49%;">
				<b>Beginner:</b>
				<table width="100%">
					<tr>
					</tr>
				</table>
			</div>
			
			<div style="position:relative;z-index:1;float:left; width:49%;padding-left:1%;">
				<b>Advanced:</b>
				<table width="100%">
					<tr>
						<td><a href="https://bitcointalk.org/index.php?topic=47417.0" target="_blank">MinerD</a></td>
						<td>
						<img src="/images/icon-windows_download.png" border=0>
						<img src="/images/icon-linux_download.png" border=0>
						<img src="/images/icon-apple_download.png" border=0>
						</td>
						<td><a href="https://bitcointalk.org/index.php?topic=47417.0" target="_blank">Download</a></td>
					</tr>

				</table>


			</div>
			<div class="clear"></div>		
		
		</div>
	</div>

	<div class="start_step_frame">
		<div class="start_step_wide">
			<h3>Configure your miner to work with ozco.in!</h3>
			<p>Here are a few suggestions on how to get started with your miner.</p>
			<div style="position:relative;z-index:1;font-size:small;margin-top:20px;">
				<div style="border: 1px solid #ffffff;">
					<div style="margin-bottom:10px;"><b>MinerD</b></div>
					<div>
						<div class="codebox">minerd.exe --algo scrypt --s 6 --threads 6 --url http://lc.ozco.in:9332/ --userpass Wayno.1:1234</div>
						<br>Use your correct username and password instead of mine ;)
					</div>
				</div>
			</div>
		
		</div>
	</div>

	<div class="start_step_frame">
		<div class="start_step_small">
			<h3>Tweak your hardware!</h3>
			<p>There are many options on what hardware to use and how to configure the miners to use your hardware effectively.</p>

			<div style="text-align: center;margin-top: 15px;"><a href="https://en.bitcoin.it/wiki/Mining_hardware_comparison" target="_blank">More Hardware Information</a></div>

		</div>
	</div>

	<div class="start_step_frame">
		<div class="start_step_small">
			<h3>Learn about Litecoin its like Bitcoin!</h3>
			<p>There is a great deal of information about Bitcoin on the web. Have a look around, do some searches.</p>
			<p>Bitcoin is an exciting new technology, with many possibilities in the future. Don't be left behind.</p>
			<div style="text-align: center;margin-top: 10px;"><a href="http://www.weusecoins.com/" target="_blank">We Use Coins</a></div>
			<div style="text-align: center;margin-top: 5px;"><a href="http://www.bitcoin.org/" target="_blank">Official site</a></div>
			<div style="text-align: center;margin-top: 5px;"><a href="https://en.bitcoin.it/wiki/Main_Page" target="_blank">Bitcoin Wiki</a></div>

		</div>
	</div>


	<div class="clear"></div>

	
<?php include("includes/footer.php"); ?>


