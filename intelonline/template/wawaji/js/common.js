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
	$(".begin").click(function(){
            var count = $("#count").val();
            if(count <= 0){
		$(".popbox").css("display","block");
            }else{
                location.href="/index.php/doll/rules";
            }
	})
	$(".close").click(function(){
		$(".popbox").css("display","none");
	})
	
//	关注覆层
  $(".attent").click(function(){
  	 $(".popbox").css("display","block")
  })
  $(".popbox2").click(function(){
  	$(".popbox").css("display","none")
  })
})
