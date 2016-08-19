
//消息搜索
function msgSearch(obj,wraper){
	switchTabs(obj);

	var searchTxt = $("#msgSearchInput").val();
	if(searchTxt == null || searchTxt ==""){
		artInfo("请输入搜索内容!");
		return false;
	}
	$.ajax({
		url:'/index.php/message/msglist?keyword='+searchTxt+'&',
//		data:{keyword:searchTxt},
		dataType:"html",
		success:function(data){
			wraper.html(data);
			$(obj).removeClass("visibility");
			$(obj).html("搜索结果");
		},
		cache:false
	});
}
//收藏消息
function message_collect(obj,id,aimstatus){
	var $obj = $(obj);
	var status = $obj.attr("data-status");
	$.ajax({
		url:"/index.php/message/makeStar/id/"+id,
		dataType:"html",
		success: function (data){
			if(data == 1)
			{
				artInfo(status == 1 ? "取消收藏成功!" : "收藏消息成功!");
				status == 1 ? ($obj.removeClass("star_orange").addClass("star_gray"), $obj.attr("data-status", 2)) : ($obj.removeClass("star_gray").addClass("star_orange"), $obj.attr("data-status", 1)), $obj.attr("title", status == 1 ? "收藏消息" : "取消收藏");		
			}else{
				artInfo(status == 1 ? "取消收藏失败!" : "收藏消息失败!");
			}	
		},
		error:function(){
			artInfo(status == 1 ? "取消收藏失败!" : "收藏消息失败!");
		},
		cache: false
	});	
}
//消息回复--显示回复框
function message_reply_show(obj){
	$(obj).parents(".message_item").addClass("replying");
}
//消息回复 -- 收起回复框
function message_pickup(obj){
	$(obj).parents(".message_item").removeClass("replying");
}
//消息回复 -- 消息发送
function message_send(obj,id,openid){
	var $message = $(obj).parents(".js_quick_reply_box").find(".reply_area");
	var message = $message.val();
	if(message == null || message ==""){
		artInfo("回复内容不能为空!");
		return false;
	}
	$.ajax({
		url:"/index.php/message/reply",
		type:'post',
		data:{id:id,content:message,openid:openid},
		success: function (data){
			if(data == 1)
			{
				$message.val("");
				$(obj).parents(".message_item").addClass("replyed");
				artInfo("发送成功");
				window.location.reload();
			}else{
				artInfo("发送失败");
			}
		},
		error:function(){
		},
		cache: false
	});
}
//显示相对于某个客户的往返消息列表 
function guestMessageList(obj,wraper,url){
	switchTabs(obj);
	$.ajax({
		url:url,
		dataType:"html",
		success:function(data){
			wraper.html(data);
			$(obj).removeClass("visibility");
			$(obj).html("消息记录");
		},
		cache:false
	});
}