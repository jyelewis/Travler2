<?php
$access = 'login';
require('SysData/init.php');
//simple: workout if the user is supposed to be at the desktop or not and
//direct them appropritaly
if ($userTied -> loginStage === 'desktop')
{
    header('location: desktop.php');
} else {
    header('location: login.php');
}
?>