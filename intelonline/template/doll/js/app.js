(function(){
	$(function () {
		"use strict";
		var time = 25;
		var iLife = 3;
		var bStatus = true;
		$(".start").on("click",function(){
			if(bStatus){
				bStatus = false;
				iLife--;
				$(this).find("img").attr("src","images/start3.png");
				timeline(function(){
					$(".prompt").show();
					$(".prompt").find(".main").hide();
					if(iLife > 0){
						$(".prompt").find(".main").eq(1).show();
					}else{
						$(".prompt").find(".main").eq(0).show();
					}
				});
			}
		})

		$(".changeActive").on("tap",function(){
			changeStatus($(this));
			return false;
		})


		// 时间用完--》继续
		$(".overtime").find("a").on("click",function(){
			$(".timeline").removeClass('active');
			$(".timenum").find("span").text("25");		
			$(".prompt").hide();
			time = 25;
		})


		function timeline(fn){
			$(".timeline").addClass("active");
			var t = setInterval(function () {
				$(".timenum span").html(time);
				if(time>0){
					time--;
				}else{
					clearInterval(t);
					bStatus = true;
					fn();
				}
			},1000);
		}

		function changeStatus($node){
			var $img = $node.find("img");
			var tmp =$img.attr("src");
			var activeImg = $img.data("src");
			$node.find("img").attr("src",activeImg);
			setTimeout(function(){
				$node.find("img").attr("src",tmp);
			},100);
		}
	})
})()