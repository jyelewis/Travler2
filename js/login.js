function usernameUpdate(username)
{
    var usernameDiv = $("#usernameAjaxText")
    $.post("ajax/loginData.php", { username: username },
    function(responce){
            if (responce != '**errNotFound**')
            {
                    usernameDiv.text(responce);
                    usernameDiv.fadeIn('fast');
            } else {
                    usernameDiv.fadeOut('fast', function(){
                        usernameDiv.val("")
                    });
            }
    });
}

    
function showMessage(message)
{
	$("#messageContainer").html(message);
	$("#messageTrigger").click();
}


$(document).ready(function(){

	$("a#messageTrigger").fancybox();
	if(kickedMessage != 0)
	{
		showMessage(kickedMessage);
	}
    if (triggerAjax === 1)
    {
        window.imagesLoaded = '0';
        window.ajaxLoaded = '0';
        $(window).load(function(){
            window.imagesLoaded = '1';
            if(window.ajaxLoaded == '1')
            {
                location.href='desktop.php';
            }
        });
        $.ajax({
            url: "ajax/desktopInit.php"
        }).done(function() { 
            window.ajaxLoaded = '1';
            if (window.imagesLoaded == '1')
            {
                location.href='desktop.php';
            }
        });
    } else {
        var passwordInput = $("#passwordInput")
        var passwordDummyInput = $("#passwordDummyInput")
        var usernameInput = $("#usernameInput")
        passwordDummyInput.focus(function(){
            passwordDummyInput.hide();
            passwordInput.show();
            passwordDummyInput.removeClass('wrongRed');
            passwordInput.focus();
        });
        passwordInput.blur(function(){
            if (passwordInput.val() == '')
            {
                passwordInput.hide();
                passwordDummyInput.show();
            }
        });
        usernameInput.focus(function(){
            if($(this).val() == 'username')
            {
                $(this).addClass('notBlank');
                $(this).val("");
            }
        });
        usernameInput.blur(function(){
            if (usernameInput.val() == '')
            {
                $(this).removeClass('notBlank');
                $(this).val("username");
            }
            usernameUpdate($(this).val());
        });
    }
    $("#userBackgroundImage").load(function(){
        $("#userBackgroundImage").fadeIn(2000);
    });
});