//添加新闻
function addDealerNews(){
	process=loading('Loading...');
	$.ajax({
//		url:"../html/newsManage/add_News.html",
		url:"/index.php/articleManage/addArticle",
		dataType:"html",
		//url: '/index.php/newsManage/add_News',		
		success: function (data){
			process.close();
			var d = art.dialog({
				title: '添加新闻内容',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//删除新闻
function deleteNews(obj,id){
	var box=$(obj).parents('tr');
	art.dialog({
		id: "deleteNews",
		content: '该新闻删除后将无法恢复，您确定要删除该新闻吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/articleManage/delArticle/aid/'+ id,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){
							box.remove();
							artInfo('删除成功');
						}else{
							artInfo('删除失败');
						}
					},
					error:function(data){
						process.close();
						artInfo('删除失败');
					},
					cache: false
				});
				return false;
			},
			focus: true
		},		
		{
			name: '取消'
		}
		],
		lock:true
	});	
}
//编辑新闻
function editingNews(obj,id){
	var editNews=art.dialog({
		title:'卡巴新闻内容修改',
		id:'editingNews',
		lock:true
	});
	// $.ajax({
	// 	url: '/index.php/news/edit_News/id/'+id,
	// 	success: function (data){
	// 		editProduct.content(data);
	// 	},
	// 	cache: false
	// });
	$.ajax({
//		url:"../html/newsManage/edit_news.html",
		url:"/index.php/articleManage/editArticle/aid/"+id,
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
//评论管理
function discussNews(obj,id){
	var discussNews=art.dialog({
		title:'卡巴新闻评论管理',
		id:'discussNews',
		lock:true
	});
	// $.ajax({
	// 	url: '/index.php/news/discuss_news/id/'+id,
	// 	success: function (data){
	// 		discussNews.content(data);
	// 	},
	// 	cache: false
	// });
	$.ajax({
            //url:"../html/newsManage/discuss_news.html",
		url:"/index.php/articleManage/getCommentList/aid/"+id,
		dataType:"html",
		success: function (data){
			discussNews.content(data);
		},
		cache: false
	});
}
//评论管理 - 通过
function discussNews_pass(obj,newsid){
	art.dialog({
		id: "stickNews",
		content: '将通过此条文章评论，您确定吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/articleManage/checkIt/passid/'+ newsid,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){
							$(obj).replaceWith("<span>已通过</span>");
							artInfo('操作成功');
						}else{
							artInfo('操作失败');
						}
					},
					error:function(data){
						process.close();
						/*套用程序时删除*/
						$(obj).replaceWith("<span>已通过</span>");
						/*套用程序时删除 end*/
						artInfo('操作失败');
					},
					cache: false
				});
				return false
			},
			focus: true
		},		
		{
			name: '取消'
		}
		],
		lock:true
	});
}
//评论管理 - 删除
function discussNews_del(obj,newsid){
	var box=$(obj).parent().parent();
	art.dialog({
		id: "stickNews",
		content: '将删除此条文章评论，您确定吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/articleManage/checkIt/delid/'+ newsid,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){
							box.remove();
							artInfo('操作成功');
						}else{
							artInfo('操作失败');
						}
					},
					error:function(data){
						process.close();
						/*套用程序时删除*/
						box.remove();
						/*套用程序时删除 end*/
						artInfo('操作失败');
					},
					cache: false
				});
				return false
			},
			focus: true
		},		
		{
			name: '取消'
		}
		],
		lock:true
	});
}
//新闻 置顶
function stickNews(obj,id){
        action_info = $(obj).val();
	art.dialog({
		id: "stickNews",
		content: '将该文章'+action_info+'，您确定吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/articleManage/lististop/aid/'+ id,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){
							var articleData =  $(obj).parents("tr").find("td");
							var number = articleData.eq(0).text();
							var $title = articleData.eq(2);
                                                        if(data.data == 2){
                                                            $title.replaceWith('<td><span class="artical_top">[置顶]</span>'+$title.text()+'</td>');
                                                            $(obj).replaceWith('<input type="button" class="global_top" onclick="stickNews(this,\''+id+'\')" value="取消置顶" /> ');
                                                        }else{
                                                        var $artical_top = $title.find(".artical_top");
                                                        $artical_top.remove();
							$(obj).replaceWith('<input type="button" class="global_top" value="置顶" onclick="stickNews(this,\''+id+'\')" /> ');
							
                                                        }
							artInfo(action_info+'成功');
						}else{
							artInfo(action_info+'失败');
						}
					},
					error:function(data){
						process.close();
						/*套用程序时删除*/
						var articleData =  $(obj).parents("tr").find("td");
						var number = articleData.eq(0).text();
						var $title = articleData.eq(1);
						$title.replaceWith('<td><span class="artical_top">[置顶]</span>'+$title.text()+'</td>');
						$(obj).replaceWith('<input type="button" class="global_top" onclick="unstickNews(this,\''+id+'\')" value="取消置顶" /> ');
						/*套用程序时删除 end*/
						artInfo(action_info+'失败');
					},
					cache: false
				});
				return false
			},
			focus: true
		},		
		{
			name: '取消'
		}
		],
		lock:true
	});
}
//新闻 置顶取消
function unstickNews(obj,id){
	art.dialog({
		id: "unstickNews",
		content: '将该文章置顶取消，您确定吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/articleManage/lististop/aid/'+ id,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){							
							var articleData =  $(obj).parents("tr").find("td");
							
							var number = articleData.eq(0).text();
							var $artical_top = articleData.eq(1).find(".artical_top");
							$artical_top.remove();
							$(obj).replaceWith('<input type="button" class="global_top" value="文章置顶" onclick="stickNews(this,\''+id+'\')" /> ');
							artInfo('取消置顶成功');
						}else{
							artInfo('取消置顶失败');
						}
					},
					error:function(data){
						process.close();
						/*套用程序时删除*/
						var articleData =  $(obj).parents("tr").find("td");
						
						var number = articleData.eq(0).text();
						var $artical_top = articleData.eq(1).find(".artical_top");
						$artical_top.remove();
						$(obj).replaceWith('<input type="button" class="global_top" value="文章置顶" onclick="stickNews(this,\''+id+'\')" /> ');
						/*套用程序时删除 end*/
						artInfo('取消置顶失败');
					},
					cache: false
				});
				return false
			},
			focus: true
		},		
		{
			name: '取消'
		}
		],
		lock:true
	});
}
