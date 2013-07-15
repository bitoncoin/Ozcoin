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
error_reporting(E_ALL);
ini_set('display_errors', '1'); 
include("includes/header.php");

//Quick and dirty RSS;
$rss = new SimpleXMLElement('<rss version="2.0"></rss>');
$channel = $rss->addChild('channel');
$channel->addChild('title', 'ozco.in');
$channel->addChild('link', 'https://ozco.in/');
$channel->addChild('description', 'Pooled Bitcoin Mining Australasia');

$blogPostsQ = mysql_query("SELECT `timestamp`, `title`, `message` FROM `news` ORDER BY `timestamp` DESC LIMIT 3");
	while($blog = mysql_fetch_array($blogPostsQ))
	{
		$item = $channel->addChild('item');
		$item->addChild('title', $blog["title"]);
		$item->addChild('link', 'https://ozco.in/');
		$item->addChild('description', $blog["message"]);
		?>
<div class="newspost">
	<div class="posthead">
 		<div style="float:left;"><h2 style="margin:0;"><u><?=$blog["title"];?></u></h2></div>
		<div style="float:right;"><?=date("M,d Y g:ia", $blog["timestamp"]);?></div>
		<div class="clear"></div>
	</div>
	<div class="postbody"><?=nl2br($blog["message"]);?></div>
</div>
<?
		}

	//Write the RSS to disk.
	$rss->asXML(dirname(__FILE__) . '/newsfeed.xml');
?>
<div style="margin-left:20px;border: 3px solid #FEF286;width:806px;">
<script src="/js/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 4,
  interval: 6000,
  width: 806,
  height: 300,
  theme: {
    shell: {
      background: '#FEFCE0',
      color: '#005E00'
    },
    tweets: {
      background: '#ffffff',
      color: '#2b2a2b',
      links: '#72e043'
    }
  },
  features: {
    scrollbar: true,
    loop: true,
    live: true,
    hashtags: true,
    timestamp: true,
    avatars: true,
    behavior: 'default'
  }
}).render().setUser('Ozcoin').start();
</script>
</div>

<?php include ("includes/footer.php"); ?>


