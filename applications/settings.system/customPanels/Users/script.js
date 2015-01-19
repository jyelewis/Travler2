$(document).ready(function(){
    $("#password input").keyup(function(){
	checkPassBox();
    });
    $("#password input").blur(function(){
	checkPassBox();
    });$("#password input").focus(function(){
	checkPassBox();
    });
    $("input").change(function(){
		$("#submit").fadeIn();
    });
	$("select").change(function(){
		$("#submit").fadeIn(300);
    });
    setTimeout(function(){ $("#formConfirm").fadeOut(1000); }, 2000);
    
});

function checkPassBox()
{
    if($("#password input").val() == '')
    {
	$("#passwordConfirm").fadeOut();
    } else {
	if ($("#passwordConfirm").css('display') == 'none')
	{
	    $("#passwordConfirm input").val('');
	}
	$("#passwordConfirm").fadeIn();
    }
}
