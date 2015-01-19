<?php
class data_serverPush
{
	function __construct($pushID, $userID = null)
	{
		global $db, $userTied, $siteRemoteRoot;
		$this->userID = ($userID === NULL)? $userTied->userID : $userID;
		$db->writeQuery("DELETE FROM serverPushData WHERE pushID = ':1' AND userID=':2'", $pushID, $this->userID);
		$this->pushID = $pushID;
		$this->serverURL = $siteRemoteRoot.'/ajax/serverPush.php';
		$this->events = array();
	}
	
	public function bindFunction($function, $event = null)
	{
		$event = ($event === null)? $function : $event;
		$this->events[] = array('function' => $function, 'event' => $event);
	}
	
	public function serverURL($url)
	{
		$this->serverURL = $url;
	}
	
	public function send($data, $event = 'serverMessage')
	{
		global $db;
		$db->writeQuery("
		INSERT INTO
			serverPushData
			(pushID, userID, event, data)
		VALUES
			(':1', ':2', ':3', ':4')
		", $this->pushID, $this->userID, $event, $data);
	}
	
	public function generateCode()
	{
		$retCode = '<script type="text/javascript">
		setTimeout(function(){var serverPushObj = new EventSource(\''.$this->serverURL.'?pushID='.$this->pushID.'\');
		';
		foreach($this->events as $event)
		{		
			$retCode .= 'serverPushObj.addEventListener(\''.$event['event'].'\', function(e) { '.$event['function'].'(e.data) }, false);';
		}
		$retCode .= '}, 100);</script>';
		return $retCode;
	}
	
	//server functions
	public static function server($pushID, $userID = null)
	{
		global $db, $userTied;
		$userID = ($userID === NULL)? $userTied->userID : $userID;
		self::serverStart();
		$startTime = time();
		while(true)
		{		
			$sendDatas = $db->query("SELECT serverPushDataID, event, data FROM serverPushData WHERE userID=':1' AND pushID=':2'", $userID, $pushID);

			if (count($sendDatas) != 0)
			{
				$sql = "DELETE FROM serverPushData WHERE 1=0";
				foreach($sendDatas as $sendData)
				{			
					self::serverPush($sendData['data'], $sendData['event']);
					$sql .= " OR serverPushDataID = '{$sendData['serverPushDataID']}'";
				}
				$db->writeQuery($sql);
			}
			if (time() - $startTime >= 60)
			{
				break;
			}
			usleep(30000);
		}
	}
	
	public static function serverStart()
	{
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		header('Connection: keep-alive');
		echo 'retry: 10'.PHP_EOL;
		flush();
		return true;
	}
	
	public static function serverPush($data, $event)
	{
		echo 'event: '.$event.PHP_EOL;
		$data = str_replace(array("\n\r"), array("\n"), $data);
		$dataLines = explode("\n", $data);
		foreach($dataLines as $line)
		{	
			echo 'data: '.$line.PHP_EOL;
		}
		echo PHP_EOL;
		flush();
	}
}
?>