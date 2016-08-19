var myEmail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z]{2,4})+$/;
/*artDialog 弹窗信息*/
function artInfo(info){
	art.dialog({
		content:info,
		lock:true,
		fixed:true
	});
}
//选项卡内容加载
function tabsChange(obj,wraper,url){
	switchTabs(obj);
	if($(".searchTab").size() > 0){
		$(".searchTab").addClass("visibility");	
	}
	var load = loading("Loading……");
	$.ajax({
		url:url,
		dataType:"html",
		success:function(data){
			load.close();
			wraper.html(data);
		},
		cache:false
	});
}
//选项卡切换
function switchTabs(obj){
	$(obj).addClass('currentTab');
	$(obj).siblings().removeClass('currentTab');
}
/* 登录验证 */
function loginCheck(obj){
	var $obj = $(obj);
	var username = $.trim($obj.find('[name=username]').val());
	var password = $.trim($obj.find('[name=password]').val());
	if(username == "" || password == ""){
		artInfo("请输入用户名或密码");
		return false;
	}
	
	return true;	
}
 //loading 窗口
 function loading(ttl){
 	var foo={}
 	var loading=art.dialog({
 		title:ttl,
 		content:'<div class="aui_loading">Loading</div>',
 		id:'process',
 		lock:true
 	});
 	foo.close=function(){
 		loading.close();
 	}
 	return foo;
 }
 //产品图片预览
function productUploadPic(f,w,h) {
	var file = $(f).val();
	var hide=$('#product_img');
	var upId=$(f).attr('id');
	var img=$('#preview');
	//转换文件名为小写
	file = file.toLowerCase();
	//检测文件类型必须是图片
	if (!/.(gif|jpg|jpeg|png)$/.test(file)) {
		alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
	} else {
		$.ajaxFileUpload({
			url: '/index.php/common/upload/w/'+w+'/h/'+h+'/aim/all', //服务器端程序
			secureuri: false,
			fileElementId: upId, //input框的ID
			dataType: 'json', //返回数据类型
			success: function(data) {//上传成功
				console.log(data);
				if(data.status){
                                    var fileData = data.data;
                                    if(fileData.orginal){
                                        hide.val(fileData.thumbnail);
                                        $("#original_pic").val(fileData.orginal);
					img.html('<img id="imghead" src="'+fileData.thumbnail+'" />');
                                    }else{
                                        hide.val(data.data);
					img.html('<img id="imghead" src="'+data.data+'" />');
                                    }
				}
			}
		});

	}
}
/*订单管理*/
function showingOrder(obj,id){
	var showProduct=art.dialog({
		title:'订单详情',
		id:'showProduct',
		ok:function(){},
		lock:true
	});
	$.ajax({
//		url:"../html/orderManage/order_detail.html",
		url:"/index.php/orderManage/orderInfo/oid/"+id,
		dataType:"html",
		success: function (data){
			showProduct.content(data);
		},
		cache: false
	});
}
/*演示脚本 套用模板时需要删除*/
//jQuery(function($){
//	$.ajax({
//		url:"../html/public/header.html",
//		dataType:"html",
//		success:function(data){
//			$("#header").html(data);
//		}
//	});
//	$.ajax({
//		url:"../html/public/left_menu.html",
//		dataType:"html",
//		success:function(data){
//			$(data).appendTo(".container");
//		}
//	});
//	$.ajax({
//		url:"../html/public/footer.html",
//		dataType:"html",
//		success:function(data){
//			$(data).appendTo("body");
//			if($(".login_area").size() > 0){
//				$(".footer").addClass("footer_login");
//			}
//		}
//	});
//});

function fixHeight(){
		console.log($(".main").height());
	}

