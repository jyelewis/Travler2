<?php
$access = 'server';
require('../SysData/init.php');
$userTied->allowSave = false;
require('functions.php');

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$return = array();
$return['sidebar'] = generateLeftBar();
$return['data'] = 'false';
$return['log'] = array();
$stopTime = time() + 15;
$lastResult = $serverDb->query("SELECT value FROM prams WHERE title='serverPower'");
while(true)
{
	$result = $serverDb->query("SELECT content FROM pendingLog");
	if (count($result) != 0)
	{
		$return['data'] = 'true';
		foreach($result as $log)
		{
			$return['log'][] = array('content' => $log['content']);
		}
		$serverDb -> writeQuery("DELETE FROM pendingLog"); 
		break;
	}
	$result = $serverDb->query("SELECT value FROM prams WHERE title='serverPower'");
	if ($result[0]['value'] !== $lastResult[0]['value']){ sleep(1);break; }
	$lastResult = $result;
	usleep(500000);
	if (time() >= $stopTime){ break; }
}
$result = $serverDb->query("SELECT value FROM prams WHERE title='serverPower'");
$return['serverpower'] = ($result[0]['value'])? 'true' : 'false';
echo json_encode($return);
?>