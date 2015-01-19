<?php
require('../../../../SysData/init.php');

$app = new travlr_application('settings.system');
$window = $app->getWindow('panel-Users-deleteConfirm');

if (isset($_POST['confirm']) && $_POST['confirm'] == 'deleteAccount' && isset($_POST['userID']) && hasPermission('alterUsers'))
{
	$result = $db->query("SELECT * FROM Users WHERE userID=':1'", $_POST['userID']);
	$account = $result[0];
	sendMessage($_POST['userID'], '
		The account '.$account['username'].' has been deleted<br>
		and had to be forcibly logged out<br><br>
		if you are unsure why this has happened please contact your administrator
	', true);
	sleep(5);
	if (userLoggedIn($account['userID']))
	{
		$db->writeQuery("UPDATE Users SET sessionCode=' ' WHERE userID=':1'", $account['userID']);
		sleep(2);
	}
	//after here we are pretty sure the user has been logged out
	
	$db->writeQuery("DELETE FROM Users WHERE userID=':1'", $account['userID']);
	$db->writeQuery("DELETE FROM AllowedApplications WHERE userID=':1'", $account['userID']);
	$db->writeQuery("DELETE FROM BackgroundImages WHERE userID=':1'", $account['userID']);
	$db->writeQuery("DELETE FROM LauncherApps WHERE userID=':1'", $account['userID']);
	$db->writeQuery("DELETE FROM LauncherIcons WHERE userID=':1'", $account['userID']);
	$db->writeQuery("DELETE FROM Messages WHERE userID=':1'", $account['userID']);
	$db->writeQuery("DELETE FROM Windows WHERE userID=':1'", $account['userID']);
	
	exit();
}

if (isset($_POST['closeWindow']))
{
	$window->close();
	$app->getWindow('panel-Users')->close();
}

if (!hasPermission('alterUsers'))
{
	$window->close();
	exit();
}
$userId = $_GET['userID'];
$userData = $db->query("SELECT username, fullname, userID FROM Users WHERE userID=':1'", $userId);
$userData = $userData[0];

$deleteForm  = new html_form('accountDelete');
$deleteForm -> addinput('delete', 'submit', 'Delete account');

$deleteing = false;
if($deleteForm -> ispostback)
{
	//do some deleting stuff
	//echo '<h1 style="color:#a00;text-align:center;margin-top:100px;">Account deleted!</h1>';
	//$window->close();
	//die();
	$deleteForm -> postback -> addinputclass('delete', 'deleting');
	$deleteForm -> postback -> addinputattribute('delete', 'disabled', 'disabled');
	$deleteForm -> postback -> setvalue('delete', 'Deleting account...');
	$deleteing = true;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?php echo $scriptIncludes; ?>
        <style type="text/css">
        	.submit {
        		margin-top:20px;
        		width:250px;
        		height:50px;
        		font-size:20px;
        		font-weight:bold;
        	}
        	.submit:hover {
        		background-color:#c55;
        		color:#fff;
        	}
        	.submit.deleting {
        		background-color:#c55;
        		color:#fff;
        		background-image: url('<?php echo $siteRemoteRoot; ?>/images/ajax-loader1.gif');
        		background-repeat:no-repeat;
        		background-position: 225px center
        	}
        	.submit.finished {
        		background-color:#5c5;
        		color:#fff;
        		background-image: none;
        	}
        	h2 {
        		margin:0;
        	}
        </style>
        <?php if($deleteing){ ?>
        <script type="text/javascript">
        $.post("confirmDelete.php", { userID: "<?php echo $_GET['userID']; ?>", confirm: "deleteAccount" }, function(){
        	$(".submit").addClass('finished');
        	$(".submit").removeClass('deleting');
        	$(".submit").val('Account Deleted!');
        	setTimeout(function() { $.post("confirmDelete.php", { closeWindow: "true" })}, 2000);
        });
        <?php } ?>
        </script>
    </head>
    <body>
        <h2>Delete account "<?php echo $userData['username'] ?>"</h2>
        Deleting this account will wipe all the configuration for <?php echo $userData['fullname'] ?>.<br />
        This includes <?php echo $userData['fullname']; ?>'s permissions, background images, launcher apps and will prevent the user from logging in again.<br />
        Files stored in any filesystem linked to the user will remain behind but the link will no longer point to a user, make sure that you get all files wanted from off the account before deleting<br />
        <strong>Are you sure you want to delete this account and the associated configuration?</strong>
        <?php echo $deleteForm; ?>
    </body>
</html>