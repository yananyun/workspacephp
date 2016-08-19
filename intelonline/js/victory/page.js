function save_page(id){
	var name = $.trim($('input[name=name]').val());
	var dynamic = $('input[name=dynamic]:checked').val();
	var content = editor.getContent();
	$.ajax({
		url:'/index.php/page/save_page',
		data:{id:id,name:name,content:content,dynamic:dynamic},
		dataType:'json',
		type:'post',
		success:function(data){
			alert(data.info);
			if(data.status){
				location.href = '/index.php/page/index';
			}
		}
	});
}