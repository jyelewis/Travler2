<?php
class travlr_filesystem
{
    function __construct()
    {
    	global $siteLocalRoot, $userTied, $db;
		$this->fs = $db;
		$this->path = '/';
		$this->userPath = $siteLocalRoot.'/file/data/'.$userTied->userID;
		if(!file_exists($this->userPath) || !is_dir($this->userPath))
		{
			mkdir($this->userPath);
		}
    }
    public function truepath($path, $systemPosition = true){
    	global $siteLocalRoot, $userTied;
    	if ($path == '/' && !$systemPosition){ return '/'; }
    	if ($path == '/' && $systemPosition){ return $siteLocalRoot.'/file/data/'.$userTied->userID.'/'; }
		if(substr($path, 0, 1) != '/')
		{
			$path = $this->path.'/'.$path;
		}
		$path = str_replace(array('/', '\\'), '/', $path);
		$parts = array_filter(explode('/', $path), 'strlen');
		$absolutes = array();
		foreach ($parts as $part) {
			if ('.'  == $part) continue;
			if ('..' == $part) {
			array_pop($absolutes);
			} else {
			$absolutes[] = $part;
			}
		}
		$path=implode('/', $absolutes);
		if ($systemPosition)
		{
			return $siteLocalRoot.'/file/data/'.$userTied->userID.'/'.$path;
		} else {
			return '/'.$path;
		}
	}
	public static function createTempFile($data)
	{
		global $tempdir;
		$filename = md5(rand());
		file_put_contents($tempdir.$filename, $data);
		return $tempdir.$filename;
	}
	private function allFilesSearch($dir = false) 
	{ 
		$dir = ($dir)? $dir : $this->userPath;
		$root = scandir($dir);
		foreach($root as $value) 
		{ 
			if(substr($value, 0, 1) === '.') {continue;} 
			if(is_file("$dir/$value")) {$result[]="$dir/$value";continue;} 
			foreach($this->allFilesSearch("$dir/$value") as $value) 
			{ 
				$result[]=$value; 
			} 
		}
		return $result; 
	}
	public function allFiles()
	{
		$subLength = strlen($this->userPath);
		$retVal = array();
		foreach($this->allFilesSearch() as $file)
		{
			$retVal[] = substr($file, $subLength);
		}
		return $retVal;
	}
	public function dirExists($path){
		if($path == '/'){ return true; }
		$dir = $this->truePath($path);
		if(file_exists($dir) && is_dir($dir))
		{
			return true;
		} else {
			return false;
		}
    }
    public function fileExists($path){
		$path = $this->truePath($path);
		if(file_exists($path) && !is_dir($path))
		{
			return true;
		} else {
			return false;
		}
    }
    public function cd($path)
    {
		if ($this->dirExists($path))
		{
			$this->path = $this->truepath($path, false);
			return true;
		} else {
			return false;
		}
    }
    public function pwd()
    {
		return $this->truepath('', false);
    }
    public function ls($dir = '')
    {
		if (!$this->dirExists($dir)) { return false; }
		$virtualDir = $this->truepath($dir, false);
		$dir = $this->truepath($dir);
		$retVal = array();
		foreach(scandir($dir) as $file)
		{
			if(substr($file, 0, 1) != '.')
			{
				$type = (is_dir($file))? 'dir' : 'file';
				$retVal[] = array('path' => $virtualDir.'/'.$file, 'type' => $type, 'title' => $file);
			}
		}
		return $retVal;
    }
    public function file($path)
    {
    	$path = $this->truepath($path);
    	if (!$this->fileExists($path)){ return false; }
    	return file_get_contents($path);
    }
    public function fileByID($fileID)
    {
    	$result = $this->fs->query("SELECT path FROM files WHERE fileID = ':1'", $fileID);
    	return $result[0]['path'];
    }
    public function mkdir($path)
    {
    	$path = $this->truepath($path);
    	if ($this->dirExists($path)){ return false; }
    	mkdir($path);
    	return true;
    }
    public function writeFile($file, $data)
    {	
    	global $userTied;
    	$file = $this->truepath($file, false);
    	if (!$this->fileExists($file)){
    		$this->db->writeQuery("
    		INSERT INTO
    			files
    			(`userID`, `path`, `hasChanged`)
    		VALUES
    			(':1', ':2', '1')	
    		", $userTied->userID, $file);
    	}
		file_put_contents($this->truepath($file), $data);
    	return true;
    }
    public function updateMetadata($file, $meta, $mime)
    {
    	$file = $this->truepath($file, false);
    	$this->fs->writeQuery("UPDATE files SET mime=':1', id3=':2', hasChanged='0' WHERE path = ':3'", $mime, $meta, $file);
    	return true;
    }
    public function getID3($file)
    {
    	global $getID3;
    	$file = $this->truepath($file);
    	if (!file_exists($file)){ return false; }
    	$fileInfo = $getID3->analyze($file);
    	getid3_lib::CopyTagsToComments($fileInfo);
    	return $fileInfo;
    	
    }
    public function getIcon($file)
    {
    	if (!$this->fileExists($file)){ return false; }
    	$fileInfo = $this->getID3($file);
    	return $fileInfo['comments']['picture'][0];
    }
    public function getMime($file)
    {
    	if (!$this->fileExists($file)){ return false; }
    	$fileInfo = $this->getID3($file);

    	return $fileInfo['mime_type'];
    }
    public function getSize($file)
    {
    	$file = $this->truepath($file);
    	return filesize($file);
    }
    public function hasFileUpdates()
    {
    	return $this->fs->query("SELECT fileID FROM files WHERE hasChanged = '1'");
    }
    
    public function serve_file($origfile) {
    	$contenttype = $this->getMime($origfile);
    	$file = $this->truePath($origfile);
		// Avoid sending unexpected errors to the client - we should be serving a file,
		// we don't want to corrupt the data we send
		@error_reporting(0);

		// Make sure the files exists, otherwise we are wasting our time
		if (!$this->fileExists($origfile)){ header("HTTP/1.1 404 Not Found"); exit; }

		// Get the 'Range' header if one was sent
		if (isset($_SERVER['HTTP_RANGE'])) $range = $_SERVER['HTTP_RANGE']; // IIS/Some Apache versions
		else if ($apache = apache_request_headers()) { // Try Apache again
		  $headers = array();
		  foreach ($apache as $header => $val) $headers[strtolower($header)] = $val;
		  if (isset($headers['range'])) $range = $headers['range'];
		  else $range = FALSE; // We can't get the header/there isn't one set
		} else $range = FALSE; // We can't get the header/there isn't one set

		// Get the data range requested (if any)
		$filesize = filesize($file);
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
		header('filename="'.basename($file).'"');
		header('Accept-Ranges: bytes');

		// if requested, send extra headers and part of file...
		if ($partial) {
		  header('HTTP/1.1 206 Partial Content'); 
		  header("Content-Range: bytes $start-$end/$filesize"); 
		  if (!$fp = fopen($file, 'r')) { // Error out if we can't read the file
			header("HTTP/1.1 500 Internal Server Error");
			exit;
		  }
		  if ($start) fseek($fp,$start);
		  while ($length) { // Read in blocks of 8KB so we don't chew up memory on the server
			$read = ($length > 8192) ? 8192 : $length;
			$length -= $read;
			print(fread($fp,$read));
		  }
		  fclose($fp);
		} else readfile($file); // ...otherwise just send the whole file

		// Exit here to avoid accidentally sending extra content on the end of the file
		exit;

	}

}
?>
