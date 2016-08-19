window.onload = function(){
	setTimeout(function () {
		$(".loadingbox").hide(0);
	},500);
}
$(function() {
	if(window.screen.width>0 && /android\s/.test(navigator.userAgent.toLowerCase())){
		var standardDpi,dpi,w;
		w = window.screen.width;
		if(w>0){
			if(w < 320){
				standardDpi = 120;
			}else if(w < 480){
				standardDpi = 160;
			}else if(w < 640){
				standardDpi = 240;
			}else if(w < 960){
				standardDpi = 320;
			}else if(w < 1280){
				standardDpi = 480;
			}else{
				standardDpi = 640;
			}
		}
		dpi = 800*standardDpi/w;
		   document.querySelector("meta[name=viewport]").setAttribute('content','width=640,initial-scale=1.0, maximum-scale=3.0, minimum-scale=1.0,target-densitydpi='+dpi+', user-scalable=0');
	}
	touch.on('.homebtn', 'touchstart', function(ev){
		ev.preventDefault();
	});
	touch.on('.homebtn', 'tap', function(ev){
		location.href="/index.php/wapIndex";
	});

	var target = document.getElementById("target");
	var dx, dy;

	touch.on('.homebtn', 'drag', function(ev){
		dx = dx || 0;
		dy = dy || 0;
		console.log("当前x值为:" + dx + ", 当前y值为:" + dy +".");
		var offx = parseInt(dx + ev.x);
		var offy = parseInt(dy + ev.y);
		console.log(dx);
		console.log(ev.x);
		console.log(offx);
		console.log(dy);
		console.log(ev.y);
		console.log(offy);
		if(offx<10 && offx>-($(window).width()-$(".homebtn").width()) && offy<10 && offy>-($(window).height()-100)){
			this.style.webkitTransform = "translate3d(" + offx + "px," + offy + "px,0)";
		}
	});

	touch.on('.homebtn', 'dragend', function(ev){
		dx += ev.x;
		dy += ev.y;
	});
});

