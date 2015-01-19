function windowTask(jsondata)
{
	data = JSON.parse(jsondata);
	if(data.task == 'edit')
	{
		if(windowExists(data.code)) {
			   editWindow(data.code, data.title, data.appID, data.url);
		} else {
			 createWindow(data.code, data.title, data.appID, data.url);
		}
	}
	if (data.task == 'del'){
		deleteWindow(data.code);
	}
	if (data.task == 'move'){
		moveWindow(data.code, data.windowDo);
	}
}
function editWindow(code, title, appID, url)
{
    var window = getWindow(code);
   
    $(window).children(".windowHandle").children(".windowTitle").text(title);
    $(window).attr("data-app", appID);
    if ($(window).children(".windowContent").children("iframe").attr("src") != url)
    {
        $(window).children(".windowContent").children("iframe").attr("src", url)
    }
}
function updateLauncher(data)
{
    $("#launcherIcons").html(data);
    setupLauncher()
}
function createWindow(code, title, appID, url)
{
    $("#windowTemplate .windowContainer").attr("data-window", code);
    $("#windowTemplate .windowContainer .windowTitle").text(title);
    $("#windowTemplate .windowContainer").attr("data-app", appID);
    $("#windowTemplate .windowContainer iframe").attr("src", url);
    $("#windowTemplate .windowContainer").hide();
    $("#windowRestriction").append($("#windowTemplate").html());
     $("#windowTemplate .windowContainer iframe").attr("src", 'blank.html');
    var window = getWindow(code);
    setupWindow(window);
    window.fadeTo(300, 1);
    selectWindow(window);
}
function deleteWindow(code)
{
    var window = getWindow(code)
    $(window).fadeTo(300, 0, function(){
	$(window).remove();
	selectFront();
    });
}
function moveWindow(code, windowDo)
{
    if(windowDo == 'hide')
    {
        hideWindow(code)
    }
    if(windowDo == 'show')
    {
        showWindow(code)
    }
    if(windowDo == 'select')
    {
        selectWindow(getWindow(code))
    }
    if(windowDo == 'fsTrue')
    {
        fsWindow(getWindow(code), true)
    }
    if(windowDo == 'fsFalse')
    {
        fsWindow(getWindow(code), false)
    }
    if(windowDo == 'reload')
    {
        reloadWindow(code)
    }
}
function reloadWindow(code)
{
	var currWindow = getWindow(code);
	var currFrame = currWindow.children(".windowContent").children("iframe").prop('contentWindow')
	currFrame.location.reload();
}
function showMessage(message)
{
	$("#messageContainer").html(message);
	$("#messageTrigger").click();
}
function redirect(url)
{
	location.href = url;
}