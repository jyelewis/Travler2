function contextMenu(clickable, menu, callback)
{
	$menu = $(menu);
	$clickable = $(clickable)
	$menu.after('<div class="contextMenuCover"></div>');
	$(".contextMenuCover").css({
		'position': 'absolute',
		'top': '0',
		'bottom': '0',
		'left': '0',
		'right': '0',
		'z-index': '100',
		'display': 'none'
	});
	$menu.css('z-index', '101')
	$menu.addClass('travlrContextMenu');
	$menu.children('li:first-child').addClass('first');
	$menu.children('li:last-child').addClass('last');
	$clickable.click(function(e){
		$menu.data("clicked", this);
		$menu.css('display', 'block')
		//var posX = $clickable.offset().left, posY = $clickable.offset().top;
		$menu.css('left', e.pageX - 0)
		$menu.css('top', e.pageY - 0)
		$menu.css('display', '');
		$menu.slideDown(100);
		$(".contextMenuCover").show();
		$("#menu").click(function(){ return false; });
		
			$(".contextMenuCover").one("click", function() {
				$menu.hide()
				$(".contextMenuCover").hide();
			});
			
		setTimeout(function(){ $menu.show(); }, 3)
	});
	$menu.children('li').click(function(e){
		callback(this, $menu.data("clicked"));
	});
}