<?php
require('../../../../SysData/init.php');
require('panel.phpx');
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?php
		echo $scriptIncludes;
		$app->cssinclude('/customPanels/Users/style.css');
		$app->javascriptinclude('/customPanels/Users/script.js');
	?>
    </head>
    <body>
	<?php if($displayUserEdit){ ?>
	    <?php if ($displayData['isAdmin']){ ?>
		<a href="?" id="backLink">&lt; Back</a><br>
	    <?php } ?>
	    <?php if ($displayFormErrors){ ?>
	    	<div id="formErrors">
	    		<?php echo $formErrors; ?>
	    	</div>
	    <?php } ?>
	    
	    <?php echo $displayData['userForm']->top(); ?>
	    <table>
	    <?php if($displayData['isAdmin']){ ?>
	    <tr>
		    <td class="tdTitle" style="font-weight:bold;"><?php echo $displayData['userData']['username']; ?></td>
		    <td style="font-size:14px;"><?php echo $displayData['onlineStatus']; ?><div class="onlineState <?php echo $displayData['onlineClass']; ?>"></div></td>
		</tr>
		<tr>
		    <td class="tdTitle">Last Login:</td>
		    <td ><?php echo date('g:i a j/n', $displayData['userData']['lastLogin']); ?></td>
		</tr>
		<tr>
		    <td class="tdTitle">Last Connection:</td>
		    <td ><?php echo date('g:i a j/n', $displayData['userData']['lastConnection']); ?></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td class="tdTitle">Send message to user</td>
				<td ><?php echo $displayData['userForm']->input('messageText'); ?></td>
			</tr>
			<tr>
				<td class="tdTitle">Force logout</td>
				<td ><?php echo $displayData['userForm']->input('messageLogout'); ?></td>
			</tr>
			<tr>
				<td class="tdTitle">&nbsp;</td>
				<td ><?php echo $displayData['userForm']->input('messageSubmit'); ?></td>
			</tr>
			<tr>
				<td class="tdTitle">Drive:</td>
				<td><?php echo $displayData['userForm']->input('drive'); ?></td>
			</tr>
		<tr><td>&nbsp;</td></tr>
		<?php } ?>
		<tr>
		    <td class="tdTitle">Username:</td>
		    <td><?php echo $displayData['userForm']->input('username'); ?></td>
		</tr>
		<tr>
		    <td class="tdTitle">Full Name:</td>
		    <td><?php echo $displayData['userForm']->input('fullname'); ?></td>
		</tr>
		<tr id="password">
		    <td class="tdTitle">New Password:</td>
		    <td><?php echo $displayData['userForm']->input('password'); ?></td>
		    <td style="font-size:12px;">Leave blank to ignore</td>
		</tr>
		<tr id="passwordConfirm" class="<?php if(isset($showPassConfirm)){ echo 'show'; } ?>">
		    <td class="tdTitle">Confirm New Password:</td>
		    <td><?php echo $displayData['userForm']->input('passwordConfirm'); ?></td>
		</tr>
		<?php if($displayData['isAdmin']){ ?>
		<tr>
		    <td class="tdTitle">Group:</td>
		    <td><?php echo $displayData['userForm']->input('group'); ?></td>
		</tr>
		<?php if(hasPermission('alterPermissions')){ ?>
		<tr id="permissions">
			<td class="tdTitle">&nbsp;</td>
			<td><?php echo $displayData['userForm']->input('editPermissions'); ?></td>
		</tr>
		<?php } ?>
		<tr id="delete">
		    <td class="tdTitle">&nbsp;</td>
		    <td><?php echo $displayData['userForm']->input('delete'); ?></td>
		</tr>
		<?php } ?>
		<tr id="submit">
		    <td class="tdTitle">&nbsp;</td>
		    <td><?php echo $displayData['userForm']->input('submit'); ?></td>
		</tr>
	    </table>
	    <?php if ($displayConfirm){ ?>
	    	<div id="formConfirm">
	    		Changes saved
	    	</div>
	    <?php } ?>
	    <?php echo $displayData['userForm']->input('userID').$displayData['userForm']->bottom(); ?>
	<?php } elseif($displayUserList) { // end of single user html. Start of admin user html ?>
	    <?php echo $usersUL; ?>
	<?php } //end of single/admin if ?>
    </body>
</html>
