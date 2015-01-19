<?php
require('../../SysData/init.php');
$app = new travlr_application('test.jyelewis');
$window = $app->getWindow('window1');
$push = new data_serverPush('test', 1);
$push -> bindFunction('getData');
$push -> send('test message');
echo $scriptIncludes;
echo $push->generateCode();
?>
<script type="text/javascript">
function getData(e)
{
	$("body").prepend(e.data + '<br>');
}
</script>
<body>

</body>