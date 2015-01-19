<?php
require('../../SysData/init.php');
$app = new travlr_application('test.jyelewis');
$window = $app->getWindow('window1');

$fs  = new travlr_filesystem($siteLocalRoot.'/SysData/filesystem.db');


//$fs -> cd('');

//print_r($fs -> ls('../../../../testDir'));
/*print_r($fs -> ls(''));
echo '<br><br>';
var_dump($fs->file('music/song.mp3'));
*/

//echo sqlite3::escapeString('as\' \ \\\'df');


$fs->fs->writeQuery("DELETE FROM files WHERE userID = ':1'", $userTied->userID);
foreach($fs->allFiles() as $file)
{
	$fs->fs->writeQuery("INSERT INTO files (`userID`, `path`, `hasChanged`) VALUES (':1', ':2', '1')", $userTied->userID, $file);
}

/*
foreach(scandir('music') as $file)
{
	if(substr($file, 0, 1) != '.')
	{
		echo $file.'<br>';
		$fs->writeFile($file, file_get_contents('music/'.$file));
	}
}*/

//$fs->writeFile('video.m4v', file_get_contents('video.m4v'));
//$fs->writeFile('bigvid.mov', file_get_contents('bigvid.mov'));
//$fs->writeFile('dark.mp3', file_get_contents('dark.mp3'));
//$fs->writeFile('Spaceballs.mp4', file_get_contents('Spaceballs.mp4'));
//echo 'done!';
//$fs->writeFile('dark.mp3', "' ' \' asdfasdf ' ");
//echo (file_get_contents('bigvid.mov'));
//serverLog(memory_get_peak_usage()/1048576);

//$file1 = $fs->file('/home/jyelewis/bigvid.mov');
//$file2 = file_get_contents('bigvid.mov');

/*
for($i=10000; $i<10000*100; $i=$i+10000)
{
	hashFile($i, $file1, $file2);
}

echo substr($file1, 60000, 10000);
echo PHP_EOL.PHP_EOL.PHP_EOL;
echo substr($file2, 60000, 10000);

function hashFile($num, $file1, $file2)
{
	echo $num.PHP_EOL;
	echo md5(substr($file1, $num, 10000));
	echo PHP_EOL;
	echo md5(substr($file2, $num, 10000));
	echo PHP_EOL.PHP_EOL;
}
*/

//
//sleep(2);

/*$data = $fs->getID3('music/dark.mp3');
$data['comments']['picture'] = 'hidden';
header('content-type: '.$fs->getMime('music/dark.mp3'));
echo $fs->file('music/dark.mp3');*/
?>
