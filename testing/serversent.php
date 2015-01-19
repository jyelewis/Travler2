<?php
	$access = 'login';
	require('../SysData/init.php');
	echo $scriptIncludes;
?>
<script type="text/javascript">
var server = new EventSource('sseRequest.php');
server.addEventListener('message', function(e) {
  $("body").prepend(e.data + '<br><br>');
}, false);
</script>