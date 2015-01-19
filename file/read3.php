<?php
require('../SysData/init.php');
if(!isset($_GET['file'])){ die(); }
$fs = new travlr_filesystem();
$file = $_GET['file'];
if ($fs->fileExists($file))
{
	header('content-type: '.$fs->getMime($file));
	echo $fs->file($file);
die();	
} else {
	header("HTTP/1.0 404 Not Found");
	echo 'File doesn\'t exist!';
}

$fileContent = $fs->file($file);
$filesize = strlen($fileContent);

$offset = 0;
$length = $filesize;

if ( isset($_SERVER['HTTP_RANGE']) ) {
	// if the HTTP_RANGE header is set we're dealing with partial content

	$partialContent = true;

	// find the requested range
	// this might be too simplistic, apparently the client can request
	// multiple ranges, which can become pretty complex, so ignore it for now
	preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);

	$offset = intval($matches[1]);
	$length = (isset($matches[2])) ? $matches[2] : $filesize - $offset;
	$length = ((isset($matches[2])) ? intval($matches[2]) : $filesize) - $offset;
	//$length = $filesize;
	serverLog($_SERVER['HTTP_RANGE']);
	serverLog($length);
	
} else {
	$partialContent = false;
	serverLog('whole content');
}



$data1 = substr($fileContent, $offset, $length);

//$data2 = $fileContent;
/*echo md5($data1);
echo '<br>';
echo md5($data2);
*/
if ( $partialContent ) {
	// output the right headers for partial content

	header('HTTP/1.1 206 Partial Content');

	header('Content-Range: bytes ' . $offset . '-' . ($offset + $length-1) . '/' . $filesize);
	serverLog('Content-Range: bytes ' . $offset . '-' . ($offset + $length - 1) . '/' . $filesize);
}

// output the regular HTTP headers
header('Content-Type: ' . $fs->getMime($file));
header('Content-Length: ' . $filesize);
//header('filename="' . basename($file) . '"');
//header('Accept-Ranges: bytes');

// don't forget to send the data too
echo($data1);


?>