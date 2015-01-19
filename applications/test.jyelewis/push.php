<?php
require('../../SysData/init.php');
$app = new travlr_application('test.jyelewis');
$window = $app->getWindow('window1');
$push = new data_serverPush('test', 1);
$form  = new html_form();
$form -> addinput('message', 'text');
$form -> addinput('submit', 'submit', 'send');
if($form->ispostback)
{
	$push->send($form->postback->getvalue('message'));
}
echo $form;
?>