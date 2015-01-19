<?php
require('../SysData/init.php');
if (!isset($_POST['fileCode']) || !isset($_POST['posTop']) || !isset($_POST['posLeft'])){ die(); }
if (count($db->query("SELECT desktopIconID from DesktopIcons WHERE filename=':1' AND userID=':2'", decriptWindowCode($_POST['fileCode']), $userTied->userID)) == 0)
{
    $db->writeQuery("INSERT INTO DesktopIcons (`userID`, `filename`, `POStop`, `POSleft`) VALUES (':1', ':2', ':3', ':4')",
	    $userTied->userID, decriptWindowCode($_POST['fileCode']), $_POST['posTop'], $_POST['posLeft']);
    
} else {
    $db->writeQuery("UPDATE DesktopIcons SET POStop=':3', POSleft=':4' WHERE userID=':1' AND filename=':2'",
	    $userTied->userID, decriptWindowCode($_POST['fileCode']), $_POST['posTop'], $_POST['posLeft']);
}
echo 'OK';
?>
