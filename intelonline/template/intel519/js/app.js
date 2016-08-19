$(function(){
	$(document).ready(function() {
		$(".pointbox").on("click",function(){
			$.ajax({
				url: '/index.php/edison/getLottery',
				type: 'POST',
				dataType: 'JSON',
			})
			.done(function(data) {
				console.log(data);


				var a = Math.floor(Math.random()*5)*60 + 30;
				$(".pointbox").rotate({
					duration:3000,
					angle: 0, 
					animateTo:1440+a,
					easing: $.easing.easeOutSine,
					callback: function(){
						var num = (a-30)/60;
						var num=3;
						var str="";
						flag = true;
						if(num==4){
							if(chancenum>0){
								$(".popbox_cj_fail").fadeIn();
							}else{
								$(".popbox_cj_fail_nochance").fadeIn();
							}
						}else{
							if(num==0){
								str = "麦当劳代金券";
								var popstr ="恭喜您，您已抽中"+str+"，为保证奖品发放顺利，请填写以下信息，我们将在活动结束后统一寄送奖品。";
								if(chancenum>0){
									$(".popbox_cj_success .pop_txt p").html(popstr);
									$(".popbox_cj_success").fadeIn();
									flag = true;
								}else{
									$(".popbox_cj_success_nochance .pop_txt p").html(popstr);
									$(".popbox_cj_success_nochance").fadeIn();
									flag = true;
								}
							}else{
								if(num==1){
									str = "物美超市电子券";
								}else if(num==2){
									str = "星巴克咖啡券";
								}else if(num==3){
									str = "好利来蛋糕券";
								}else{
									str = "时光网电影票";
								}
								$(".popup_zp_suc .popup_zp_suc_memo span").html(str);
								$(".popup_zp_suc").fadeIn();
							}
						}
					}
				});
				





				if(chancenum>0 && flag){
					chancenum--;
					flag = false;
					var chancenum_str = chancenum;
					$(".zp_memo .chancenum").html(chancenum_str);
					
				}



			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
			});
			
		})
	});
})()