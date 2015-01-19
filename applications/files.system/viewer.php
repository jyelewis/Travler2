<?php
require('../../SysData/init.php');
require('viewer.phpx');
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
    	<?php foreach($files as $file){ ?>
        <a href="?open=<?php echo urlencode($file['path']); ?>" class="fileContainer" data-file="<?php echo urlencode($file['path']); ?>" data-type="<?php echo $file['type']; ?>">
		    <div class="fileBackground"></div>
		    <div class="fileIcon">
		    	<img src="<?php echo $file['icon']; ?>" />
		    </div>
		    <div class="fileTitle"><?php echo $file['title']; ?></div>
		</a>
		<?php } ?>
    </body>
</html>
