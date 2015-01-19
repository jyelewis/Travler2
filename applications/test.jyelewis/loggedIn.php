<?php
require('../../SysData/init.php');

//var_dump(userLoggedIn('addisonlewis'));

/*
$form = new html_form();
$form -> addinput('userID', 'text');
$form -> addinput('message', 'text');
$form -> addinput('submit', 'submit', 'show message');
if($form->ispostback)
{
	sendMessage($form->postback->getvalue('userID'), $form->postback->getvalue('message'), true);
}
*/

$form  = new html_form();
$form -> addinput('input', 'textarea');
$form -> ishtml('input', true);

echo $scriptIncludes;

echo $form;
?>
<script type="text/javascript">
function hideWindow()
{
	showText('window hide triggered');
}
function showWindow()
{
	showText('window show triggered');
}
function selectWindow()
{
	showText('window select triggered');
}
function showText(text)
{
	$("body").prepend(text + "<br>");
}
</script>
