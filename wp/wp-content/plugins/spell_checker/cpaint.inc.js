// CPAINT - Cross-Platform Asynchronous INterface Toolkit - version 1.01
// Copyright (c) 2005 Boolean Systems, Inc. - http://cpaint.sourceforge.net

var cpaint_shared_httpobj;

function cpaint_get_connection_object() {
	try {
		cpaint_httpobj_temp = new ActiveXObject('Msxml2.XMLHTTP');
	} catch (e) {
		try {  
			cpaint_httpobj_temp = new ActiveXObject('Microsoft.XMLHTTP');
		} catch (oc) {
			cpaint_httpobj_temp = null;
		} 
	}
	if (!cpaint_httpobj_temp && typeof XMLHttpRequest != 'undefined') 
		cpaint_httpobj_temp = new XMLHttpRequest();
	if (!cpaint_httpobj_temp) alert('[CPAINT Error] Could not create connection object');
	return cpaint_httpobj_temp;
}

function cpaint_call() { 
	// Arguments:  {url}, {method}, {backend function name}, {argument1} ... {argumentN}, {JS callback function}, {returnType - OPTIONAL = TEXT | XML (default is 'TEXT')}
	var cpaint_args = cpaint_call.arguments, cpaint_url ='', cp_querystring = '', cp_i, cpaint_httpobj;
	var cpaint_cbfunction = '', cpaint_lastargument = 0, cpaint_returntype = '';
	if ((cpaint_args[cpaint_args.length - 1] == 'TEXT') || (cpaint_args[cpaint_args.length - 1] == 'XML')) {
		cpaint_cbfunction = cpaint_args[cpaint_args.length - 2];
		cpaint_lastargument = cpaint_args.length - 2;
		cpaint_returntype = cpaint_args[cpaint_args.length - 1];
	} else {
		cpaint_cbfunction = cpaint_args[cpaint_args.length - 1];
		cpaint_lastargument = cpaint_args.length - 1;
		cpaint_returntype = 'TEXT';
	}
	if (typeof(cpaint_use_multiple_connections) == 'undefined') cpaint_use_multiple_connections = false;
	if (typeof(cpaint_debug) == 'undefined') cpaint_debug = false;
	if (cpaint_args[0] == 'SELF') {
		cpaint_url = document.location.href;
	} else {
		cpaint_url = cpaint_args[0];
	}
	for (cp_i = 3; cp_i < cpaint_lastargument; cp_i++) 
		cp_querystring = cp_querystring + '&cpaint_argument[]=' + encodeURIComponent(cpaint_args[cp_i]);
	if (cpaint_args[1] == 'GET') {
		cpaint_url = cpaint_url + '?cpaint_function=' + cpaint_args[2] + cp_querystring;
	} else {
		cp_querystring = 'cpaint_function=' + cpaint_args[2] + cp_querystring;
	}
	if (cpaint_returntype == 'XML') cp_querystring = cp_querystring + '&cpaint_returnxml=true';
	if (cpaint_use_multiple_connections == true)
	{
		if (cpaint_debug == true) alert('[CPAINT Debug] Using new connection object');
		cpaint_httpobj = cpaint_get_connection_object();
	} else {
		if (cpaint_debug == true) alert('[CPAINT Debug] Using shared connection object.');
		if (typeof(cpaint_shared_httpobj) == 'undefined') {
			if (cpaint_debug == true) alert('[CPAINT Debug] Getting new shared connection object.');
			cpaint_shared_httpobj = cpaint_get_connection_object();
		}
		cpaint_httpobj = cpaint_shared_httpobj;
	}
	if (cpaint_httpobj.readyState != 4) cpaint_httpobj.abort();
	cpaint_httpobj.open(cpaint_args[1], cpaint_url, true);
	if (cpaint_args[1] == "POST") {
		try {
			cpaint_httpobj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		} catch(cp_err) {
			alert('[CPAINT Error] POST cannot be completed due to incompatible browser.  Use GET as your request method.');
		}
	}
	cpaint_httpobj.onreadystatechange = function() {
		if (cpaint_httpobj.readyState != 4) return; 
		if (cpaint_debug == true) alert('[CPAINT Debug] ' + cpaint_httpobj.responseText);
		if (cpaint_returntype == 'XML') {
			cpaint_cbfunction(cpaint_httpobj.responseXML); 
		} else {
			cpaint_cbfunction(cpaint_httpobj.responseText);
		}
	}
	if (cpaint_args[1] == 'GET') {
		cpaint_httpobj.send(null);
	} else {
		cpaint_httpobj.send(cp_querystring);
	}
} 				

function cpaint_get_remote_file() {
	// Arguments:  {proxy_file}, {remoteURL}, {method}, {returnType}, {JS callback function}, {param1_name}, {param1_value} ... {paramN_name}, {paramN_value}
	// {proxy_file} = cpaint_proxy.asp | cpaint_proxy.php
	// {method} = GET | POST
	// {returnType} = TEXT | XML
	var cpaint_args = cpaint_get_remote_file.arguments;
	var cpaint_url = cpaint_args[0];
	var cp_querystring = '', cp_i = 5, cpaint_httpobj;
	if (typeof(cpaint_use_multiple_connections) == 'undefined') cpaint_use_multiple_connections = false;
	if (typeof(cpaint_debug) == 'undefined') cpaint_debug = false;
	while (cp_i <= cpaint_args.length - 1) {
		cp_querystring = cp_querystring + escape(cpaint_args[cp_i] + '=' + cpaint_args[cp_i + 1] + '&');
		cp_i = cp_i + 2;
	}
	if (cpaint_args[2] == 'GET') {
		cpaint_url = cpaint_url + '?cpaint_remote_url=' + escape(cpaint_args[1]) + '&cpaint_remote_method=' + cpaint_args[2] + '&cpaint_remote_returntype=' + cpaint_args[3] + '&cpaint_remote_query=' + cp_querystring;
	} else {
		cp_querystring = 'cpaint_remote_url=' + escape(cpaint_args[1]) + '&cpaint_remote_method=' + cpaint_args[2] + '&cpaint_remote_returntype=' + cpaint_args[3] + '&cpaint_remote_query=' + cp_querystring;
	}
	if (cpaint_use_multiple_connections == true)
	{
		if (cpaint_debug == true) alert('[CPAINT Debug] Using new connection object');
		cpaint_httpobj = cpaint_get_connection_object();
	} else {
		if (cpaint_debug == true) alert('[CPAINT Debug] Using shared connection object.');
		if (typeof(cpaint_shared_httpobj) == 'undefined') {
			if (cpaint_debug == true) alert('[CPAINT Debug] Getting new shared connection object.');
			cpaint_shared_httpobj = cpaint_get_connection_object();
		}
		cpaint_httpobj = cpaint_shared_httpobj;
	}
	if (cpaint_httpobj.readyState != 4) cpaint_httpobj.abort();
	cpaint_httpobj.open(cpaint_args[2], cpaint_url, true);
	if (cpaint_args[2] == "POST") {
		try {
			cpaint_httpobj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		} catch(cp_err) {
			alert('[CPAINT Error] POST cannot be completed due to incompatible browser.  Use GET as your request method.');
		}
	}
	cpaint_httpobj.onreadystatechange = function() {
		if (cpaint_httpobj.readyState != 4) return; 
		if (cpaint_debug == true) alert('[CPAINT Debug] ' + cpaint_httpobj.responseText);
		if (cpaint_args[3] == 'TEXT') {
			cpaint_args[4](cpaint_httpobj.responseText); 
		} else {
			cpaint_args[4](cpaint_httpobj.responseXML); 
		}
	}
	if (cpaint_args[2] == 'GET') {
		cpaint_httpobj.send(null);
	} else {
		cpaint_httpobj.send(cp_querystring);
	}
}