<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=UTF-8");

$target_date = $_GET["date"];
if ($target_date == "") {
  $target_date = formatDateYYYYMMDD();
}

// 現在の年月を取得
$year = substr($target_date, 0, 4);
$month = intval(substr($target_date, 4, 2));
// 月末日を取得
$last_day = date('j', mktime(0, 0, 0, $month+1, 0, $year));
$calendar = array();

print " <table id='calendar_table'>"
      .  "<thead>"
      .    "<tr>"
      .      "<th colspan=7>" . $year . "年" . $month . "月</th>"
      .    "</tr>"
      .    "<tr>"
      .      "<th>日</th>"
      .      "<th>月</th>"
      .      "<th>火</th>"
      .      "<th>水</th>"
      .      "<th>木</th>"
      .      "<th>金</th>"
      .      "<th>土</th>"
      .    "</tr>"
      .  "</thead>"
      .  "<tbody>"
      .    "<tr>";
for ($i = 1; $i < $last_day + 1; $i++) {
  // 曜日を取得
  $week = date('w', mktime(0, 0, 0, $month, $i, $year));
  // 1日の場合
  if ($i == 1) {
    // 1日目の曜日までをループ
    for ($s = 1; $s <= $week; $s++) {
      // 前半に空文字をセット
      print "<td></td>";
    }
  }
  // 週初め
  if ($week == 0) {
    print "</tr><tr>";
  }
  // 日付設定
  print "<td>" . $i . "</td>";
  // 月末の場合
  if ($i == $last_day) {
    for ($s = $week + 1; $s <= 6; $s++) {
      // 週末まで空文字をセット
      print "<td></td>";
    }
  }
}
print "</tr></tbody></table>";


function formatDateYYYYMMDD($date) {
  $dateTime = new DateTime($date);
  return $dateTime->format("Ymd");
}

?>
