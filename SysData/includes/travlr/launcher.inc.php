<?php
class travlr_launcher
{
    public static function add($appID, $rebuild = true, $isClosed = false)
    {
        global $db, $userTied;
        $doesExists = $db->query("SELECT launcherIconID FROM LauncherIcons WHERE applicationID = ':1' AND userID = ':2' AND windowExists = '1'", $appID, $userTied->userID);
        $notExists  = $db->query("SELECT launcherIconID FROM LauncherIcons WHERE applicationID = ':1' AND userID = ':2' AND windowExists = '0'", $appID, $userTied->userID);
        
        if (count($doesExists) == 0 && count($notExists) == 0)
        {
            $result = $db->query("SELECT title,icon FROM Applications WHERE applicationID = ':1'", $appID);
            $appName = $result[0]['title'];
            $appIcon = $result[0]['icon'];
            if ($isClosed)
            {
                $color = '#999';
                $exists = '0';
            } else {
                $color = '#acf';
                $exists = '1';
            }
            $db->writeQuery("INSERT INTO LauncherIcons (
                                    `userID`, `applicationID`, `title`, `color`, `icon`, `windowExists`
                                ) VALUES (
                                    ':1', ':2', ':3', ':4', ':5', ':6'
                                )", $userTied->userID, $appID, $appName, $color, $appIcon, $exists);
        
            if ($rebuild)
            {
                rebuildLauncher();
            }
        }
        if(count($notExists) == 1)
        {
            $db->writeQuery("UPDATE LauncherIcons SET windowExists = '1', color = '#acf' WHERE applicationID = ':1'", $appID);
            rebuildLauncher();
        }
    }
    
    public static function remove($appID, $rebuild = true)
    {
        global $db, $userTied;
        $iconExists = $db->query("SELECT windowID FROM Windows WHERE applicationID = ':1' AND hasChanged = '0' AND userID=':2'", $appID, $userTied->userID);
        $appSelID = $db->query("SELECT applicationID FROM Applications WHERE idCode = 'applications.system'");
        $iconPerm = $db->query("SELECT launcherAppID from LauncherApps WHERE applicationID=':1' AND userID = ':2'", $appID, $userTied->userID);
        if (count($iconExists) == 0 && count($iconPerm) == 0 && $appID != $appSelID[0]['applicationID'])
        {
            $db->writeQuery("DELETE FROM LauncherIcons WHERE applicationID = ':1' AND userID=':2'", $appID, $userTied->userID);
            if ($rebuild)
            {
                rebuildLauncher();
            }
        }
        if (count($iconExists) == 0 && count($iconPerm) == 1 || $appID == $appSelID[0]['applicationID'])
        {
            $db->writeQuery("UPDATE LauncherIcons SET windowExists = '0', color = '#999' WHERE applicationID = ':1'", $appID);
            if ($rebuild)
            {
                rebuildLauncher();
            }
        }
    }
}

?>
