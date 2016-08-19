//添加经销商阅读内容
function addDealerNews(){
	process=loading('Loading...');
	$.ajax({
		url:"/index.php/readManage/addArticle",
		dataType:"html",
		success: function (data){
			process.close();
			var d = art.dialog({
				title: '添加经销商阅读内容',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//删除经销商阅读内容
function deleteDealerNew(obj,id){
	var box=$(obj).parents('tr');
	art.dialog({
		id: "deleteDealerNew",
		content: '该产品删除后将无法恢复，您确定要删除该产品吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/readManage/delArticle/aid/'+ id,
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
//编辑经销商阅读内容
function editingDealerNew(obj,id){
	var editProduct=art.dialog({
		title:'经销商阅读内容修改',
		id:'editDealerNew',
		lock:true
	});
	// $.ajax({
	// 	url: '/index.php/dealerNew/edit_DealerNew/id/'+id,
	// 	success: function (data){
	// 		editProduct.content(data);
	// 	},
	// 	cache: false
	// });
	$.ajax({
		url:"/index.php/readManage/editArticle/aid/"+id,
		dataType:"html",
		success:function(data){
			editProduct.content(data);
		}
	});
}
//显示经销商阅读内容详情
function showingDealerNew(obj,id){
	var showDealerNews=art.dialog({
		title:'经销商阅读内容详情',
		id:'showDealerNews',
		ok:function(){},
		lock:true
	});
	$.ajax({
		url:"/index.php/readManage/articleInfo/aid/"+id,
		dataType:"html",
		success: function (data){
			showDealerNews.content(data);
		},
		cache: false
	});
}
//经销商阅读内容 置顶
function stickDealerNew(obj,id){
	art.dialog({
		id: "stickDealerNew",
		content: '将该文章置顶，您确定吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/readManage/lististop/aid/'+ id,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){
							var articleData =  $(obj).parents("tr").find("td");							
							var number = articleData.eq(0).text();
							var $title = articleData.eq(1);
							$title.replaceWith('<td><span class="artical_top">[置顶]</span>'+$title.text()+'</td>');
							$(obj).replaceWith('<input type="button" class="global_top"  onclick="unstickDealerNew(this,\''+id+'\')" value="置顶取消" /> ');
							artInfo('置顶成功');
                                                        window.location.reload();
						}else{
							artInfo('置顶失败');
						}
					},
					error:function(data){
						process.close();
						/*套用程序时删除*/
						var articleData =  $(obj).parents("tr").find("td");
						var number = articleData.eq(0).text();
						var $title = articleData.eq(1);
						$title.replaceWith('<td><span class="artical_top">[置顶]</span>'+$title.text()+'</td>');
						$(obj).replaceWith('<input type="button" class="global_top" onclick="unstickDealerNew(this,\''+id+'\')" value="置顶取消" /> ');
						/*套用程序时删除 end*/
						artInfo('置顶失败');
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
//经销商阅读内容 置顶取消
function unstickDealerNew(obj,id){
	art.dialog({
		id: "unstickDealerNew",
		content: '将该文章置顶取消，您确定吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/readManage/lististop/aid/'+ id,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){
							var articleData =  $(obj).parents("tr").find("td");
							var number = articleData.eq(0).text();
							var $artical_top = articleData.eq(1).find(".artical_top");
							$artical_top.remove();
//							$for_top.replaceWith('<input type="button" class="global_top" value="文章置顶" onclick="stickDealerNew(this,\''+id+'\')" /> ');
							artInfo('取消置顶成功');
                                                        window.location.reload();
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
						$(obj).replaceWith('<input type="button" class="global_top"  value="文章置顶" onclick="stickDealerNew(this,\''+id+'\')" /> ');
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
