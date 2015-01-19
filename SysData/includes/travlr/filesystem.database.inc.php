<?php
class travlr_filesystem
{
    function __construct($path = null)
    {
    	global $siteLocalRoot, $userTied, $db;
		$this->fs = $db;
		$this->path = '/';
    }
    private function truepath($path){
    	if ($path == '/'){ return '/'; }
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
		return '/'.$path;
	}
	public static function createTempFile($data)
	{
		global $tempdir;
		$filename = md5(rand());
		file_put_contents($tempdir.$filename, $data);
		return $tempdir.$filename;
	}
	public function dirExists($path){
		if($path == '/'){ return true; }
		$path = $this->truepath($path);
		$result = $this->fs->query("SELECT fileDirectoryID FROM file_directories WHERE CONCAT(path, title)=':1'", $path);
		if(count($result) === 0)
		{
			echo $path;
			return false;
		} else {
			return true;
		}
    }
    public function fileExists($path){
    	$path = $this->truepath($path);
		$result = $this->fs->query("SELECT fileID FROM file_files WHERE CONCAT(path, title)=':1'", $path);
		if(count($result) === 0)
		{
			return false;
		} else {
			return true;
		}
    }
    public function cd($path)
    {
		$path = $this->truepath($path);
		if ($this->dirExists($path))
		{
			$this->path = $path;
			return true;
		} else {
			return false;
		}
    }
    public function pwd()
    {
		return $this->truepath('');
    }
    public function ls($dir = '')
    {
		$path = $this->truepath($dir);
		if (!$this->dirExists($path)){ return false; }
		if ($path != '/'){ $extendSlash = '/'; } else { $extendSlash = ''; }
		$dirResult = $this->fs->query("SELECT fileDirectoryID,title FROM file_directories WHERE path = ':1'", $path.$extendSlash);
		$filesResult = $this->fs->query("SELECT fileID, title FROM file_files WHERE path = ':1'", $path.$extendSlash);
		$retval = array();
		foreach ($dirResult as $result)
		{
			$retval[] = array(
				 'id' => $result['fileDirectoryID']
				,'type' => 'dir'
				,'title' => $result['title']
			);
		}
		foreach ($filesResult as $result)
		{
			$retval[] = array(
				 'id' => $result['fileID']
				,'type' => 'file'
				,'title' => $result['title']
			);
		}
		return $retval;
    }
    public function file($path, $start = null, $length = null)
    {
    	$path = $this->truepath($path);
    	if (!$this->fileExists($path)){ return false; }
    	if ($start == null)
    	{
    		$result = $this->fs->query("SELECT data FROM file_files WHERE CONCAT(path, title) = ':1'", $path);
    		return $result[0]['data'];
    	} else {
    		$result = $this->fs->query("SELECT SUBSTRING(data, :2, :3) as subdata FROM file_files WHERE CONCAT(path, title) = ':1'", $path, $start, $length);
    	    return $result[0]['subdata'];
    	}

    }
    public function fileByID($fileID)
    {
    	$result = $this->fs->query("SELECT CONCAT(path, title) as file FROM file_files WHERE fileID = ':1'", $fileID);
    	$result = $result[0]['file'];
    	return $result;
    }
    public function touch($path)
    {
    	$path = $this->truepath($path);
    	if ($this->fileExists($path)){ return false; }
    	$fileName = basename($path);
    	$filePath = substr($path, 0, -strlen($fileName));
    	$this->fs->writeQuery("INSERT INTO file_files (`title`, `path`) VALUES (':1', ':2')",
    		$fileName, $filePath);
    	return true;
    }
    public function mkdir($path)
    {
    	$path = $this->truepath($path);
    	if ($this->dirExists($path)){ return false; }
    	$dirName = basename($path);
    	$dirPath = substr($path, 0, -strlen($fileName));
    	$this->fs->writeQuery("INSERT INTO file_directories (`title`, `path`) VALUES (':1', ':2')",
    		$dirName, $dirPath);
    	return true;
    }
    public function writeFile($file, $data)
    {
    	$file = $this->truepath($file);
    	if (!$this->fileExists($file)){ $this->touch($file); }
    	$data = $this->fs->db->real_escape_string($data);
    	$file = $this->fs->db->real_escape_string($file);
    	$sql = "UPDATE file_files SET data='{$data}', hasChanged='1' WHERE CONCAT(path, title) = '{$file}'";
    	$this->fs->db->query($sql);
    	$data = null;
    	return true;
    }
    public function updateMetadata($file, $meta, $mime, $filesize)
    {
    	$file = $this->truepath($file);
    	if (!$this->fileExists($file)){ $this->touch($file); }
    	$this->fs->writeQuery("UPDATE file_files SET mime=':1', id3=':2', filesize=':3', hasChanged='0' WHERE CONCAT(path, title) = ':4'", $mime, $meta, $filesize, $file);
    	return true;
    }
    public function getID3($file)
    {
    	$file = $this->truepath($file);
    	if (!$this->fileExists($file)){ return false; }
    	$result = $this->fs->query("SELECT id3 FROM file_files WHERE CONCAT(path, title)=':1' LIMIT 1", $file);
    	return unserialize($result[0]['id3']);
    	
    }
    public function getMime($file)
    {
    	$file = $this->truepath($file);
    	if (!$this->fileExists($file)){ return false; }
    	$result = $this->fs->query("SELECT mime FROM file_files WHERE CONCAT(path, title)=':1' LIMIT 1", $file);
    	return $result[0]['mime'];
    	
    }
    public function getSize($file)
    {
    	$file = $this->truepath($file);
    	if (!$this->fileExists($file)){ return false; }
    	$result = $this->fs->query("SELECT filesize FROM file_files WHERE CONCAT(path, title)=':1' LIMIT 1", $file);
    	return $result[0]['filesize'];
    }
    public function hasFileUpdates()
    {
    	return $this->fs->query("SELECT fileID FROM file_files WHERE hasChanged = '1'");
    }
}
?>
