<?php
require('../../SysData/init.php');
echo $scriptIncludes;
?>
<script>
	function menuText(menuItem, clicked)
	{
		alert($(menuItem).text());
	}
	$(document).ready(function(){
		contextMenu('#clickInHere', '#menu', menuText);
	});
</script>
<style>
	#clickInHere {
		width:300px;
		height:300px;
		background-color:red;
		margin:50px;
	}
</style>
<div id="clickInHere">
</div>
<ul id="menu">
	<li>Text item</li>
	<li>some other test</li>
	<li>Translate To English</li>
</ul>