<?php
require('notes.phpx');
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?php echo $scriptIncludes; ?>
        <?php $app->cssInclude('style.css'); ?>
        <script type="text/javascript">
			$(document).ready(function(){
				contextMenu('.note', '#menu', menuCallback);
				function menuCallback(menuItem, clicked) {
					key = $(menuItem).text();
					if (key == 'Open')
					{
						go('notes.php?noteID=' + $(clicked).attr('data-note'));
					}
					if (key == 'Delete')
					{
						go('notes.php?deleteNote=' + $(clicked).attr('data-note'));
					}
					
					if(navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod')
					{
						 $("#noteEdit").css("position", "static");
					};
				}
				
			});
        </script>
    </head>
    <body class="ajaxLinks">
    	<?php if(isset($noteForm)) { echo $noteForm->top(); } ?>
		<div id="notes">
			<a href="notebooks.php" class="btnLink">Back</a><br>
			<?php if(isset($noteForm)) { echo $noteForm->input('submit'); } ?>
			<a href="notes.php?newNote=1" class="btnLink">Create new note</a>
			<?php echo $notesUL; ?>
		</div>
		<div id="noteEdit">
			<?php if(isset($noteForm)) { echo $noteForm->input('textarea'); } ?>
		</div>
		<?php if(isset($noteForm)) { echo $noteForm->bottom(); } ?>
		<ul id="menu">
			<li>Open</li>
			<li>Delete</li>
		</ul>
    </body>
</html>
