<?php
$access = 'login';
require('../SysData/init.php');
$nameQuery = $db->query("SELECT fullname FROM Users WHERE username=':1'", $_POST['username']);
if (count($nameQuery) == 0)
{
    echo '**errNotFound**';
} else {
    echo $nameQuery[0]['fullname'];
}
?>
