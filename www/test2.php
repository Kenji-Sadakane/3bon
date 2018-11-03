<?php
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
if ($referer != null) {
  print $referer;
  if( preg_match("|^http://3bon\.net.*$|", $referer)) {
     print "<span>right access</span>";
  } else {
    print "<span>unauthorized access</span>";
  }
} else {
  print "<span>unauthorized access</span>";
}

?>
