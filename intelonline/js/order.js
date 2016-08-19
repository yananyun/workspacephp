//编辑预订单
function editingOrders(obj,id){
	var editNews=art.dialog({
		title:'预定信息审核',
		id:'editingOrders',
		lock:true
	});
	$.ajax({
//		url:"../html/newsManage/edit_news.html",
		url:"/index.php/orderManage/editOrderCompany/oid/"+id,
		dataType:"html",
		success:function(data){
			editNews.content(data);
		}
	});
}
//显示新闻详情
function showingNews(obj,id){
	var showingNews=art.dialog({
		title:'新闻详情',
		id:'showDealerNews',
		ok:function(){},
		lock:true
	});
	$.ajax({
//		url:"../html/newsManage/news_detail.html",
		url:"/index.php/articleManage/articleInfo/aid/"+id,
		dataType:"html",
		success: function (data){
			showingNews.content(data);
		},
		cache: false
	});
}
