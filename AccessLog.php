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
<?php
	// Firstly time to declare the main arrays
	// Don't touch these lines unless you know what you're doing
	$file = array();
	$entry = array();


	// Now, configuration.

	// Store entries in a rich-text format?
	// if false, entries will be in a text file
	$richtext = true;
	// filename (minus extension)
	$file['name'] = 'log';
	// directory that you want your file to be in
	// ensure there isn't a trailing /
	$file['path'] = '.';

	// This is the part where stuff starts happening


	// Check if server globals for IP, User-agent, referrer and URI exist, and ensures they have a valid value.
	$headers = array('REMOTE_ADDR' => 'IP', 'HTTP_USER_AGENT' => 'useragent', 'HTTP_REFERER' => 'referrer', 'REQUEST_URI' => 'uri');
	foreach($headers as $header => $output) {
		if(isset($_SERVER[$header]) && ($_SERVER[$header] != "")) {
			$entry[$output] = $_SERVER[$header];
		}
		else
		{
			$entry[$output] = 'N/A';
		}
	}

	// Hostname, this is self explainatory.
	$entry['hostname'] = gethostbyaddr($entry['IP']);
	// Time, as above if you can't understand this, you shouldn't be reading this.
	$entry['time'] = date('d-m-Y H:i:s');

	// Just so that the logs don't have to be spammed while testing
	if(isset($_GET['debug'])) {
		die(print_r($entry, 1));
	}
	// "Rich-text"
	elseif($richtext) {
		$file['extension'] = '.html';
		$entry['data'] = '<tr><td>' . $entry['time'] . '</td>\n\r<td><a href="http://www.geobytes.com/IpLocator.htm?GetLocation&IpAddress=' . $entry['IP'] . '">' .$entry['IP'] . '</td>\n\r<td>' . $entry['hostname'] . '</td>\n\r<td>' . $entry['useragent']. '</td>\n\r<td>' . $entry['uri'] . '</td>\n\r<td>' . $entry['referrer'] . '</td>\n\r</tr>\n\r';
	}
	// Plaintext
	elseif(!$richtext) {
		$file['extension'] = '.txt';
		$entry['data'] = $entry['time'] . ' - ' . $entry['IP'] . ' - ' . $entry['hostname'] . ' - ' . $entry['useragent'] . ' - ' . $entry['uri'] . ' - ' . $entry['referrer'] . "\n\r";
	}

	// Assembling the full filename
	$file['name'] = $file['path'] . '/' . $file['name'] . $file['extension'];

	// Writing the file
	$file['data'] = fopen($filename,"a");
	@fwrite($file['data'],$entry['data']);
	@fclose($file['name']);
?>
