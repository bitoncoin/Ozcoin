<?php
include ("includes/header.php");
	
$goodMessage = "";
$returnError = "";
//Scince this is the Admin panel we'll make sure the user is logged in and "isAdmin" enabled boolean; If this is not a logged in user that is enabled as admin, redirect to a 404 error page

if(!$cookieValid || $isAdmin != 1) {
	header('Location: /');
	exit;
}
if(isset($_POST["action"])){
$action = $_POST["action"];
}

if($_POST["action"] == "newsupdate") {
$title = $_POST["title"];
$title = sqlesc($title);
$news = $_POST["news"];
$news = sqlesc($news);
$id = $_POST["id"];
$currentTime = time();

mysql_query("UPDATE news SET title = $title, message = $news, timestamp = $currentTime WHERE id = $id");
?><meta http-equiv="refresh" content="0; url=/"><?
}

if($_POST["action"] == "newnews") {
$title = $_POST["title"];
$title = sqlesc($title);
$news = $_POST["news"];
$news = sqlesc($news);
$currentTime = time();

mysql_query("INSERT INTO news (title, message, timestamp) VALUES ($title, $news, $currentTime)");
?><meta http-equiv="refresh" content="0; url=/"><?
}

if($_POST["action"] == "update") {
$id = $_POST["id"];
$res = mysql_query("SELECT title, message, id FROM news WHERE id=$id");
$row = mysql_fetch_array($res);

echo "<h2>Edit news</h2><br/>";
echo "<form action=news.php method=post>";
echo "<input type=hidden name=action value=newsupdate>";
echo "<input type=hidden name=id value=$row[id]>";
echo "Title<br>";
echo "<textarea name=title rows=1 cols=80>" . htmlspecialchars($row["title"]) . "</textarea><br>";
echo "News<br>";
echo "<textarea name=news rows=10 cols=80>" . htmlspecialchars($row["message"]) . "</textarea>";
echo "<br><input type=submit value=Submit>";
echo "</form>";
}

if($_POST["action"] == "new") {
echo "<h2>Edit news</h2><br/>";
echo "<form action=news.php method=post>";
echo "<input type=hidden name=action value=newnews>";
echo "Title<br>";
echo "<textarea name=title rows=1 cols=80></textarea><br>";
echo "News<br>";
echo "<textarea name=news rows=10 cols=80></textarea>";
echo "<br><input type=submit value=Submit>";
echo "</form>";
}
?>