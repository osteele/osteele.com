<?php
  // Proxy server for del.ico.us.
  //
  // This file proxies authenticated requests to del.icio.us,
  // throttled to less than one per second.
  //
  // Copyright 2005 by Oliver Steele.  All rights reserved.

  //
  // Configuration:
  //
$url = "http://del.icio.us/api/posts/all";
$user_agent = 'expialidocio.us';
$authentication_prompt = 'del.iciou.us account info';
// Interval, in seconds, between requests.  The del.icio.us terms of
// service call for one second, but wait two to be conservative, and
// also to accomodate server delays between the modtime update and the
// request.
$interval=2;
// The next two files are used to throttle requests across processes.
// They must already exist and have server write privileges.
// There are two files because acquiring a file's lock updates its
// timestamp, but we can't read the timestamp until we have the lock
// otherwise another process might read the same timestamp.
$lockfile='lock.txt'; // implements the lock; contents and timestamp aren't used
$timefile='time.txt'; // implements the timestamp; lock and contents aren't used

//
// Execution:
//

if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_POST['user'])) {
	header('WWW-Authenticate: Basic realm="'.$authentication_prompt.'"');
	header('HTTP/1.0 401 Unauthorized');
	echo '<cancel/>'; // Parseable XML response
	exit;
 } else {
	if (isset($_SERVER['PHP_AUTH_USER'])) {
		$user = $_SERVER['PHP_AUTH_USER'];
		$passwd = $_SERVER['PHP_AUTH_PW'];
	} else {
		$user = $_POST['user'];
		$passwd = $_POST['password'];
	}
	
	clearstatcache(); // so that filemtime works below
	
	// First, acquire the lock, so that no one else reads
	// the same time and sleeps only until the next interval
	// even though we're going to make a request then.
	$lf=fopen($lockfile,'w');
	flock($lf, LOCK_EX);
	
	$fm = filemtime($timefile);
	$next_access_time = $fm + $interval;
	$delay = $next_access_time - time();
	if ($delay > 0) sleep($delay);
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$passwd);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	
	curl_exec($ch);
	
	if (curl_errno($ch)) {
		$err = curl_error($ch);
		$codestr = '';
		if (preg_match('/requested URL returned error:\s*(\d+)/mi', $err, $matches)) {
			$codestr = " code='$matches[1]' ";
		}
		$message = urlencode($err);
		echo "<error message='$message' $codestr/>";
		exit;
	}
	
	curl_close($ch);
	
	// Update the timestamp
	$tfs = fopen($timefile, 'w');	 
	// It doesn't actually matter what's written here;
	// just touch the modtime
	fwrite($tfs, "$next_access_time\n");
	fclose($tfs);
	
	flock($lf, LOCK_UN);
	fclose($lf);
 }
?>
