//删除经销商
function deleteDealer(obj,id,openid){
	var box=$(obj).parents('tr');
	if(openid == '0')
	{
		var url = '/index.php/dealer/deleteDealerWRZ';
	}else{
		var url = '/index.php/dealer/deleteDealer';
	}
	art.dialog({
		id: "deleteDealer",
		content: '该经销商信息删除后将无法恢复，您确定要删除吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: url+'/id/'+ id+'/openid/'+openid,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){
							artInfo('删除成功');
							window.location.reload();
							box.remove();
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

//显示经销商详情
function showingDealer(obj,id){
	var showingDealer=art.dialog({
		title:'经销商详情',
		id:'showingDealer',
		ok:function(){},
		lock:true
	});
	
	$.ajax({
		url:"../html/certificateManage/certificate_detail.html",
		dataType:"html",
		success: function (data){
			showingDealer.content(data);
			changeDealer("#first",$(".dealerContainer"),"dealer_info");
		},
		cache: false
	});
}
//经销商详情切换
function changeDealer(obj,box,tabName){
	var id = $('#openid').val();
	var uid = $('#id').val();
	$(obj).addClass('currentTab').siblings('a').removeClass('currentTab');
	if(tabName == 'dealer_info')
	{
		$.ajax({
			//url: '/index.php/member/giftList/id/'+id,
			url:"../html/certificateManage/dealer_info.html",
			dataType:"html",
			success: function (data){
				box.html(data)
			},
			cache: false
		});
	}
	if(tabName == 'gift_info')
	{
		$.ajax({
			//url: '/index.php/member/giftList/id/'+uid,
			url:"../html/certificateManage/gift_info.html",
			dataType:"html",
			success: function (data){
				box.html(data)
			},
			cache: false
		});
	}
}
//导入经销商
function certificateImport(){
	process=loading('Loading...');
	$.ajax({
		url:"/index.php/dealer/getExcel",
//		url:"../html/certificateManage/certificate_import.html",
		dataType:"html",
		//url: '/index.php/product/add_product',		
		success: function (data){
			process.close();
			var d = art.dialog({
				title: '导入经销商',
				content:data,
				id:"certificateImport",
				lock:true
			});
		},
		cache: false
	});
}
