<?php
require('../../SysData/init.php');
$app = new travlr_application('notes.jyelewis');
$window = $app->getWindow('unlockNotebook');
$notesdb = new db_sqlite('database.db');
$form = new html_form();
$form -> addinput('password', 'password');
$form -> addinputclass('password', 'text');
$form -> addinput('submit', 'submit', 'Unlock notebook');
$form -> separator('<br>');

$name = $notesdb->query("SELECT title FROM notebooks WHERE notebookID = ':1'", $window->data);


if($form->ispostback)
{
	if(md5($form->postback->getvalue('password')) == $userTied->data['password'])
	{
		$notesdb -> writeQuery("UPDATE notebooks SET locked='0' WHERE notebookID=':1' AND userID=':2'", $window->data, $userTied->userID);
		$app->reloadWindow('main');
		$window->close();
	} else {
		$form -> postback -> addinputclass('password', 'incorrect');
		$form -> postback -> setvalue('password', '');
	}
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?php echo $scriptIncludes; ?>
        <?php $app->cssInclude('style.css'); ?>
    </head>
    <body>
    	<div id="newNotebookForm">
			Please enter your password to unlock the notebook<br>"<?php echo $name[0]['title']; ?>"
			<?php echo $form; ?>
        </div>
    </body>
</html>
