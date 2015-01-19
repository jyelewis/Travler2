<?php
require('../../SysData/init.php');
$app = new travlr_application('testing.addison');
$window = $app->getWindow('window1');

$window->hide();
sleep(1);
$window->show();
/*$randomNum = rand(1,3);
//echo 'this is a random number ' . $randomNum;
if ($randomNum == 1)
{
	echo 'you win';
}*/

function getName()
{
	$names = array('jaimee', 'jye', 'addison');
	shuffle($names);
	return $names[0];
}

$a1 = getName();
$a2 = getName();
$a3 = getName();

echo $a1.'<br>';
echo $a2.'<br>';
echo $a3.'<br><br>';

if ($a1 == $a2 && $a2 == $a3)
{
	echo $a1.' Wins!';
} else {
	echo 'sorry no one won';
}

if (!isset($_GET['count']))
{
	$count = 1;
} else {
	$count = $_GET['count']+1;
}

?>
<br>
<a href="?count=<?php echo $count; ?>">Try again</a>
<br>
<?php echo $count; ?>