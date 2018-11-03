<?php
header("Content-Type: text/html; charset=UTF-8");

$param = 'mysql:dbname=antena_db;host=mysql331.db.sakura.ne.jp;charset=utf8mb4';
$user = 'XXXXX';
$pass = 'XXXXX';

$pdo = new PDO($param, $user, $pass);
$pdo->query('SET NAMES utf8;');

$sql = "SELECT * "
     . "  FROM ( "
     . "    SELECT * , 0 AS siteNo,  '' AS siteName FROM category "
     . "    UNION "
     . "    SELECT category.* , site.no, site.name "
     . "      FROM category "
     . "      INNER JOIN site ON category.id = site.category "
     . "     where enable is true"
     . "    ) AS query "
     . " ORDER BY disp, siteName ";
$stmt = $pdo->query($sql);

$step = 0;
$id = 0;
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  // ï\é¶äKëwéZèo
  $myStep = strlen($row['id']);
  if ($row['siteName'] != null) {
    $myStep++;
  }
  // ëOçsÇ∆î‰ärÇµäKëwè∏ç~
  if ($step < $myStep) {
    if ($myStep > 2) {
      print "<ul style='list-style:none;'>";
    } else {
      print "<ul style='list-style:none;'>";
    }
  } else if ($step > $myStep) {
    for ($i=0; $i<$step-$myStep; $i++) {
      print "</ul>";
    }
  }
  // ÉJÉeÉSÉäñºï\é¶
  $checkId = $row['id'] . "_". $row['siteNo'];
  if ($id != $row['id']) {
    print "<li style='list-style:none; color: #50a050;'>"
        . "  <input type='checkbox' checked='checked' id='" . $checkId . "' class='category_check' category='" . $row['id'] . "'>"
        . "  <label class='category_label' for='" . $checkId . "'>" . $row['name'] . "</label>"
        . "</li>";
        
  }
  // ÉTÉCÉgñºï\é¶
  if ($row['siteName'] != null) {
    print "<li style='list-style:none; display: none'>"
        . "  <input type='checkbox' checked='checked' id='" . $checkId . "' class='site_check' site='site" . $row['siteNo'] . "'>"
        . "  <span>"
        . "  <label for='" . $checkId ."'>"
        .      $row['siteName']
        . "  </label>"
        . "  </span>"
        . "</li>";
  }
  $step = $myStep;
  $id = $row['id'];
}


unset($pdo);

?>
