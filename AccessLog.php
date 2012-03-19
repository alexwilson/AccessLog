<?php
/*
	AccessLog Version 1 (final) - 29/06/2011
	This was originally a module for my very first CMS, which never reached
	completion. I picked it up earlier today after discovering it in an old
	backup. All I've done is cleaned it up a little. Intended for inclusion
	within another page, either an include(); or an <iframe>.

	For changelog, please see my git repo, at http://git.antoligy.com/


	Copyright (c) 2009-2011 Alex "Antoligy" Wilson <antoligy@antoligy.com>

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/
	// Firstly time to declare the main arrays
	// Don't touch these lines unless you know what you're doing
	$file = array();
	$entry = array();


	// Now, configuration:

	// Store entries in a rich-text format?
	// if false, entries will be in a text file
	$richtext = true;

	// filename (minus extension)
	$file['name'] = 'logs';

	// directory that you want your file to be in
	// ensure there isn't a trailing /
	$file['path'] = '.';

	// What shall we use for the IP locator (if none, leave blank)?
	$IP_lookup = 'http://www.geobytes.com/IpLocator.htm?GetLocation&IpAddress='


	// This is the part where stuff starts happening


	// Check if server globals for IP, User-agent, referrer and URI exist, and ensures they have a valid value.
	$headers = array(	'HTTP_USER_AGENT' => 'useragent', 
			'HTTP_REFERER' => 'referrer', 
			'REQUEST_URI' => 'uri',
			'REMOTE_ADDR' => 'IP'
			);
	// Rudimentary reverse-proxy detection, (With order generic -> cloudflare, in case you use Varnish or similar).
	switch($_SERVER)) {
		case isset($_SERVER['HTTP_X_REAL_IP']) {
			$headers['HTTP_X_REAL_IP'] = 'IP';
		break;

		case isset($_SERVER['HTTP_CF_CONNECTING_IP']) {
			$headers['HTTP_CF_CONNECTING_IP'] = 'IP';
		break;
	}

	// Looping through $_SERVER globals to fetch headers.
	foreach($headers as $header => $output) {
		if(isset($_SERVER[$header]) && ($_SERVER[$header] != "")) {
			$entry[$output] = $_SERVER[$header];
		} else {
			$entry[$output] = 'N/A';
		}
	}

	// Hostname, this is self explainatory.
	$entry['hostname'] = gethostbyaddr($entry['IP']);
	// Time, as above if you can't understand this, you shouldn't be reading this.
	$entry['time'] = date('d-m-Y H:i:s');

	// Getting file extension (I know this should be grouped with the other if statement)
	if($richtext) {
		$file['extension'] = '.html';
	} else {
		$file['extension'] = '.txt';
	}

	// Assembling the full filename
	$file['name'] = $file['path'] . '/' . $file['name'] . $file['extension'];

	// Just so that the logs don't have to be spammed while testing
	if(isset($_GET['debug'])) {
		die(print_r($entry, 1));
	}
	// "Rich-text"
	elseif($richtext) {
	//	$file['extension'] = '.html';
		if(!file_exists($file['name'])) {
			$entry['data'] = "<table border=\"1\"><tr> \n <th>Time</th> \n <th>IPAddress</th> \n <th>Hostname</th> \n <th>User-agent</th> \n <th>URI</th> \n <th>Referrer</th></tr> \n";
		}
		$entry['data'] .= '<tr><td>' . $entry['time'] . '</td>' . "\n" . '<td><a href="$IP_trace . $entry['IP'] . '">' .$entry['IP'] . '</td>' . "\n" . '<td>' . $entry['hostname'] . '</td>' . "\n" . '<td>' . $entry['useragent']. '</td>' . "\n" . '<td>' . $entry['uri'] . '</td>' . "\n" . '<td>' . $entry['referrer'] . '</td>' . "\n" . '</tr>' . "\n";
	}
	// Plaintext
	elseif(!$richtext) {
	//	$file['extension'] = '.txt';
		$entry['data'] = $entry['time'] . ' - ' . $entry['IP'] . ' - ' . $entry['hostname'] . ' - ' . $entry['useragent'] . ' - ' . $entry['uri'] . ' - ' . $entry['referrer'] . "\n";
	}


	// Writing the file
	$file['data'] = fopen($file['name'],"a");
	fwrite($file['data'],$entry['data']);
	fclose($file['data']);
?>
