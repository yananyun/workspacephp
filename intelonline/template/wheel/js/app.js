// 1星巴克咖啡券 2时光网电影券 3京东券 4携程网旅游券 5智能手环 0谢谢参与



$(".pointbox").on("click",function(){
	if($(".pointbox").hasClass('active') === false){
		$(".pointbox").addClass('active');
		$.ajax({
			url: '/index.php/edison/getLottery',
			type: 'post',
			dataType: 'JSON',
		})
		.done(function(data) {
			console.log(data);
			if(data['status'] == false){
				$(".p4").show();
				$(".p4").find(".popup_zp_suc_memo").text(data['info']);
			}else{
				if(data['data'] == 0){
					var a = 90;
					$(".pointbox").rotate({
						duration:3000,
						angle: 0, 
						animateTo:1440+a,
						easing: $.easing.easeOutSine,
						callback: function(){
							$(".p2").find(".popup_zp_suc_memo").text(data['info']);
							$(".p2").show();
							$(".pointbox").removeClass('active');
						}
					});
				}else{
					var a;
					if(data['data'] == 1){
						// a = randNum(180,240)
						a = 210;
					}else if(data['data'] == 2){
						// a = randNum(300,360);
						a = 330;
					}else if(data['data'] == 3){
						// a = randNum(0,60);
						a = 30;
					}else if(data['data'] == 4){
						// a = randNum(120,180);
						a = 150;
					}else if(data['data'] == 5){
						// a = randNum(240,300);
						a = 270
					}
					var aPrize = ['星巴克咖啡券','时光网电影券','京东券','携程网旅游券','智能手环'];
					var prize = aPrize[data['data']-1];
					$(".pointbox").rotate({
						duration:3000,
						angle: 0, 
						animateTo:1440+a,
						easing: $.easing.easeOutSine,
						callback: function(){
							$(".p1").find(".popup_zp_suc_memo").find("span").html(prize);
							$(".p1").show();
							$(".pointbox").removeClass('active');
						}
					});


				}
				
			}
		})

	}
})


function randNum(n1,n2){
	return Math.floor(Math.random() * (n2-n1) + n1); 
}

$(".zp_suc_frm_subbtn").on("click",function(){
	var name = $("input[name = 'name']").val();
	var address = $("input[name = 'address']").val();
	var mobile = $("input[name = 'mobile']").val();
	if(name=="" ){
		alert_pop("请填写姓名");
		return false;
	}
	if(!(/^1\d{10}$/.test(mobile))){
		alert_pop("请正确填写您的手机号");
		return false;
	}
	if(address==""){
		alert_pop("请填写地址");
		return false;
	}
	$.ajax({
		url: '/index.php/edison/upAddress',
		type: 'POST',
		data: {name: name,address:address,mobile:mobile},
	})
	$("input[name = 'name']").val("");
	$("input[name = 'address']").val("");
	$("input[name = 'mobile']").val("");
	$(".p1").hide();
})


$(".zp_suc_frm_subbtn2").on("click",function(){
	// $(".zpbox").find("a").css("webkit",)
	$(".p2").hide();
})



$(".popbox_alert_pop .pop_ok").click(function () {
	$(".popbox_alert_pop").fadeOut();
});



function alert_pop(str) {
	$(".popbox_alert_pop .pop_txt").html(str);
	$(".popbox_alert_pop").fadeIn();
}





// var a = randNum(2,52);
// console.log()

// $(".pointbox").rotate({
// 	duration:3000,
// 	angle: 0, 
// 	animateTo:1440+120,
// 	easing: $.easing.easeOutSine,
// 	callback: function(){
// 		var num = (a-30)/60;
// 		var num=3;
// 		var str="";
// 		flag = true;
// 		// if(num==4){
// 		// 	if(chancenum>0){
// 		// 		$(".popbox_cj_fail").fadeIn();
// 		// 	}else{
// 		// 		$(".popbox_cj_fail_nochance").fadeIn();
// 		// 	}
// 		// }else{
// 		// 	if(num==0){
// 		// 		str = "麦当劳代金券";
// 		// 		var popstr ="恭喜您，您已抽中"+str+"，为保证奖品发放顺利，请填写以下信息，我们将在活动结束后统一寄送奖品。";
// 		// 		if(chancenum>0){
// 		// 			$(".popbox_cj_success .pop_txt p").html(popstr);
// 		// 			$(".popbox_cj_success").fadeIn();
// 		// 			flag = true;
// 		// 		}else{
// 		// 			$(".popbox_cj_success_nochance .pop_txt p").html(popstr);
// 		// 			$(".popbox_cj_success_nochance").fadeIn();
// 		// 			flag = true;
// 		// 		}
// 		// 	}else{
// 		// 		if(num==1){
// 		// 			str = "物美超市电子券";
// 		// 		}else if(num==2){
// 		// 			str = "星巴克咖啡券";
// 		// 		}else if(num==3){
// 		// 			str = "好利来蛋糕券";
// 		// 		}else{
// 		// 			str = "时光网电影票";
// 		// 		}
// 		// 		$(".popup_zp_suc .popup_zp_suc_memo span").html(str);
// 		// 		$(".popup_zp_suc").fadeIn();
// 		// 	}
// 		// }
// 	}
// });

