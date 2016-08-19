//添加企业
function addCompany(){
	var name = $('input[name=name]').val();
	if(name == ''){
		alert('企业名称不能为空');
		return false;
	}
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

//编辑企业
function editCompany(id){
	var name = $('input[name=name]').val();
	if(name == ''){
		alert('企业名称不能为空');
		return false;
	}
	$.ajax({
		url:"/index.php/company/editCompany",
		dataType:"json",
		type:'post',
		data:{name:name,id:id},
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
	var del = confirm('确定要删除吗？');
	if(!del){
		return false;
	}
	$.ajax({
		url:"/index.php/company/del_company",
		dataType:"json",
		type:'post',
		data:{id:id},
		success: function (data){
			//alert(data.info);
			if(data.status){
				window.location.href ="/index.php/company/index";
			}
		},
	});
}
//搜索

function search(){
	var name = $('input[name=name]').val();
	if(name == ''){
		alert('请输入搜索内容');
		return false;
	}
	loadContent('',$('.intelListWrap'),'/index.php/company/lists/keyword/'+name);
}