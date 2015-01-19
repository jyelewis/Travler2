$(document).ready(function(){

var jQT = new $.jQTouch({
                icon: 'icon.png',
                icon4: 'iconRet.png',
                addGlossToIcon: false,
                statusBar: 'black',
                preloadImages: []
            });

	$('#customTimeSave').click(function(){
		var sub = $("#customTimeSub").is(':checked');
		if (sub)
		{
			sub = '1'
		} else {
			sub = '0';
		}
		$.post("request.php", {
			do: "saveCustomTime",
			customTimeHour: $("#customTimeHour").val(),
			customTimeMin: $("#customTimeMin").val(),
			customTimeSub: sub,
		}, function(){
			$("#customTimeHour").val('');
			$("#customTimeMin").val('');
		});
	});
	ajaxUpdate();    
   	$(".stopTimer").click(function(){
    	$.post("request.php", { do: "stopTimers" } );
    });
    $(".startComputer").click(function(){
    	$.post("request.php", { do: "startComputer" } );
    });
    $(".startOutside").click(function(){
    	$.post("request.php", { do: "startOutside" } );
    });
    $(".stopButton").hide();
    setInterval(updateFlash, 500);
    setInterval(ajaxUpdate, 1000);
});

function updateFlash()
{
	if ($('.mainTimer').hasClass('flash'))
	{
		if ($('.mainTimer').hasClass('flashState'))
		{
			$('.mainTimer').removeClass('flashState');
		} else {
			$('.mainTimer').addClass('flashState');
		}
	}
}

function showTimer()
{
	var time = 300;
	if(!$('.currentTimer').is(":visible"))
	{
		$(".currentTimer").slideDown(time);
		$(".stopButton").slideDown(time);
		$(".timerButtons").slideUp(time);
	}
}

function hideTimer()
{
	var time = 300;
	if($('.currentTimer').is(":visible"))
	{
		$(".currentTimer").slideUp(time);
		$(".stopButton").slideUp(time);
		$(".timerButtons").slideDown(time);
	}
}

function ajaxUpdate()
{	
	var currDate = new Date().getTime();
	updateRequest = $.ajax({
		url: "request.php?randDate="+currDate,
		timeout: 20000,
		dataType: "json"
	});
	
	updateRequest.done(function(data) {
    	$(".mainTimer").text(data.mainTimer);
    	if (data.timerType == 'none')
    	{
    		hideTimer();
    	}
    	if (data.timerType == 'computer')
    	{
    		$('.currentTimer .timerCounter').text(data.currTimer)
    		$('.timerType').removeClass('green').addClass('red').text('Computer');
    		showTimer();
    	}
    	if (data.timerType == 'outside')
    	{
    		$('.currentTimer .timerCounter').text(data.currTimer)
    		$('.timerType').removeClass('red').addClass('green').text('Outside');
    		showTimer();
    	}
    	if (data.flash == 'yes')
    	{
    		$('.mainTimer').addClass('flash');
    	}
    	if (data.flash == 'no')
    	{
    		$('.mainTimer').removeClass('flash').removeClass('flashState');
    	}
    	
	});
        
	updateRequest.complete(function(jqXHR){
	});
}