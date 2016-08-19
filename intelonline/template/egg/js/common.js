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
//zero js 代码
$(function(){
	$(".loadingbox").hide(0);//隐藏开场加载动画效果
	
	$(".start").on("click",function(event){
		event.preventDefault();
		$(".pop_za").css('display','none');
		$(this).css('display','none')
		ga('send', 'event', title, '点击', "浮层消失");

		$Ajax({
			url:"/index.php/GameInfo/egg",
			type:"post",
			datatype:"json",
			success:function(data) {    
        		alert(data);
     		}    
		});



	})
	//浮层显示
	$(".close").on("click",function(event){
		event.preventDefault();
		$(".popbox").css('display','none');
		ga('send', 'event', title, '点击', "浮层消失");
	})
	$(".egg_close1").on("click",function(event){
		event.preventDefault();
		$(".popbox").css('display','none');
		ga('send', 'event', title, '点击', "浮层消失");
	})
	//浮层消失
	$(".attent").on('click',function(){
		$(".popbox").css('display','block');
		ga('send', 'event', title, '点击', "浮层出现");
	})
	$(".pop_attent").on('click',function(){
		$(".popbox").css('display','none');
		ga('send', 'event', title, '点击', "浮层消失");
	})
	 //检测代码
	 $(".index_lastA").click(function(){
	 	ga('send', 'event', title, '点击', "购买Edision");
	 })
	 $(".btn_buy").click(function(){
	 	ga('send', 'event', title, '点击', "购买Edision");
	 })
	 
	 $(".test_zero").click(function(){
	 	ga('send', 'event', title, '点击', "报名参加Edision挑战赛");
	 })
	 
	 $(".rules_lastA").click(function(){
	 	ga('send', 'event', title, '点击', "开始玩");
	 })
	 
	 $(".egg_play").click(function(){
	 	ga('send', 'event', title, '点击', "开始玩");
	 })
	 
	 $(".go_zero").click(function(){
	 	ga('send', 'event', title, '点击', "开始玩");
	 })
	 
	 
	 
	 
	 
	
	
})



