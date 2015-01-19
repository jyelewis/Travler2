<?php
require('../../SysData/init.php');
$app = new travlr_application('notes.jyelewis');
$window = $app->getWindow('deleteNote');
$notesdb = new db_sqlite('database.db');
$form = new html_form();
$form -> addinput('submit', 'submit', 'Delete note');
$form -> separator('<br>');

if($form->ispostback)
{
	$notesdb->writeQuery("
		DELETE FROM
			notes
		WHERE
			noteID = ':1'
	", $window->data, $userTied->userID);
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
			Are you sure you want to delete the note?
			<?php echo $form; ?>
        </div>
    </body>
</html>
