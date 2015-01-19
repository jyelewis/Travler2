$(document).ready(function(){
	$('a').click(function(e){
		if($(this).attr('ajax') != 'false' && $('body').hasClass('ajaxLinks'))
		{
			e.preventDefault();
			go($(this).attr('href'));
		}
	});
});

function go(url)
{
	$('body').fadeOut(30);
	$.ajax({
	  url: url,
	  cache: false,
	}).done(function(data) { 
		//$('body').hide();
		$('body').html(data)
		setTimeout( function(){ $('body').fadeIn(200) }, 5);
		window.history.replaceState('','', url);
	});
	console.log(url);
}