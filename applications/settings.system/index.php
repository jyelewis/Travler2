<?php
require('../../SysData/init.php');
$app = new travlr_application('settings.system');
$window = $app->getWindow('main');

$openPanel = false;
$openClass = false;
$panels = array();


if (isset($_GET['panel'])) {
    $openPanel = $_GET['panel'];
    if (strpos($openPanel, '.') === false && strpos($openPanel, '/') === false)
    {
	$app->newWindow('panel-'.$openPanel, $openPanel, '/panels/'.$openPanel.'/panel.php');
    }
} elseif (isset($_GET['customPanel'])) {
    $openPanel = $_GET['customPanel'];
    if (strpos($openPanel, '.') === false && strpos($openPanel, '/') === false)
    {
	$app->newWindow('panel-'.$openPanel, $openPanel, '/customPanels/'.$openPanel.'/panel.php');
    }
}
    
function isValidPanel($panel){
    if ($panel == '.' || $panel == '..')
    {
	return false;
    }
    if (!is_dir($panel))
    {
	return false;
    }
    if (!file_exists($panel.'/icon.png') || !file_exists($panel.'/panel.php'))
    {
	return false;
    }
    return true;
}

function addPanel($path, $title) {
	global $panels;
	if(!isValidPanel('customPanels/'.$path)){ return false; }
	$panels[] = array('path' => 'customPanels/'.$path, 'title' => $title, 'url' => 'customPanel='.$path);
	return true;
}

include('customPanels.php');

foreach(scandir('panels/') as $panel) {
	if(!isValidPanel('panels/'.$panel)){ continue; }
	$panels[] = array('path' => 'panel/'.$panel, 'title' => $panel, 'url' => 'panel='.$path);
}

?>
<!DOCUTYPE html>
<html>
    <head>
        <?php
	$app->cssInclude('style.css');
	echo $scriptIncludes;
	?>
	<script type="text/javascript">
	    setTimeout(resetPanel, 5000);
	    function resetPanel(){
			$(".openPanel").removeClass("openPanel");
	    }
	</script>
    </head>
    <body>
        <?php foreach($panels as $panel){ if ($panel == $openPanel){ $openClass = ' openPanel'; } ?>
            <a href="<?php echo '?'.$panel['url']; ?>" class="itemContainer<?php echo $openClass; ?>">
                <img src="<?php echo $panel['path'].'/icon.png'; ?>" />
                <div class="itemTitle">
                    <div class="itemTitleColor"></div>
                    <div class="itemTitleText">
                        <?php echo $panel['title']; ?>
                    </div>
                </div>
            </a>
        <?php } ?>
    </body>
</html>
