<?php
require('../../SysData/init.php');
$app = new travlr_application('files.system');
$window = $app->getWindow('file');
?>
<video style="margin:0 auto;text-align:center;" controls="" autoplay="" name="media"><source src="<?php echo $siteRemoteRoot.'/file'.$window->data['file']; ?>" type="<?php echo $window->data['mime'] ?>"></video>