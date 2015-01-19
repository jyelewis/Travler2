<?php
class data_tied {
    private $dbCache;
    private $dbFile;
    private $destroy = false;
    private $tiedID;
    private $tiedDB;
    function __construct($file, $sessionName = false) {
        global $siteLocalRoot, $db;
        if ($sessionName != false){
            if (!isset($_COOKIE['tied'])){
                $cookieID = md5(uniqid($_SERVER['REMOTE_ADDR']).rand());
                setcookie('tied', $cookieID, 0, "/");
                $file = $cookieID.'--'.md5($sessionName).'';
                $this->tiedID = $cookieID;
            } else {
                $file = $_COOKIE['tied'].'--'.md5($sessionName).'';
                $this->tiedID = $_COOKIE['tied'];
            }
        }
        $this->tiedDB = $db;
        if (!count($this->tiedDB->query("SELECT tiedDataID FROM tiedData WHERE title=':1'", $file)))
        {
            $this->tiedDB->writeQuery("INSERT INTO tiedData (`title`, `data`) VALUES (':1', ':2')", $file, $this->encode(array()));
            $this->dbCache = array();
        } else {
        	$data = $this->tiedDB->query("SELECT data FROM tiedData WHERE title=':1'", $file);
            $this->dbCache = $this->decode($data[0]['data']);
        }
        
        $this->allowSave = true;
        $this->dbFile = $file;
        return $this;
    }
    function __destruct() {
        if (!$this->destroy)
        {
            if(count($this->tiedDB->query("SELECT tiedDataID FROM tiedData WHERE title=':1'", $this->dbFile)) && $this->allowSave == true)
            {
                $this->tiedDB->writeQuery("UPDATE tiedData SET data=':2' WHERE title=':1'", $this->dbFile, $this->encode($this->dbCache));
            }
        } else {
            $this->tiedDB->writeQuery("DELETE FROM tiedData WHERE title=':1'", $this->dbFile);
        }
    }
    function __get($var) {
        if(array_key_exists($var, $this->dbCache)){
            return $this->dbCache[$var];
        } else {
            return false;
        }
    }
    function __set($var, $value) {
        $this->dbCache[$var] = $value;
    }
    
    function clearAllData()
    {
        $this->dbCache = array();
        $this->destroy = true;
    }
    function getSessionID()
    {
        return $this->tiedID;
    }
    public function rawWrite($data)
    {
        $this->allowSave = false;
        $this->tiedDB->writeQuery("UPDATE tiedData SET data=':2' WHERE title=':1'", $this->dbFile, $this->encode($data));
    }
    private function encode($data)
    {
    	return base64_encode(serialize($data));
    }
    private function decode($data)
    {
    	return unserialize(base64_decode($data));
    }
}
?>
