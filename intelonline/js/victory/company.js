//添加企业
function addCompany(){
	var name = $('input[name=name]').val();
	$.ajax({
		url:"/index.php/company/addCompany_do",
		dataType:"json",
		type:'post',
		data:{name:name},
		success: function (data){
			alert(data.info);
			if(data.status){
				window.location.href ="/index.php/company/index";
			}
		},
	});
}
//删除企业
function del_company(id){
	$.ajax({
		url:"/index.php/company/del_company",
		dataType:"json",
		type:'post',
		data:{id:id},
		success: function (data){
			alert(data.info);
			if(data.status){
				window.location.href ="/index.php/company/index";
			}
		},
	});
}
//编辑企业
function editingCompany(obj,id){
	var editNews=art.dialog({
		title:'企业修改',
		id:'editingNews',
		lock:true
	});	
	$.ajax({
		url:"/index.php/articleManage/editCompany/id/"+id,
		dataType:"html",
		success:function(data){
			editNews.content(data);
		}
	});
}