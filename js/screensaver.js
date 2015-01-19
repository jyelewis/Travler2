$(document).ready(function(){
	window.s_saver = null;
	setInterval(function(){ $("iframe").contents().bind("mousemove click keydown", function() { screensaverMove(); }); }, 1000);

	$(document).bind('mousemove click keydown', function(){
		screensaverMove();
	});
	screensaverMove();
});

function screensaverMove(){
	
	clearTimeout(window.s_saver);

	window.s_saver = setTimeout(function(){
		startScreensaver();
	}, 50*1000);


	if (window.runScreensaver)
	{
		stopScreensaver();
	}
}


function startScreensaver(){
	var canvas = document.getElementById("scrensaverCanvas");
	var ctx = canvas.getContext("2d");
	
	window.runScreensaver = true;
	
	$("#screensaverContainer").click(function(){
		stopScreensaver();
	});
	
	//Lets make the canvas occupy the full page
	var W = window.innerWidth, H = window.innerHeight;
	canvas.width = W;
	canvas.height = H;
	
	//Lets make some window.ScreensaverParticles
	window.ScreensaverParticles = [];
	for(var i = 0; i < 25; i++)
	{
		window.ScreensaverParticles.push(new particle());
	}
	$("#screensaverContainer").topZIndex().fadeIn(3000);
	setTimeout(function(){
	draw();
	$("#scrensaverCanvas").fadeIn(400);
	}, 3000);
	
}

function stopScreensaver()
{
	window.runScreensaver = false;
	$("#screensaverContainer").fadeOut(300);
}

function particle()
{
	var W = window.innerWidth, H = window.innerHeight;
	//location on the canvas
	this.location = {x: Math.random()*W, y: Math.random()*H};
	//radius - lets make this 0
	this.radius = 0;
	//speed
	this.speed = 3;
	//steering angle in degrees range = 0 to 360
	this.angle = Math.random()*360;
	//colors
	var r = Math.round(Math.random()*255);
	var g = Math.round(Math.random()*255);
	var b = Math.round(Math.random()*255);
	var a = Math.random();
	this.rgba = "rgba("+r+", "+g+", "+b+", "+a+")";
}
		
//Lets draw the window.ScreensaverParticles
function draw()
{
	var canvas = document.getElementById("scrensaverCanvas");
	var ctx = canvas.getContext("2d");
	var W = window.innerWidth, H = window.innerHeight;

	//re-paint the BG
	//Lets fill the canvas black
	//reduce opacity of bg fill.
	//blending time
	ctx.globalCompositeOperation = "source-over";
	ctx.fillStyle = "rgba(0, 0, 0, 0.02)";
	ctx.fillRect(0, 0, W, H);
	ctx.globalCompositeOperation = "lighter";
	
	for(var i = 0; i < window.ScreensaverParticles.length; i++)
	{
		var p = window.ScreensaverParticles[i];
		ctx.fillStyle = "white";
		ctx.fillRect(p.location.x, p.location.y, p.radius, p.radius);
		
		//Lets move the window.ScreensaverParticles
		//So we basically created a set of window.ScreensaverParticles moving in random direction
		//at the same speed
		//Time to add ribbon effect
		for(var n = 0; n < window.ScreensaverParticles.length; n++)
		{
			var p2 = window.ScreensaverParticles[n];
			//calculating distance of particle with all other window.ScreensaverParticles
			var yd = p2.location.y - p.location.y;
			var xd = p2.location.x - p.location.x;
			var distance = Math.sqrt(xd*xd + yd*yd);
			//draw a line between both window.ScreensaverParticles if they are in 200px range
			if(distance < 200)
			{
				ctx.beginPath();
				ctx.lineWidth = 1;
				ctx.moveTo(p.location.x, p.location.y);
				ctx.lineTo(p2.location.x, p2.location.y);
				ctx.strokeStyle = p.rgba;
				ctx.stroke();
				//The ribbons appear now.
			}
		}
		
		//We are using simple vectors here
		//New x = old x + speed * cos(angle)
		p.location.x = p.location.x + p.speed*Math.cos(p.angle*Math.PI/180);
		//New y = old y + speed * sin(angle)
		p.location.y = p.location.y + p.speed*Math.sin(p.angle*Math.PI/180);
		//You can read about vectors here:
		//http://physics.about.com/od/mathematics/a/VectorMath.htm
		
		if(p.location.x < 0) p.location.x = W;
		if(p.location.x > W) p.location.x = 0;
		if(p.location.y < 0) p.location.y = H;
		if(p.location.y > H) p.location.y = 0;
	}
	if(window.runScreensaver)
	{
		setTimeout(draw, 30);
	}
}