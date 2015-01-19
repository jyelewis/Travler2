<?php
require('../../../../SysData/init.php');
require('panel.phpx');
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?php echo $scriptIncludes; ?>
    	<?php $app->cssinclude('customPanels/UserPermissions/style.css'); ?>
    	<script type="text/javascript">
    		$(document).ready(function(){
    			$(".groupli .title").click(function(){
    				if($(this).parent().children('.permissionsCollapse').is(':visible'))
    				{
    					$(this).parent().children('.permissionsCollapse').slideUp(200);
    				} else {
    					$(this).parent().children('.permissionsCollapse').slideDown(200);
    				}
    			});
    			$.contextMenu({
					selector: '.permissionsCollapse ul li',
					trigger: 'left',
					callback: function(key, options) {
						if (key == 'userTrue')
						{
							$(options.$trigger).removeClass('hasFalse');
							$(options.$trigger).addClass('hasTrue');
						}
						if (key == 'userFalse')
						{
							$(options.$trigger).removeClass('hasTrue');
							$(options.$trigger).addClass('hasFalse');
						}
						if (key == 'groupTrue')
						{
							$(options.$trigger).children('.groupAllow').remove();
							$(options.$trigger).append('<div class="groupAllow">group: true</div>');
						}
						if (key == 'groupFalse')
						{
							$(options.$trigger).children('.groupAllow').remove();
							$(options.$trigger).append('<div class="groupAllow">group: false</div>');
						}
						if (key == 'userClear')
						{
							$(options.$trigger).removeClass('hasFalse');
							$(options.$trigger).removeClass('hasTrue');
						}
						if (key == 'groupClear')
						{
							$(options.$trigger).children('.groupAllow').remove();
						}
						$.ajax({
						  type: "POST",
						  data: { update: key, permission: $(options.$trigger).attr('data-permission') }
						}).done(function( msg ) {
						  console.log("Data Saved: " + msg );
						});
					},
					items: {
						"userTrue": {name: "User:&nbsp;&nbsp;&nbsp;True"},
						"userFalse": {name: "User:&nbsp;&nbsp;&nbsp;False"},
						"sep1": "---------",
						"groupTrue": {name: "Group: True"},
						"groupFalse": {name: "Group: False"},
						"sep2": "---------",
						"userClear": {name: "User:&nbsp;&nbsp;&nbsp;Clear"},
						"groupClear": {name: "Group: Clear"},
					}
				});
				$("#colAll").click(function(){
					$('.permissionsCollapse').slideUp(200);
				});
				$("#expAll").click(function(){
					$('.permissionsCollapse').slideDown(200);
				});
    		});
    	</script>
    </head>
    <body>
    	<div id="title"><?php echo $currUserData['username']; ?>'s permissions</div>
    	<div id="buttons">
    		<div id="expAll" class="btnLink">Expand all</div>
			<div id="colAll" class="btnLink">Collapse all</div>
    	</div>
    	<div class="clear"></div>
    	<?php echo $permissionsUL; ?>
    </body>
</html>
