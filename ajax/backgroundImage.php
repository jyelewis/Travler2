<?php
$access = 'login';
require('../SysData/init.php');
$userTied->allowSave = false;

//keep background images cached, the url cant change with the image
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
    Header("HTTP/1.0 304 Not Modified");
}
//end cache code

$result = $db->query("SELECT image FROM backgroundImages WHERE userID=':1' AND backgroundImageID = ':2'",
$userTied->userID, $_GET['backgroundImageID']);
header('Content-Type: image/jpeg');
echo base64_decode($result[0]['image']);
?>