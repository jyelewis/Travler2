<?php
require('../../SysData/init.php');
$app = new travlr_application('applications.system');
$window = $app->getWindow('appIcons');

if(isset($_GET['applicationID']))
{
    launchApp($_GET['applicationID']);
    $window->close();
    die();
}

$window->fullscreen(true);
$allApps = $db->query("SELECT * FROM Applications WHERE idCode != 'applications.system'");

?>
<!DOCUTYPE html>
<html>
    <head>
        <?php
	    $app->cssInclude('style.css');
	    echo $scriptIncludes;
	?>
    </head>
    <body>
        <?php foreach($allApps as $app) { $image = $iconURL = getAppPath($app['applicationID'], true).'/'.$app['icon']; ?>
            <a href="<?php echo '?applicationID='.$app['applicationID'] ?>" class="itemContainer">
                <img src="<?php echo $image; ?>" />
                <div class="itemTitle">
                    <div class="itemTitleColor"></div>
                    <div class="itemTitleText">
                        <?php echo $app['title']; ?>
                    </div>
                </div>
            </a>
        <?php } ?>
    </body>
</html>
