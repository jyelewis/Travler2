<?php
$outsideRatio = 1.5;

$access = 'login';
require('../../SysData/init.php');
$appDb = new db_sqlite('database.db');
$current = $appDb->query("SELECT * FROM currentTimer LIMIT 1");
if (count($current) != 0)
{
	$current = $current[0];
} else {
	$current = array('type' => 'none');
}

if(isset($_POST['do']))
{
	switch ($_POST['do'])
	{
		case 'stopTimers':
			$appDb->writeQuery("
			INSERT INTO
				times
				(`date`, `type`, `length`)
			VALUES
				(':1', ':2', ':3')
			", $current['startTime'], $current['type'], time()-$current['startTime']);
			$appDb->writeQuery("DELETE FROM currentTimer");
		break;
		case 'startComputer':
			$appDb->writeQuery("
			INSERT INTO
				currentTimer
				(`startTime`, `type`)
			VALUES
				(':1', 'computer')
			", time());
		break;
		case 'startOutside':
			$appDb->writeQuery("
			INSERT INTO
				currentTimer
				(`startTime`, `type`)
			VALUES
				(':1', 'outside')
			", time());
		break;
		case 'saveCustomTime':
			$cusTime = 0;
			$cusTime = $cusTime + ($_POST['customTimeHour'] * 3600);
			$cusTime = $cusTime + ($_POST['customTimeMin'] * 60);
			if ($_POST['customTimeSub'] == '0')
			{
				$cusType = 'outside';
			} else {
				$cusType = 'computer';
			}
			$appDb->writeQuery("
			INSERT INTO
				times
				(`date`, `type`, `length`)
			VALUES
				(':1', ':2', ':3')
			", time(), $cusType, $cusTime);
		break;
	}
	die();
}
$all = $appDb->query("SELECT * FROM times");

if ($current['type'] != 'none')
{
	$currentAdd = array('type' => $current['type'], 'length' => time() - $current['startTime']);
	$all[] = $currentAdd;
}
$totalTime = 0;
foreach($all as $time)
{
	if($time['type'] == 'outside')
	{
		$totalTime = $totalTime + ($time['length'] * $outsideRatio);
	}
	if($time['type'] == 'computer')
	{
		$totalTime = $totalTime - $time['length'];
	}
}



$array = array(
	 'mainTimer' => formatTime($totalTime)
	,'timerType' => $current['type']
);

if ($totalTime < 0)
{
	$array['flash'] = 'yes';
} else {
	$array['flash'] = 'no';
}

if ($current['type'] !== 'none')
{
	$array['currTimer'] = formatTime(time() - $current['startTime']);
}
echo json_encode($array);

function formatTime($time, $isTimestamp = false)
{
	//return $time;
	if (!$isTimestamp)
	{
		$negative = '';
		if($time < 0)
		{
			$time = $time * -1;
			$negative = '-';
		}
		$hours = floor($time / 3600);
		$mins = number_pad(floor(($time - ($hours*3600)) / 60), 2);
		$secs = number_pad(floor(($time - ($hours*3600) - ($mins*60))), 2);
		if ($hours != 0)
		{
			return $negative.$hours.':'.$mins.':'.$secs;
		} else {
			return $negative.$mins.':'.$secs;
		}
	} else {
		return date('g:i:s', $time);
	}
}
function number_pad($number,$n) {
	return str_pad((int) $number,$n,"0",STR_PAD_LEFT);
}
?>