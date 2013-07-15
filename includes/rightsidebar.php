<?php
//DELETE
error_reporting(E_ALL);
ini_set('display_errors', '1');

if(!$cookieValid){
//No valid cookie show login//
}else if($cookieValid){
//Valid cookie YES! Show this user stats//
?>
<div id="leftsidebar">
<?php
echo "<div id=\"rightsidebar\">";
echo "</div>";
echo "<script type=\"text/javascript\">
function RightStats()
{
$.get('/includes/sidebargetright.php', function(data){
  $('#rightsidebar').html(data);
});
}
RightStats();";

if($livestats == "yes")
{
	echo "setInterval( RightStats(), ";
	if( $userInfo->update_interval > 20 )
	{
		echo $userInfo->update_interval;
	}
	else
	{
		echo "60";
	}
	echo "000 )";
}

echo "</script>";

?>
</div>
<?php

}

?>

