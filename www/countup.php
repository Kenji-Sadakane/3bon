<?php
$pageId = $_GET["pageId"];
if (empty($pageId) || strpos($pageId, ' ') !== false){
  print "<span>parameter invalid</span>";
}

$param = 'mysql:dbname=antena_db;host=mysql331.db.sakura.ne.jp';
$user = 'XXXXX';
$pass = 'XXXXX';

$pdo = new PDO($param, $user, $pass);
$pdo->query('SET NAMES utf8;');

$sql = "update rss set count = count + 1 where id= :pageId";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':pageId', $pageId, PDO::PARAM_STR);
$stmt->execute();

unset($pdo);
?>
