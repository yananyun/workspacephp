/**
 * @date    2014-06-20 15:56:47
 */

$(function(){
	// 全选
	(function(){
		var checkAll=$('.j-checkboxAll')||null;
		if(checkAll.length){
			var aCheck=checkAll.find('input[type="checkbox"]');
			aCheck.eq(0).on('click', function(){
				if($(this).attr('checked')){
					aCheck.each(function(index, el) {
						$(this).attr('checked', true);
					});
				}else{
					aCheck.each(function(index, el) {
						$(this).attr('checked', false);
					});
				}
			})
		}
	})();

	   // 上传缩略图
   (function(){
        function change(img, v) {
            getPath2(img, v, v.value);
        }
        //附带不用修改浏览器安全配置的javascript代码，兼容ie， firefox全系列
        function getPath2(obj, fileQuery, transImg) {
            if (window.navigator.userAgent.indexOf("MSIE") >= 1) {
                obj.setAttribute("src", transImg);
            } else {
                var file = fileQuery.files[0];
                var reader = new FileReader();
                reader.onload = function (e) {
                    obj.setAttribute("src", e.target.result)
                }
                reader.readAsDataURL(file);
            }
        }

        var thumbBtn=$('.upload-thumb-btn')||null;
        var thumbImgBox=$('.upload-thumb-img')||null;
        if(thumbBtn.length){
            // for(var i=0;i<thumbBtn.length;i++){
            //     (function(_i){
            //         thumbBtn[i].onchange=function(){
            //             var thumbImg=thumbImgBox[_i].getElementsByTagName('img')[0];
            //             change(thumbImg, this);
            //         }
            //     })(i);
            // }
            thumbBtn.each(function(index, el) {
            	$(this).on('change', function(){
            		var thumbImg=thumbImgBox.eq(index).find('img');
            		change(thumbImg.get(0), $(this).get(0));
            	})
            });
        }
   })();



})


// 打开负责人管理
function openPrincipal(id){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/store/leader_manage',
		success: function (data){
			process.close();
			art.dialog({
				title: '负责人管理',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
// 添加负责人
function addPrincipal(obj, id){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/store/add_principal',
		success: function (data){
			process.close();
			art.dialog({
				title: '负责人管理',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
// 删除负责人
function delPrincipal(obj, id){
	var box=$(obj).parents('li');
	var foo=confirm('您确定要删除该负责人吗？');
	if(foo){
		process=showloading('loading...');
		$.ajax({
			url: '/index.php/store/del_principal/',
			data:'id='+id,
			dataType:'json',
			type:'post',
			success: function (data){
				process.close();
				if(data.status){
					alert('删除成功');
					box.slideUp(1000,function(){
						$(this).remove();
					});
				}else{
					alert('删除失败');
				}
			},
			cache: false
		});
	}
}