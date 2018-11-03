#!/usr/local/bin/perl5
use strict;
use utf8;
use Encode;
use DBI;
use XML::FeedPP;
use Digest::MD5  qw(md5 md5_hex md5_base64);

my $d = 'DBI:mysql:antena_db:mysql331.db.sakura.ne.jp';
my $u = 'XXXXX';
my $p = 'XXXXX';

my $dbh = DBI->connect($d, $u, $p, {mysql_enable_utf8 => 1, on_connect_do => ['SET NAMES utf8']});
my $sth = $dbh->prepare("select no, rss_url from site where enable is true");
$sth->execute;

my @rows = ();
while(my @tmp = $sth->fetchrow_array) {
  push(@rows, [@tmp]);
}
foreach my $row (@rows) {
  my $no = @{$row}[0];
  my $rss_url = @{$row}[1];
  my $feed = XML::FeedPP->new($rss_url);
  foreach($feed->get_item()) {
    my $url = $_->link();
    my $id = md5_hex($url);
    my $title = decode('UTF-8', $_->title());
    my $year = substr($_->pubDate(), 0, 4);
    my $mon = substr($_->pubDate(), 5, 2);
    my $day = substr($_->pubDate(), 8, 2);
    my $hour = substr($_->pubDate(), 11, 2);
    my $min = substr($_->pubDate(), 14, 2);
    my $pubDate = "$year$mon$day";
    my $pubTime = "$hour$min";
    
    &insertRecord($id, $url, $title, $pubDate, $pubTime, $no);
    &createPage($id, $url, $title, $pubDate);
  }
}

$sth->finish;
$dbh->disconnect;

sub insertRecord {
  (my $id, my $url, my $title, my $pubDate, my $pubTime, my $no) = @_;
  $sth = $dbh->prepare("select * from rss where url = \'$url\' ");
  $sth->execute;
  my $num = $sth->rows;
  if ($num == 0) {
    $sth = $dbh->prepare("INSERT INTO rss (id, url, title, pubDate, pubTime, site_no) VALUES (\'$id\', \'$url\', \'$title\', \'$pubDate\', \'$pubTime\', \'$no\')");
    $sth->execute;
  }
}

sub createPage {
  (my $id, my $url, my $title, my $pubDate) = @_;
  
  my $dateDir = "/home/antena/www/3bon/page/" . $pubDate;
  my $dir = $dateDir . "/" . $id;
  if (!-d $dateDir){ mkdir $dateDir; }
  if (!-d $dir){ mkdir $dir; }
  
  my $file = $dir . "/index.html";
  open (OUT, ">$file") or die "$!";
  
  &printPage($url, $title)
}

sub printPage {
  (my $url, my $title) = @_;
  &printLine("<html lang='ja'>");
  &printLine("  <head>");
  &printLine("    <title>". $title ."</title>");
  &printLine("  </head>");
  &printLine("  <body>");
  &printLine("    <a href='". $url ."'>記事ページに遷移する</a>");
  &printLine("  </body>");
  &printLine("  <script type='text/javascript'>");
  &printLine("    window.onload = function() {");
  &printLine("      window.location.href = '". $url ."';");
  &printLine("    }");
  &printLine("  </script>");
  &printLine("</html>");
}

sub printLine {
  (my $str) = @_;
  print OUT encode('shift-jis', $str . "\n");
#  print OUT $str . "\n";
}
