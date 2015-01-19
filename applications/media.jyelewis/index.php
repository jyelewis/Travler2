<?php
$access = 'login';
require('../../SysData/init.php');
?>
<!DOCUTYPE html>
<html>
    <head>
        <?php
			echo $scriptIncludes;
		?>
		<link rel="stylesheet" href="themes/css/apple.css" title="jQTouch">
        <script src="jqtouch-jquery.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="jqtouch.min.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" src="script.js"></script>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<title>Media clock</title>
    </head>
	<body>
		<div id="home" class="current">
			<div class="toolbar">
				<h1>Media clock</h1>
				<a class="button slideup" id="infoButton" href="#about">About</a>
			</div>
			<div class="timerInfo">
				<h1>Current time count</h1>
				<div class="timerCounter mainTimer">0:00</div>
			</div>
			<div class="timerInfo currentTimer">
				<h1>You have a timer going</h1>
				<div class="timerCounter">0:00</div>
				<div class="timerType green"></div>
			</div>
			<div class="scroll">
				<ul class="rounded stopButton">
					<li class=""><a href="#" class="stopTimer">Stop Current Timer</a></li>
				</ul>
				<ul class="rounded">
					<li class=""><a class="cube" href="#custom">Add Custom Time</a></li>
					<li class=""><a href="#alltimings">View All Timings</a></li>
				</ul>
				<ul class="rounded timerButtons">
					<li class=""><a href="#" class="startComputer">Start Timer: Computer</a></li>
					<li class=""><a href="#" class="startOutside">Start Timer: Outside</a></li>
				</ul>
			</div>
		</div>
		<div id="about" class="selectable">
				<div id="aboutText">
					<h1>Computer timer</h1>
					<strong>Created by Jye Lewis</strong>
				</div>
				<p><br><br><a href="#" class="grayButton goback">Close</a></p>
		</div>
		<div id="custom">
			<div class="toolbar">
				<h1>Custom Time</h1>
				<a class="back" href="#">Back</a>
			</div>
			<form class="scroll">
			<ul class="edit rounded">
				<li><input type="text" placeholder="Hours" id="customTimeHour" /></li>
				<li><input type="text" placeholder="Minuets" id="customTimeMin" /></li>
				<li>Subtract <span class="toggle"><input type="checkbox" id="customTimeSub" /></span></li>
				<li><a href="#" id="customTimeSave" class="WhiteButton goBack">Save</a></li>
			</ul>
			
			</form>
		</div>
    </body>
</html>
