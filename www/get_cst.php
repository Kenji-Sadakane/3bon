<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=UTF-8");

$referer = $_GET["url"];
if (!preg_match("|^http://3bon\.net.*$|", $referer)) {
  if (!preg_match("|^http://www\.3bon\.net.*$|", $referer)) {
    print $referer;
    print "<span>unauthorized access</span>";
    return;
  }
}
$page = $_GET["p"];
if(!preg_match("/^[0-9]+$/", $page)) {
  $page = 0;
}

$target_date = $_GET["date"];
if ($target_date == "") {
  $target_date = formatDateYYYYMMDD();
}
if (!isValidDate($target_date)) {
  print "<span>parameter invalid</span>";
  return;
}

$param = 'mysql:dbname=antena_db;host=mysql331.db.sakura.ne.jp';
$user = 'XXXXX';
$pass = 'XXXXX';

$pdo = new PDO($param, $user, $pass);
$pdo->query('SET NAMES utf8;');

$sql = "select rss.id, rss.url, rss.title, rss.pubDate, rss.pubTime, rss.site_no, site.name, site.category "
     . "  from rss inner join site on rss.site_no = site.no "
     . " where rss.pubDate='" . $target_date . "' and enable is true"
     . " order by pubTime desc "
     . " limit 500"
     . " offset " . $page * 500;
$stmt = $pdo->query($sql);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  print createTrTag($row);
}

unset($pdo);

function formatDateYYYYMMDD($date) {
  $dateTime = new DateTime($date);
  return $dateTime->format("Ymd");
}

function isValidDate($date) {
  $result = false;
  if (strlen($date) != 8) {
    return $result;
  }
  $year = substr($date, 0, 4);
  $mon = substr($date, 4, 2);
  $day = substr($date, 6, 2);
  if (checkdate($mon, $day, $year)) {
    $result = true;
  }
  return $result;
}

function createTrTag($row) {
  $link = "page/" . $row['pubDate'] . "/" . $row['id'];
  return ""
    . "<tr class='category" . $row['category'] . " site" . $row['site_no'] . "'>"
    . "  <td>"
    .      substr($row['pubTime'], 0, 2) . ":" . substr($row['pubTime'], 2, 2)
    . "  </td>"
    . "  <td>"
    . "    <div>"
    .        createHrefTag($row['title'], $link, $row['id'])
    . "    </div>"
    . "  </td>"
    . "  <td>"
    .      $row['name']
    . "  </td>"
    . "</tr>";
}

function createHrefTag($title, $link, $id) {
  return '<a href="' . $link . '" target="_blank" class="link" pageId="' . $id . '">' . $title . '</a>';
}
?>
