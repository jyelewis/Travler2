<?php require('login.phpx');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Travlr - login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="initial-scale=1.0; maximum-scale=1.0;">
        <?php
            echo $scriptIncludes;
            javascriptInclude('/js/login.js');
            cssInclude('/css/login.css');
        ?>
        <script type="text/javascript"><?php echo $runScripts; ?></script>
    </head>
    <body>
        <div id="backgroundContainer">
             <img src="images/background.gif" alt="" />
            <?php if($stage=='init'){ ?>
                <img src="<?php echo getDefaultBG($userTied->userID); ?>" alt="" id="userBackgroundImage" style="display:none;" />
            <?php } ?>
        </div>
        <div id="loginWindow">
            <?php if(isset($usernameAjaxText)){ ?>
                <div id="usernameAjaxText"><?php echo $usernameAjaxText; ?></div>
            <?php } else { ?>
                <div id="usernameAjaxText" style="display:none;"></div>
            <?php } ?>

            <?php if($stage == 'login'){ ?>
                <div id="loginForm">
                    <?php echo $loginForm; ?>
                </div>
            <?php } else { ?>
            <div id="usernameAjaxText"><?php echo $userTied->data['fullname']; ?></div>
            <div id="InitTextContainer">
                <div id="DesktopInitText">Logging in...</div>
            </div>
            <?php } ?>
        </div>
        
        <div id="preload">
            <?php if($stage=='init'){ foreach($preloadImages as $image){ ?>
                <img src="<?php echo $image; ?>" alt="preload image" />
            <?php }} ?>
        </div>
        <div id="messageContainer"></div>
        <a id="messageTrigger" href="#messageContainer"></a>
    </body>
</html>
