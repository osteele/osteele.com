/*Code for 404.php*/
<?
$search_term = substr($_SERVER['REQUEST_URI'],1);
$search_term = urldecode(stripslashes($search_term));
$search_url = 'http://weblogtoolscollection.com/index.php?s=';
$full_search_url = $search_url . $search_term;
$full_search_url = preg_replace('/ /', '%20', $full_search_url);
$full_page = implode("", file($full_search_url));
print_r($full_page); die();
?>
