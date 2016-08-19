$(function() {
    $(".m_wenben").show().siblings().hide();
    $(".m_wen_l").addClass('navClass');
});
function addMenu() {
    var i = $('.m_ul').find("li.m_ml").length;
    if (i >= 3) {
        alert("你已经创建了3个一级菜单，不能再创建了！");
        return false;
    } else {

        var createText = art.dialog({
            id: 'createText',
            title: '菜单',
            content: '加载中...',
            lock: true,
            cache: false
        });
        $.ajax({
            url: '/index.php/menu/AddFristMenu/',
            success: function(data) {
                createText.content(data);
                createText.button({
                    name: '保存',
                    focus: true,
                    callback: function() {
                        var menuName = $('#name').val();
                        $.ajax({
                            url:'/index.php/menu/SaveFristMenu',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                name: menuName
                            },
                            success: function(data) {
                                if (data.status == true) {
                                    var id = data.data;
                                    $('.m_ul').append("<li class='m_ml'><input type='text' id='text_" + id + "' value='" + menuName + "' disabled='true' class='m_text' onclick='showContent(" + id + ")' /><span  class='m_menu_l'><a href='javascript:void(0)' onclick='addChildMenu(this," + id + ")'>添加子菜单</a><a href='javascript:void(0)' onclick='updateMenu(this,\"" + id + "\")'>编辑</a><a href='javascript:void(0)' onclick='deleteMenu(this," + id + ")'>删除</a></span><ul id='childMenu'></ul></li>");
                                    createText.close();
                                }
                                art.dialog.alert(data.info);
                            },
                            cache: false
                        });
                        return false;
                    }
                }, {
                    name: '取消'
                }

                );
            },
            cache: false
        });
    }
}
//add a new child menu
function addChildMenu(obj, level) {
    var obj = obj;
    art.dialog({
        title: '提示',
        content: '添加子菜单后一级菜单的设置将被取消',
        lock: true,
        ok: function() {
            $.ajax({
                url:'/index.php/menu/cancelMenuSet',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: level
                },
                success: function(data) {
                    var j = $(obj).parent().parent().find('ul#childMenu li').length;
                    if (j >= 5) {
                        alert("你已经创建了5 个子菜单，不能再创建了！");
                        return false;
                    }
                    var createText = art.dialog({
                        id: 'createText',
                        title: '菜单',
                        content: '加载中...',
                        lock: true,
                        cache: false
                    });

                    $.ajax({
                        url:'/index.php/menu/AddMenuChildMenu',
                        success: function(data) {
                            createText.content(data);
                            createText.button({
                                name: '保存',
                                focus: true,
                                callback: function() {
                                    var menuName = $('#name').val();
                                    $.ajax({
                                        url:'/index.php/menu/SaveChildMenu',
                                        type: 'post',
                                        dataType: 'json',
                                        data: {
                                            name: menuName, 
                                            pid: level
                                        },
                                        success: function(data) {
                                            if (data.status == true) {
                                                var id = data.data;
                                                //$(obj).parent().parent().find('ul#childMenu').append("<li style='height:20px;position:relative'><span onclick='showContent(" + id + ")' class='m_text_span'></span><input type='text' id='text_" + level + "_" + j + "' value='" + menuName + "' disabled='true' class='m_text_li' onblur='onblurMenu(this," + id + ")' /><span class='m_menu_l'><a href='javascript:void(0)' onclick='updateMenu(this,\"" + level + "\")'>编辑</a><a href='javascript:void(0)' onclick='deleteChildMenu(this,\"" + id + "\")'>删除</a></span></li>");
                                                $(obj).parent().parent().find('ul#childMenu').append("<li style='height:20px;position:relative'><span onclick='showContent(" + id + ")' class='m_text_span'></span><input type='text' id='text_" + id + "' value='" + menuName + "' readonly='readonly'  class='m_text_li' onblur='onblurMenu(this," + id + ")' /><span class='m_menu_l'><a href='javascript:void(0)' onclick='updateMenu(this,\"" + id + "\")'>编辑</a><a href='javascript:void(0)' onclick='deleteChildMenu(this,\"" + id + "\")'>删除</a></span></li>");
                                                createText.close();
                                            }
                                            art.dialog.alert(data.info)
                                        },
                                        cache: false
                                    });
                                    return false;
                                }
                            }, {
                                name: '取消'
                            }

                            );
                        },
                        cache: false
                    });

                },
                cache: false
            });
        },
        cancelVal: '关闭',
        cancel: true
    });


}
function updateMenu(obj, idstr) {
    var m = $(obj).parent().parent().find("#text_" + idstr);
    //    m.removeAttr("disabled").css("border", "1px solid #999");
    //    onblur='onblurMenu(this," + id + ")';
    m.css("border", "1px solid #999");
    m.removeAttr('onclick');
    m.removeAttr('readonly');
    m.attr('onblur', "onblurMenu(this," + idstr + ")");
    
}
function onblurMenu(obj, level) {
    var box = $(obj);
    var val = $(obj).val();
    $.ajax({
        url:"/index.php/menu/UpdateNameMenu",
        type:'post',
        dataType:'json',
        data:{
            id: level, 
            value: val
        },
        success:function(data){
            if(data.status == true){
                box.css("border", "0");
                box.removeAttr('onblur');
                box.attr('onclick', "showContent(" + level + ")");
                box.attr('readonly','readonly');
                art.dialog.alert(data.info);
            }else{
                art.dialog.alert(data.info);
                box.focus();
            } 
        },
        cache:false
    });
//曹洪猛废除
//    $.post('/admin.php/Menu/UpdateNameMenu', {
//        id: level, 
//        value: val
//    }, function(data) {
//        $(obj).attr("disabled", "true").css("border", "0");
//        alert('修改成功');
//    }, 'json');



}
function deleteMenu(obj, level) {
    $.post('/index.php/menu/CountChildMenu', {
        id: level
    }, function(data) {
        if (data.data > 0) {
            alert('请先删除子菜单');
        } else {
            deleteHandler(obj, level);
        }
    }, 'json');
}
function deleteChildMenu(obj, level) {
    deleteHandler(obj, level);
}
function deleteHandler(obj, level) {
    art.dialog({
        title: '提示',
        content: '确定要删除此菜单吗？',
        lock: true,
        ok: function() {
            $.post('/index.php/menu/DeleteMenu', {
                id: level
            }, function(data) {
                if (data.data) {
                    alert('删除成功');
                    $(obj).parent().parent().remove();

                } else {
                    alert('删除失败');
                }
            }, 'json');
        },
        cancelVal: '关闭',
        cancel: true
    })
}
function m_wenben(obj, type) {

    $(obj).addClass('navClass').siblings().removeClass('navClass');
    $(".m_wenben").show().siblings().hide();
    $("#btnGreen").removeAttr('onclick');
    $("#btnGreen").attr('onclick', 'm_determine(\'' + type + '\',\'' + arguments[2] + '\')');
}
function m_image(obj, type) {

    $(obj).addClass('navClass').siblings().removeClass('navClass');
    $(".m_image").show().siblings().hide();
    $("#btnGreen").removeAttr('onclick');
    $("#btnGreen").attr('onclick', 'm_determine(\'' + type + '\',\'' + arguments[2] + '\')');
}
function m_yinyue(obj, type) {

    $(obj).addClass('navClass').siblings().removeClass('navClass');
    $(".m_yuyin").show().siblings().hide();
    $("#btnGreen").removeAttr('onclick');
    $("#btnGreen").attr('onclick', 'm_determine(\'' + type + '\',\'' + arguments[2] + '\')');
}
function m_url(obj, type) {
    $(obj).addClass('navClass').siblings().removeClass('navClass');
    $(".m_view").show().siblings().hide();
    $("#btnGreen").removeAttr('onclick');
    $("#btnGreen").attr('onclick', 'm_determine(\'' + type + '\',\'' + arguments[2] + '\')');
}
function m_determine(type, level) {
    var d = $("input[name='" + type + "']:checked").val();
    if (type == 'taburl') {
        d = $("input[name='" + type + "']").val();
    }
    $.post('Menu/SaveMenuSet', {
        id: level, 
        typeStr: type, 
        val: d
    }, function(data) {

        //if(data.data){
        alert('操作成功');
    //}else{
    //	alert('操作失败');
    //}
    }, 'json');
}

function releaseMenu(){
    $.ajax({
        url:'/index.php/menu/releaseMenu',
        type:'post',
        dataType:'json',
        success:function(data){
            if(data.status == 0){
                var menus = new Array();
                var err_data = data.data;
                var menus;
                for(menux in err_data){
                    $('#text_' + err_data[menux]).css("border", "1px solid #f00");
                }
                alert(data.info);
            }else{
                alert('自定义菜单发布成功');
            }
            
        }
    });
    
}
function xx(){
    $x = {
        "button":[{
            "name":"\u667a\u6052\u4e92\u52a8",
            "sub_button":[{
                "name":"\u4f60\u597d\u554a\u554a",
                "type":"click",
                "key":"5af6d65a0fff8cda41d42fe63fdf9ae8"
            },{
                "name":"\u554a\u6492\u65e6\u6cd5",
                "type":"click",
                "key":"58875d7fb76898967fadc7e06e17d3c5"
            }]
        },{
            "name":"\u667a\u80fd12",
            "type":"click",
            "key":"60cf9db91ef90b8a6fafb08c175eb7d5"
        },{
            "name":"\u4e92\u52a8\u4e92\u52a8",
            "type":"click",
            "key":"7134636817a2d0e4aa97a87581fafc65"
        }]
    };
}
function jsonxx(){
    var x = {
        "account_id":"4",
        "ToUserName":"gh_4a1f5466a693",
        "FromUserName":"owVSJjqRO2ZDvoGsvEWut75h6iQE",
        "CreateTime":"1378204479",
        "MsgType":"event",
        "MsgId":null,
        "Event":"CLICK",
        "EventKey":"5af6d65a0fff8cda41d42fe63fdf9ae8"
    };
}
function showContent(level) {
    $.get('/index.php/menu/ResourcesList', {
        id: level
    }, function(data) {
        $('.m_r_rightContent').html(data)

    });

}




//发布菜单方法,废除!!!
function send() {
    var len = $('.m_ul').find("li.m_ml").length;
    if (len == 0) {
        alert('请添加菜单');
        return false;
    } else {
        var str = '{';
        $('.m_ml').each(function(index, element) {
            var index = index + 1;
            var textvalue = $('#text_' + index).val();//一级菜单名
            var j = $(this).find('#childMenu li').length;
            str += '"' + index + '":{'
            str += '"name":"' + textvalue + '",';
            if (j == 0) {
                str += '"tabtext":"' + $("#tabtext_" + index + "_" + j).val() + '",';
                str += '"tabimgtext":"' + $("#tabimgtext_" + index + "_" + j).val() + '",';
                str += '"tabyinyue":"' + $("#tabyinyue_" + index + "_" + j).val() + '",';
                str += '"taburl":"' + $("#taburl_" + index + "_" + j).val() + '"';
            } else {
                for (i = 1; i <= j; i++) {
                    var m = $('#text_' + index + "_" + i).val();//二级菜单名
                    str += '"' + i + '":{'
                    str += '"name":"' + m + '",';
                    str += '"tabtext":"' + $("#tabtext_" + index + "_" + i).val() + '",';
                    str += '"tabimgtext":"' + $("#tabimgtext_" + index + "_" + i).val() + '",';
                    str += '"tabyinyue":"' + $("#tabyinyue_" + index + "_" + i).val() + '",';
                    str += '"taburl":"' + $("#taburl_" + index + "_" + i).val() + '"';
                    str += '},';
                }

                str = str.substr(0, str.length - 1);
            }
            str += '}';
        });
        str += "}";
        alert(str);
    }
}

function getC(n,type)
{
	if(n == 1){
		$('#sucaiC').css('display','none');
		$('#textC').css('display','none');
		$('#urlC').css('display','block');
	}else if(n == 2){
		$('#urlC').css('display','none');
		$('#sucaiC').css('display','none');
		$('#textC').css('display','block');
	}else if(n == 3){
		$('#urlC').css('display','none');
		$('#textC').css('display','none');
		$('#sucaiC').css('display','block');
		loadContent('',$('#sucaiC'),'/index.php/menu/getMaterialList/type/'+type);
	}
}

function updateMenuMsg(id)
{
	var id = id;
	var type =  $("input[name='type']:checked").val();
	var urlContent = $("#urlContent").val();
	var textContent = $('#textContent').val();
	var Sucai =  $("input[name='Sucai']:checked").val();
	
	 $.post('/index.php/menu/updateMenuMsg', {
        id: id,
		type:type,
		urlContent:urlContent,
		textContent:textContent,
		Sucai:Sucai
    }, function(data) {
        if (data == 1) {
            alert('更新成功');
        } else if(data == 4) {
            alert('更新失败');
        }
    }, 'json');
}

/**
 * 以下为新增方法
 */

function showMenuInfo(type, id){
    loadContentMenu('.m_r_con', '/admin.php/Menu/' + type + '/id/' + id);
}

function loadContentMenu(obj, url){
    $.ajax({
        url:url,
        success:function(data){
            $(obj).html(data);
        }
    });
    
}

function m_submit(stype){
    var type = $("input[name='type']").val();
    if(type == '1' || type == '2'){
        var menu_values = new Array();
        $("input[name='menu_value']:checked").each(function() {
            menu_values.push($(this).val());// 在数组中追加元素
        });

        if (menu_values.length == 0) {
            alert("请选择要发布的选项！");
        }
        if (menu_values.length > 10) {
            alert("选项超过十个！");
        }
    }else{
        var menu_values = $("[name='menu_value']:checked").val();
    }
    alert
    var id = $("input[name='id']").val();
    $.ajax({
        url:"/admin.php/Menu/SaveMenuSet",
        dataType:'json',
        type:'post',
        data:{
            menu_values:menu_values,
            id:id,
            type:type
        },
        success:function(data){
            if(data.status == true){
                art.dialog.alert(data.info);
            }else{
                art.dialog.alert(data.info);
            }
        }
        
    });
    
}