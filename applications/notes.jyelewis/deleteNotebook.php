<?php
require('../../SysData/init.php');
$app = new travlr_application('notes.jyelewis');
$window = $app->getWindow('deleteNotebook');
$notesdb = new db_sqlite('database.db');
$form = new html_form();
$form -> addinput('submit', 'submit', 'Delete notebook');
$form -> separator('<br>');

$name = $notesdb->query("SELECT title,locked FROM notebooks WHERE notebookID = ':1'", $window->data);


if($form->ispostback)
{
	$notesdb->writeQuery("
		DELETE FROM
			notebooks
		WHERE
			notebookID = ':1' AND
			userID = ':2'
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
			<?php if ($name[0]['locked'] != 1){ ?>
				Are you sure you want to delete the notebook<br>"<?php echo $name[0]['title']; ?>"?
				<?php echo $form; ?>
			<?php } else { ?>
				Notebook locked!
			<?php } ?>
        </div>
    </body>
</html>
