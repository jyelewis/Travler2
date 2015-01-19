$(document).ready(function(){
    
    window.failCount = 0;
    window.ajaxTime = 0;
    window.mobileView = false;
    var isiPhone = navigator.userAgent.toLowerCase().indexOf("iphone");
    var isiPod = navigator.userAgent.toLowerCase().indexOf("ipod");
    var isiPad = navigator.userAgent.toLowerCase().indexOf("ipad");
    if(isiPhone > -1 || isiPod > -1)
    {
		window.mobileView = true;
    }
    if (isiPad > -1)
    {
    	$("body").addClass('ipad');
    }
    if((isiPhone > -1) || (isiPod > -1) || (isiPad > -1))
    {
		$("body").addClass('touch');
    }
    if (window.mobileView)
    {
		$("body").addClass('mobileView');
    }
    
    $(document).keyup(function (e) {
		if (e.which == 18) $.isAlt=false;
	}).keydown(function (e) {
		if (e.which == 18) $.isAlt=true;
	});
	$(document).keypress(function (e) {
		keyCommand(e);
	});
	setInterval(function(){
		$("iframe").each(function(){
			var windowID = $(this).parent().parent().attr('data-window');
			$(this).contents().bind("keydown", function(e) { if (e.which == 18) $.isAlt=true; });
			$(this).contents().bind("keyup", function(e) { if (e.which == 18) $.isAlt=false; });
			$(this).contents().bind("keydown", function(e) { window.parent.iframeKeyCommand(e, windowID) });
		});
	}, 1000);

	
    
    $("#windowRestriction .windowContainer").each(function(){
	setupWindow(this);
    });
    setupDesktopIcons();
    setupLauncher();
    $("a#messageTrigger").fancybox();
    $("#windowRestriction .windowContainer:last-child").topZIndex();
    
})
$(window).load(function(){
    $("#prepWindowContainer").fadeOut(900, 0);
    
    //setTimeout(ajaxUpdate, 1000);
    
    selectFront();
    
    $('#backgroundContainer').cycle({ 
        fx:      'fade', 
        speed:    5000, 
        timeout:  60000 
    });
});