$(function(){
	fnAutoFill();
});
function fnMaterialPicUpload(self,dBox,onBox,preview){
	dBox.show();
	var valCol=onBox.find('[name=uploadPath]');
	var onPreview=onBox.find('.preViewImg');
	preview.html('<h5>上传中...</h5>');
	var file = $(self).val();
	var width=$(self).attr('imgWidth');
	var height=$(self).attr('imgHeight');
	var upId=$(self).attr('id');
	file = file.toLowerCase();
	if (!/.(gif|jpg|jpeg|png)$/.test(file)){
		alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
	}else{
		$.ajaxFileUpload({
			url: '/index.php/common/upload/w/'+width+'/h/'+height, //服务器端程序
			secureuri: false,
			fileElementId: upId,
			dataType: 'json',
			success: function(data){
				if(data.status){
					valCol.val(data.data);
					preview.html('<img src="'+data.data+'" />');
					onPreview.html('<img src="'+data.data+'" />');
				}
			}
		});
	}
}


	
	
function fnPicMsgUpload(self,loading){
	var file = $(self).val();
	var width=$(self).attr('imgWidth');
	var height=$(self).attr('imgHeight');
	var upId=$(self).attr('id');
	file = file.toLowerCase();
	if (!/.(gif|jpg|jpeg|png)$/.test(file)){
		alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
	}else{
		loading.show();
		$.ajaxFileUpload({
			url: '/index.php/material/create_pic_msg/w/'+width+'/h/'+height, //服务器端程序
			secureuri: false,
			fileElementId: upId,
			dataType: 'json',
			success: function(data){
				if(data.status){
					window.location=window.location;
				}
			}
		});
	}
}

function fnDelUpload(valBox,pBox,dBox){
	var preViewImg=$('.editOn').find('.preViewImg');
	preViewImg.html('<h1>封面图片</h1>');
	valBox.val('');
	pBox.html('<h5>上传中...</h5>');
	dBox.hide();
}

function fnAutoFill(){
	var meTitle=$('[name=meTitle]');
	var meAuthor=$('[name=meAuthor]');
	var meCoverFlag=$('[name=meCoverFlag]');
	var meComment=$('[name=meComment]');
	var meUrl=$('[name=meUrl]');
	var meArticle=$('#materialArticle');
	var meLink=$('[name=meLink]');
	window.onmouseup=window.onkeyup=function(){
		if(meTitle.size()==1){
			var meTtlDis=$('.editOn').find('.materialPicTxtTtl');
			var sTitle=$('.editOn').find('[name=sTitle]');
			var ttl=trim(meTitle.val());
			/*if(ttl==''){
				ttl='标题';
			}
			meTtlDis.html(ttl);
			sTitle.val(ttl);
			*/
			
			if(ttl==''){
				meTtlDis.html('标题');
				sTitle.val('');
			}else{
				sTitle.val(ttl);
				meTtlDis.html(ttl);
			}
			
		}
		if(meAuthor.size()==1){
			var sAuthor=$('.editOn').find('[name=sAuthor]');
			var author=trim(meAuthor.val());
			sAuthor.val(author);
		}
		if(meCoverFlag.size()==1){
			var cFlag=$('.editOn').find('[name=coverFlag]');
			var flag=meCoverFlag.is(':checked');
			if(flag){
				cFlag.val(1);
			}else{
				cFlag.val(0);
			}
		}
		if(meComment.size()==1){
			var meCommentDis=$('.editOn').find('.materialPicTxtComment');
			var sComment=$('.editOn').find('[name=sComment]');
			var cVal=trim(meComment.val());
			meCommentDis.html(cVal);
			sComment.val(cVal);
		}
		if(meUrl.size()==1){
			var sUrl=$('.editOn').find('[name=sUrl]');
			var url=trim(meUrl.val());
			sUrl.val(url);
		}
		if(meArticle.size()==1){
			var sArticle=$('.editOn').find('.sArticle');
			var article = editor.getContent();
			sArticle.html(article);
		}
		if(meLink.size()==1){
			var sLink=$('.editOn').find('[name=sLink]');
			var link=trim(meLink.val());
			sLink.val(link);
		}
	}
}

function fnAppendCol(box){
	var col='\
	<div class="pic_txt_col">\
		<p class="materialPicTxtTtl">标题</p>\
		<div class="materialThumb"><div class="preViewImg"><h1>封面图片</h1></div></div>\
		<div class="editBase"><p><a href="javascript:void(0);" onclick="fnEditCurrentCol(this)">编辑</a><a href="javascript:void(0);" onclick="fnDelCurrentCol(this)">删除</a></p></div>\
		<input type="hidden" name="sTitle" /><input type="hidden" name="sAuthor" /><input type="hidden" name="uploadPath" /><input type="hidden" name="coverFlag" value="0" /><input type="hidden" name="sUrl" /><input type="hidden" name="sArticle" /><div class="sArticle" style="display:none;"></div><input type="hidden" name="sLink" />\
	</div>';
	var foo=box.find('.pic_txt_col').size();
	if(foo<7){
		box.append(col);
	}else{
		alert('你最多只能添加8条图文消息');
	}
}

function fnDelCurrentCol(self){
	var foo=confirm('确定删除么');
	if(foo){
		var editForm=$('#editForm').get(0);
		editForm.reset();
		var pBox=$(self).parents('.pic_txt_col');
		pBox.remove();
	}
}

function fnEditCurrentCol(self,order){
	var editForm=$('#editForm').get(0);
	editForm.reset();
	
	var preViewBox=$('.preViewBox');
	var uploadView=$('.uploadView');
	uploadView.html('<h5>上传中...</h5>');
	var editWrap=$('.materialEditWrap');
	var pBox=$(self).parents('.pic_txt_col');
	var mt=0;
	var up_picture=$('#up_picture');
	if(order==0){
		up_picture.attr('imgWidth',640).attr('imgHeight',320);
		editWrap.removeClass('miniCover');
	}else{
		up_picture.attr('imgWidth',200).attr('imgHeight',200);
		editWrap.addClass('miniCover');
		var index=pBox.index();
		mt=(index+1)*111;
	}
	editWrap.stop().animate({marginTop:mt+'px'},function(){
		$('.pic_txt_col').removeClass('editOn');
		pBox.addClass('editOn');
		var meTitle=$('[name=meTitle]');
		var meAuthor=$('[name=meAuthor]');
		var meCoverFlag=$('[name=meCoverFlag]');
		var meComment=$('[name=meComment]');
		var meUrl=$('[name=meUrl]');
		var meArticle=$('#materialArticle');
		var meLink=$('[name=meLink]');
		var sTitle=$('.editOn').find('[name=sTitle]');
		var ttl=sTitle.val();
		if(ttl=='标题'){
			ttl='';
		}
		meTitle.val(ttl);
		
		var sAuthor=$('.editOn').find('[name=sAuthor]');
		var author=sAuthor.val();
		meAuthor.val(author);
		
		var imgPath=$('.editOn').find('[name=uploadPath]').val();
		if(imgPath!=''){
			preViewBox.show();
			uploadView.html('<img src="'+imgPath+'" />');
		}else{
			preViewBox.hide();
			uploadView.html('<h5>上传中...</h5>');
		}
		
		var cFlag=$('.editOn').find('[name=coverFlag]').val();
		if(cFlag=='1'){
			meCoverFlag.attr('checked',true);
		}else{
			meCoverFlag.attr('checked',false);
		}
		
		if(meComment.size()==1){
			var sComment=$('.editOn').find('[name=sComment]');
			var comment=sComment.val();
			meComment.val(comment);
		}
		
		var sUrl=$('.editOn').find('[name=sUrl]');		
		var url=sUrl.val();
		meUrl.val(url);
		
		var sArticle=$('.editOn').find('.sArticle');
		var article=sArticle.html();
		//meArticle.html(article);
		//CKEDITOR.instances.materialArticle.setData(article);
		editor.setContent(article);
		
		var sLink=$('.editOn').find('[name=sLink]');
		var link=sLink.val();
		meLink.val(link);

	});
}

function fnEditPicMsg(self){
	var input=$(self).parents('li').find('.materialName');
	var mid = $(self).attr('mid');
	input.addClass('inputOn').attr('readonly',false).focus();
	input.blur(function(){
		var val=input.val();
		//如果成功
		$.ajax({
			url:'/index.php/material/edit_pic_msg',
			data:{mid:mid,name:val},
			type:'post',
			dataType:'json',
			success:function(data){
				if(data.status == 1){
					input.removeClass('inputOn').attr('readonly',true);
					input.unbind();
				}
			}
		});
		
	});
}

function trim(val){
	return val.replace(/(^\s*)|(\s*$)/g, "");
}