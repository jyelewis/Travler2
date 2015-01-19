<?php
require('../SysData/init.php');
if(!isset($_GET['file'])){ die(); }
$fs = new travlr_filesystem();
$file = $_GET['file'];

if ($fs->fileExists($file))
{
	serve_file_resumable($file);
	die();	
} else {
	header("HTTP/1.0 404 Not Found");
	echo 'File doesn\'t exist!';
}

function serve_file_resumable($filename) {
global $fs;
// Avoid sending unexpected errors to the client - we should be serving a file,
// we don't want to corrupt the data we send
@error_reporting(0);

// Get the 'Range' header if one was sent
if (isset($_SERVER['HTTP_RANGE'])) $range = $_SERVER['HTTP_RANGE']; // IIS/Some Apache versions
else if ($apache = apache_request_headers()) { // Try Apache again
  $headers = array();
  foreach ($apache as $header => $val) $headers[strtolower($header)] = $val;
  if (isset($headers['range'])) $range = $headers['range'];
  else $range = FALSE; // We can't get the header/there isn't one set
} else $range = FALSE; // We can't get the header/there isn't one set

// Get the data range requested (if any)
$filesize = $fs->getSize($filename);
$contenttype = $fs->getMime($filename);

if ($range) {
  $partial = true;
  list($param,$range) = explode('=',$range);
  if (strtolower(trim($param)) != 'bytes') { // Bad request - range unit is not 'bytes'
	header("HTTP/1.1 400 Invalid Request");
	exit;
  }
  $range = explode(',',$range);
  $range = explode('-',$range[0]); // We only deal with the first requested range
  if (count($range) != 2) { // Bad request - 'bytes' parameter is not valid
	header("HTTP/1.1 400 Invalid Request");
	exit;
  }
  if ($range[0] === '') { // First number missing, return last $range[1] bytes
	$end = $filesize - 1;
	$start = $end - intval($range[0]);
  } else if ($range[1] === '') { // Second number missing, return from byte $range[0] to end
	$start = intval($range[0]);
	$end = $filesize - 1;
  } else { // Both numbers present, return specific range
	$start = intval($range[0]);
	$end = intval($range[1]);
	if ($end >= $filesize || (!$start && (!$end || $end == ($filesize - 1)))) $partial = false; // Invalid range/whole file specified, return whole file
  }      
  $length = $end - $start + 1;
} else $partial = false; // No range requested

// Send standard headers
header("Content-Type: $contenttype");
header("Content-Length: $filesize");
header('filename="'.basename($filename).'"');
header('Accept-Ranges: bytes');

// if requested, send extra headers and part of file...
if ($partial) {
  header('HTTP/1.1 206 Partial Content'); 
  header("Content-Range: bytes $start-$end/$filesize"); 

  echo $fs->file($filename, $start, $length);
} else $fs->file($filename); // ...otherwise just send the whole file

// Exit here to avoid accidentally sending extra content on the end of the file
exit;

}
?>