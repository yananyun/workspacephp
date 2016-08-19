//礼品添加
function addGift(){
	process=loading('Loading...');
	$.ajax({
		url:"/index.php/gift/add",
		dataType:"html",
		success: function (data){
			process.close();
			var d = art.dialog({
				title: '添加礼品',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//礼品补货
function giftReplenishment(){
	process=loading('Loading...');
	$.ajax({
		url:"/index.php/gift/plus",
		dataType:"html",
		success: function (data){
			process.close();
			var d = art.dialog({
				title: '礼品补货',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//删除礼品
function deleteGift(obj,id){
	var box=$(obj).parents('tr');
	art.dialog({
		id: "deleteGift",
		content: '该礼品信息删除后将无法恢复，您确定要删除吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/gift/deleteGift/id/'+ id,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){
							box.remove();
							artInfo('删除成功');
							window.location.reload();
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

//显示礼品详情
function showingGift(obj,id){
	var showingDealer=art.dialog({
		title:'礼品详情',
		id:'showingDealer',
		ok:function(){},
		lock:true
	});
	
	$.ajax({
		url:"/index.php/gift/detail/id/"+id,
		dataType:"html",
		success: function (data){
			showingDealer.content(data);
			changeGift("#first",$(".dealerContainer"),"receive",id);
		},
		cache: false
	});
}
//礼品详情切换
function changeGift(obj,box,tabName,id,title,price){
	$(obj).addClass('currentTab').siblings('a').removeClass('currentTab');
	
	//领取详情
	if(tabName == 'receive')
	{
		$.ajax({
//			url:"../html/giftManage/gift_receive.html",
			url:"/index.php/gift/log/gid/"+id,
			dataType:"html",
			success: function (data){
				box.html(data)
			},
			cache: false
		});
	}
	//补货详情
	if(tabName == 'replenishment')
	{
		$.ajax({
			url: '/index.php/gift/plusLog/id/'+id,
			dataType:"html",
			type:"post",
			data:{title:title,price:price},
			success: function (data){
				box.html(data)
			},
			cache: false
		});
	}
}

function uploadGiftImg(f,w,h,type) {
	
	var $ = jQuery;
	var file = $(f).val();	
	var upId=$(f).attr('id');
	var img= $('#preview');
	//转换文件名为小写
	filestr = file.toLowerCase();

	if(filestr == ''){
		return false;
	}
	//检测文件类型必须是图片
	if (!/.(gif|jpg|jpeg|png)$/.test(filestr)) {
		alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
	} else {
		$.ajaxFileUpload({
			//url: '/index.php/common/upload/w/'+w+'/h/'+h, //服务器端程序
			url:'/index.php/common/upload/w/'+w+'/h/'+h+'/aim/'+ type,
			secureuri: false,
			fileElementId: upId, //input框的ID
			dataType: 'json', //返回数据类型
			success: function(data) {//上传成功
				if(data.status){		
					$('#product_img').val(data.data);					
					if(typeof(img) == 'object'){
						$("#preview").find("img").attr('src',data.data);
					}
				}
			}
		});
	}
}