<div id="menuBar">
	<div class="menuBtn">
		<a href="/index.php" class="menu">News</a>
	</div>
	<?php
		if(!$cookieValid){
		//Display this menu if the user isn't logged in
	?>
	<div class="menuBtn">
		<a href="/register.php" class="menu">Register</a>
	</div>
	<?php
	} else if($cookieValid){
	?>
	<div class="menuBtn">
		<a href="/accountdetails.php" class="menu">Account</a>
	</div>
	<div class="menuBtn">
		<a href="/my_stats.php" class="menu">Stats</a>
	</div>

	<?php
	//If this user is an admin show the adminPanel.php link
	if($isAdmin){
	?>
	<div class="menuBtn">
		<a href="/adminPanel.php" class="menu">(Admin Panel)</a>
	</div>
	<?php
		}
	}
	?>
	<div class="menuBtn">
		<a href="/stats.php" class="menu">Pool</a>
	</div>
	<div class="menuBtn">
		<a href="/gettingstarted.php" class="menu">Get Started</a>
	</div>
	<div class="menuBtn">
		<a href="/chat.php" class="menu">Webirc</a>
	</div>
	<div class="menuBtn">
		<a href="about.php" class="menu">About</a>
	</div>
	<div class="menuBtn">
		<a href="newsfeed.xml" class="menu" target="_blank">RSS</a>
	</div>
</div>