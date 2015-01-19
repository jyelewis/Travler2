<?php
class travlr_window
{
    function __construct($isNew, $appID, $name, $title = false, $url = false, $data = false)
    {
        global $db, $userTied;
        if ($isNew)
        {
            $db->writeQuery("INSERT INTO Windows (
                                    `userID`, `name`, `title`, `applicationID`, `URL`, `data`, `hasChanged`
                                ) VALUES (
                                    ':1', ':2', ':3', ':4', ':5', ':6', '1' 
                                )
                            ", $userTied->data['userID'], $name, $title, $appID, $url, base64_encode(serialize($data)));
            $this->windowID = $db->lastRowID();
            travlr_launcher::add($appID);
        } else {
            $result = $db->query("SELECT windowID FROM Windows WHERE applicationID=':1' AND name=':2' AND userID=':3'", $appID, $name, $userTied->userID);
            $this->windowID = $result[0]['windowID'];
        }
        $result = $db->query("SELECT * FROM Windows WHERE windowID=':1' AND userID=':2'", $this->windowID, $userTied->userID);
        $this->info = $result[0];
        $this->data = unserialize(base64_decode($this->info['data']));
        return $this;
    }
    public function close()
    {
        global $db, $userTied;
        $db->writeQuery("UPDATE Windows SET hasChanged='2' WHERE windowID=':1' AND userID=':2'", $this->windowID, $userTied->userID);
        travlr_launcher::remove($this->info['applicationID']);
    }
    public function setTitle($title)
    {
        global $db, $userTied;
		$result = $db->query("SELECT title FROM Windows WHERE title=':1' AND userID=':2'", $title, $userTied->userID);
		if (count($result) == 0){
				$db->writeQuery("UPDATE Windows SET title=':1', hasChanged='1' WHERE windowID=':2'", $title, $this->windowID);
		}
    }
    public function setData($data)
    {
    	global $db, $userTied;
    	$db->writeQuery("UPDATE Windows SET data=':1' WHERE windowID=':2'", base64_encode(serialize($data)), $this->windowID);
    	$this->data = $data;
    }
    public function setURL($url)
    {
        global $db;
        $db->writeQuery("UPDATE Windows SET URL=':1', hasChanged='1' WHERE windowID=':2'", $url, $this->windowID);
    }
    public function show()
    {
        global $db;
        $db->writeQuery("UPDATE Windows SET forceActive='1' WHERE windowID=':1'", $this->windowID);
    }
    public function hide()
    {
        global $db;
        $db->writeQuery("UPDATE Windows SET forceHide='1' WHERE windowID=':1'", $this->windowID);
    }
    public function fullscreen($bool)
    {
        global $db;
        $FSstate = ($bool) ? '1' : '0';
        $db->writeQuery("UPDATE Windows SET FSstate=':2' WHERE windowID=':1'", $this->windowID, $FSstate);
        $db->writeQuery("UPDATE Windows SET forceFS='1' WHERE windowID=':1'", $this->windowID);
    }
}
?>
