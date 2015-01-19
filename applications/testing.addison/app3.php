<?php
require('../../SysData/init.php');
$app = new travlr_application('testing.addison');
$window = $app->getWindow('window1');

$form = new html_form();
$form->addinput('value1', 'text');
$form->addinput('value2', 'text');
$form->addinput('submit', 'submit', 'calcualte');

if ($form->ispostback)
{
	$value1 = $form->postback->getvalue('value1');
	$value2 = $form->postback->getvalue('value2');
	echo $value1. ' x '. $value2 . ' = ' .$value1*$value2;
}

echo $form;
?>