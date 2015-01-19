<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
$timeout = 60;
$startTime = time();
echo 'retry: 10'.PHP_EOL;
while(true)
{
	echo 'data: current time: '.time().'<br>'.'script start time: '.$startTime.PHP_EOL.PHP_EOL;
	flush();
	if (sseCheckTime()){
		break;
	}
	sleep(1);
}

function sseCheckTime()
{
	global $startTime, $timeout;
	if (time() - $startTime >= $timeout)
	{
		flush();
		return true;
	} else {
		return false;
	}
}
?>