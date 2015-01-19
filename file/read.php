<?php
require('../SysData/init.php');

//var_dump($fs->fileExists($_GET['file']));

$fs->serve_file($_GET['file']);


?>