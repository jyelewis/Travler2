<?php
require('notebooks.phpx');
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
				contextMenu('.notebook', '#menu', menuCallback);
				function menuCallback(menuItem, clicked) {
					key = $(menuItem).text();
					if (key == 'Open')
					{
						go('notebooks.php?notebookID=' + $(clicked).attr('data-notebook'));
					}
					if (key == 'Lock')
					{
						go('notebooks.php?lockNotebook=' + $(clicked).attr('data-notebook'));
					}
					if (key == 'Unlock')
					{
						go('notebooks.php?unlockNotebook=' + $(clicked).attr('data-notebook'));
					}
					if (key == 'Delete')
					{
						go('notebooks.php?deleteNotebook=' + $(clicked).attr('data-notebook'));
					}
				}
			});
        </script>
    </head>
    <body class="ajaxLinks">
		<div id="notebooks">
			<?php echo $notebooksUL; ?>
		</div>
		<ul id="menu">
			<li>Open</li>
			<li>Lock</li>
			<li>Unlock</li>
			<li>Delete</li>
		</ul>
    </body>
</html>
