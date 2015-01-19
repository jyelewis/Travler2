<?php
$access = 'login';
require('../SysData/init.php');

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

if(!$userTied->userID && !$userTied->kicked)
{
    echo json_encode(array('error' => 'logout'));
    die();
}

$desktop = new ajax_desktopAjax();
$sendQuery = false;
$startTime = time();
$userTied->allowSave = false;
while(true)
{
    checkTime();
    updateUserTied();
    
    //start launcher code
   
    if ($userTied->data['rebuildLauncher'] == '1')
    {
        $desktop->launcherUpdate(generateLauncherCode());
        $db->writeQuery("UPDATE Users SET rebuildLauncher = '0'");
        $desktop->ready = true;
    }
    //end launcher code
    
    //start message code
    if (count($messages = $db->query("SELECT messageID, message, forceLogout, sessionCode FROM Messages WHERE userID=':1'", $userTied->userID)) > 0)
    {
    	if ($messages[0]['forceLogout'] == 0)
    	{
    		$desktop->showMessage($messages[0]['message']);
    	} else {
    		if ($messages[0]['sessionCode'] == $userTied->data['sessionCode'])
    		{
    			$desktop->logout($messages[0]['message']);
    		}
    	}
    	$db->writeQuery("DELETE FROM Messages WHERE messageID=':1'", $messages[0]['messageID']);
    }
    //end message code
    
    //start move windows code
    $moveWindows = $db->query("SELECT windowID, forceActive, forceHide, forceFS, FSstate, forceReload from Windows WHERE
        userID = ':1' AND forceActive='1' OR
        userID = ':1' AND forceHide='1' OR
        userID = ':1' AND forceFS='1' OR
        userID = ':1' AND forceReload='1'", $userTied->userID);
    if (count($moveWindows) > 0)
    {
        foreach($moveWindows as $moveWindow)
        {
            if ($moveWindow['forceActive'] == '1')  { $desktop->moveWindow($moveWindow['windowID'], 'select'); }
            if ($moveWindow['forceHide'] == '1')    { $desktop->moveWindow($moveWindow['windowID'], 'hide'); }
            if ($moveWindow['forceReload'] == '1')  { $desktop->moveWindow($moveWindow['windowID'], 'reload'); }
            if ($moveWindow['forceFS'] == '1')      {
                 if ($moveWindow['FSstate'] == '1') { $desktop->moveWindow($moveWindow['windowID'], 'fsTrue'); }
                 if ($moveWindow['FSstate'] == '0') { $desktop->moveWindow($moveWindow['windowID'], 'fsFalse'); }
            }
        }
        
    }
    //end move windows code

    
    //start edit windows code
    $changedWindows = $db->query("SELECT * FROM Windows WHERE userID=':1' AND hasChanged='1'", $userTied->userID);
    if(count($changedWindows) > 0)
    {
        foreach($changedWindows as $changedWindow)
        {
            $desktop->editWindow($changedWindow['windowID'], $changedWindow['title'], $changedWindow['applicationID'], $changedWindow['URL']);
        }
    }
    //end edit windows code
    
    //start delete windows code
    $deleteWindows = $db->query("SELECT * FROM Windows WHERE userID=':1' AND hasChanged='2'", $userTied->userID);
    if(count($deleteWindows) > 0)
    {
        foreach($deleteWindows as $deleteWindow)
        {
            $desktop->delWindow($deleteWindow['windowID']);
        }
        $db->writeQuery("DELETE FROM Windows WHERE userID=':1' AND hasChanged='2'", $userTied->userID);
    }
    //end delete windos code
    
    //keep the database up to date with connections so you know when a user is logged out
    if ($userTied->data['lastConnection'] != time())
    {
    	$db->writeQuery("UPDATE Users SET lastConnection=':1' WHERE userID=':2'", time(), $userTied->userID);
    	updateUserTied();
    }
    //end connection time code
        
    //start logout from remote access
    checkSession(true);
    //end logout from remote access
    
    if($desktop->ready){ $desktop->send(); }
    usleep(100000);
}


//functions
function checkTime()
{
    global $startTime;
    if ($startTime+17 <= time())
    {
        ajax_desktop::timeout();
    }
    return true;
}
?>