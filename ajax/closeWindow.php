<?php
require('../SysData/init.php');
$windowID = decriptWindowCode($_POST['windowID']);
$result = $db->query("SELECT applicationID FROM Windows WHERE windowID=':1' AND userID=':2'", $windowID, $userTied->userID);
if (count($result)){
    $db->writeQuery("UPDATE Windows SET hasChanged='2' WHERE windowID=':1' AND userID=':2'", $windowID, $userTied->userID);
    travlr_launcher::remove($result[0]['applicationID']);
    echo 'ok';
} else {
    echo 'killWindow';
    rebuildLauncher();
}
?>