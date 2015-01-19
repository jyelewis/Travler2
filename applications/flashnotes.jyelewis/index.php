<?php
$access = 'login';
require('index.phpx');
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Flash notes</title>
	<?php echo $scriptIncludes; ?>
	<script type="text/javascript">
		$("document").ready(function(){
			setTimeout(function() { reloadIf(); },30000);
		});
		
		function reloadIf()
		{
			if ($("textarea").val() == '')
			{
				location.reload(true);
			}
			setTimeout(function() { reloadIf(); },30000);
		}
		
	</script>
	<style type="text/css">
		body {
			background-color:#bbb;
		}
		html, body, table, tr {
			height:100%;
			margin:0;
			padding:0;
			font-family:Arial;
		}
		table {
			width:100%;
		}
		td, tr {
			vertical-align: top;
		}
		#notesSidebar {
			width:200px;
			min-width:200px;
			max-width:200px;
			height:100%;
			border-right: 2px solid #555;
		}
		#notesSidebar .sidebarNote {
			margin: 3px 2px;
			padding: 3px;
			background-color:#ccc;
			color:#000;
			display:block;
			text-decoration:none;
			text-align:center;
			font-weight:bold;
		}
		#createButton {
			background-color:#bbb !important;
		}
		#createButton:hover {
			background-color:#333 !important;
			color:#fff;
		}
		#notesSidebar .sidebarNote:hover {
			background-color:#555;
			color:#fff;
		}
		#noteContent {
		
		}
		#pointUI {
			margin:0;
			padding:0;
			list-style:none;
		}
		#pointUI li {
			margin: 5px;
			padding:5px;
			word-wrap: break-word;
		}
		#pointUI li.stripe1 {
			background-color:#aaa;
		}
		#pointUI li.stripe2 {
			background-color:#ccc;
		}
		#newPointInput {
			width:80%;
			height: 100px;
			margin: 20px 50px;
		}
		#submit {
			border: 2px solid #aaa;
			background-color:#ccc;
			padding:5px 10px;
			font-weight:bold;
		}
		#submit:hover {
			border: 2px solid #aaa;
			background-color:#555;
			color:#fff;
		}
		#noteTitle {
			width: 500px;
			font-size:23px;
			margin: 0 0 0 100px;
		}
		.from {
			font-weight:bold;
			font-size:11px;
			display:inline-block;
			margin-right:25px;
			margin-top:3px;
			float:right;
		}
		.pointLink {
			display:block;
			text-decoration:none;
			color:#000;
		}
		.cancelLink {
			font-size:20px;
			text-decoration:none;
			color:#000;
		}
		.cancelLink:hover {
			text-decoration:underline;
		}
		.clear { clear:both; }
	</style>
</head>
<body>
	<table>
		<tr>
			<td id="notesSidebar">
				<a href="?createNote=1" class="sidebarNote" id="createButton">Create new note</a>
				<?php foreach($allNotes as $note) { ?>
				<a class="sidebarNote" href="?noteID=<?php echo $note['noteID']; ?>"><?php echo $note['title']; ?></a>
				<?php } ?>
			</td>
			<td id="noteContent">
				<?php
					echo $titleForm;
				?>
				<ul id="pointUI">
					<?php foreach($pointLI as $point){
						if ($point['pointID'] != $editPoint){
					?>
						<a href="?noteID=<?php echo $_GET['noteID']; ?>&editPoint=<?php echo $point['pointID']; ?>" class="pointLink">
							<li class="<?php echo $point['class']; ?>"><?php echo $point['content']; ?><div class="clear"></div></li>
						</a>
					<?php } else { ?>
						<li class="<?php echo $point['class']; ?> editing">
							<?php echo $editPointForm; ?>
							<a href="?noteID=<?php echo $_GET['noteID']; ?>" class="cancelLink">Cancel</a>
						</li>
						
					<?php } } ?>
				</ul>
				<?php
					echo $pointForm;
				?>
			</td>
		</tr>
	</table>
</body>
</html>
