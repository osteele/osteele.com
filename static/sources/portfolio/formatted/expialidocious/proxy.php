<!DOCTYPE html PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML>
<HEAD>
<TITLE>proxy.php</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#1F00FF" ALINK="#FF0000" VLINK="#9900DD">
<A NAME="top">
<A NAME="file1">
<H1>expialidocious/proxy.php</H1>

<PRE>
<B><FONT COLOR="#5F9EA0">&lt;?</FONT></B>php
  <I><FONT COLOR="#B22222">// Proxy server for del.ico.us.
</FONT></I>  <I><FONT COLOR="#B22222">//
</FONT></I>  <I><FONT COLOR="#B22222">// This file proxies authenticated requests to del.icio.us,
</FONT></I>  <I><FONT COLOR="#B22222">// throttled to less than one per second.
</FONT></I>  <I><FONT COLOR="#B22222">//
</FONT></I>  <I><FONT COLOR="#B22222">// Copyright 2005 by Oliver Steele.  All rights reserved.
</FONT></I>
  <I><FONT COLOR="#B22222">//
</FONT></I>  <I><FONT COLOR="#B22222">// Configuration:
</FONT></I>  <I><FONT COLOR="#B22222">//
</FONT></I><FONT COLOR="#B8860B">$url</FONT> = <FONT COLOR="#BC8F8F"><B>&quot;http://del.icio.us/api/posts/all&quot;</FONT></B>;
<FONT COLOR="#B8860B">$user_agent</FONT> = <FONT COLOR="#BC8F8F"><B>'expialidocio.us'</FONT></B>;
<FONT COLOR="#B8860B">$authentication_prompt</FONT> = <FONT COLOR="#BC8F8F"><B>'del.iciou.us account info'</FONT></B>;
<I><FONT COLOR="#B22222">// Interval, in seconds, between requests.  The del.icio.us terms of
</FONT></I><I><FONT COLOR="#B22222">// service call for one second, but wait two to be conservative, and
</FONT></I><I><FONT COLOR="#B22222">// also to accomodate server delays between the modtime update and the
</FONT></I><I><FONT COLOR="#B22222">// request.
</FONT></I><FONT COLOR="#B8860B">$interval</FONT>=2;
<I><FONT COLOR="#B22222">// The next two files are used to throttle requests across processes.
</FONT></I><I><FONT COLOR="#B22222">// They must already exist and have server write privileges.
</FONT></I><I><FONT COLOR="#B22222">// There are two files because acquiring a file's lock updates its
</FONT></I><I><FONT COLOR="#B22222">// timestamp, but we can't read the timestamp until we have the lock
</FONT></I><I><FONT COLOR="#B22222">// otherwise another process might read the same timestamp.
</FONT></I><FONT COLOR="#B8860B">$lockfile</FONT>=<FONT COLOR="#BC8F8F"><B>'lock.txt'</FONT></B>; <I><FONT COLOR="#B22222">// implements the lock; contents and timestamp aren't used
</FONT></I><FONT COLOR="#B8860B">$timefile</FONT>=<FONT COLOR="#BC8F8F"><B>'time.txt'</FONT></B>; <I><FONT COLOR="#B22222">// implements the timestamp; lock and contents aren't used
</FONT></I>
<I><FONT COLOR="#B22222">//
</FONT></I><I><FONT COLOR="#B22222">// Execution:
</FONT></I><I><FONT COLOR="#B22222">//
</FONT></I>
<B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#5F9EA0">(</FONT></B><B><FONT COLOR="#0000FF">!</FONT></B><B><FONT COLOR="#0000FF">isset</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$_SERVER</FONT><B><FONT COLOR="#5F9EA0">[</FONT></B><FONT COLOR="#BC8F8F"><B>'PHP_AUTH_USER'</FONT></B><B><FONT COLOR="#5F9EA0">]</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B> &amp;&amp; <B><FONT COLOR="#0000FF">!</FONT></B><B><FONT COLOR="#0000FF">isset</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$_POST</FONT><B><FONT COLOR="#5F9EA0">[</FONT></B><FONT COLOR="#BC8F8F"><B>'user'</FONT></B><B><FONT COLOR="#5F9EA0">]</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B> <B><FONT COLOR="#5F9EA0">{</FONT></B>
	<B><FONT COLOR="#0000FF">header</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#BC8F8F"><B>'WWW-Authenticate: Basic realm=&quot;'</FONT></B>.<FONT COLOR="#B8860B">$authentication_prompt</FONT>.<FONT COLOR="#BC8F8F"><B>'&quot;'</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	<B><FONT COLOR="#0000FF">header</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#BC8F8F"><B>'HTTP/1.0 401 Unauthorized'</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	<B><FONT COLOR="#0000FF">echo</FONT></B> <FONT COLOR="#BC8F8F"><B>'&lt;cancel/&gt;'</FONT></B>; <I><FONT COLOR="#B22222">// Parseable XML response
</FONT></I>	<B><FONT COLOR="#0000FF">exit</FONT></B>;
 <B><FONT COLOR="#5F9EA0">}</FONT></B> <B><FONT COLOR="#A020F0">else</FONT></B> <B><FONT COLOR="#5F9EA0">{</FONT></B>
	<B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#5F9EA0">(</FONT></B><B><FONT COLOR="#0000FF">isset</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$_SERVER</FONT><B><FONT COLOR="#5F9EA0">[</FONT></B><FONT COLOR="#BC8F8F"><B>'PHP_AUTH_USER'</FONT></B><B><FONT COLOR="#5F9EA0">]</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B> <B><FONT COLOR="#5F9EA0">{</FONT></B>
		<FONT COLOR="#B8860B">$user</FONT> = <FONT COLOR="#B8860B">$_SERVER</FONT><B><FONT COLOR="#5F9EA0">[</FONT></B><FONT COLOR="#BC8F8F"><B>'PHP_AUTH_USER'</FONT></B><B><FONT COLOR="#5F9EA0">]</FONT></B>;
		<FONT COLOR="#B8860B">$passwd</FONT> = <FONT COLOR="#B8860B">$_SERVER</FONT><B><FONT COLOR="#5F9EA0">[</FONT></B><FONT COLOR="#BC8F8F"><B>'PHP_AUTH_PW'</FONT></B><B><FONT COLOR="#5F9EA0">]</FONT></B>;
	<B><FONT COLOR="#5F9EA0">}</FONT></B> <B><FONT COLOR="#A020F0">else</FONT></B> <B><FONT COLOR="#5F9EA0">{</FONT></B>
		<FONT COLOR="#B8860B">$user</FONT> = <FONT COLOR="#B8860B">$_POST</FONT><B><FONT COLOR="#5F9EA0">[</FONT></B><FONT COLOR="#BC8F8F"><B>'user'</FONT></B><B><FONT COLOR="#5F9EA0">]</FONT></B>;
		<FONT COLOR="#B8860B">$passwd</FONT> = <FONT COLOR="#B8860B">$_POST</FONT><B><FONT COLOR="#5F9EA0">[</FONT></B><FONT COLOR="#BC8F8F"><B>'password'</FONT></B><B><FONT COLOR="#5F9EA0">]</FONT></B>;
	<B><FONT COLOR="#5F9EA0">}</FONT></B>
	
	<B><FONT COLOR="#0000FF">clearstatcache</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B>; <I><FONT COLOR="#B22222">// so that filemtime works below
</FONT></I>	
	<I><FONT COLOR="#B22222">// First, acquire the lock, so that no one else reads
</FONT></I>	<I><FONT COLOR="#B22222">// the same time and sleeps only until the next interval
</FONT></I>	<I><FONT COLOR="#B22222">// even though we're going to make a request then.
</FONT></I>	<FONT COLOR="#B8860B">$lf</FONT>=<B><FONT COLOR="#0000FF">fopen</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$lockfile</FONT>,<FONT COLOR="#BC8F8F"><B>'w'</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	<B><FONT COLOR="#0000FF">flock</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$lf</FONT>, LOCK_EX<B><FONT COLOR="#5F9EA0">)</FONT></B>;
	
	<FONT COLOR="#B8860B">$fm</FONT> = <B><FONT COLOR="#0000FF">filemtime</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$timefile</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	<FONT COLOR="#B8860B">$next_access_time</FONT> = <FONT COLOR="#B8860B">$fm</FONT> <B><FONT COLOR="#0000FF">+</FONT></B> <FONT COLOR="#B8860B">$interval</FONT>;
	<FONT COLOR="#B8860B">$delay</FONT> = <FONT COLOR="#B8860B">$next_access_time</FONT> <B><FONT COLOR="#0000FF">-</FONT></B> <B><FONT COLOR="#0000FF">time</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	<B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$delay</FONT> &gt; 0<B><FONT COLOR="#5F9EA0">)</FONT></B> <B><FONT COLOR="#0000FF">sleep</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$delay</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	
	<FONT COLOR="#B8860B">$ch</FONT> = <B><FONT COLOR="#0000FF">curl_init</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$url</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	<B><FONT COLOR="#0000FF">curl_setopt</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$ch</FONT>, CURLOPT_USERAGENT, <FONT COLOR="#B8860B">$user_agent</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	<B><FONT COLOR="#0000FF">curl_setopt</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$ch</FONT>, CURLOPT_USERPWD, <FONT COLOR="#B8860B">$user</FONT>.<FONT COLOR="#BC8F8F"><B>':'</FONT></B>.<FONT COLOR="#B8860B">$passwd</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	<B><FONT COLOR="#0000FF">curl_setopt</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$ch</FONT>, CURLOPT_FAILONERROR, 1<B><FONT COLOR="#5F9EA0">)</FONT></B>;
	
	<B><FONT COLOR="#0000FF">curl_exec</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$ch</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	
	<B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#5F9EA0">(</FONT></B><B><FONT COLOR="#0000FF">curl_errno</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$ch</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B> <B><FONT COLOR="#5F9EA0">{</FONT></B>
		<FONT COLOR="#B8860B">$err</FONT> = <B><FONT COLOR="#0000FF">curl_error</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$ch</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
		<FONT COLOR="#B8860B">$codestr</FONT> = <FONT COLOR="#BC8F8F"><B>''</FONT></B>;
		<B><FONT COLOR="#A020F0">if</FONT></B> <B><FONT COLOR="#5F9EA0">(</FONT></B><B><FONT COLOR="#0000FF">preg_match</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#BC8F8F"><B>'/requested URL returned error:\s*(\d+)/mi'</FONT></B>, <FONT COLOR="#B8860B">$err</FONT>, <FONT COLOR="#B8860B">$matches</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B> <B><FONT COLOR="#5F9EA0">{</FONT></B>
			<FONT COLOR="#B8860B">$codestr</FONT> = <FONT COLOR="#BC8F8F"><B>&quot; code='$matches[1]' &quot;</FONT></B>;
		<B><FONT COLOR="#5F9EA0">}</FONT></B>
		<FONT COLOR="#B8860B">$message</FONT> = <B><FONT COLOR="#0000FF">urlencode</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$err</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
		<B><FONT COLOR="#0000FF">echo</FONT></B> <FONT COLOR="#BC8F8F"><B>&quot;&lt;error message='$message' $codestr/&gt;&quot;</FONT></B>;
		<B><FONT COLOR="#0000FF">exit</FONT></B>;
	<B><FONT COLOR="#5F9EA0">}</FONT></B>
	
	<B><FONT COLOR="#0000FF">curl_close</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$ch</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	
	<I><FONT COLOR="#B22222">// Update the timestamp
</FONT></I>	<FONT COLOR="#B8860B">$tfs</FONT> = <B><FONT COLOR="#0000FF">fopen</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$timefile</FONT>, <FONT COLOR="#BC8F8F"><B>'w'</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B>;	 
	<I><FONT COLOR="#B22222">// It doesn't actually matter what's written here;
</FONT></I>	<I><FONT COLOR="#B22222">// just touch the modtime
</FONT></I>	<B><FONT COLOR="#0000FF">fwrite</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$tfs</FONT>, <FONT COLOR="#BC8F8F"><B>&quot;$next_access_time\n&quot;</FONT></B><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	<B><FONT COLOR="#0000FF">fclose</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$tfs</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
	
	<B><FONT COLOR="#0000FF">flock</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$lf</FONT>, LOCK_UN<B><FONT COLOR="#5F9EA0">)</FONT></B>;
	<B><FONT COLOR="#0000FF">fclose</FONT></B><B><FONT COLOR="#5F9EA0">(</FONT></B><FONT COLOR="#B8860B">$lf</FONT><B><FONT COLOR="#5F9EA0">)</FONT></B>;
 <B><FONT COLOR="#5F9EA0">}</FONT></B>
<B><FONT COLOR="#5F9EA0">?&gt;</FONT></B>
</PRE>
<HR>
<ADDRESS>Generated by <A HREF="http://www.iki.fi/~mtr/genscript/">GNU enscript 1.6.1</A>.</ADDRESS>
</BODY>
</HTML>
