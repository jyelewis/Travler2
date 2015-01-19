<?php
require('index.phpx');
?>
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?php echo $scriptIncludes; ?>
        <?php cssInclude('/server/style.css'); ?>
        <script type="text/javascript">
        <?php
        if($serverPower)
        {
        	echo 'window.serverRun = true;';
        } else {
        	echo 'window.serverRun = false;';
        }
        ?>
        function fetchRequest()
		{
			var currDate = new Date().getTime();
			fetchRequestajx = $.ajax({
			  url: "fetch.php?randDate="+currDate,
			  timeout: 20000,
			  dataType: "json"
			});
			
			fetchRequestajx.done(function(data) {
				$("#dynLeft").html(data.sidebar);
				if (data.serverpower == 'true')
				{	
					if (window.serverRun != true)
					{
						window.serverRun = true;
						serverRequest();
						$("#powerButton").text('Power down server');
					}
				}
				if (data.serverpower == 'false')
				{
					window.serverRun = false;
					$("#powerButton").text('Power up server');
				}
				if (data.data == 'true')
				{
					$("#logContainer ul li").removeClass('bold');
					$.each(data.log, function(){
						$("#logContainer ul").append('<li class="stripe1 bold">' + this.content + '</li>');
					});
				}
			});
			
			fetchRequestajx.fail(function(jqXHR, textStatus) {
				console.log( "Request failed: " + textStatus );
			});
				
			fetchRequestajx.complete(function(jqXHR){
				fetchRequest();
				//console.log(jqXHR.responseText);
			});
		}
		function serverRequest(){
			/*var currDate = new Date().getTime();
			serverRequestajx = $.ajax({
			  url: "serverProcess.php?randDate="+currDate,
			  timeout: 20000
			});

			serverRequestajx.complete(function(jqXHR){
				if(window.serverRun == true)
				{
					serverRequest();
				}
			});
			
			serverRequestajx.done(function(data) {
				if (data != 'safequit'){
					$("#logContainer ul").append('<li class="stripe1 bold">Server seems to be having issues... Check the server log</li>');
				}
			});*/
		}
        $(document).ready(function(){
        	fetchRequest();
        	if(window.serverRun == true)
			{
				serverRequest();
				$("#powerButton").text('Power down server');
			} else {
				$("#powerButton").text('Power up server');
			}
			$("#powerButton").click(function(){
				if(window.serverRun == true)
				{
					$.ajax('?power=down');
					$("#powerButton").text('Powering down...');
				} else {
					$.ajax('?power=up');
					$("#powerButton").text('Powering up...');
				}
				
			});
        });
        </script>
    </head>
    <body>
    	<div id="colContainer">
			<div id="colLeft" class="col">
				<div id="dynLeft">
					<?php echo generateLeftBar(); ?>
				</div>
				<div id="powerButton" class="btnLink"></div>
			</div>
			<div id="colCenter" class="col">
				<div id="logContainer">
					<ul>
						
					</ul>
				</div>
			</div>
		</div>
    </body>
</html>