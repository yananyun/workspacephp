// 1星巴克咖啡券 2时光网电影券 3京东券 4携程网旅游券 5智能手环 0谢谢参与
$(".pointbox").on("click",function(){
	var title = document.title;
	ga('send', 'event', title, '点击', '大转盘开始');
	if($(".pointbox").hasClass('active') === false){
		$(".pointbox").addClass('active');
		$.ajax({
			url: '/index.php/edison/getLottery',
			type: 'post',
			dataType: 'json'
		})
		.done(function(data) {
			console.log(data);
			// console.log(data[data][count]));
			if(data['status'] == false ){
				//没有机会
				$(".p2").show();
				$(".p2").find(".popup_zp_suc_memo").text(data['info']);
				$(".pointbox").removeClass('active');
			}else{
				console.log(data);
				var icount = data.data.count;
				$(".tips").find("span").text(icount);

				// 没有抽中
				var a;
				if(data['data']["data"] == 0){
					a  = 90;
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
					if(data['data'] ["data"]== 1){
						a = 210;
					}else if(data['data'] ["data"]== 2){
						a = 330;
					}else if(data['data'] ["data"]== 3){
						a = 30;
					}else if(data['data'] ["data"]== 4){
						a = 150;
					}else if(data['data'] ["data"]== 5){
						a = 270
					}
					var aPrize = ['星巴克咖啡券','时光网电影券','京东券','携程网旅游券','智能手环'];
					var prize = aPrize[data['data']['data']-1];
					$(".pointbox").rotate({
						duration:3000,
						angle: 0, 
						animateTo:1440+a,
						easing: $.easing.easeOutSine,
						callback: function(){
							if(data['data'] ["data"]== 2){
								$(".p4").show();
							}else{
								$(".p1").find(".popup_zp_suc_memo").find("span").text(prize);
								$(".p1").show();
							}
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
	ga('send', 'event', "抽中奖品", '点击', '确定按钮');
	wx.closeWindow();
})


$(".zp_suc_frm_subbtn2").on("click",function(){
	ga('send', 'event', "没有中奖弹窗", '点击', '确定按钮');
	$(".p2").hide();
})



$(".zp_suc_frm_subbtn3").on("click",function(){
	ga('send', 'event', "没有抽奖机会", '点击', '确定按钮');
	$(".p3 ").hide();
})

$(".zp_suc_frm_subbtn4").on("click",function(){
	// alert(1);
	ga('send', 'event', "抽中奖品", '点击', '确定按钮');
	$(".p4 ").hide();
	wx.closeWindow();
})




$(".popbox_alert_pop .pop_ok").click(function () {
	$(".popbox_alert_pop").fadeOut();
});



function alert_pop(str) {
	$(".popbox_alert_pop .pop_txt").html(str);
	$(".popbox_alert_pop").fadeIn();
}

$(".btn-detail").on("click",function(){
	var title = document.title;
	ga('send', 'event', "没有中奖弹窗", '点击', '活动细则');
})


