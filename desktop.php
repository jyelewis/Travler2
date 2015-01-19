<?php require('desktop.phpx'); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Travlr - <?php echo $userTied->data['username']; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="initial-scale=1.0; maximum-scale=1.0;">
        <?php
            echo $scriptIncludes;
            javascriptinclude('/js/desktopFunctions.js');
            javascriptinclude('/js/serverFunctions.js');
            javascriptinclude('/js/desktop.js');
            javascriptinclude('/js/screensaver.js');
            echo $push->generateCode(); //generates the javascript to setup the server sent events and binds the functions and events
            cssinclude('/css/desktop.css');
        ?>
    </head>
    <body class="<?php echo $bodyClass; ?>">
        <!-- start cover that shows "preparing your desktop" until loaded -->
        <div id="prepWindowContainer">
            <div id="prepBackgroundContainer">
                <?php echo $defaultBG; ?>
            </div>
            <div id="PreparingWindow">
                <div id="usernameAjaxText"><?php echo $userTied->data['fullname']; ?></div>
                <div id="InitTextContainer">
                    <div id="DesktopInitText">Preparing your desktop...</div>
                </div>
            </div>
        </div>
        <!-- end cover -->
        <div id="backgroundContainer">
            <?php echo $backgrounds; ?>
        </div>
        <div id="menuBar">
            <a id="logoutButton" href="logout.php"></a>
            <div id="clockText"><?php echo $currTime; ?></div>
            <div id="netTraffic"></div>
        </div>
        <div id="desktopContainer">
            <div id="windowRestriction">
		<?php foreach($desktopIcons as $desktopIcon){ ?>
		<div class="fileContainer" data-filecode="<?php echo $desktopIcon['filecode']; ?>" <?php echo $desktopIcon['posString']; ?>>
		    <div class="fileBackground"></div>
		    <div class="fileIcon"></div>
		    <div class="fileTitle"><?php echo $desktopIcon['filename']; ?></div>
		</div>
		<?php } ?>
                <?php foreach($windows as $currWindow){ ?>
                <div class="windowContainer" data-app="<?php echo $currWindow['dataApp']; ?>" data-window="<?php echo $currWindow['dataWindow']; ?>">
                        <div class="windowCover">
                            <div class="windowHandle"></div>
                        </div>
                        <div class="windowCoverGrey"></div>
                        <div class="windowHandle">
                            <div class="windowTitle">
                                <?php echo $currWindow['title']; ?>
                            </div>
                            <div class="windowButtons">
                                <img src="images/windowTitleFade.png" class="windowTitleFade" alt="" />
                                <div class="btnHide"></div>
                                <div class="btnFS"></div>
                                <div class="btnClose"></div>
                            </div>
                        </div>
                        <div class="windowContent">
                            <iframe src="<?php echo $currWindow['url']; ?>" class="windowFrame" noresize="noresize">
                                    Your browser does not support iframes!
                            </iframe>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div id="launcher">
            <div id="launcherColor"></div>
            <div id="launcherIcons">
                <?php echo generateLauncherCode(); ?>
            </div>
        </div>
        <div id="screensaverContainer">
        	<div id="screensaverClock"><?php echo $currTime; ?></div>
        	<canvas id="scrensaverCanvas"></canvas>
        </div>
        <div id="windowTemplate" style="display:none;">
            <div class="windowContainer" data-app="" data-window="">
                <div class="windowCover">
                    <div class="windowHandle"></div>
                </div>
                <div class="windowCoverGrey"></div>
                <div class="windowHandle">
                    <div class="windowTitle">
                        
                    </div>
                    <div class="windowButtons">
                        <img src="images/windowTitleFade.png" class="windowTitleFade" alt="" />
                        <div class="btnHide"></div>
                        <div class="btnFS"></div>
                        <div class="btnClose"></div>
                    </div>
                </div>
                <div class="windowContent">
                    <iframe src="blank.html" class="windowFrame" noresize="noresize">
                            Your browser does not support iframes!
                    </iframe>
                </div>
            </div>
        </div>
        <div id="messageContainer"></div>
        <a id="messageTrigger" href="#messageContainer"></a>
    </body>
</html>
