<?php
//this should stay at the top as it deturmens wether a file is with in its access lev
date_default_timezone_set("Australia/Sydney"); // remove this line for production
ini_set("memory_limit","3000M");

$dbLogin = array(
	 'host' => 'localhost'
	,'username' => 'travlr'
	,'password' => 'travlr'
	,'database' => 'travlr'
);


//----functions------------------------------------------------------------------------
function __autoload($className)
{
	if ($className == 'getID3')
	{
		include_once(dirname(__FILE__) . '/includes/getid3/getid3.php');
		return true;
	}
    $classDir = str_replace('_', '/', $className);
    include_once(dirname(__FILE__) . '/includes/'.$classDir.'.inc.php');
}
function javascriptInclude($scriptURL)
{
    global $siteRemoteRoot;
    echo '<script type="text/javascript" src="'.$siteRemoteRoot.$scriptURL.'"></script>';
}
function cssInclude($scriptURL)
{
    global $siteRemoteRoot;
    echo '<link rel="stylesheet" type="text/css" href="'.$siteRemoteRoot.$scriptURL.'" />';
}
function updateUserTied()
{
    global $userTied, $db;
    if ($userTied->userID)
    {
        $query = $db->query("
        SELECT
        	 userID
        	,groupID
        	,username
        	,password
        	,fullname
        	,drive
        	,sessionCode
        	,lastLogin
        	,lastConnection
        	,rebuildLauncher
        FROM
        	Users
        WHERE
        	userID=':1'
        ", $userTied->userID);
        $userTied -> data = $query[0];
    }
}
function generateWindowCode($code)
{
    //return substr(md5($code.'!@#16$^wje%$#hfg'), 0, 8);
    return base64_encode('ewko2'.$code);
}

function decriptWindowCode($code)
{
    return substr(base64_decode($code), 5);
}

function generateLauncherCode()
{
    global $db, $userTied, $siteRemoteRoot;
    $launcherIcons = $db->query("
    SELECT
    	 LauncherIcons.applicationID
    	,LauncherIcons.icon
    	,LauncherIcons.title
    	,LauncherIcons.color
    	,LauncherIcons.windowExists
    	,Applications.idCode
    FROM 
    	LauncherIcons
    		INNER JOIN Applications
    			ON LauncherIcons.applicationID = Applications.applicationID
    WHERE
    	userID=':1'
    ", $userTied->userID);
    $launcherIconCode = '';
    foreach($launcherIcons as $launcherIcon)
    {
        if ($launcherIcon['windowExists'] == '1')
        {
            $applicationID = generateWindowCode($launcherIcon['applicationID']);
        } else {
            $applicationID = $launcherIcon['applicationID'];
        }
        
		$iconURL = $siteRemoteRoot.'/applications/'.$launcherIcon['idCode'].'/'.$launcherIcon['icon'];
        $launcherIconCode .= '
            <div class="launcherIcon" data-exists="'.$launcherIcon['windowExists'].'" data-app="'.$applicationID.'" style="background-color:'.$launcherIcon['color'].'">
                <img src="'.$iconURL.'" class="launcherIconImage" />
                <div class="launcherText">
                    <div class="launcherTextStyle">
                        '.$launcherIcon['title'].'
                    </div>
                </div>
            </div>
        ';
    }
    return $launcherIconCode;
}

function checkSession($isAjax = false)
{
    if ($isAjax)
    {
        global $userTied, $desktop;
    } else {
        global $userTied;
    }
    if ($userTied->data['sessionCode'] != $userTied->getSessionID() && $userTied->kicked != true)
    {
        //user has been logged in from another location, kick them out
        $message = '
        	Your account was logged in from another location
            <br>This session has been closed
            <br><br>logging in again will cause any other<br>connections from this account to be logged out
        ';
        if ($isAjax) {
            $desktop->logout($message);
        } else {
        	$userTied->rawWrite(array('kicked' => true, 'kickedMessage' => $message, 'loginStage' => 'login', 'lastUsername' => $userTied->data['username']));
            header('location: login.php');
            die();
        }
    }
}

function rebuildLauncher()
{
    global $db, $userTied;
    $db->writeQuery("UPDATE Users SET rebuildLauncher = '1' WHERE userID = ':1'", $userTied->userID);
}

function getAppPath($appID, $isRemote = false)
{
    global $db, $siteLocalRoot, $siteRemoteRoot;
    $result = $db->query("SELECT idCode FROM Applications WHERE applicationID=':1'", $appID);
    if (!count($result)){ return false; }
    if (!$isRemote) {
        return $siteLocalRoot.'/applications/'.$result[0]['idCode'];
    } else {
        return $siteRemoteRoot.'/applications/'.$result[0]['idCode'];
    }
}

function getAppID($appCode)
{
	global $db;
	$result = $db->query("SELECT applicationID FROM applications WHERE idCode = ':1' LIMIT 1", $appCode);
	return $result[0]['applicationID'];
}

function launchApp($appID)
{
    global $db, $userTied;
    require(getAppPath($appID).'/launch.php');
}


function getDefaultBG($userID)
{
    global $db, $siteRemoteRoot;
    $defaultBG = $db->query("SELECT backgroundImageID from BackgroundImages WHERE isDefault = '1' AND userID=':1' LIMIT 1", $userID);
    if(count($defaultBG) > 0)
    {
        return $siteRemoteRoot.'/ajax/backgroundImage.php?backgroundImageID='.$defaultBG[0]['backgroundImageID']; 
    } else {
        return $siteRemoteRoot.'/images/background.gif';
    }
    
}


function hasPermission($permission, $userID = false)
{
    global $db, $userTied;
	if ($userID == false){
		$userID = $userTied->userID;
	}
	$permissions = $db->query("
	SELECT
		value
	FROM
		permissions
	WHERE
		(userID = ':1' OR groupID = ':2')
		AND title = ':3'
	ORDER BY
		userID DESC
	", $userID, $userTied->groupID, $permission);
   	if (count($permissions) != 0) {
		return $permissions[0]['value'];
    }
    return false;
}

function updateDesktopIcons()
{
    
}

function userLoggedIn($userID)
{
	global $db;
	$result = $db->query("SELECT lastConnection FROM Users WHERE userID = ':1'", $userID);
	if ($result[0]['lastConnection'] >= time()-3)
	{
		return true;
	} else {
		return false;
	}
}

function sendMessage($userID, $message, $forceLogout = false)
{
	global $db;
	if (!hasPermission('sendMessage')){ return false; }
	$forceLogout = ($forceLogout && hasPermission('forceLogout'))? 1 : 0;
	$db->writeQuery("
	INSERT INTO Messages (
	`userID`, `message`, `forceLogout`, `sessionCode`
		) VALUES (
	':1', ':2', ':3', (SELECT sessionCode FROM Users WHERE userID=':1')
	)", $userID, $message, $forceLogout);
}


function serverIsRunning()
{
	global $db;
	$result = $db->query("SELECT value FROM serverPrams WHERE title = 'lastLoop' LIMIT 1");
	if ($result[0]['value'] >= time()-15)
	{
		return true;
	} else {
		return false;
	}
}

function serverLog($content)
{
	global $serverDb, $siteLocalRoot;
	if (!isset($serverDb))
	{
		$serverDb = new db_sqlite($siteLocalRoot.'/SysData/database/server.db');
	}
	$serverDb->writeQuery("INSERT INTO pendingLog (`content`) VALUES (':1')", $content);
}
//----end functions--------------------------------------------------------------------

if (!isset($access)){ $access = 'desktop'; }
$siteLocalRoot = realpath(substr(__FILE__, 0, -9).'/../');
$siteRemoteRoot = substr(substr($siteLocalRoot, strlen($_SERVER['DOCUMENT_ROOT'])), 1);
if ($siteRemoteRoot != '')
{
	$siteRemoteRoot = '/'.$siteRemoteRoot;
}


$db = new db_mysql($dbLogin['host'], $dbLogin['username'], $dbLogin['password'], $dbLogin['database']);
$userTied = new data_tied(false, 'user');
$fs = new travlr_filesystem();
$getID3 = new getID3();


if ($access == 'desktop' && $userTied->loginStage != 'desktop')
{
    header('location: '.$siteRemoteRoot.'/login.php');
    die();
}
if ($access == 'init' && $userTied->loginStage != 'desktopInit')
{
    die();
} 




$tempdir = $siteLocalRoot.'/SysData/temp/';

if ($userTied -> isAuth && $userTied->data['lastConnection'] != time())
{
	$db->writeQuery("UPDATE Users SET lastConnection=':1' WHERE userID=':2'", time(), $userTied->userID);
	updateUserTied();
}

if($userTied->loginStage == 'desktop')
{
    checkSession();
}



$scriptIncludes  = '<script type="text/javascript" src="'.$siteRemoteRoot.'/js/jquery.js"></script>'.PHP_EOL;
$scriptIncludes .= '<link rel="stylesheet" type="text/css" href="'.$siteRemoteRoot.'/css/jquibase/jquery.ui.all.css" />'.PHP_EOL;
$scriptIncludes .= '<script type="text/javascript" src="'.$siteRemoteRoot.'/js/jqueryui.js"></script>'.PHP_EOL;
$scriptIncludes .= '<script type="text/javascript" src="'.$siteRemoteRoot.'/js/fancybox/jquery.fancybox.js"></script>'.PHP_EOL;
$scriptIncludes .= '<script type="text/javascript" src="'.$siteRemoteRoot.'/js/topzindex.js"></script>'.PHP_EOL;
$scriptIncludes .= '<script type="text/javascript" src="'.$siteRemoteRoot.'/js/jqueryui.touch-punch.js"></script>'.PHP_EOL;
$scriptIncludes .= '<script type="text/javascript" src="'.$siteRemoteRoot.'/js/jquerycycle.js"></script>'.PHP_EOL;
$scriptIncludes .= '<script type="text/javascript" src="'.$siteRemoteRoot.'/js/travlr.contextMenu.js"></script>'.PHP_EOL;
$scriptIncludes .= '<link rel="stylesheet" type="text/css" href="'.$siteRemoteRoot.'/js/fancybox/jquery.fancybox.css"/ >'.PHP_EOL;
$scriptIncludes .= '<script type="text/javascript" src="'.$siteRemoteRoot.'/js/jquery.contextMenu.js"></script>'.PHP_EOL;
$scriptIncludes .= '<link rel="stylesheet" type="text/css" href="'.$siteRemoteRoot.'/css/jquery.contextMenu.css"/ >'.PHP_EOL;
$scriptIncludes .= '<link rel="stylesheet" type="text/css" href="'.$siteRemoteRoot.'/css/appTheme.css"/ >'.PHP_EOL;
$scriptIncludes .= '<script type="text/javascript" src="'.$siteRemoteRoot.'/js/appTheme.js"></script>'.PHP_EOL;

$currTime = date("h:i A");
?>
