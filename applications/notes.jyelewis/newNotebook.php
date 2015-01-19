<?php
require('../../SysData/init.php');
$app = new travlr_application('notes.jyelewis');
$window = $app->getWindow('newNotebook');
$notesdb = new db_sqlite('database.db');
$form = new html_form();
$form -> addinput('title', 'text');
$form -> addinputclass('title', 'text');
$form -> addinput('submit', 'submit', 'Create notebook');
$form -> separator('<br>');

if($form->ispostback)
{
	$notesdb->writeQuery("
		INSERT INTO
			notebooks
			(`title`, `userID`)
		VALUES
			(':1', ':2')
	", $form->postback->getvalue('title'), $userTied->userID);
	$app->reloadWindow('main');
	$window->close();
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
        <?php echo $form; ?>
        </div>
    </body>
</html>
