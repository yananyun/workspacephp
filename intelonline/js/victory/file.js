
//添加文件
function add_file(){
	var cid = $('select[name=cid]').val();
	var year = $('select[name=year]').val();
	var month = $('select[name=month]').val();
	var type = $('select[name=type]').val();
	var title = $('input[name=title]').val();
	var filepath = $('#upfile').attr('val');
	$.ajax({
		url:"/index.php/file/save_file",
		dataType:"json",
		type:'post',
		data:{cid:cid,year:year,month:month,type:type,title:title,filepath:filepath},
		success: function (data){
			alert(data.info);
			if(data.status){
				window.location.href ="/index.php/file/add";
			}
		},
	});
}

//文件搜索

function search_file(){
	var type = $('select[name=type]').val();
	var year = $('select[name=year]').val();
	var month = $('select[name=month]').val();
	var cid = $('select[name=cid]').val();
	loadContent('',$('.intelListWrap'),'/index.php/file/lists/type/'+type+'/year/'+year+'/month/'+month+'/cid/'+cid);
}


function uploadFile(f) {
	var obj = $(f);
    var file = obj.val();   
    var upId=obj.attr('id');
    //转换文件名为小写
    file = file.toLowerCase();
    //检测文件类型必须是图片
    if (!/.(csv)$/.test(file)) {
        alert("文件类型必须是.csv");
    } else {
        $.ajaxFileUpload({
            url: '/index.php/common/uploadFile', //服务器端程序
            secureuri: false,
            fileElementId: upId, //input框的ID
            dataType: 'json', //返回数据类型
            success: function(data) {//上传成功            	
                if(data.status){
                	$('#upfile').attr("val",data.data);
                }
            }
        });
    }
}

//删除file

function del_file(id){
	var del = confirm('确定要删除吗？');
	if(!del){
		return false;
	}
	$.ajax({
		url:"/index.php/file/del_file",
		dataType:"json",
		type:'post',
		data:{id:id},
		success: function (data){
			//alert(data.info);
			if(data.status){
				window.location.href ="/index.php/file/index";
			}
		},
	});
}




//修改文件


function edit_file(id){
	var cid = $('select[name=cid]').val();
	var year = $('select[name=year]').val();
	var month = $('select[name=month]').val();
	var type = $('select[name=type]').val();
	var title = $('input[name=title]').val();
	var filepath = $('#upfile').attr('val');
	var ofilepath = $('input[name=ofilepath]').val();
	$.ajax({
		url:"/index.php/file/edit_file",
		dataType:"json",
		type:'post',
		data:{id:id,cid:cid,year:year,month:month,type:type,title:title,filepath:filepath,ofilepath:ofilepath},
		success: function (data){
			alert(data.info);
			if(data.status){
				window.location.href ="/index.php/file/index";
			}
		},
	});
}

