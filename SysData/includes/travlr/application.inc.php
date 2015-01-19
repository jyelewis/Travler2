<?php
class travlr_application
{
    function __construct($idCode)
    {
        global $db;
        $result = $db->query("SELECT * From Applications WHERE idCode=':1'", $idCode);
        $this->appInfo = $result[0];
        return $this;
    }
    public function newWindow($name, $title, $url, $data = false)
    {
        global $db;
        $url = getAppPath($this->appInfo['applicationID'], true).$url;
        if (!$this->windowExists($name))
        {
            return new travlr_window(true, $this->appInfo['applicationID'], $name, $title, $url, $data);
        } else {
            $db->writeQuery("UPDATE Windows SET hasChanged='1' WHERE name=':1'", $name);
        }
    }
    public function getWindow($name)
    {
        if($this->windowExists($name))
        {
            return new travlr_window(false, $this->appInfo['applicationID'], $name);
        }
    }
    public function windowExists($name)
    {
       global $db, $userTied;
       $query = $db->query("SELECT windowID FROM Windows
                                WHERE applicationID=':1' AND name=':2' AND userID=':3'",
                            $this->appInfo['applicationID'], $name, $userTied->userID);
       if(count($query)>0)
       {
           return true;
       } else {
           return false;
       }
    }
    public function reloadWindow($name)
    {
    	global $db, $userTied;
    	$query = $db->writeQuery("UPDATE Windows
       						SET forceReload = '1'
                                WHERE applicationID=':1' AND name=':2' AND userID=':3'",
                            $this->appInfo['applicationID'], $name, $userTied->userID);
	}
    public function javascriptInclude($scriptURL)
    {
        global $siteRemoteRoot;
        echo '<script type="text/javascript" src="'.$siteRemoteRoot.'/applications/'.$this->appInfo['idCode'].'/'.$scriptURL.'"></script>';
    }
    public function cssInclude($scriptURL)
    {
        global $siteRemoteRoot;
        echo '<link rel="stylesheet" type="text/css" href="'.$siteRemoteRoot.'/applications/'.$this->appInfo['idCode'].'/'.$scriptURL.'" />';
    }
}
?>
