<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

$serverDb = new db_sqlite($siteLocalRoot.'/SysData/database/server.db');

if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'] && $userTied -> isAuth != true)
{
	header('location: ../index.php');
}
if ($userTied -> isAuth == true && !hasPermission('runServer'))
{
	header('location: ../index.php');
}

$serverRunning = serverIsRunning();
$query = $serverDb->query("SELECT value FROM prams WHERE title = 'serverPower'");
$serverPower   = ($query[0]['value'] == '1')? true : false;


function generateLeftBar()
{
	global $db, $serverDb, $serverPower, $serverRunning, $userTied, $siteLocalRoot, $siteRemoteRoot;
	
	$usersFetch = $db->query("SELECT username FROM Users WHERE lastConnection > :1 ORDER BY username", time()-2);
	$usersUL = new html_ul();
	$usersUL -> stripe();
	$usersUL -> li -> addclass('stripe1');
	$usersUL -> stripeli -> addclass('stripe2');
	
	if (count($usersFetch) > 0)
	{
		foreach($usersFetch as $user){
			$usersUL->addli($user['username']);
		}
	} else {
		$usersUL = false;
	}

	$notificationsFetch = $serverDb->query("SELECT text FROM notifications ORDER BY notificationID");
	$notificationsUL = new html_ul();
	$notificationsUL -> stripe();
	$notificationsUL -> li -> addclass('stripe1');
	$notificationsUL -> stripeli -> addclass('stripe2');
	
	if ($serverPower){
		$notificationsFetch[]['text'] = 'Server power on';
	} else {
		$notificationsFetch[]['text'] = 'Server power off';
	}
	if ($serverRunning){
		$notificationsFetch[]['text'] = 'Server running';
	} else {
		$notificationsFetch[]['text'] = 'Server not running';
	}
	if (count($usersFetch) == 0) {
		$notificationsFetch[]['text'] = 'No users connected';
	}
	foreach($notificationsFetch as $notification){
		$notificationsUL->addli($notification['text']);
	}
	
	$serverStats[] = array('Server IP', $_SERVER['SERVER_ADDR']);
	$serverStats[] = array('Local root', $siteLocalRoot.'/');
	$serverStats[] = array('Remote root', $siteRemoteRoot.'/');
	
	if ($serverPower)
	{
		$serverStats[] = array('Server power', 'on');
	} else {
		$serverStats[] = array('Server power', 'off');
	}
	if ($serverRunning)
	{
		$result = $serverDb->query("SELECT value FROM prams WHERE title='startTime'");
		$uptime = time()-$result[0]['value'];
		$serverStats[] = array('Uptime', floor($uptime/60).' minutes');
		$serverStats[] = array('Server running', 'yes');
	} else {
		$serverStats[] = array('Server running', 'no');
	}
	if ($userTied->isAuth)
	{
		$serverStats[] = array('Logged in user', $userTied->data['username']);
	} else {
		$serverStats[] = array('Logged in user', 'NULL');
	}
	
	$serverStatus = new html_ul();
	$serverStatus -> stripe();
	$serverStatus -> li -> addclass('stripe1');
	$serverStatus -> stripeli -> addclass('stripe2');
	foreach($serverStats as $serverStat)
	{
		$serverStatus -> addli('<div class="title">'.$serverStat[0].'</div><div class="value">'.$serverStat[1].'</div><div class="clear"></div>');
	}

	$retVal = '';
	$retVal .= '<div id="serverStatus" class="panel">'.$serverStatus.'</div>';
	if ($usersUL)
	{
		$retVal .= '<div id="usersBox" class="panel">Logged in users'.$usersUL.'</div>';
	}
	if ($notificationsUL)
	{
		$retVal .= '<div id="notificationsBox" class="panel">Notifications'.$notificationsUL.'</div>';
	}
	return $retVal;
}

function startServer()
{
	global $db, $serverDb, $serverRunning, $serverPower;
	if ($serverRunning) { return true; }
	$serverDb->writeQuery("UPDATE prams SET value = '1' WHERE title='serverPower'");
	$db->writeQuery("UPDATE serverPrams SET value = ':1' WHERE title='lastLoop'", time());
	$serverDb->writeQuery("UPDATE prams SET value = ':1' WHERE title='startTime'", time());
	$serverPower = true;
	sendLog('Powering up server...');
}

function stopServer()
{
	global $db, $serverDb, $serverRunning, $serverPower;
	if(!$serverRunning) { return true; }
	sendLog('Logging out users');
	$result = $db->query("SELECT userID FROM Users WHERE lastConnection > :1", time()-2);
	foreach($result as $user)
	{
		sendMessage($user['userID'], 'The server has been shutdown', true);
	}
	sleep(5);
	$serverDb->writeQuery("UPDATE prams SET value = '0' WHERE title='serverPower'");
	$serverPower = false;
	sendLog('Powering down server...');
}

function sendLog($content)
{
	serverLog($content);
}
?>