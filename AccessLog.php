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
	$config = array();


	// Now, configuration:

	// filename (minus extension)
	$file['name'] = 'logs';

	// directory that you want your file to be in
	// ensure there isn't a trailing /
	$file['path'] = '.';

	// What service shall we use for the IP locator (if none, leave blank)?
	$config['IP_lookup_service'] = 'http://www.geobytes.com/IpLocator.htm?GetLocation&IpAddress=';

	 // Format for timestamp
        $config['timestamp'] = 'd-m-Y H:i:s';

	// Store entries in a rich-text format?
	// if false, entries will be in a text file
	$config['richtext'] = true;


	// This is the part where stuff starts happening


	// Check if server globals for IP, User-agent, referrer and URI exist, and ensures they have a valid value.
	$headers = array('HTTP_USER_AGENT' => 'useragent', 
			'HTTP_REFERER' => 'referrer', 
			'REQUEST_URI' => 'uri',
			'REMOTE_ADDR' => 'IP'
			);
	// Rudimentary reverse-proxy detection, (With order generic -> cloudflare, in case you use Varnish or similar).
	switch($_SERVER) {
		case isset($_SERVER['HTTP_X_REAL_IP']):
			$headers['HTTP_X_REAL_IP'] = 'IP';
		break;

		case isset($_SERVER['HTTP_CF_CONNECTING_IP']):
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
	$entry['time'] = date($config['timestamp']);

	// Getting file extension (I know this should be grouped with the other if statement)
	if($config['richtext']) {
		$file['extension'] = '.html';
	} else {
		$file['extension'] = '.txt';
	}

	// Assembling the full filename
	$file['name'] = $file['path'] . '/' . $file['name'] . $file['extension'];

	// Just so that the logs don't have to be spammed while testing
	/* if(isset($_GET['debug'])) {
		die(print_r($entry, 1));
	} */
	
	// "Rich-text"
	if($config['richtext']) {
		$entry['data'] = (!file_exists($file['name']) or filesize($file['name']) == 0) ? "<table border=\"1\">\n<tr> \n <th>Time</th> \n <th>IP Address</th> \n <th>Hostname</th> \n <th>User-agent</th> \n <th>URI</th> \n <th>Referrer</th> \n</tr> \n" : ""
		. "<tr><td> " . $entry['time'] . " </td> \n <td><a href=\"" . $config['IP_lookup_service'] . $entry['IP'] . "\">" . $entry['IP'] . "</a></td> \n <td>" . $entry['hostname'] . "</td> \n <td>" . $entry['useragent'] . "</td> \n <td><a href=\"" . $entry['uri'] . "\">" . $entry['uri'] . "</a></td> \n <td>" . $entry['referrer'] . "</td> \n</tr> \n";
	}
	// Plaintext
	elseif(!$config['richtext']) {
		 $entry['data'] = $entry['time'] . ' -  ' . $entry['IP'] . ' - ' . $entry['hostname'] . ' - ' . $entry['useragent'] . '  - ' . $entry['uri'] . ' - ' . $entry['referrer'] . "\n";
	}


	// Writing the file
	$file['data'] = @fopen($file['name'], 'a');
	@fwrite($file['data'], $entry['data']);
	@fclose($file['data']);
?>
