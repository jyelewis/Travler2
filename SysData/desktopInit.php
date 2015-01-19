<?php
sleep(1);
$db->writeQuery("UPDATE Users SET sessionCode = ':1', lastLogin=':3' WHERE userID=':2'", $userTied->getSessionID(), $userTied->userID, time());

$db->writeQuery("DELETE FROM Windows WHERE userID=':1'", $userTied->userID);
$db->writeQuery("DELETE FROM LauncherIcons WHERE userID=':1'", $userTied->userID);
$db->writeQuery("DELETE FROM Messages WHERE userID=':1' AND forceLogout='1'", $userTied->userID);

//add the applications.system app to the top of the launcher no matter what
$applicationsApp = $db->query("SELECT applicationID FROM Applications WHERE idCode = 'applications.system'");
travlr_launcher::add($applicationsApp[0]['applicationID'], false, true);

//add the rest of launcher apps to the launcher
foreach($db->query("SELECT * FROM LauncherApps WHERE userID=':1'", $userTied->userID) as $launcherItem)
{
    travlr_launcher::add($launcherItem['applicationID'], false, true);
}

$userTied->loginStage = 'desktop';
$userTied->loginStage = 'desktop';

//from database scripts here

$loginScript = $db->query("SELECT loginScript FROM Users WHERE userID=':1'", $userTied->userID);
$loginScript = $loginScript[0]['loginScript'];
if ($loginScript !== '')
{
	eval($loginScript);
}


//finish database scripts

?>
