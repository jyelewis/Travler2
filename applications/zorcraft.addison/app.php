<?php
require('../../SysData/init.php');
$app = new travlr_application('zorcraft.addison');
$window = $app->getWindow('window1');
$appdb = new db_sqlite('database.db');

echo $scriptIncludes;

//echo $userTied->data['fullname'];

//echo '<br>';
 
$allowedCommands = array('walk forward', 'walked forward 2 steps' );

$form  = new html_form();
$form -> addinput('input', 'text');
$form -> addinput('submit', 'submit', 'do');

if ($form->ispostback)
{
	$input = $form->postback->getvalue('input');
	if (!in_array($input, $allowedCommands))
	{
		echo 'sorry didnt understand that';
		finish();
	}
	
	if ($input == 'walk forward')
	{
		echo 'walked forward 1 step';
		$appdb -> writeQuery("UPDATE positions SET steps = steps + 1 WHERE userID=':1'", $userTied->userID);
		finish();
	}
	if($input == 'walked forward 2 steps')
	{
		echo 'walked forward 2 steps';
		finish();
	}
	echo $form;
	
	finish();
}

$result = $appdb -> query("SELECT steps FROM positions WHERE userID=':1'", $userTied->userID);

$position = $result[0]['steps'];

echo 'current position: '.$position.'<br>';

function finish(){
	global $form;
	echo $form;
	exit();	
}

finish();

?>