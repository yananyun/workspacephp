var viewport = document.getElementById("viewport");
if (window.devicePixelRatio >= 1.3)
{
	var scale = (screen.width)/640;
	//viewport.content = "width=640, minimum-scale="+ scale +", maximum-scale="+scale+",user-scalable=no";
	viewport.content = 'target-densitydpi=device-dpi,width=640, minimum-scale='+ scale +', maximum-scale='+scale+',initial-scale='+scale;
}else
{
	viewport.content = "width=device-width, minimum-scale=1, maximum-scale=1, target-densitydpi=device-dpi";
}

window.onload = function(){
	$(".loadingbox").hide(0);
}

$(function(){
	$(".linksa").click(function() {
	 	  $(".f41").addClass('animate1').removeClass('animate11');
	 	  $(".f42").addClass('animate2').removeClass('animate22');
	 	   ga('send', 'event', "硬享公社", 'click', 'linksa');
	 });
	 $(".fanui1").click(function(){
	 	
	 	 $(".f41").removeClass('animate1').addClass('animate11');
	 	 $(".f42").removeClass('animate2').addClass('animate22');
	 	 ga('send', 'event', "硬享公社", 'click', 'fanui1');
	 });
	 $(".closebtn").click(function(){
  		  $(".f4alert").hide();
  		  $(".f41").removeClass('animate1').removeClass('animate11');
  		  $(".f42").removeClass('animate2').removeClass('animate22');
  		   ga('send', 'event', "硬享公社", 'click', 'closebtn');
  		  
	 	});
	 $(".geting").click(function(){
	 	 $(this).closest(".swiper-slide").find(".f4alert").show();
	 	  ga('send', 'event', "硬享公社", 'click', 'geting');
	 });
	 
})

