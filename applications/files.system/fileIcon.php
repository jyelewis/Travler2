<?php
$access = 'desktop';
require('../../SysData/init.php');
$icon = $_GET['file'];
$icon = $fs->getIcon($icon);
header('Content-Type: '.$icon['image_mime']);
echo $icon['data'];
?>