<?
/*
 created by Kae - kae@verens.com
 I can't be bothered with crappy copyright notices.
 I wrote this. Feel free to use it.
 Please retain this notice.
*/
include_once('../../wp-blog-header.php');
$month=($_GET['month']<10)?'0'.$_GET['month']:$_GET['month'];
$q=mysql_query('select post_date,post_title from '.$table_prefix.'posts where post_status="publish" and post_date like "'.$_GET['year'].'-'.$month.'-%" order by post_date');
$date='';
while($r=mysql_fetch_array($q)){
 $r['post_date']=preg_replace('/ .*/','',$r['post_date']);
 if($r['post_date']!=$date){
  if($date!='')echo "\n";
  $date=$r['post_date'];
  echo $date.': ';
 }else echo ', ';
 echo $r['post_title'];
}
?>
