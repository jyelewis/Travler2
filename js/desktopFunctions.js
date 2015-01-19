function selectFront()
{
	var best;
	var maxz;    
	$('#windowRestriction').children().each(function(){
		var z = parseInt($(this).css('z-index'), 10);
		if (!best || maxz<z) {
			best = this;
			maxz = z;
		}
	});
	if($(best).hasClass("windowContainer"))
	{
		selectWindow(best);
	}
}
function keyCommand(e)
{
	if ((String.fromCharCode(e.which) == 'Q' && $.isAlt == true) || e.which == 339)
	{
		reqCloseWindow($(".windowContainer.selectedWindow").attr('data-window'));
	}
	if ((String.fromCharCode(e.which) == 'H' && $.isAlt == true) || e.which == 729)
	{
		hideWindow($(".windowContainer.selectedWindow").attr('data-window'));
	}
}
function iframeKeyCommand(e, windowID)
{
	if ((String.fromCharCode(e.which) == 'Q' && $.isAlt == true) || e.which == 339)
	{
		parent.reqCloseWindow(windowID);
	}
	if ((String.fromCharCode(e.which) == 'H' && $.isAlt == true) || e.which == 729)
	{
		parent.hideWindow(windowID);
	}
}

function setupWindow(obj)
{
    if (!window.mobileView)
    {
	$(obj).draggable({containment: '#windowRestriction', handle: '.windowHandle', 
	    start: function(){
		$(this).children(".windowCover").addClass("trans").show();
	    },
	    stop: function(){
		$(this).children(".windowCover").removeClass("trans").hide();
	    }
	});
	$(obj).resizable({containment: '#windowRestriction', minHeight: 250, minWidth: 350, 
	    start: function(){
		$(this).children(".windowCover").addClass("trans").show();
	    },
	    stop: function(){
		$(this).children(".windowCover").removeClass("trans").hide();
	    }
	});
    }
    $(obj).mousedown(function(){
       selectWindow(this);
    });
    $(obj).find(".btnHide").click(function(){
       hideWindow($(this).parent().parent().parent().attr("data-window")); 
    });

    $(obj).find(".btnFS").click(function(){
       fsToggle($(this).parent().parent().parent()); 
    });

    $(obj).find(".btnClose").click(function(){
       reqCloseWindow($(this).parent().parent().parent().attr("data-window")); 
    });
    if (window.mobileView)
    {
	$(".btnFS").remove();
	fsWindow(obj, true, 0);
    }
    selectWindow(obj);
}
function hideWindow(code)
{
    var currWindow = getWindow(code)
    currWindow.fadeOut(400);
    var currFrame = currWindow.children(".windowContent").children("iframe").prop('contentWindow')
    if(typeof currFrame.hideWindow == 'function')
    {
		currFrame.hideWindow();
    }
}
function showWindow(code)
{
    var currWindow = getWindow(code);
    currWindow.fadeIn(400);
    var currFrame = currWindow.children(".windowContent").children("iframe").prop('contentWindow')
    if(typeof currFrame.showWindow == 'function')
    {
        currFrame.showWindow();
    }

}
function fsWindow(obj, tofs, timing)
{
    timing = typeof timing !== 'undefined' ? timing : 200;
    if (!tofs)
    {
            if (!window.mobileView)
	    {
		$(obj).resizable("option", "disabled", false);
		$(obj).draggable("option", "disabled", false);
		$(obj).css('border-radius', '8px');
		$(obj).removeClass('fullscreen');
		$(obj).animate({
		width: '500px',
		height: '300px',
		top: '50',
		left: '50'
		}, timing, function(){
		    $(obj).css('box-shadow', '2px 2px 10px #333');
		});
	    }
    } else {
            $(obj).resizable("option", "disabled", true);
            $(obj).draggable("option", "disabled", true);
            $(obj).removeClass("ui-resizeable-disabled");
            $(obj).removeClass("ui-draggable-disabled");
            $(obj).removeClass("ui-state-disabled");
            $(obj).css('box-shadow', 'none');
            $(obj).animate({
            width: '100%',
            height: '100%',
            top: '0',
            left: '0',
            }, timing, function(){
                $(obj).css('border-radius', '0px');
		$(obj).addClass('fullscreen');
            });    
    }
}
function fsToggle(obj)
{
	if ($(obj).width() == $("#desktopContainer").width())
	{
		fsWindow(obj, false);
	} else {
		fsWindow(obj, true);
	}
}
function selectWindow(obj)
{
	if($(obj).is(':visible') == false)
	{   
		showWindow($(obj).attr('data-window'));
	}
    if (obj.length != 0 && !$(obj).hasClass('selectedWindow'))
    {
        $(".windowContainer").removeClass('selectedWindow');
        $(".windowContainer .windowCover").show();
        $(obj).children(".windowCover").hide()
        $(obj).topZIndex("#windowRestriction");
        $(obj).addClass('selectedWindow');
        
		var currFrame = $(obj).children(".windowContent").children("iframe").prop('contentWindow')
		if(typeof currFrame.selectWindow == 'function')
		{
			currFrame.selectWindow();
		}
    }
}
function setupLauncher()
{
    $(".launcherIcon").mouseover(function(){
        $(this).children(".launcherText").topZIndex(); 
    });
    
    $(".launcherIcon[data-exists=1]").click(function(){
        selectApplication($(this).attr("data-app"));
        $(this).children(".launcherText").topZIndex(); 
    });
    $(".launcherIcon[data-exists=0]").click(function(){
        launchApplication($(this).attr("data-app"));
        $(this).children(".launcherText").topZIndex();
    });
}
function launchApplication(appID)
{
    netTraffic();
    var currDate = new Date().getTime();
    $.post("ajax/launchApp.php?randDate="+currDate, {appID: appID});
}
function getWindow(code)
{
    return $("#windowRestriction .windowContainer[data-window='"+code+"']")
}
function windowExists(code)
{
    if(getWindow(code).length == 0){
        return false
    } else {
        return true
    }
}


function selectApplication(appName)
{
    $('#windowRestriction .windowContainer[data-app="'+appName+'"]').each(function(){
        selectWindow(this);
    });
}
function reqCloseWindow(windowID, secondaryReq)
{
    var currWindow = getWindow(windowID);
	var canClose = true;
	if (secondaryReq != true)
	{
		var currFrame = currWindow.children(".windowContent").children("iframe").prop('contentWindow')
		if(typeof currFrame.closeWindow == 'function')
		{
			canClose = currFrame.closeWindow(reqCloseWindow, windowID);
		}
	}
	if (canClose == true)
	{
		currWindow.children(".windowCoverGrey").fadeIn(300);
		var currDate = new Date().getTime();
		netTraffic();
		$.post("ajax/closeWindow.php?randDate="+currDate, {windowID: windowID}, function(data){
			if(data == 'killWindow' && windowExists(windowID))
			{
				deleteWindow(windowID);
			}
		});
		setTimeout(function(){checkClose(windowID)},500)
	}
}
function checkClose(windowID) {
    if (windowExists(windowID)){
        reqCloseWindow(windowID, true);
    }
   
}
function netTraffic()
{ 
    netBlue();
    setTimeout(netGrey, 300);
    setTimeout(netBlue, 600);
    setTimeout(netGrey, 900);
}
function netError()
{   
    netRed();
    setTimeout(netGrey, 300);
    setTimeout(netRed, 600);
    setTimeout(netGrey, 900);
    setTimeout(netRed, 1200);
    setTimeout(netGrey, 1500);
    setTimeout(netRed, 1800);
    setTimeout(netGrey, 2100);
    setTimeout(netRed, 2400);
    setTimeout(netGrey, 2700);
    setTimeout(netRed, 3000);
}
function netGrey()
{
    $("#netTraffic").removeClass('blue').removeClass('red');
}
function netBlue()
{
    $("#netTraffic").removeClass('red').addClass('blue');
}
function netRed()
{
    $("#netTraffic").removeClass('blue').addClass('red');
}
function setupDesktopIcons()
{
	$(".fileContainer").each(function(){
		$(this).css('top', convertPos($(this).attr('data-postop'), 'top', false) + 'px');
		$(this).css('left', convertPos($(this).attr('data-posleft'), 'left', false) + 'px');
	});
    $(".fileContainer").draggable({containment: '#windowRestriction',
	stop: function(){
	    var position = $(this).position();
	    var fileCode = $(this).attr('data-filecode');
	    var currDate = new Date().getTime();
	    $.post("ajax/saveDesktopIcon.php?randDate="+currDate, {fileCode: fileCode, posTop: convertPos(position.top, 'top', true), posLeft: convertPos(position.left, 'left', true)});
	    console.log("top: " + convertPos(position.top, 'top', true) + " left: " + convertPos(position.top, 'left', true));
	}	
    });
}

function convertPos(position, type, toPercent)
{
	if(type == 'top')
	{
		var screen = $(document).height();
	} else {
		var screen = $(document).width();
	}
	if (toPercent)
	{
		return (position/screen)*100;
	} else {
		return (position/100)*screen;
	}
}