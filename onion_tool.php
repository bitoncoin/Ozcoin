<?php

/**
 *  WRITTEN FOR OZCO.IN, ALL HAIL THE MIGHTY GRAET
 *  By Tom Lightspeed (tomlightspeed@gmail.com)
 *  Do not delete credit plox
 *  Please consider donating 1LYjJCtP8RFJ6zz6orxf5ym9CrvxPS4pDr
 *
 *  May the force be with you
 **/

$pageTitle = "- Admin Onion Tool";
include ("includes/header.php");

// ADMINS ONLY
if(!$cookieValid || $isAdmin != 1) {
  header('Location: /');
  exit;
}

// FOR DEVELOPMENT
error_reporting(E_ALL);
ini_set('display_errors', '1');

// ONION TOOL (most stale % + must be active this round)  *************************************************************************************************************************

$zeros = false;
$query = "";

if( isset($_GET['zeros']) && $_GET['zeros'] == "true" )
{
   $zeros = true;
}

if( isset($_GET['removeallstales']) && isset($_GET['uid']) && $_GET['removeallstales'] == "true" )
{
  $userid = $_GET['uid'];
  $query = "UPDATE webUsers SET stale_share_count = 0 WHERE id = " . $userid;
  mysql_query( $query );
  echo "COMMAND: Delete All Stales for Userid: " . $userid . ". Records Updated: " . mysql_affected_rows() . ".";
}


 echo "<div id=\"onion_tool\">";

 if( $zeros == true )
 {
   echo "<a class=\"fancy_button\" href=\"onion_tool.php?zeros=false\"><span style=\"background-color: #070;\">Only Show Users With Work</span></a><br /><br />";
   $query = "SELECT id, username, share_count, stale_share_count, (stale_share_count / share_count)*100 AS stale_percent FROM webUsers ORDER BY stale_percent DESC";
 }
 else
 {
   echo "<a class=\"fancy_button\" href=\"onion_tool.php?zeros=true\"><span style=\"background-color: #070;\">Show All Users</span></a><br /><br />";
   $query = "SELECT id, username, share_count, stale_share_count, (stale_share_count / share_count)*100 AS stale_percent FROM webUsers WHERE share_count > 0 AND stale_share_count > 0 ORDER BY stale_percent DESC";
 }

 echo "<table class=\"stats_table tool_width\">";
 echo "<tr><th colspan=\"7\" scope=\"col\">Onion Tool (All Users)</th></tr>";
 echo "<tr><th scope=\"col\">Rank</th><th scope=\"col\">User Name</th><th scope=\"col\">Share Count</th><th scope=\"col\">Stale Shares</th><th scope=\"col\">% Of Stales</th><th scope=\"col\">Stales This Round</th><th scope=\"col\">Operations</th></tr>";

 $result = mysql_query($query);
 $rank = 1;
 $user_found = false;

 while ($resultrow = mysql_fetch_object($result)) {
   if( $cookieValid && $resultrow->username == $userInfo->username )
   {
     echo "<tr class=\"user_position\">";
     $user_found = true;
   }
   else
   {
   echo "<tr class=\"d" . ($rank & 1) . "\">";
   }

   echo "<td>" . $rank;

   echo "</td><td>" . $resultrow->username . "</td><td>" . number_format($resultrow->share_count) . "</td><td>" . number_format($resultrow->stale_share_count) . "</td><td>" . number_format($resultrow->stale_percent, 2) . "%</td>";

   echo "<td>";

   /* too laggy ?
   $round_stales_query = "SELECT count(*) as round_stales FROM shares_history where userId = " . $resultrow->id . " and our_result = 'N' and reason = 'stale'";

   $stales_result = mysql_query($round_stales_query);
   $stales_row = mysql_fetch_object($stales_result);
   echo $stales_row->round_stales;
   */
   echo "</td>";

   echo "<td><a class=\"fancy_button tiny_button\" href=\"onion_tool.php?removeallstales=true&uid=" . $resultrow->id . "\"><span style=\"background-color: #070;\">Delete&nbsp;All&nbsp;Stales</span></a></td>";

   echo "</tr>";
   $rank++;
 }

echo "</table></div>";

?>