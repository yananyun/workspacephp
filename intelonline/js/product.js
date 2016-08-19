//添加产品
function addProduct(){
	process=loading('Loading...');
	$.ajax({
//		url:"../html/productManage/add_product.html",
		url:"/index.php/productManage/addProduct",
		dataType:"html",
		success: function (data){
			process.close();
			var d = art.dialog({
				title: '添加产品',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
/*
*修改的
*/
//删除产品
function deleteProduct(obj,id){
	var box=$(obj).parents('tr');
	art.dialog({
		id: "delProduct",
		content: '该产品删除后将无法恢复，您确定要删除该产品吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/productManage/delProduct',
					dataType:'json',
					type:'POST',
                                        data:{'pid':id},
					success: function (data){
						process.close();
						if(data.status){
//							box.remove();
							artInfo('删除成功');
                                                        location.reload();
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
// 编辑产品时 图片预览
function productEditPic(f) {
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
			url: '/index.php/product/upload', //服务器端程序
			secureuri: false,
			fileElementId: upId, //input框的ID
			dataType: 'json', //返回数据类型
			success: function(data) {//上传成功
				if(data.status){
					hide.val(data.data);
					//previewImage(f);
					img.html('<img id="imghead" src="'+data.data+'" />');
				}
			}
		});
	}
}
//编辑产品
function editingProduct(obj,id){
	var editProduct=art.dialog({
		title:'产品修改',
		id:'editProduct',
		lock:true
	});
	// $.ajax({
	// 	url: '/index.php/product/edit_product/id/'+id,
	// 	success: function (data){
	// 		editProduct.content(data);
	// 	},
	// 	cache: false
	// });
	$.ajax({
		url:"/index.php/productManage/editProduct/pid/"+id,
		dataType:"html",
		success:function(data){
			editProduct.content(data);
		}
	});
}
//显示产品详情
function showingProduct(obj,id){
	var showProduct=art.dialog({
		title:'产品详情',
		id:'showProduct',
		ok:function(){},
		lock:true
	});
	$.ajax({
//		url:"../html/productManage/product_detail.html",
		url:"/index.php/productManage/productInfo/pid/"+id,
		dataType:"html",
		success: function (data){
			showProduct.content(data);
		},
		cache: false
	});
}
