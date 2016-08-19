var link =/http:\/\/[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)+([-A-Z0-9a-z_\$\.\+\!\*\(\)\/,:;@&=\?\~\#\%]*)*/i //url地址
var em=/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.|\-]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,4}$/; //email地址正则
var enNum=(/^[a-zA-Z0-9]{4}$/g);
var wrap_url;
    $(function(){
        var winUrl=window.location;
        var leftLi=$('.leftNav').children('li');
        var login=/index.php\/user\//;
        leftLi.each(function(){
            var li=$(this);
            var link=new RegExp(li.children('a').attr('href'));
            if(link.test(winUrl)){
                li.addClass('currentLiAccount');
            }
        });
	
        if(login.test(winUrl)){
            var input=$('.inputCol').children('input');
            input.each(function(){
                var label=$(this).siblings('label');
                /* var inputVal=$(this).val();
			var labelTxt=label.html();
			if(inputVal=='' || inputVal==labelTxt){
				label.show();
			}else{
				label.hide();
			} */
                label.hide();
            });
            input.focus(function(){
                var label=$(this).siblings('label');
                if(label){
                    label.hide();
                }
            }).blur(function(){
                var label=$(this).siblings('label');
                if(label){
                    var labelTxt=label.html();
                    var inputVal=$(this).val().replace(/ /g,'');
                    if(inputVal=='' || inputVal==labelTxt){
                        label.show();
                    }
                }
            });
        }
	
        //页面自适应布局，包括滚动，窗口尺寸操作的适应
        getWinHeight();
        $(window).scroll(function(){
            getWinHeight();
        });
        $(window).resize(function(){
            getWinHeight();
        });
    });
    function changePassFun(obj,box){
        var flag=$(obj).is(':checked');
        if(flag){
            box.show();
        }else{
            box.hide();
        }
    }
    function countDown(obj,num){
        if(!isNaN(num)){
            num--;
            if(num==0){
                document.location.href='login.html';
            }
            obj.html(num);
        }
        t=setTimeout(function(){
            countDown(obj,num)
        },1000);
    }

    function checkLoginForm(obj){
        var adminAccount=obj.find('[name=adminAccount]');
        var adminPass=obj.find('[name=adminPass]');
        var aName=$getVal(adminAccount);
        var aPass=$getVal(adminPass);
	
        if(aName==''||aPass==''){
            alert('请输入用户名密码');
            return false;
        }else if(!em.test(aName)){
            alert('用户名必须为email地址');
            return false;
        }
    }
    function Gaming_checkLoginForm(obj){
        var adminAccount=obj.find('[name=adminAccount]');
        var adminPass=obj.find('[name=adminPass]');
        var aName=$getVal(adminAccount);
        var aPass=$getVal(adminPass);
	
        if(aName==''||aPass==''){
            alert('请输入用户名密码');
            return false;
        }
    }
    function checkFindPass(obj){
        var loginEmail=$(obj).find('[name=loginEmail]');
        var adminVerify=$(obj).find('[name=adminVerify]');
        var lEmail=$getVal(loginEmail);
        var aVerify=$getVal(adminVerify);
        if(lEmail==''){
            alert('请输入登录邮箱');
            return false;
        }else if(!em.test(lEmail)){
            alert('请正确输入email地址');
            return false;
        }else{
            if(aVerify==''||!enNum.test(aVerify)){
                alert('请正确输入验证码');
                return false;
            }
        }
    }
    function checkChangePass(obj){
        var adminPassNew=$(obj).find('[name=adminPassNew]');
        var adminPassConfirm=$(obj).find('[name=adminPassConfirm]');
        var aPassN=$getVal(adminPassNew);
        var aPassC=$getVal(adminPassConfirm);
        if(aPassN=='' ||aPassC==''){
            alert('请输入并确认新密码');
            return false;
        }else if(aPassN!=aPassC){
            alert('确认密码和新密码不一致');
            return false;
        }
    }
    function checkAccountUpdate(obj){
        var loginPass=$(obj).find('[name=loginPass]');
        var newPass=$(obj).find('[name=newPass]');
        var confirmPass=$(obj).find('[name=confirmPass]');
        var lPass=$getVal(loginPass);
        var nPass=$getVal(newPass);
        var cPass=$getVal(confirmPass);
        if(nPass=='' ||cPass==''){
            alert('请输入并确认新密码');
            return false;
        }else if(nPass.length>16){
            alert('密码不能超过16位');
            return false;
        }else if(nPass!=cPass){
            alert('确认密码和新密码不一致');
            return false;
        }
    }
    function $getVal(obj){
        //return obj.val().replace(/ /g,'');
        return obj.val().replace(/(^\s*)|(\s*$)/g, '');
    }

    //添加账号
    function addAccount(){
        process=showloading('loading...');
        $.ajax({
            url: '/index.php/account/add_account',
            success: function (data){
                process.close();
                art.dialog({
                    title: '添加成员',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }
	
	//添加二维码
	function addSpread()
	{
		process=showloading('loading...');
        $.ajax({
            url: '/index.php/spread/add',
            success: function (data){
                process.close();
                art.dialog({
                    title: '添加推广二维码方案',
                    content:data,
					ok:function(){						
						var name = $('#name').val();
						var instruction = $('#instruction').val();
						$.post('/index.php/spread/doAddSpread', {
								name:name,
								instruction:instruction
							}, function(data) {
								alert('生成成功');
							}, 'json');	
							loadContent('',$('.intelListWrap'),'/index.php/spread/list_spread');							
					},
					cancel:function(){
						
					},
					okVal:'保存',
					cancelVal:'取消',
                    lock:true
                });
            },
            cache: false
        });
	}

	function doAddSpread()
	{
		var name = $('#name').val();
		var instruction = $('#instruction').val();
		$.post('/index.php/spread/doAddSpread', {
				name:name,
				instruction:instruction
			}, function(data) {
				alert('生成成功');
			}, 'json');
	}
	
	function doUpdateSpread()
	{
		var id = $('#id').val();
		var name = $('#name').val();
		var instruction = $('#instruction').val();
		$.post('/index.php/spread/doUpdateSpread', {
				id:id,
				name:name,
				instruction:instruction
			}, function(data) {
				alert('更新成功');
			}, 'json');
	}


    //删除列表
    function listDel(obj,gid){
        var box=$(obj).parents('li');
        var foo=confirm('您确定要删除该账号吗？');
        var userid = $(obj).attr('userid');
        var username = $(obj).attr('username');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/account/del_account/id/'+gid,
                data:'userid='+userid+'&username='+username,
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
    //删除列表
    function del_reply(obj,kid){
        var box=$(obj).parents('li');
        var foo=confirm('您确定要删除此条规则吗？');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/reply/delReply/kid/'+kid,
                type:'post',
                success: function (data){
                    process.close();
                    if(data == 1){
                        alert('删除成功');
//                        box.slideUp(1,function(){
//                            $(this).remove();
//                        });
						window.location.href = window.location.href;
                    }else{
                        alert('删除失败');
						window.location.href = window.location.href;
                    }
                },
                cache: false
            });
        }
    }

//删除列表
    function del_product(obj,kid){
        var box=$(obj).parents('li');
        var foo=confirm('您确定要删除这个商品吗？');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/productManage/delProduct/',
                type:'POST',
                data:{'pid':kid},
                dataType:'JSON',
                success: function (data){
                    process.close();
                    if(data.status == 1){
                        alert(data.info);
                        box.slideUp(1,function(){
                            $(this).remove();
                        });
                    }else{
                        alert(data.info);
                    }
                },
                cache: false
            });
        }
    }
    //礼券删除列表
    function giftListDel(obj,gid){
        var box=$(obj).parents('li');
        var foo=confirm('您确定要删除该信息吗？');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/gift/delGift/id/'+gid,
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



    function showProduct(obj,id){
        var showProduct=art.dialog({
            title:'产品详情',
            id:'showProduct',
			ok:function(){},
            lock:true
        });
        $.ajax({
            url: '/index.php/product/product_detail/id/'+id,
            success: function (data){
                showProduct.content(data);
            },
            cache: false
        });
    }
	
	function showH5Product(obj,id){
        var showProduct=art.dialog({
            title:'产品详情',
            id:'showProduct',
			ok:function(){},
            lock:true
        });
        $.ajax({
            url: '/index.php/h5manage/product_detail/id/'+id,
            success: function (data){
                showProduct.content(data);
            },
            cache: false
        });
    }
	
	function showH5Trial(obj,id){
        var showProduct=art.dialog({
            title:'试用产品详情',
            id:'showProduct',
			ok:function(){},
            lock:true
        });
        $.ajax({
            url: '/index.php/trial/trial_detail/id/'+id,
            success: function (data){
                showProduct.content(data);
            },
            cache: false
        });
    }
	
	function showH5Apply(obj,id){
        var showProduct=art.dialog({
            title:'产品申请名单详情',
            id:'showProduct',
			ok:function(){},
            lock:true
        });
        $.ajax({
            url: '/index.php/trial/apply_detail/id/'+id,
            success: function (data){
                showProduct.content(data);
            },
            cache: false
        });
    }

    function releaseMsg(){
        var releaseMsg=art.dialog({
            title:'发布消息',
            id:'releaseMsg',
            lock:true
        });
        $.ajax({
            url: '/index.php/message/release_message',
            success: function (data){
                releaseMsg.content(data);
            },
            cache: false
        });
    }

    function showMessage(obj,id){
        var showProduct=art.dialog({
            title:'消息详情',
            id:'showMessage',
            lock:true
        });
        $.ajax({
            url: '/index.php/message/message_detail/id/'+id,
            success: function (data){
                showProduct.content(data);
            },
            cache: false
        });
    }

    function showTrain(obj,id){
        var showProduct=art.dialog({
            title:'培训详情',
            id:'showTrain',
            lock:true
        });
        $.ajax({
            url: '/index.php/train/train_detail/id/'+id,
            success: function (data){
                showProduct.content(data);
            },
            cache: false
        });
    }

    function delProduct(obj,id){
        var box=$(obj).parents('li');
        var foo=confirm('该产品删除后将无法恢复，您确定要删除该产品吗？');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/product/delete_product/id/'+ id,			
                dataType:'json',
                type:'get',
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

    function editProduct(obj,id){
        var editProduct=art.dialog({
            title:'产品修改',
            id:'editProduct',
            lock:true
        });
        $.ajax({
            url: '/index.php/product/edit_product/id/'+id,
            success: function (data){
                editProduct.content(data);
            },
            cache: false
        });
    }

    function uploadPic(f) {
        var file = $(f).val();
        var hide=$(f).siblings('#picURl');
        var upId=$(f).attr('id');
        var img=$('.proImgWrap').find('#preview');
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


    function uploadPicGift(f) {
        var file = $(f).val();
        var hide=$(f).siblings('#picURl');
        var upId=$(f).attr('id');
        var img=$('.proImgWrap').find('#preview');
        //转换文件名为小写
        file = file.toLowerCase();
        //检测文件类型必须是图片
        if (!/.(gif|jpg|jpeg|png)$/.test(file)) {
            alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
        } else {
            $.ajaxFileUpload({
                url: '/index.php/gift/upload', //服务器端程序
                secureuri: false,
                fileElementId: upId, //input框的ID
                dataType: 'json', //返回数据类型
                success: function(data) {//上传成功
                    if(data.status){
                        hide.val(data.data);
                        $("#imghead").attr("src",data.data);
                        img.html('<img id="imghead" src="'+data.data+'" />');
                    }
                }
            });
        }
    }
    //删除文章
    function deleteArticle(obj){
        var box=$(obj).parents('li');
        var id = $(obj).attr('flagId');
        var foo=confirm('该文章删除后将无法恢复，您确定要删除该文章吗？');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url:'/index.php/product/edit_article/handle/delete/id/'+id,			
                dataType:'json',
                type:'get',
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
    //停用文章
    function turnoffArticle(obj){
        var id = $(obj).attr('flagId');
        $.ajax({
            url:'/index.php/product/edit_article/handle/turnoff/id/'+id,
            dataType:'json',
            type:'get',
            success:function(data){
				var flag=data.status;
				if(flag==1){
					alert(data.info);
					$(obj).removeClass('turnOff').addClass('turnOn').html('使用').attr('onclick','turnonArticle(this)');
				}else{
					alert(data.info);
				}
            }
        });
    }
    //使用文章
    function turnonArticle(obj){
        var id = $(obj).attr('flagId');
        $.ajax({
            url:'/index.php/product/edit_article/handle/turnon/id/'+id,
            dataType:'json',
            type:'get',
            success:function(data){
                var flag=data.status;
				if(flag==1){
					alert(data.info);
					$(obj).removeClass('turnOn').addClass('turnOff').html('停用').attr('onclick','turnoffArticle(this)');
				}else{
					alert(data.info);
				}
            }
        });
    }
    //删除评论
    function deleteReview(obj){
        var box=$(obj).parents('li');
        var id = $(obj).attr('flagId');
        var foo=confirm('该评论删除后将无法恢复，您确定要删除该评论吗？');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url:'/index.php/product/edit_comment/handle/delete/id/'+id,			
                dataType:'json',
                type:'get',
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
	
	//停用评论
    function turnoffReview(obj){
        var id = $(obj).attr('flagId');
        $.ajax({
            url:'/index.php/product/edit_comment/handle/turnoff/id/'+id,
            dataType:'json',
            type:'get',
            success:function(data){
				var flag=data.status;
				if(flag==1){
					alert(data.info);
					$(obj).removeClass('turnOff').addClass('turnOn').html('使用').attr('onclick','turnonReview(this)');
				}else{
					alert(data.info);
				}
            }
        });
    }
    //使用评论
    function turnonReview(obj){
        var id = $(obj).attr('flagId');
        $.ajax({
            url:'/index.php/product/edit_comment/handle/turnon/id/'+id,
            dataType:'json',
            type:'get',
            success:function(data){
                var flag=data.status;
				if(flag==1){
					alert(data.info);
					$(obj).removeClass('turnOn').addClass('turnOff').html('停用').attr('onclick','turnoffReview(this)');
				}else{
					alert(data.info);
				}
            }
        });
    }

    function changeTab(obj,box,className){	
		var openid = $('#openid').val();
		var id = $('#id').val();
		var did = $('#did').val();
        $(obj).addClass('currentTab').siblings('a').removeClass('currentTab');
        var box = $("."+box);
        box.show().siblings().hide();
		//订单记录
		if(className == 'couponsBox')
		{
			 $.ajax({
				url: '/index.php/member/getOrderListById/mid/'+id,
				success: function (data){
                    console.log(data);
                    box.html(data);
				},
				cache: false
			});
		}
		//礼品订单记录
		if(className == 'exchangeBox')
		{
			 $.ajax({
				url: '/index.php/member/getGiftOrderListById/did/'+did,
				success: function (data){
					box.html(data);
				},
				cache: false
			});
		}
//		
//		if(className == 'likeBox')
//		{
//			 $.ajax({
//				url: '/index.php/member/collectList/id/'+id,
//				success: function (data){
//					box.html(data)
//				},
//				cache: false
//			});
//		}
//		
//		if(className == 'rewardsBox')
//		{
//			 $.ajax({
//				url: '/index.php/member/integrationList/uid/'+uid,
//				success: function (data){
//					box.html(data)
//				},
//				cache: false
//			});
//		}
		
    }

    function showReviewDetail(obj){
        var box=$(obj).siblings('.reviewDetail');
        var flag=box.is(':visible');
        if(flag){
            box.hide();
        }else{
            box.show()
        }
    }
	
	function allMakeQr()
	{
		
		var memberList=$('.listOperaBottom').find('[name=ms]:checked');
		if(memberList.size() == 0)
		{
			alert('您尚未选择任何会员');
		}else{
			 var member=new Array(0);
			for(i=0;i<memberList.size();i++){
				var val=memberList.eq(i).val();
				member.push(val);
			}
			$.post('/index.php/member/allMakeQrCode', {
				member: member
			}, function(data) {
				alert('批量生成成功');
				window.location.href = window.location.href;
			}, 'json');
		}
	}
	
	function allCancelQr()
	{
		
		var memberList=$('.listOperaBottom').find('[name=ms]:checked');
		if(memberList.size() == 0)
		{
			alert('您尚未选择任何会员');
		}else{
			 var member=new Array(0);
			for(i=0;i<memberList.size();i++){
				var val=memberList.eq(i).val();
				member.push(val);
			}
			$.post('/index.php/member/allCancelQrCode', {
				member: member
			}, function(data) {
				alert('批量取消成功');
				window.location.href = window.location.href;
			}, 'json');
		}
	}
	
	function makeQr(id)
	{
		$.post('/index.php/member/makeQrCode', {
			id: id
		}, function(data) {
			alert('生成成功');
			window.location.href = window.location.href;
		}, 'json');
	}
	
	function cancelQr(id)
	{
		$.post('/index.php/member/cancelQrCode', {
			id: id
		}, function(data) {
			alert('取消成功');
			window.location.href = window.location.href;
		}, 'json');
	}
	
	function selectMember()
	{
		return false;
		var keyword = $.trim($(':input[name=keyword]').val());
		var type=$('#memberType').val();
		loadContent('',$('.intelListWrap'),'/index.php/member/list_member/keyword/'+keyword + '/type/'+type);
	}

    function showMemberDetail(id){
        var showMemberDetail=art.dialog({
            title:'会员详情',
            id:'showMemberDetail',
			ok:function(){},
            lock:true
        });
        $.ajax({
            url: '/index.php/member/member_detail/id/'+id,
            success: function (data){
                showMemberDetail.content(data);
				// changeTab("#frist",$('.couponsBox'),'couponsBox')

                changeTab(this,'aaa','couponsBox');
            },
            cache: false
        });
    }
	
    function showCoupons(id){
        var showCoupons=art.dialog({
            title:'礼券详情',
            id:'showCoupons',
            lock:true
        });
        $.ajax({
            url: '/index.php/member/coupons_detail/id/'+id,
            success: function (data){
                showCoupons.content(data);
            },
            cache: false
        });
    }

    function confirmCoupons(obj,gid){
        var confirmCoupons=art.dialog({
            title:'礼券审核',
            id:'confirmCoupons',
            lock:true
        });
        $.ajax({
            url: '/index.php/audit/audit_gift/id/'+gid,
            success: function (data){
                confirmCoupons.content(data);
                confirmCoupons.button({
                    name:'通过',
                    callback:function(){
                        auditInfo(2,gid,confirmCoupons);
                        return false;
                    },
                    focus:true
                });
                confirmCoupons.button({
                    name:'驳回',
                    callback:function(){
                        auditInfo(3,gid,confirmCoupons);
                        return false;
                    },
                });
            },
            cache: false
        });
    }

    function auditInfo(status,gid,obj){
        var flag=$('[name=couponsFlag]').val();
        var gid=$('[name=gid]').val();
        if(flag=='store'){
            var gtype = 1;
            var couponsName=$('.storeCoupons').find('[name=couponsName]');
            //var couponsContent=$('.storeCoupons').find('[name=couponsContent]');
			var couponsContent = CKEDITOR.instances.couponsContent1.getData();
            var couponsStore=$('.storeCoupons').find('[name=storeArr]');
            var couponsSeries=$('.storeCoupons').find('[name=couponsSeries]:checked');						
            var startDate=$('.storeCoupons').find('[name=startDate]');
            var endDate=$('.storeCoupons').find('[name=endDate]');
            var couponsGift=$('.storeCoupons').find('[name=couponsGift]');
            var couponsNum=$('.storeCoupons').find('[name=couponsNum]');
            var ishot = $(".storeCoupons").find('[name=ishot]:checked').val();
			var optionTotalStr = $('#UsedNum').val();
			var optionNumStr = $('.storeCoupons').find('[name=couponsGift]').find("option:selected").attr("num");
			var couponsPlan=$('.storeCoupons').find('[name=couponsPlan]');
			var sendDate = $(".storeCoupons").find('[name=sendDate]').val();
			var useTimeDay = $(".storeCoupons").find('[name=useTimeDay]').val();
            if($getVal(couponsName)==''){
                alert('请输入礼券名称');
                return false;
            }else if(couponsContent==''){
                alert('请输入礼券内容');
                return false;
            }else if(couponsStore.size()==0){
                alert('选择适用店面');
                return false;
            }else if(couponsSeries.size()==0){
                alert('选择适用品类');
                return false;
            }else if($getVal(startDate)==''){
                alert('请输入起始日期');
                return false;
            }else if($getVal(endDate)==''){
                alert('请输入结束日期');
                return false;
            }else if(couponsGift.val()==0){
                alert('选择关联奖品');
                return false;
            }else{
			
                var strStore=new Array(0);
                for(i=0;i<couponsStore.size();i++){
                    var val=couponsStore.eq(i).val();
                    strStore.push(val);
                }
                couponsStoreStr = strStore.toString();
					

                var str=new Array(0);
                for(i=0;i<couponsSeries.size();i++){
                    var val=couponsSeries.eq(i).val();
                    str.push(val);
                }
                cateData = str.toString();						
                var num = parseInt(couponsNum.val());
                var selectNum =  parseInt($('.storeCoupons').find("#num").val());
               /* if(!selectNum){
                    alert('请选择关联奖品');
                    return false;
                }*/
                if(!num){
                    alert('请输入礼券数量');
                    return false;
                }
			if(parseInt(couponsNum.val())>(parseInt(optionTotalStr)+parseInt(optionNumStr))){
				alert('您填写的最大数量不能超过奖品数量');
					return false;
				}
                if($getVal(couponsPlan)==''){
					alert('请输入礼券方案');
                    return false;
				}
				if(!sendDate){
					alert('请输入定时发布日期');
					return false;
				}
				if(useTimeDay==''){
					alert('请输入使用时间段限制');
					return false;
				}
/*                var picImage = $("#picURl").val();
                if(!picImage){
                    alert('请上传礼品图片');
                    return false;
                }*/
                $.ajax({
                    url:"/index.php/audit/audit_gift/id/"+gid,
                    type:"post",
                    dataType:"json",
                    data:{
                        dosubmit:'yes',
                        gname:couponsName.val(),
                        //gcontent:couponsContent.val(),
						gcontent:couponsContent,
                        couponsStore:couponsStoreStr,
                        cateData:cateData,
                        startDate:startDate.val(),
                        endDate:endDate.val(),
                        couponsGift:couponsGift.val(),
                        couponsNum:couponsNum.val(),
					    useTimeDay:useTimeDay,	
					    sendDate:sendDate,
						couponsPlan:couponsPlan.val(),
                        couponsFlag:gtype,
                        gid:gid,
                        status:status
                    },
                    success: function(data){
                        if(data.info==10000){
                            alert(data.data)
                            obj.close()
                        loadContent('',$('.intelListWrap'),wrap_url)
                        }else{
                            alert(data.data)
                        }
                    }

                });
            }
        }else if(flag=='online'){
            var gtype = 2;
            var couponsPlat=$('.onlineCoupons').find('[name=couponsPlat]');
            var couponsName=$('.onlineCoupons').find('[name=couponsName]');
            //var couponsContent=$('.onlineCoupons').find('[name=couponsContent]');
			var couponsContent = CKEDITOR.instances.couponsContent2.getData();
            var couponsSeries=$('.onlineCoupons').find('[name=couponsSeries]:checked');
            var startDate=$('.onlineCoupons').find('[name=startDate]');
            var endDate=$('.onlineCoupons').find('[name=endDate]');
            var couponsNum=$('.onlineCoupons').find('[name=couponsNum]');
			var couponsGift=$('.onlineCoupons').find('[name=couponsGift]');			
			var optionTotalStr = $('.onlineCoupons').find('#UsedNum').val();
			var optionNumStr = $('.onlineCoupons').find('[name=couponsGift]').find("option:selected").attr("num");
			var moneyNum = $('.onlineCoupons').find('[name=money]').val();
			var colorStr = $(".onlineCoupons").find('[name=colorValue]:checked').val()
			var sendDate = $(".onlineCoupons").find('[name=sendDate]').val();
            if(couponsPlat.val()==0){
                alert('选择电商名称');
                return false;
            }else if($getVal(couponsName)==''){
                alert('请输入礼券名称');
                return false;
            }else if(couponsContent==''){
                alert('请输入礼券内容');
                return false;
            }else if(couponsSeries.size()==0){
                alert('选择适用品类');
                return false;
            }else if($getVal(startDate)==''){
                alert('请输入起始日期');
                return false;
            }else if($getVal(endDate)==''){
                alert('请输入结束日期');
                return false;
            }else if(couponsGift.val()==0){
                alert('选择关联奖品');
                return false;
            }else {
			
                var str=new Array(0);
                for(i=0;i<couponsSeries.size();i++){
                    var val=couponsSeries.eq(i).val();
                    str.push(val);
                }
                cateData = str.toString();						
 				var num = parseInt(couponsNum.val());
                var selectNum =  parseInt($('.onlineCoupons').find("#num").val());
                if(!num){
                    alert('请输入礼券数量');
                    return false;
                }
			if(parseInt(couponsNum.val())>(parseInt(optionTotalStr)+parseInt(optionNumStr))){
				alert('您填写的最大数量不能超过奖品数量');
					return false;
				}
				if(!moneyNum){
					alert("请输入礼券内容");
					return false;
				}
               if(!colorStr){
								
				alert("请选择优惠券背景颜色");
				return false;
								
				}
				if(!sendDate){
					alert('请输入定时发布日期');
					return false;
				}
                $.ajax({
                    url:"/index.php/audit/audit_gift/id/"+gid,
                    type:"post",
                    dataType:"json",
                    data:{
                        dosubmit:'yes',
                        couponsPlat:couponsPlat.val(),
                        gname:couponsName.val(),
                        //gcontent:couponsContent.val(),
						gcontent:couponsContent,
                        cateData:cateData,
                        startDate:startDate.val(),
                        endDate:endDate.val(),
                        couponsNum:couponsNum.val(),
						couponsGift:couponsGift.val(),
                        sendDate:sendDate,	
						color:colorStr,
                        couponsFlag:gtype,
						money:moneyNum,
                        gid:gid,
                        status:status
                    },
                    success: function(data){
                        if(data.info==10000){
                            alert(data.data)
                            obj.close()
                        loadContent('',$('.intelListWrap'),wrap_url)
                        }else{
                            alert(data.data)
                        }
                    }

                });
            }
        }

    }

    function checlAll(obj,wrap){
        var selectBox=wrap.find('.selectBox');
        var flag=$(obj).is(':checked');
        for(i=0;i<selectBox.size();i++){
            if(flag){
                selectBox[i].checked=true;
            }else{
                selectBox[i].checked=false;
            }
        }
    }
    //删除列表
    function listRandomDel(platform,id){
        var foo=confirm('您确定要删除该账号吗？');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/promotion/deleteRandomItem/platform/'+platform+'/id/'+id,			
                dataType:'json',			
                success: function (data){
                    process.close();
                    if(data.status){
                        alert('删除成功');
                        location.href = location.href;					
                    }else{
                        alert('删除失败');
                    }
                },
                cache: false
            });
        }
    }

    //编辑账号
    function editAccount(obj){
        process=showloading('loading...');
        var userid = $(obj).attr('userid');
        $.ajax({
            url: '/index.php/account/edit_account/userid/'+userid,
            success: function (data){
                process.close();
                art.dialog({
                    title: '编辑账号',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }
    function setFocus(obj,txt){
        $(obj).removeClass('intelHint');
        var val=$getVal($(obj));
        if(val==txt){
            $(obj).val('');
        }
    }
    function setBlur(obj,txt){
        var val=$getVal($(obj));
        if(val==txt || val==''){
            $(obj).val(txt).addClass('intelHint');
        }
    }


    //活动字数限制
    function checkVal(obj){
        if(obj){
            setInterval(function(){
                setVal(obj)
            },100);
        }
    }
    function setVal(obj){
        var txt=obj.val();
        if(txt.length > 34){
            var val=txt.slice(0,34)
            obj.val(val);
        }
    }
    function loadContent(obj,wrap,url){
        wrap_url = url;
        wrap.html('');
        process=showloading('loading...');
        $.ajax({
            url: url,
            success: function (data) {
                process.close();
                wrap.html(data);
                switchTab(obj);
				if($('.materialCol').size()==2){
					fnSplitCol();
				}
            },
            cache: false
        });
    }
    function switchTab(obj){
        $(obj).addClass('tabCurrent');
        $(obj).siblings().removeClass('tabCurrent');
    }
    function tabTrigger(){
        var subTab=$('.subTab').children('li').eq(0);
        subTab.trigger('click');
    }

    function releaseRSP(){
        var releaseRSP=art.dialog({
            title:'RSP发布',
            id:'releaseRSP',
            lock:true
        });
        $.ajax({
            url: '/index.php/rsp/rsp_release',
            success: function (data){
                releaseRSP.content(data);
            },
            cache: false
        });
    }

    function showStore(obj,id){
        var showStore=art.dialog({
            title:'店面详情',
            id:'showStore',
            lock:true
        });
        $.ajax({
            url: '/index.php/store/store_detail/id/'+id,
            success: function (data){
                showStore.content(data);
            },
            cache: false
        });
    }
	
	function showEzStore(obj,id){
        var showStore=art.dialog({
            title:'店面详情',
            id:'showStore',
            lock:true
        });
        $.ajax({
            url: '/index.php/ezstore/store_detail/id/'+id,
            success: function (data){
                showStore.content(data);
            },
            cache: false
        });
    }

    function editStore(obj,id){
        var editStore=art.dialog({
            title:'店面修改',
            id:'editStore',
            lock:true
        });
        $.ajax({
            url: '/index.php/store/edit_store/id/'+id,
            success: function (data){
                editStore.content(data);
            },
            cache: false
        });
    }
	//修改至尊店面信息
	function editEzStore(obj,id){
        var editStore=art.dialog({
            title:'店面修改',
            id:'editStore',
            lock:true
        });
        $.ajax({
            url: '/index.php/ezstore/edit_store/id/'+id,
            success: function (data){
                editStore.content(data);
            },
            cache: false
        });
    }

    //loading 窗口
    function showloading(ttl){
        var foo={}
        var loading=art.dialog({
            title:false,
            content:'<div class="popLoading">Loading</div>',
            id:'process',
            lock:true
        });
        foo.close=function(){
            loading.close();
        }
        return foo;
    }

    function getWinHeight(){
        var winHeight=0;
        if (window.innerHeight){
            winHeight = window.innerHeight; 
        }else if((document.body) && (document.body.clientHeight)){
            winHeight = document.body.clientHeight; 
        }
        if (document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth){ 
            winHeight = document.documentElement.clientHeight; 
        }
        $('.container').css('min-height',winHeight);
        $('.mainLeft').css('min-height',winHeight);
        $('.mainWrap').css('min-height',winHeight);
    }


    function copyToClipboard(txt) {  
        if(window.clipboardData) {  
            window.clipboardData.clearData();  
            window.clipboardData.setData("Text", txt);  
        } else if(navigator.userAgent.indexOf("Opera") != -1) {  
            window.location = txt;  
        } else if (window.netscape) {  
            try {  
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");  
            } catch (e) {  
                alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将 'signed.applets.codebase_principal_support'设置为'true'");  
            }  
            var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard); 
            if (!clip)  
                return;  
            var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable); 
            if (!trans)  
                return;  
            trans.addDataFlavor('text/unicode');  
            var str = new Object();  
            var len = new Object();  
            var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);  
            var copytext = txt;  
            str.data = copytext;  
            trans.setTransferData("text/unicode",str,copytext.length*2);  
            var clipid = Components.interfaces.nsIClipboard;  
            if (!clip)  
                return false;  
            clip.setData(trans,null,clipid.kGlobalClipboard);  
        }  
        alert("复制成功！") ;
    }

    function clickPage(url,block){
        if(block==0){
            location.href = url;
        }else{
            var block = $("."+block);
            loadContent('',block,url);
        }
		if($('.materialCol').size()==2){
			fnSplitCol();
		}
    }
    function checkHintName(getName,hintName){
        if(getName==hintName){
            getName='';
        }
        return getName;
    }

    function setChannel(channel,itemArr){
        var cVal=itemArr.cateids;
        var cn=channel.find('[name=channelList]');
        for(i=0;i<cVal.length;i++){
            cn.each(function(){
                var cnVal=$(this).val();
                if(cnVal==cVal[i]){
                    $(this).attr('checked',true);
                }
            });
        }
        var cnNum=cn.size();
        var CkedNum=channel.find('[name=channelList]:checked').size();
        if(CkedNum==cnNum){
            channel.find('[name=checkAll]').attr('checked',true);
        }
    }

    function triggerFile(obj){
        var imgPath=$(obj).siblings(':file');
        imgPath.trigger('click');
        imgPath.change(function(){
            $(obj).siblings(':submit').trigger('click');
            $("#sucaiMulitImage").val("");
        });
    }

    function checkWrap(wrap){
        var normalPrice=wrap.find('[name=normalPrice]').val();
        var disTtl=wrap.find('[name=disTtl]').val();
        var disURL=wrap.find('[name=disURL]').val();
        var itemPic=wrap.find('#productimg');
        var itemPic2=wrap.find('#productimgTwo');
        if(wrap.html()==''){
            alert('请置入投放物料');
            return false;
        }else if(normalPrice==''){
            alert('请输入原始价格');
            return false;
        }else if(isNaN(normalPrice)){
            alert('请检查原始价格是否是数字！');
            return false;
        }else if(disTtl==''){
            alert('请输入标题样式');
            return false;
        }else if(disTtl.length>15){
            alert('标题样式最多只能输入15个字');
            return false;
        }else if(disURL=='' || !disURL.match(link)){
            alert('请正确输入介绍地址');
            return false;
        }else if(itemPic.attr('src')==''){
            alert('请上传产品图片(样式1)');
            return false;
        }/* else if(itemPic2.attr('src')==''){
		alert('请上传产品图片(样式2)');
		return false;
	} */else{
            return true;
        }
    }
    // add sucai
    function fmCheck(){
	
        var data = $(".pre_dialog").html();
        art.dialog({
            title:"提示信息",
            width:350,
            height:150,
            content:data,
            focus:true
        })
		
    }
    function pre_addModel(){
        window.location.href="/index.php/item/item_add";
    }
    function pre_modelList(){
        window.location.href="/index.php/item/item_manage";
    }
    //勾选全部客户
    function selectAllCustomer(obj,wrap){
        var flag=$(obj).is(':checked');
        var customer=wrap.children('.pre_li');
        if(flag){
            customer.addClass('preSelected');
        }else{
            customer.removeClass('preSelected');
        }
        selectCustomerCount();
	
    }
    //勾选个数统计
    function selectCustomerCount(){
        var selectNum=$('.preSelected').size();
        if(selectNum==0){
            $('[name=n_pre_cx]').prop('checked',false).attr('checked',false);
        }else{
            $('[name=n_pre_cx]').prop('checked',true).attr('checked',true);
        }
    //alert(selectNum);
    }
    //删除单个客户
    function deleteTheCustomer(obj){
        var customerWrap=$(obj).parents('li');
        var flag = confirm('确定要删除这个信息？');
        if(flag){
            $.get('script/action.js',function(data){
                if(data==1){
                    customerWrap.slideUp(function(){
                        alert('删除成功!');
                        customerWrap.remove();
                        selectCustomerCount();
                    });
                }else{
                    alert('删除失败');
                }
            });
        }
    }
    //批量删除客户
    function deleteGroupCustomer(wrap){
        var cus=wrap.find('.preSelected');
        if(cus.size()>0){
            var flag = confirm('确定要删除？');
            if(flag){
                $.get('script/action.js',function(data){
                    if(data==1){
                        alert('删除成功!');
                        cus.slideUp(function(){
                            cus.remove();
                            selectCustomerCount();
                        });
                    }else{
                        alert('删除失败');
                    }
                });
            }
        }else{
            alert('没有勾选任何列表');
        }
    }

    function addCoupons(){
        var addCoupons=art.dialog({
            title:'创建礼券',
            id:'addCoupons',
            lock:true
        });
        $.ajax({
            url: '/index.php/gift/add_gift',
            success: function (data){
                addCoupons.content(data);
                addCoupons.button({
                    name:'创建',
                    callback:function(){
                        var flag=$('[name=couponsFlag]:checked').val();
                        if(flag=='store'){
                            var gtype = 1;
                            var couponsName=$('.storeCoupons').find('[name=couponsName]');
                            //var couponsContent=$('.storeCoupons').find('[name=couponsContent]');
                            var couponsStore=$('.storeCoupons').find('[name=storeArr]');
                            var couponsPlan=$('.storeCoupons').find('[name=couponsPlan]');
						
                            var couponsSeries=$('.storeCoupons').find('[name=couponsSeries]:checked');						
                            var startDate=$('.storeCoupons').find('[name=startDate]');
                            var endDate=$('.storeCoupons').find('[name=endDate]');
                            var couponsGift=$('.storeCoupons').find('[name=couponsGift]');
                            var couponsNum=$('.storeCoupons').find('[name=couponsNum]');
                            //var ishot = $(".storeCoupons").find('[name=ishot]:checked').val();
							var sendDate = $(".storeCoupons").find('[name=sendDate]').val();
							var useTimeDay = $(".storeCoupons").find('[name=useTimeDay]').val();
                            //var ishot = $(".storeCoupons").find('[name=ishot]').val();
							var couponsContent = CKEDITOR.instances.couponsContent1.getData();
                            if($getVal(couponsName)==''){
                                alert('请输入礼券名称');
                                return false;
                            }else if(couponsContent==''){
                                alert('请输入礼券内容');
                                return false;
                            }else if(couponsStore.size()==0){
                                alert('选择适用店面');
                                return false;
                            }else if(couponsSeries.size()==0){
                                alert('选择适用品类');
                                return false;
                            }else if($getVal(startDate)==''){
                                alert('请输入起始日期');
                                return false;
                            }else if($getVal(endDate)==''){
                                alert('请输入结束日期');
                                return false;
                            }else if(couponsGift.val()==0){
                                alert('选择关联奖品');
                                return false;
                            }else{
							
                                var strStore=new Array(0);
                                for(i=0;i<couponsStore.size();i++){
                                    var val=couponsStore.eq(i).val();
                                    strStore.push(val);
                                }
                                couponsStoreStr = strStore.toString();	
												
                                var str=new Array(0);
                                for(i=0;i<couponsSeries.size();i++){
                                    var val=couponsSeries.eq(i).val();
                                    str.push(val);
                                }
                                cateData = str.toString();						
                                var num = parseInt(couponsNum.val());
                                var selectNum =  parseInt($('.storeCoupons').find("#num").val());
/*                                if(!selectNum){
                                    alert('请选择关联奖品');
                                    return false;
                                }*/
                                if(!num){
                                    alert('请输入礼券数量');
                                    return false;
                                }
							
                                if(num>selectNum){
                                    alert('您填写的最大数量不能超过奖品数量');
                                    return false;
                                }
								
								if($getVal(couponsPlan)==''){
								 	alert('请输入礼券方案');
                               		return false;
								}
								if(!sendDate){
								 	alert('请输入定时发布日期');
                               		return false;
								}
								if(useTimeDay==''){
								 	alert('请输入使用时间段限制');
                               		return false;
								}
/*                                var picImage = $("#picURl").val();
                                if(!picImage){
                                    alert('请上传礼品图片');
                                    return false;
                                }*/
                                $.ajax({
                                    url:"/index.php/gift/add_gift",
                                    type:"post",
                                    dataType:"json",
                                    data:{
                                        dosubmit:'yes',
                                        gname:couponsName.val(),
                                        //gcontent:couponsContent.val(),
										gcontent:couponsContent,
                                        couponsStore:couponsStoreStr,
                                        cateData:cateData,
                                        startDate:startDate.val(),
                                        endDate:endDate.val(),
                                        couponsGift:couponsGift.val(),
                                        couponsNum:couponsNum.val(),
                                        useTimeDay:useTimeDay,	
										couponsPlan:couponsPlan.val(),
                                        sendDate:sendDate,
                                        couponsFlag:gtype
                                    },
                                    success: function(data){
                                        if(data.info==10000){
                                            alert(data.data)
                                            addCoupons.close()
                                            loadContent('',$('.intelListWrap'),'/index.php/gift/list_coupons/')
                                        }else{
                                            alert(data.data)
                                        }
                                    }
				
                                });
                            }
                        }else if(flag=='online'){
                            var gtype = 2;
                            var couponsPlat=$('.onlineCoupons').find('[name=couponsPlat]');
                            var couponsName=$('.onlineCoupons').find('[name=couponsName]');
                            //var couponsContent=$('.onlineCoupons').find('[name=couponsContent]');
                            var couponsSeries=$('.onlineCoupons').find('[name=couponsSeries]:checked');
                            var startDate=$('.onlineCoupons').find('[name=startDate]');
                            var endDate=$('.onlineCoupons').find('[name=endDate]');
                            var couponsNum=$('.onlineCoupons').find('[name=couponsNum]');
                            var ishot = $(".onlineCoupons").find('[name=ishot2]:checked').val();
							var couponsGift=$('.onlineCoupons').find('[name=couponsGift]');
                            //var ishot = $(".onlineCoupons").find('[name=ishot2]').val();
							var moneyNum = $('.onlineCoupons').find('[name=money]').val();
							var colorStr = $(".onlineCoupons").find('[name=colorValue]:checked').val()
							var sendDate = $(".onlineCoupons").find('[name=sendDate]').val();
							var couponsContent = CKEDITOR.instances.couponsContent2.getData();
                            if(couponsPlat.val()==0){
                                alert('选择电商名称');
                                return false;
                            }else if($getVal(couponsName)==''){
                                alert('请输入礼券名称');
                                return false;
                            }else if(couponsContent==''){
                                alert('请输入礼券内容');
                                return false;
                            }else if(couponsSeries.size()==0){
                                alert('选择适用品类');
                                return false;
                            }else if($getVal(startDate)==''){
                                alert('请输入起始日期');
                                return false;
                            }else if($getVal(endDate)==''){
                                alert('请输入结束日期');
                                return false;
                            }else if(couponsGift.val()==0){
                                alert('选择关联奖品');
                                return false;
                            }else if($getVal(couponsNum)==''){
                                alert('请输入礼券数量');
                                return false;
                            }else{
							
                                var str=new Array(0);
                                for(i=0;i<couponsSeries.size();i++){
                                    var val=couponsSeries.eq(i).val();
                                    str.push(val);
                                }
                                cateData = str.toString();						
                                var num = parseInt(couponsNum.val());
                                var selectNum =  parseInt( $(".onlineCoupons").find("#num").val());
	
                                if(!num){
                                    alert('请输入优惠券数量');
                                    return false;
                                }
							
                                if(num>selectNum){
                                    alert('您填写的最大数量不能超过奖品数量');
                                    return false;
                                }
								if(!moneyNum){
								    alert("请输入礼券金额");
									return false;
								}
								if(!colorStr){
								
									alert("请选择优惠券背景颜色");
									return false;
								
								}
								if(!sendDate){
								 	alert('请输入定时发布日期');
                               		return false;
								}					
                                $.ajax({
                                    url:"/index.php/gift/add_gift",
                                    type:"post",
                                    dataType:"json",
                                    data:{
                                        dosubmit:'yes',
                                        couponsPlat:couponsPlat.val(),
                                        gname:couponsName.val(),
                                        //gcontent:couponsContent.val(),
										gcontent:couponsContent,
                                        cateData:cateData,
                                        startDate:startDate.val(),
                                        endDate:endDate.val(),
                                        couponsNum:couponsNum.val(),
										couponsGift:couponsGift.val(),
                                        sendDate:sendDate,	
                                        color:colorStr,
										money:moneyNum,
                                        couponsFlag:gtype
                                    },
                                    success: function(data){
                                        if(data.info==10000){
                                            alert(data.data)
                                            addCoupons.close()
                                            loadContent('',$('.intelListWrap'),'/index.php/gift/list_coupons/')
                                        }else{
                                            alert(data.data)
                                        }
                                    }
				
                                });
							
                            }
                        }
                        return false;
                    },
                    focus: true
                });
                addCoupons.button({
                    name:'取消'
                });
            },
            cache: false
        });
    }

    function findStore(obj,box){
        var findStore=art.dialog({
            title:'查找店面',
            id:'findStore',
            lock:true
        });
		var existStr=box.find('[name=storeArr]');
        $.ajax({
            url: '/index.php/store/find_store/',
            success: function (data){
                findStore.content(data);
                findStore.button({
                    name:'确定',
                    callback:function(){
                        var store=$('[name=checkStore]:checked');
                        if(store.size()>0){
                            var wrap=$('#storeSearch');
                            var prov=wrap.find('#storeProvince').val();
                            var city=wrap.find('#storeCity').val();
                            var arr='';
							var existArr= new Array(0);
							var newAdd= new Array(0);
							var newList= new Array(0);
                            for(i=0;i<store.size();i++){
								var prov=store.eq(i).parents('li').find('.provinceStore').val();
                            	var city=store.eq(i).parents('li').find('.cityStore').val();
                                var storeName=store.eq(i).val();
                                var storeId=store.eq(i).attr('id');
								var str=prov+'|'+city+'|'+storeName+'|'+storeId;
								newAdd.push(prov+'|'+city+'|'+storeName+'|'+storeId);
                            }
							
							if(existStr.size()>0){
								for(x=0; x<existStr.size();x++){
									existArr.push(existStr.eq(x).val());
								}
								for(n=0;n<newAdd.length;n++){
									for(z=0; z<existArr.length;z++){
										if(newAdd[n]==existArr[z]){
											newAdd.shift(newAdd[n]);
										}
									}
								}
								for(k=0;k<newAdd.length;k++){
									var strArr=newAdd[k].split("|");
									arr+='<li>'+strArr[0]+' - '+strArr[1]+' - '+strArr[2]+'<input type="hidden" name="storeArr" value="'+newAdd[k]+'" /><a href="javascript:void(0);" onclick="removeList(this)"> 删除 </a></li>';
								}
							}else{
								for(k=0;k<newAdd.length;k++){
									var strArr=newAdd[k].split("|");
									arr+='<li>'+strArr[0]+' - '+strArr[1]+' - '+strArr[2]+'<input type="hidden" name="storeArr" value="'+newAdd[k]+'" /><a href="javascript:void(0);" onclick="removeList(this)"> 删除 </a></li>';
								}
							}
						
                            //var box=$(obj).parent('.fitStoreWrap');
                            box.prepend(arr);
                            findStore.close();
                        }else{
                            alert('请选择店面');
                        }
                        return false;
                    },
                    focus:true
                });
                findStore.button({
                    name:'取消'
                });
            },
            cache: false
        });
    }
	function findezStore(obj,box){
        var findStore=art.dialog({
            title:'查找店面',
            id:'findStore',
            lock:true
        });
		var existStr=box.find('[name=storeArr]');
        $.ajax({
            url: '/index.php/ezstore/find_store/',
            success: function (data){
                findStore.content(data);
                findStore.button({
                    name:'确定',
                    callback:function(){
                        var store=$('[name=checkStore]:checked');
                        if(store.size()>0){
                            var wrap=$('#storeSearch');
                            var prov=wrap.find('#storeProvince').val();
                            var city=wrap.find('#storeCity').val();
                            var arr='';
							var existArr= new Array(0);
							var newAdd= new Array(0);
							var newList= new Array(0);
                            for(i=0;i<store.size();i++){
								var prov=store.eq(i).parents('li').find('.provinceStore').val();
                            	var city=store.eq(i).parents('li').find('.cityStore').val();
                                var storeName=store.eq(i).val();
                                var storeId=store.eq(i).attr('id');
								var str=prov+'|'+city+'|'+storeName+'|'+storeId;
								newAdd.push(prov+'|'+city+'|'+storeName+'|'+storeId);
                            }
							
							if(existStr.size()>0){
								for(x=0; x<existStr.size();x++){
									existArr.push(existStr.eq(x).val());
								}
								for(n=0;n<newAdd.length;n++){
									for(z=0; z<existArr.length;z++){
										if(newAdd[n]==existArr[z]){
											newAdd.shift(newAdd[n]);
										}
									}
								}
								for(k=0;k<newAdd.length;k++){
									var strArr=newAdd[k].split("|");
									arr+='<li>'+strArr[0]+' - '+strArr[1]+' - '+strArr[2]+'<input type="hidden" name="storeArr" value="'+newAdd[k]+'" /><a href="javascript:void(0);" onclick="removeList(this)"> 删除 </a></li>';
								}
							}else{
								for(k=0;k<newAdd.length;k++){
									var strArr=newAdd[k].split("|");
									arr+='<li>'+strArr[0]+' - '+strArr[1]+' - '+strArr[2]+'<input type="hidden" name="storeArr" value="'+newAdd[k]+'" /><a href="javascript:void(0);" onclick="removeList(this)"> 删除 </a></li>';
								}
							}
						
                            //var box=$(obj).parent('.fitStoreWrap');
                            box.prepend(arr);
                            findStore.close();
                        }else{
                            alert('请选择店面');
                        }
                        return false;
                    },
                    focus:true
                });
                findStore.button({
                    name:'取消'
                });
            },
            cache: false
        });
    }
	
 function findStoreBest(obj,box){
        var findStore=art.dialog({
            title:'查找店面',
            id:'findStore',
            lock:true
        });
		var existStr=box.find('[name=storeArr]');
        $.ajax({
            url: '/index.php/store/findBest_store/',
            success: function (data){
                findStore.content(data);
                findStore.button({
                    name:'确定',
                    callback:function(){
                        var store=$('[name=checkStore]:checked');
                        if(store.size()>0){
                            var wrap=$('#storeSearch');
                            var prov=$('.intelListWrap').eq(0).find('[name=provinceStore]').val();
                            var city=wrap.find('#storeCity').val();
                            var arr='';
							var existArr= new Array(0);
							var newAdd= new Array(0);
							var newList= new Array(0);
                            for(i=0;i<store.size();i++){
                                var storeName=store.eq(i).val();
                                var storeId=store.eq(i).attr('id');
								var str=prov+'|'+city+'|'+storeName+'|'+storeId;
								newAdd.push(prov+'|'+city+'|'+storeName+'|'+storeId);
                            }
							if(existStr.size()>0){
								for(x=0; x<existStr.size();x++){
									existArr.push(existStr.eq(x).val());
								}
								for(n=0;n<newAdd.length;n++){
									for(z=0; z<existArr.length;z++){
										if(newAdd[n]==existArr[z]){
											newAdd.shift(newAdd[n]);
										}
									}
								}
								for(k=0;k<newAdd.length;k++){
									var strArr=newAdd[k].split("|");
									arr+='<li>'+strArr[0]+' - '+strArr[1]+' - '+strArr[2]+'<input type="hidden" name="storeArr" value="'+newAdd[k]+'" /><a href="javascript:void(0);" onclick="removeList(this)"> 删除 </a></li>';
								}
							}else{
								for(k=0;k<newAdd.length;k++){
									var strArr=newAdd[k].split("|");
									arr+='<li>'+strArr[0]+' - '+strArr[1]+' - '+strArr[2]+'<input type="hidden" name="storeArr" value="'+newAdd[k]+'" /><a href="javascript:void(0);" onclick="removeList(this)"> 删除 </a></li>';
								}
							}
						
                            //var box=$(obj).parent('.fitStoreWrap');
                            box.prepend(arr);
                            findStore.close();
                        }else{
                            alert('请选择店面');
                        }
                        return false;
                    },
                    focus:true
                });
                findStore.button({
                    name:'取消'
                });
            },
            cache: false
        });
    }

    function removeList(obj){
        $(obj).parents('li').remove();
    }

    function offShelf(obj,gid){
        var flag=confirm('该礼券下架后，用户可在前端页面不可见该礼券，您确定要下架该礼券吗？');
        if(flag){
            $.ajax({
                dataType:"json",
                url: '/index.php/gift/shelvesGift/id/'+gid,		
                success: function (data){
                    if(data.data){
                        alert("操作成功");
                        loadContent('',$('.intelListWrap'),wrap_url)
                    }else{
                        alert("操作失败")
                    }
                }
            })
        }
    }
	
	function offezShelf(obj,gid){
        var flag=confirm('该礼券下架后，用户可在前端页面不可见该礼券，您确定要下架该礼券吗？');
        if(flag){
            $.ajax({
                dataType:"json",
                url: '/index.php/gift/shelvesGift/id/'+gid,		
                success: function (data){
                    if(data.data){
                        alert("操作成功");
                        loadContent('',$('.intelListWrap'),wrap_url)
                    }else{
                        alert("操作失败")
                    }
                }
            })
        }
    }

    function publishCoupons(obj,gid){
        var flag=confirm('该礼券发布后，用户可在前端页面进行领取，您确定要发布该礼券吗？');
        if(flag){
            $.ajax({
                dataType:"json",
                url: '/index.php/gift/publishGift/id/'+gid,		
                success: function (data){
                    if(data.data){
                        alert("发布成功");
                        loadContent('',$('.intelListWrap'),wrap_url)
                    }else{
                        alert("发布失败")
                    }
                }
            })
        }
    }

    function editCoupons(obj,gid){
        var editCoupons=art.dialog({
            title:'修改礼券',
            id:'editCoupons',
            lock:true
        });
        $.ajax({
            url: '/index.php/gift/edit_gift/id/'+gid,		
            success: function (data){
                if(data=="礼券信息不存在"){
                    alert(data);
                    editCoupons.close();
                    return false;
                }
                editCoupons.content(data);
                editCoupons.button({
                    name:'保存',
                    callback:function(){
                        var flag=$('[name=couponsFlag]').val();
                        var gid=$('[name=gid]').val();						
						
                        if(flag=='store'){						
                            var gtype = 1;
                            var couponsName=$('.storeCoupons').find('[name=couponsName]');
                            //var couponsContent=$('.storeCoupons').find('[name=couponsContent]');
                            var couponsStore=$('.storeCoupons').find('[name=storeArr]');
                            var couponsSeries=$('.storeCoupons').find('[name=couponsSeries]:checked');						
                            var startDate=$('.storeCoupons').find('[name=startDate]');
                            var endDate=$('.storeCoupons').find('[name=endDate]');
                            var couponsGift=$('.storeCoupons').find('[name=couponsGift]');
                            var couponsNum=$('.storeCoupons').find('[name=couponsNum]');
							var optionTotalStr = $('.storeCoupons').find("#UsedNum").val();
							var optionNumStr = $('.storeCoupons').find('[name=couponsGift]').find("option:selected").attr("num");
							var couponsPlan=$('.storeCoupons').find('[name=couponsPlan]');
                            var sendDate = $(".storeCoupons").find('[name=sendDate]').val();
							var useTimeDay = $(".storeCoupons").find('[name=useTimeDay]').val();
							var couponsContent = CKEDITOR.instances.couponsContent1.getData();
                            if($getVal(couponsName)==''){
                                alert('请输入礼券名称');
                                return false;
                            }else if(couponsContent==''){
                                alert('请输入礼券内容');
                                return false;
                            }else if(couponsStore.size()==0){
                                alert('选择适用店面');
                                return false;
                            }else if(couponsSeries.size()==0){
                                alert('选择适用品类');
                                return false;
                            }else if($getVal(startDate)==''){
                                alert('请输入起始日期');
                                return false;
                            }else if($getVal(endDate)==''){
                                alert('请输入结束日期');
                                return false;
                            }else if(couponsGift.val()==0){
                                alert('选择关联奖品');
                                return false;
                            }else{
                                var strStore=new Array(0);
                                for(i=0;i<couponsStore.size();i++){
                                    var val=couponsStore.eq(i).val();
                                    strStore.push(val);
                                }
                                couponsStoreStr = strStore.toString();
					

                                var str=new Array(0);
                                for(i=0;i<couponsSeries.size();i++){
                                    var val=couponsSeries.eq(i).val();
                                    str.push(val);
                                }
                                cateData = str.toString();						
                                var num = parseInt(couponsNum.val());
                                var selectNum =  parseInt($('.storeCoupons').find("#num").val());
/*                                if(!selectNum){
                                    alert('请选择关联奖品');
                                    return false;
                                }*/
                                if(!num){
                                    alert('请输入礼券数量');
                                    return false;
                                }
							
								if(parseInt(num)>(parseInt(optionTotalStr)+parseInt(optionNumStr))){
									
									 alert('您填写的最大数量不能超过奖品数量');
										return false;
								
								}
								if($getVal(couponsPlan)==''){
								 alert('请输入礼券方案');
                               	 return false;
								}
								if(!sendDate){
								 	alert('请输入定时发布日期');
                               		return false;
								}
								if(useTimeDay==''){
								 	alert('请输入使用时间段限制');
                               		return false;
								}
/*                                var picImage = $("#picURl").val();
                                if(!picImage){
                                    alert('请上传礼品图片');
                                    return false;
                                }*/
                                $.ajax({
                                    url:"/index.php/gift/edit_gift/id/"+gid,
                                    type:"post",
                                    dataType:"json",
                                    data:{
                                        dosubmit:'yes',
                                        gname:couponsName.val(),
                                        //gcontent:couponsContent.val(),
										gcontent:couponsContent,
                                        couponsStore:couponsStoreStr,
                                        cateData:cateData,
                                        startDate:startDate.val(),
                                        endDate:endDate.val(),
                                        couponsGift:couponsGift.val(),
                                        couponsNum:couponsNum.val(),
                                        useTimeDay:useTimeDay,	
  										sendDate:sendDate,
										couponsPlan:couponsPlan.val(),
                                        couponsFlag:gtype,
                                        gid:gid
                                    },
                                    success: function(data){
                                        if(data.info==10000){
                                            alert(data.data)
                                            editCoupons.close()
                                            loadContent('',$('.intelListWrap'),wrap_url)
                                        }else{
                                            alert(data.data)
                                        }
                                    }
				
                                });
                            }
                        }else if(flag=='online'){
                            var gtype = 2;
                            var couponsPlat=$('.onlineCoupons').find('[name=couponsPlat]');
						
                            //alert(couponsPlat.val());
                            //return false;
                            var couponsName=$('.onlineCoupons').find('[name=couponsName]');
                            //var couponsContent=$('.onlineCoupons').find('[name=couponsContent]');
                            var couponsSeries=$('.onlineCoupons').find('[name=couponsSeries]:checked');
                            var startDate=$('.onlineCoupons').find('[name=startDate]');
                            var endDate=$('.onlineCoupons').find('[name=endDate]');
                            var couponsNum=$('.onlineCoupons').find('[name=couponsNum]');
							var couponsGift=$('.onlineCoupons').find('[name=couponsGift]');
							var optionTotalStr = $('.onlineCoupons').find("#UsedNum").val();
							var optionNumStr = $('.onlineCoupons').find('[name=couponsGift]').find("option:selected").attr("num");
							var moneyNum = $('.onlineCoupons').find('[name=money]').val();
							var colorStr = $(".onlineCoupons").find('[name=colorValue]:checked').val()
							var sendDate = $(".onlineCoupons").find('[name=sendDate]').val();
							var couponsContent = CKEDITOR.instances.couponsContent2.getData();
                            if(couponsPlat.val()==0){
                                alert('选择电商名称');
                                return false;
                            }else if($getVal(couponsName)==''){
                                alert('请输入礼券名称');
                                return false;
                            }else if(couponsContent==''){
                                alert('请输入礼券内容');
                                return false;
                            }else if(couponsSeries.size()==0){
                                alert('选择适用品类');
                                return false;
                            }else if($getVal(startDate)==''){
                                alert('请输入起始日期');
                                return false;
                            }else if($getVal(endDate)==''){
                                alert('请输入结束日期');
                                return false;
                            }else if(couponsGift.val()==0){
                                alert('选择关联奖品');
                                return false;
                            }else{
							
                                var str=new Array(0);
                                for(i=0;i<couponsSeries.size();i++){
                                    var val=couponsSeries.eq(i).val();
                                    str.push(val);
                                }
                                cateData = str.toString();						
                                var num = parseInt(couponsNum.val());
                                var selectNum =  parseInt($(".onlineCoupons").find("#num").val());

                                if(!num){
                                    alert('请输入礼券数量');
                                    return false;
                                }
							
								if(parseInt(num)>(parseInt(optionTotalStr)+parseInt(optionNumStr))){
									
									 alert('您填写的最大数量不能超过奖品数量');
										return false;
								
								}
								if(!moneyNum){
								    alert("请输入礼券金额");
									return false;
								}
                                if(!colorStr){
									alert("请选择优惠券背景颜色");
									return false;
								
								}
								if(!sendDate){
								 	alert('请输入定时发布日期');
                               		return false;
								}
                                $.ajax({
                                    url:"/index.php/gift/edit_gift/id/"+gid,
                                    type:"post",
                                    dataType:"json",
                                    data:{
                                        dosubmit:'yes',
                                        couponsPlat:couponsPlat.val(),
                                        gname:couponsName.val(),
                                        //gcontent:couponsContent.val(),
										gcontent:couponsContent,
                                        cateData:cateData,
                                        startDate:startDate.val(),
                                        endDate:endDate.val(),
										couponsGift:couponsGift.val(),
                                        couponsNum:couponsNum.val(), 
  										sendDate:sendDate,
									    color:colorStr,
										money:moneyNum,
                                        couponsFlag:gtype,
                                        gid:gid
                                    },
                                    success: function(data){
                                        if(data.info==10000){
                                            alert(data.data)
                                            editCoupons.close()
                                            loadContent('',$('.intelListWrap'),wrap_url)
                                        }else{
                                            alert(data.data)
                                        }
                                    }
				
                                });
                            }
                        }
                        return false;
                    },
                    focus: true
                });
                editCoupons.button({
                    name:'取消'
                });
            },
            cache: false
        });
    }

    function showCouponsInfo(obj,gid){
        var showCouponsInfo=art.dialog({
            title:'礼券详情',
            id:'showCouponsInfo',
            lock:true
        });
        $.ajax({
            url: '/index.php/gift/coupons_info/id/'+gid,
            success: function (data){
                showCouponsInfo.content(data);
                getUsedGift(gid)
            },
            cache: false
        });
    }
	
	
	function showEzCouponsInfo(obj,gid){
        var showCouponsInfo=art.dialog({
            title:'礼券详情',
            id:'showCouponsInfo',
            lock:true
        });
        $.ajax({
            url: '/index.php/ezstore/coupons_info/id/'+gid,
            success: function (data){
                showCouponsInfo.content(data);
                getEzUsedGift(gid);
            },
            cache: false
        });
    }
	
	function getEzUsedGift(gid){
        $.ajax({
            url: '/index.php/ezstore/getGiftUsedMember/id/'+gid+"/p/1",
            success: function (data){
                $(".getGiftUsedMember").html(data)
            },
            cache: false
        });
    }

    function getUsedGift(gid){
        $.ajax({
            url: '/index.php/gift/getGiftUsedMember/id/'+gid+"/p/1",
            success: function (data){
                $(".getGiftUsedMember").html(data)
            },
            cache: false
        });
    }


    function switchAddType(obj,box){
        box.show().siblings('.couponsEditBox').hide();
    }
    //文本素材  图片素材样式切换
    function textClass(){
        $(".mm_image").hide();
        $(".mm_text").show();
        $(".m_s").removeClass("m_image_tog").addClass("m_right");
    }
    function imageClass(){
        $(".mm_image").show();
        $(".mm_text").hide();
        $(".m_s").removeClass("m_right").addClass("m_image_tog");
    }
    function preAppQD(obj){
        var m_pre_con=$(obj).parent().find(".preappCon");
	
        if($(obj).is(':checked')){
            $(m_pre_con).prop('checked',true).attr('checked',true);
        }else{
            $(m_pre_con).prop('checked',false).attr('checked',false);
        }
    }
    function sucaiTextChange(){
        var num = $("#sucaiText").val().length;
        if( num > 0 ){
            $("#sucaiMulitText").val("");
        }
    //	setTimeout('sucaiTextChange()',100);	
    }

    function setCheckStyle(check){
        setInterval(function(){
            var checkbox=$('.'+check);
            if(checkbox.size()>=1){
                for(i=0;i<checkbox.size();i++){
                    var flag=checkbox.eq(i).is(':checked');
                    if(flag){
                        checkbox.eq(i).parents('li').addClass('listCurrent');
                    }else{
                        checkbox.eq(i).parents('li').removeClass('listCurrent');
                    }
                }
            }
        },500);
	
    }

    function selectfield(obj,box){
        var  reg = /(\d*)/;
        var  optionText = $(obj).find("option:selected").attr("num");
        box.find("#num").val(optionText);
    }

    function numcheck(obj){
        var num = parseInt($(obj).val());
        var selectNum =  parseInt($("#num").val());
        if(!selectNum){
            alert('请选择关联奖品');
            $(obj).val('')
            return false;
        }
        if(!num){
            alert('请输入礼券数量');
            return false;
        }
	
        if(num>selectNum){
            alert('您填写的最大数量不能超过奖品数量');
            $(obj).val('')
            return false;
        }
	
    }

    function numcheck(obj,type){
        var num = parseInt($(obj).val());
	
        if(type==1){

		var optionTotalStr = $("#UsedNum").val();
		var optionNumStr = $('.storeCoupons').find('[name=couponsGift]').find("option:selected").attr("num");
			if(parseInt(num)>(parseInt(optionTotalStr)+parseInt(optionNumStr))){
				
			 alert('您填写的最大数量不能超过奖品数量');
               $(obj).val('')
               return false;
			}	
            
        }
	
	
        if(!num){
            alert('请输入礼券数量');
            return false;
        }
    }

    function loadProvince(){
        var area = $('#storeArea').val();
        $.ajax({
            url:'/index.php/common/region_list/',
            data:{
                type:1,
                key:area
            },
            dataType:'json',
            type:'post',
            success:function(data){
                $('#storeCity').html('<option value="0">全部</option>');
                $('#storeProvince').html(data.data);
            }
        });
	
    }

    function loadCity(){
        var province = $('#storeProvince').val();
        $.ajax({
            url:'/index.php/common/region_list/',
            data:{
                type:2,
                key:province
            },
            dataType:'json',
            type:'post',
            success:function(data){
                $('#storeCity').html(data.data);
            }
        });
    }

    function loadStoreCity(val){
        $.ajax({
            url:'/index.php/common/getCity/',
            data:{
                city:val
            },
            type:'post',
            success:function(data){
                $('#storeCity').html(data);
            }
        });
    }

    function loadStore(){
        var city = $('#storeCity').val();
        $.ajax({
            url:'/index.php/common/getStore/',
            data:{
                city:city
            },
            type:'post',
            success:function(data){
                $('#store').html(data);
            }
        });
    }
	
	function loadEzStore(){
        var city = $('#storeCity').val();
        $.ajax({
            url:'/index.php/common/getStore/',
            data:{
                city:city,type:3
            },
            type:'post',
            success:function(data){
                $('#store').html(data);
            }
        });
    }

    function searchRsp(){
        var stor_id = $('#store').val();
        if(stor_id > 0){
            loadContent('',$('.intelListWrap'),'/index.php/rsp/rsp_list/stor_id/' + stor_id)
        }else{
            alert('请选择条件之后搜索');
        }
    }

    function loadStoreData(){
        val = $("#storeCity").val();
        if(!val){
            alert("请选择城市");
        }
        $.ajax({
            url:'/index.php/common/getStoreData/',
            data:{
                store:val
            },
            type:'post',
            success:function(data){
                $('#intelListWrap').html(data);
            }
        });
    }

    function loadmemberCity(){
        var fid = $('#memberProvince').val();
		if(fid != 0){
			$.ajax({
				url:'/index.php/common/location_list/',
				data:{
					fid:fid
				},
				dataType:'json',
				type:'post',
				success:function(data){
					$('#memberCity').html(data.data);
				}
			});
		}else{
			$('#memberCity').html('<option value="0">全部</option>');
		}
        countMemberNum();
    }

    function delRsp(obj,id){
        var box=$(obj).parents('li');
        var foo=confirm('该RSP会员删除后将无法恢复，您确定要删除该RSP会员吗？');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/store/delete_rsp/id/'+ id,			
                dataType:'json',
                type:'get',
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
	
	function delEzRsp(obj,id){
        var box=$(obj).parents('li');
        var foo=confirm('该RSP会员删除后将无法恢复，您确定要删除该RSP会员吗？');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/ezstore/delete_rsp/id/'+ id,			
                dataType:'json',
                type:'get',
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

    function countMemberNum(){
        var memberProvince = $('#memberProvince').val();
        var memberCity = $('#memberCity').val();
        var memberType = $('#memberType').val();
        var memberCount = $('#memberCount');
        $.ajax({
            url:'/index.php/member/memberCount/',
            data:{
                memberProvince:memberProvince,
                memberCity:memberCity,
                memberType:memberType
            },
            dataType:'json',
            type:'post',
            success:function(data){
                if(data.status == true){
                    $('#totalNum').html(data.data);
                }else{
                    art.dialog.alert('发生错误，请不要攻击系统！');
                }
				
            }
        });

    }
    //添加电商
    function addBusiness(){
        process=showloading('loading...');
        $.ajax({
            url: '/index.php/business/add_business',
            success: function (data){
                process.close();
                art.dialog({
                    title: '添加电商',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }
    //编辑电商
    function editBusiness(obj){
        process=showloading('loading...');
        var businessid = $(obj).attr('businessid');
        $.ajax({
            url: '/index.php/business/edit_business/businessid/'+businessid,
            success: function (data){
                process.close();
                art.dialog({
                    title: '编辑电商',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }
    //修改赛程
    function edit_fighting(obj,type){
        process=showloading('loading...');
        var sort = $(obj).attr('fighting_id');
        $.ajax({
            url: '/index.php/fighting/sel_fighting/sort/'+sort+'/type/'+type,
            success: function (data){
                process.close();
                art.dialog({
                    title: '编辑信息',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }  
    //导出excel观众名单
    function output(type){
        process=showloading('loading...');
        $.ajax({
            url: '/index.php/spectator/output_ex/',
            success: function (data){
                process.close();
                art.dialog({
                    title: '观众名单',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }
    //添加学校，战队，城市
    function addmsg(type){
        process=showloading('loading...');
        $.ajax({
            url: '/index.php/Gaming16/add/type/'+type,
            success: function (data){
                process.close();
                art.dialog({
                    title: '添加信息',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }
    //编辑学校
    function editmsg(obj,type){
        process=showloading('loading...');
        var id = $(obj).attr(type+'_id');
        $.ajax({
            url: '/index.php/Gaming16/edit/'+type+'_id/'+id+'/type/'+type,
            success: function (data){
                process.close();
                art.dialog({
                    title: '编辑学校',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }    
    //删除学校，战队，城市信息
    function DelMsg(obj,type){
        var box=$(obj).parents('li');
        var title;
        switch(type)
        {
        case 'city':
        	title='城市';
        	break;
        case 'school':
        	title='学校';
        	break;
        default:
        	title='战队';
        }
        var foo=confirm('您确定要删除该'+title+'吗？');
        var id = $(obj).attr(type+'_id');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/Gaming16/delmsg/',
                data:'id='+id+'&type='+type,
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
    //修改签到人员弹窗
    function edit_scan(obj){
        process=showloading('loading...');
        var id = $(obj).attr('scan_id');
        $.ajax({
            url: '/index.php/scan/edit_scan/id/'+id,
            success: function (data){
                process.close();
                art.dialog({
                    title: '编辑信息',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }  
    //添加签到人员
    function add_scan(){
        process=showloading('loading...');
        $.ajax({
            url: '/index.php/scan/addmsg',
            success: function (data){
                process.close();
                art.dialog({
                    title: '添加信息',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }
    //删除签到人员
    function del_scan(obj){
        var box=$(obj).parents('li');
        var foo=confirm('您确定要删除该人员信息吗？');
        var id = $(obj).attr('scan_id');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/scan/del_scan/',
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
    //修改领奖扫描人员弹窗
    function edit_scanPrize(obj){
        process=showloading('loading...');
        var id = $(obj).attr('scanPrize_id');
        $.ajax({
            url: '/index.php/scanPrize/edit_scanPrize/id/'+id,
            success: function (data){
                process.close();
                art.dialog({
                    title: '编辑信息',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }  
    //添加领奖扫描人员
    function add_scanPrize(){
        process=showloading('loading...');
        $.ajax({
            url: '/index.php/scanPrize/addmsg',
            success: function (data){
                process.close();
                art.dialog({
                    title: '添加信息',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }
    //删除领奖扫描人员
    function del_scanPrize(obj){
        var box=$(obj).parents('li');
        var foo=confirm('您确定要删除该人员信息吗？');
        var id = $(obj).attr('scanPrize_id');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/scanPrize/del_scanPrize/',
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
    //修改入场限制弹窗
    function edit_limit(obj){
        process=showloading('loading...');
        var id = $(obj).attr('limit_id');
        $.ajax({
            url: '/index.php/limit/edit_limit/id/'+id,
            success: function (data){
                process.close();
                art.dialog({
                    title: '编辑信息',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }  
    //添加入场人员限制
    function add_limit(){
        process=showloading('loading...');
        $.ajax({
            url: '/index.php/limit/addmsg',
            success: function (data){
                process.close();
                art.dialog({
                    title: '添加限制人数',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }
    //添加媒体信息
    function add_media(){
        process=showloading('loading...');
        $.ajax({
            url: '/index.php/media/add/',
            success: function (data){
                process.close();
                art.dialog({
                    title: '添加电商',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }    
    //删除媒体信息
    function del_media(obj){
        var box=$(obj).parents('li');
        var foo=confirm('您确定要删除该媒体信息吗？');
        var media_id = $(obj).attr('media_id');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/media/del_media/',
                data:'id='+media_id,
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
    //删除战队成员
    function del_player(obj){
        var box=$(obj).parents('li');
        var foo=confirm('您确定要删除该成员信息吗？');
        var player_id = $(obj).attr('player_id');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/Gaming16/del_player/',
                data:'id='+player_id,
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
    //添加参赛选手
    function add_player(){
        process=showloading('loading...');
        $.ajax({
            url: '/index.php/Gaming16/add_player/',
            success: function (data){
                process.close();
                art.dialog({
                    title: '添加参赛选手',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }   
    //删除电商
    function listDelBusiness(obj){
        var box=$(obj).parents('li');
        var foo=confirm('您确定要删除该电商吗？');
        var businessid = $(obj).attr('businessid');
        var business_name = $(obj).attr('business_name');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/business/del_business/',
                data:'businessid='+businessid+'&business_name='+business_name,
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
    //媒体。观众手动签到
    function manual_sign_in(obj,type,id){
        var box=$(obj).parents('li');
        if(type == 'media'){
        	var foo=confirm('您确定要将该媒体人置为“已签到”吗？');
        }else if(type == 'spectator'){
        	var foo=confirm('您确定要将该观众置为“已签到”吗？');
        }else if(type == 'tencentUser'){
        	var foo=confirm('您确定要将该腾讯网友置为“已签到”吗？');
        }
        if(foo){
        	var url = '/index.php/'+type+'/manual_sign_in/'
            process=showloading('loading...');
            $.ajax({
                url: url,
                data:'type='+type+'&id='+id,
                dataType:'json',
                type:'post',
                success: function (data){
                    process.close();
                    if(data.status == 1){
                        alert('签到成功');
                        location.href = location.href;
                    }else if(data.status == 3){
                        alert('超过入场人数限制！');
                    }else if(data.status == 0){
                        alert('签到失败');
                    }
                },
                cache: false
            });
        }
    }
    //修改抽奖活动开始结束时间
    function edit_lottery(obj,type){
        var box=$(obj).parents('li');
        var foo=confirm('确定'+type+'该活动？');
        var id = $(obj).attr('lottery_id');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/gaminglottery/edit_state/',
                data:'id='+id+'&type='+type,
                dataType:'json',
                type:'post',
                success: function (data){
                    process.close();
                    if(data.status){
                        alert(type+'成功');
                        location.href = location.href;
                    }else{
                        alert(type+'失败');
                    }
                },
                cache: false
            });
        }
    }
    //添加决赛回合
    function add_ante(){
        process=showloading('loading...');
        $.ajax({
            url: '/index.php/ante/addmsg',
            success: function (data){
                process.close();
                art.dialog({
                    title: '添加信息',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }
  //删除决赛回合
    function del_ante(obj){
        var box=$(obj).parents('li');
        var foo=confirm('您确定要删除该比赛回合？这样将不能进行此轮抽奖');
        var id = $(obj).attr('ante_id');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/ante/del_ante/',
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
    //修改签到人员弹窗
    function edit_ante(obj){
        process=showloading('loading...');
        var id = $(obj).attr('ante_id');
        $.ajax({
            url: '/index.php/ante/edit_ante/id/'+id,
            success: function (data){
                process.close();
                art.dialog({
                    title: '编辑信息',
                    content:data,
                    lock:true
                });
            },
            cache: false
        });
    }  
    /**
 * 首页礼券添加设置
**/
    function addGiftSet(){
        var findStore=art.dialog({
            title:'首页热门礼券设置',
            id:'findStore',
            lock:true
        });
		
        $.ajax({
            url: '/index.php/gift/getPublishGift/',
            dataType:"html",
            success: function (data){
                findStore.content(data);
                findStore.button({
                    name:'确定',
                    callback:function(){
                        var store=$('[name=checkStore]:checked');
                        if(store.size()>0){
                            var wrap=$('#storeSearch');
                            var prov=wrap.find('#storeProvince').val();
                            var city=wrap.find('#storeCity').val();
                            var arr= new Array();
                            for(i=0;i<store.size();i++){
                                var storeName=store.eq(i).val();
                                var storeId=store.eq(i).attr('id');
                                arr.push(storeId);
                            }
                            idStr = arr.toString();						
                            $.ajax({
                                url: '/index.php/gift/setGiftHot/',
                                data:{
                                    idStr:idStr
                                },
                                dataType:'json',
                                success: function (data){
                                    if(data.info==10000){
                                        alert(data.data);
                                        window.location.href="/index.php/h5manage/chip_set";
                                    }else{
                                        alert(data.data);
                                    }
                                },
                                cache: false
                            });
                        //findStore.close();
                        }else{
                            alert('请选择添加礼券');
                        }
                        return false;
                    },
                    focus:true
                });
                findStore.button({
                    name:'取消'
                });
            },
            cache: false
        });
    }
    function findGIftHot(){
        var gtype=$('[name=gtype]').val();
        var keyword=$('[name=keyword]').val();//
        $.ajax({
            url: '/index.php/gift/getPublishGift/',
            data:{
                gtype:gtype,
                keyword:keyword
            },
            dataType:"html",
            success: function (data){
		
                $(".findStoreWrap").html(data);
		
            }
        })

    }

    function setCouponsInfo(obj,gid){

        var box=$(obj).parents('li');
        var foo=confirm('您确定要移除该礼券吗？');
        if(foo){
            process=showloading('loading...');
            $.ajax({
                url: '/index.php/gift/removeHot/id/'+gid,
                dataType:'json',
                type:'post',
                success: function (data){
                    process.close();
                    if(data.status){
                        alert('移除成功');
                        box.slideUp(1000,function(){
                            $(this).remove();
                        });
                    }else{
                        alert('移除失败');
                    }
                },
                cache: false
            });
        }
    }
	
	function updateSpread(id)
	{
		process=showloading('loading...');
        $.ajax({
            url: '/index.php/spread/update/id/'+id,
            success: function (data){
                process.close();
                art.dialog({
                    title: '更新二维码',
                    content:data,
					ok:function(){
						var id = $('#id').val();
						var name = $('#name').val();
						var instruction = $('#instruction').val();
						$.post('/index.php/spread/doUpdateSpread', {
								id:id,
								name:name,
								instruction:instruction
							}, function(data) {
								alert('更新成功');
							}, 'json');
						loadContent('',$('.intelListWrap'),'/index.php/spread/list_spread');
					},
					cancel:function(){},
					okVal:'保存',
					cancel:'取消',
                    lock:true
                });
				
            },
            cache: false
        });
	}
	
	function detailSpread(id)
	{
		process=showloading('loading...');
        $.ajax({
            url: '/index.php/spread/detail/id/'+id,
            success: function (data){
                process.close();
                art.dialog({
                    title: '二维码详情',
                    content:data,
					ok:function(){},
                    lock:true
                });
            },
            cache: false
        });
	}
	
	function deleteSpread(id,qr_id)
	{
		var result = window.confirm('是否删除？');
		if(result)
		{
			process=showloading('loading...');
			$.ajax({
				url: '/index.php/spread/delete/id/'+id+'/qr_id/'+qr_id,
				success: function (data){
					process.close();
				alert('删除成功');
				loadContent('',$('.intelListWrap'),'/index.php/spread/list_spread');
				},
				cache: false
			});
		}
	}

    function editRsp(rsp_id){
        var url = '/index.php/rsp/rsp_edit/rsp_id/' + rsp_id;
        process=showloading('loading...');
        var art1;
        $.ajax({
            url:url,
            async: false,
            success:function(data){
                process.close();
                art1 = art.dialog({
                    title:'RSP信息修改',
                    content:data,
                    lock:true,
                    ok:function(){
                        submitRspInfo(url);
                        art1.close();
                    },
                    okVal:'确定',
                    cancel:true,
                    cancelVal:'取消'
                });
            },
            cache: false
        });
    }

    function submitRspInfo(url){
        var user_name=$('#user_name').val();
        var role_id=$('#role_id').val();
        var tel=$('#tel').val();
        var email=$('#email').val();
		var emailReg=/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
		/* if(!emailReg.test(email)){
			art.dialog.alert("请填写正确的邮箱");
			return false;
		}; */
        var stor_id=$('#store').val();
        var lock_update = $('#lock_update:checked').val();
        $.ajax({
            url:url,
            async: false,
            data:{
                user_name:user_name,
                role_id:role_id,
                tel:tel,
                email:email,
                stor_id:stor_id,
                lock_update:lock_update,
                submit:'提交'
            },
            type:'post',
            dataType:'json',
            success:function(data){
                if(data.status == true){
                    alert('成功');
                    //loadContent('',$('.intelListWrap'),wrap_url);
					location.href = location.href;
                }else{
                    alert('失败');
                }
            }
        });
    }
//积分管理规则
function useRule(){	
	var useTex=$('[name=useTex]');
	var useTexVal=$('[name=useTex]').val();
	if(useTexVal == '√'){
		useTex.val('×');
	}else{
		useTex.val('√');	
	}	
}

function selectShop(obj,box){
	var val = $(obj).val();
	var url = '/index.php/gift/getAwardType/type/' + val;
	$.ajax({
		url:url,
		success:function(data){
		  box.find("[name=couponsGift]").html(data)
		},
		cache: false
	});
}

function checkAllStore(obj){
	var ck=$(obj).parents('ul').find('[name=checkStore]');
	var flag=$(obj).is(':checked');
	for(i=0;i<ck.size();i++){
		if(flag){
			ck.eq(i).attr('checked',true);
		}else{
			ck.eq(i).attr('checked',false);
		}
	}
	
}

function checkNationWide(obj,box,btn){
	var hideStoreList=$('.hideStoreList');
	var flag=$(obj).is(':checked');
	if(flag){
		var existStore=box.html();
		hideStoreList.html(existStore);
		box.html('<li><input type="hidden" value="北京|北京市|北京市店铺|0" name="storeArr">北京市店铺</li><li><input type="hidden" value="上海|上海市|上海市店铺|0" name="storeArr">上海市店铺</li><li><input type="hidden" value="广州|广州市|广州市店铺|0" name="storeArr">广州市店铺</li><li><input type="hidden" value="辽宁|沈阳市|沈阳店铺|0" name="storeArr">沈阳市店铺</li>');
		btn.hide();
	}else{
		var existStore=hideStoreList.html();
		box.html(existStore);
		btn.show();
	}
}

function selectAllItems(obj){
	var flag=$(obj).is(':checked');
	var item=$(obj).parents('ul').find('[name=checkedItem]');
	for(i=0;i<item.size();i++){
		if(flag){
			item.eq(i).attr('checked',true);
		}else{
			item.eq(i).attr('checked',false);
		}
	}
}

function deleteBatchItem(obj){
	var item=$(obj).parents('ul').find('[name=checkedItem]:checked');
	if(item.size()==0){
		alert('没有选中任何产品');
	}else{
		var flag=confirm('您确定要删除这些产品么？');
		if(flag){
			var ids = '0';
			for(i=0;i<item.size();i++){
				ids += ','+item.eq(i).val();
			}
			$.ajax({
				url:'/index.php/h5manage/delete_chip/id/'+ids,			
				dataType:'json',
				type:'get',
				success: function (data){										
					alert(data.info);								
					if(data.status){
						location.href = location.href;
					}
				},				
				cache: false
			});
		}
	}
}
//批量移除试用
function deleteTrialItem(obj){
	var item=$(obj).parents('ul').find('[name=checkedItem]:checked');
	if(item.size()==0){
		alert('没有选中任何产品');
	}else{
		var flag=confirm('您确定要删除这些产品么？');
		if(flag){
			var ids = '0';
			for(i=0;i<item.size();i++){
				ids += ','+item.eq(i).val();
			}
			$.ajax({
				url:'/index.php/trial/delete_trial/id/'+ids,			
				dataType:'json',
				type:'get',
				success: function (data){										
					alert(data.info);								
					if(data.status){
						location.href = location.href;
					}
				},				
				cache: false
			});
		}
	}
}
//批量移除申请
function deleteApplyItem(obj){
	var item=$(obj).parents('ul').find('[name=checkedItem]:checked');
	if(item.size()==0){
		alert('没有选中任何申请信息');
	}else{
		var flag=confirm('您确定要删除这些申请么？');
		if(flag){
			var ids = '0';
			for(i=0;i<item.size();i++){
				ids += ','+item.eq(i).val();
			}
			$.ajax({
				url:'/index.php/trial/delete_apply/id/'+ids,			
				dataType:'json',
				type:'get',
				success: function (data){										
					alert(data.info);								
					if(data.status){
						location.href = location.href;
					}
				},				
				cache: false
			});
		}
	}
}
//队员机票图片添加提交
function commonUploadPic_ticket(f,w,h) {
	var file = $(f).val();
	var hide=$(f).siblings('.picURL_hide1');
	var upId=$(f).attr('id');
	var img=$(f).parents('.proImgWrap').find('.preview');
	//转换文件名为小写
	file = file.toLowerCase();
	//检测文件类型必须是图片
	if (!/.(gif|jpg|jpeg|png)$/.test(file)) {
		alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
	} else {
		$.ajaxFileUpload({
			url: '/index.php/common/upload/w/'+w+'/h/'+h, //服务器端程序
			secureuri: false,
			fileElementId: upId, //input框的ID
			dataType: 'json', //返回数据类型
			success: function(data) {//上传成功
				if(data.status){
					hide.val(data.data);
					//previewImage(f);
					img.html('<img id="imghead1" src="'+data.data+'" />');
				}
			}
		});
	}
}
//队员机票图片添加弹窗
function add_player_ticket(id){
	process=showloading('loading...');
    $.ajax({
        url: '/index.php/Gaming16/add_player_ticket/'+'id/'+id,
        success: function (data){
            process.close();
            art.dialog({
                title: '上传图片',
                content:data,
                lock:true
            });
        },
        cache: false
    });
}
//机票，酒店查看
function show_pic(type,id){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/Gaming16/show_pic/type/'+type+'/id/'+id,
		success: function (data){
			process.close();
			art.dialog({
				title: '查看机票/酒店信息',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//媒体机票，酒店查看
function show_media_pic(type,id){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/media/show_pic/type/'+type+'/id/'+id,
		success: function (data){
			process.close();
			art.dialog({
				title: '查看机票/酒店信息',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//添加酒店信息
//h5首页轮播图添加
function add_hotel(){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/Gaming16/add_hotel',
		success: function (data){
			process.close();
			art.dialog({
				title: '添加轮播图片',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//媒体酒店图片添加跳页
function add_media_hotel(){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/media/add_media_hotel',
		success: function (data){
			process.close();
			art.dialog({
				title: '添加轮播图片',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//媒体机票图片添加弹窗
function add_media_ticket(id){
	process=showloading('loading...');
    $.ajax({
        url: '/index.php/media/add_media_ticket/'+'id/'+id,
        success: function (data){
            process.close();
            art.dialog({
                title: '上传图片',
                content:data,
                lock:true
            });
        },
        cache: false
    });
}//添加赛程图片弹窗
function add_race(){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/race/race_add',
		success: function (data){
			process.close();
			art.dialog({
				title: '添加赛程图片',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//赛程图片删除
function delete_race(id){
	$.ajax({
		url: '/index.php/race/race_delete/id/'+id,
		dataType:'json',
		success: function (data){			
			alert(data.info);
			if(data.status){
				location.href = location.href;
			}			
		},
		cache: false
	});
}
//h5首页轮播图添加
function addFocusmap(){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/h5manage/focusmap_add',
		success: function (data){
			process.close();
			art.dialog({
				title: '添加轮播图片',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}

//h5首页轮播图修改
function editrace(id){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/race/race_edit/id/'+id,
		success: function (data){
			process.close();
			art.dialog({
				title: '修改轮播图片',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//h5首页轮播图删除
function deleteFocusmap(id){
	//process=showloading('loading...');
	$.ajax({
		url: '/index.php/h5manage/focusmap_delete/id/'+id,
		dataType:'json',
		success: function (data){			
			alert(data.info);
			if(data.status){
				location.href = location.href;
			}			
		},
		cache: false
	});
}

function uploadFocusmapPic(f) {
	var file = $(f).val();
	var hide=$(f).siblings('#picURl');
	var upId=$(f).attr('id');
	var img=$('.proImgWrap').find('#preview');
	//转换文件名为小写
	file = file.toLowerCase();
	//检测文件类型必须是图片
	if (!/.(gif|jpg|jpeg|png)$/.test(file)) {
		alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
	} else {
		$.ajaxFileUpload({
			url: '/index.php/h5manage/uploadFocusmap', //服务器端程序
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

//h5首页产品系列添加
function addSeries(){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/h5manage/series_add',
		success: function (data){
			process.close();
			art.dialog({
				title: '添加产品系列',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}

//h5首页产品系列修改
function editSeries(id){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/h5manage/series_edit/id/'+id,
		success: function (data){
			process.close();
			art.dialog({
				title: '修改产品系列',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//h5首页产品系列删除
function deleteSeries(id){
	//process=showloading('loading...');
	$.ajax({
		url: '/index.php/h5manage/series_delete/id/'+id,
		dataType:'json',
		success: function (data){			
			alert(data.info);
			if(data.status){
				location.href = location.href;
			}
		},
		cache: false
	});
}
//产品系列首页显示图片上传01
function uploadSeries(f,w,h) {
	var file = $(f).val();
	var hide=$(f).siblings('.picURL_hide');
	var upId=$(f).attr('id');
	var img=$(f).parents('.proImgWrap').find('.preview');
	//转换文件名为小写
	file = file.toLowerCase();
	//检测文件类型必须是图片
	if (!/.(gif|jpg|jpeg|png)$/.test(file)) {
		alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
	} else {
		$.ajaxFileUpload({
			url: '/index.php/common/upload/w/'+w+'/h/'+h, //服务器端程序
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

//产品系列首页显示图片上传01
function commonUploadPic(f,w,h) {
	var file = $(f).val();
	var hide=$(f).siblings('.picURL_hide');
	var upId=$(f).attr('id');
	var img=$(f).parents('.proImgWrap').find('.preview');
	//转换文件名为小写
	file = file.toLowerCase();
	//检测文件类型必须是图片
	if (!/.(gif|jpg|jpeg|png)$/.test(file)) {
		alert("图片类型必须是.gif,jpeg,jpg,png中的一种");
	} else {
		$.ajaxFileUpload({
			url: '/index.php/common/upload/w/'+w+'/h/'+h, //服务器端程序
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

function confirmUser(obj,id){
	var box=$(obj).parents('li');
	$.ajax({
		url: '/index.php/ride/authbox/id/'+id+'/up/0',
		success: function (data){
			art.dialog({
				title: '认证信息',
				id:'confirmUser',
				content:data,
				ok:function(){
					var box = $('#userbox');
					var openid = box.find('input[name=openid]').val();
					var name = box.find('input[name=name]').val();
					var gender = box.find(':input[name=gender]:checked').val();
					var province = box.find('#storeProvince').val();
					var city = box.find('#storeCity').val();
					var department = box.find(':input[name=department]').val();
					var email = box.find('input[name=email]').val();
					var tel = box.find('input[name=tel]').val();
					var phone = box.find('input[name=phone]').val();
					if(province==0){
						alert('请选择省份');
						return false;
					}
					
					if(city==0){
						alert('请选择城市');
						return false;
					}
					$.ajax({
						url:'/index.php/ride/authbox_do',
						data:{
							openid:openid,
							name:name,
							gender:gender,
							province:province,
							city:city,
							department:department,
							tel:tel,
							phone:phone,
							email:email,
						},
						dataType:'json',
						type:'post',
						success:function(data){
							alert(data.info);
							if(data.status){
								location.href = location.href;
							}
						}
					});
					return false;
				},
				okVal:'认证',
				cancel:true,
				cancelVal:'取消',
				lock:true
			});
		},
		cache: false
	});
}
function editExistUser(obj,id){
	var box=$(obj).parents('li');
	$.ajax({
		url: '/index.php/ride/authbox/id/'+id,
		success: function (data){
			art.dialog({
				title: '修改认证信息',
				id:'editExistUser',
				content:data,
				ok:function(){
					var box = $('#userbox');
					var openid = box.find('input[name=openid]').val();
					var name = box.find('input[name=name]').val();
					var gender = box.find(':input[name=gender]:checked').val();
					var province = box.find('#storeProvince').val();
					var city = box.find('#storeCity').val();
					var department = box.find(':input[name=department]').val();
					var email = box.find('input[name=email]').val();
					var tel = box.find('input[name=tel]').val();
					var phone = box.find('input[name=phone]').val();					
					if(province==0){
						alert('请选择省份');
						return false;
					}
					
					if(city==0){
						alert('请选择城市');
						return false;
					}
					$.ajax({
						url:'/index.php/ride/authbox_do',
						data:{
							openid:openid,
							name:name,
							gender:gender,
							province:province,
							city:city,
							department:department,
							tel:tel,
							phone:phone,
							email:email,
							up:1
						},
						dataType:'json',
						type:'post',
						success:function(data){
							alert(data.info);
							if(data.status){
								location.href = location.href;
							}
						}
					});
				},
				okVal:'保存',
				cancel:true,
				cancelVal:'取消',
				lock:true
			});
		},
		cache: false
	});
}
function switchTab(obj){

}

function fnAccept(obj,id){
	var flag=confirm('确定要采纳么？');
	if(flag){
		$.ajax({
			url:'/index.php/donate/acceptDonate/id/'+id,
			dataType:'json',
			type:'get',
			success:function(data){
				alert(data.info);
				if(data.status){
					location.href = location.href;
				}
			}
		});
	}
}

function fnTopicDetail(obj){
	var ttl=$(obj).siblings('.topicTitle').html();
	var img=$(obj).siblings('#hideImg').val();
	var imgCont='';
	if(img!=''){
		imgCont='<img src="'+img+'" />';
	}
	var content=$(obj).siblings('#hideContent').val();
	var msg='<p>'+ttl+'</p><p class="topicDetail">'+content+imgCont+'</p>'
	art.dialog({
		title: '内容奉献详情',
		content:msg,
		width:500,
		ok:true,
		okVal:'关闭',
		lock:true
	});
}

function fnCallFansList(id){
	myWindow=window.open('/index.php/membergroup/filtrate_list/group_id/'+id,'fansList','width=1000,height=500,scrollbars=yes');
	myWindow.focus();
}
function fnSetNoneOption(self){
	var self=$(self);
	var bro=self.parents('label').siblings('label').find('[type=checkbox]');
	var flag=self.is(':checked');
	for(i=0;i<bro.size();i++){
		if(flag){
			bro.eq(i).attr('checked',false).attr('disabled',true);
		}else{
			bro.eq(i).attr('disabled',false);
		}
	}
}

function fnSaveGroup(){
		var groupName=$getVal($('[name=groupName]'));
		var groupDes=$getVal($('[name=groupDes]'));
		if(groupName==''){
			alert('请输入分组名称');
			return false;
		}else if(groupDes==''){
			alert('请填写分组说明');
			return false;
		}

		$('#form1').submit();
	}
function fnSaveProduct(){
		$('#form1').submit();
	}

function fnSetNoneRadio(self){
	var self=$(self);
	var bro=self.parents('label').siblings('label').find('[type=radio]');
	var flag=self.is(':checked');
	for(i=0;i<bro.size();i++){
		if(flag){
			bro.eq(i).attr('checked',false).attr('disabled',true);
		}else{
			bro.eq(i).attr('disabled',false);
		}
	}
}
function fnSetNoneSelect(self){
	var self=$(self);
	var bro=self.parents('label').siblings('select');
	var flag=self.is(':checked');
	for(i=0;i<bro.size();i++){
		if(flag){
			bro.eq(i).val('').attr('disabled',true);
		}else{
			bro.eq(i).attr('disabled',false);
		}
	}
}

function trim(val){
	return val.replace(/(^\s*)|(\s*$)/g, "");
}
//添加gaming观众
function add_JUNgamingspectator(){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/JUNgamingspectator/add/',
		success: function (data){
			process.close();
			art.dialog({
				title: '添加电商',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//删除媒体信息
function del_JUNgamingspectator(obj,id){
	var box=$(obj).parents('li');
	var foo=confirm('您确定要删除该观众信息吗？');
	var spectator_id = $(obj).attr('spectator_id');
	if(foo){
		process=showloading('loading...');
		$.ajax({
			url: '/index.php/JUNgamingspectator/del_spectator/',
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
    //媒体。观众手动签到
function JUNgaming_manual_sign_in(obj,id){
	var box=$(obj).parents('li');
	var foo=confirm('您确定要将该观众置为“已签到”吗？');
	if(foo){
		var url = '/index.php/JUNgamingspectator/manual_sign_in/'
		process=showloading('loading...');
		$.ajax({
			url: url,
			data:'id='+id,
			dataType:'json',
			type:'post',
			success: function (data){
				process.close();
				if(data.status == 1){
					alert('签到成功');
					location.href = location.href;
				}else if(data.status == 0){
					alert('签到失败');
				}
			},
			cache: false
		});
	}
}

function fnSplitCol(){
	var lis=$('.materialPicTxtWrap').find('.materialPicTxtList');
	var box1=$('.leftMaterial');
	var box2=$('.rightMaterial');
	lis.each(function(){
		var self=$(this);
		var he=self.height();
		var he1=box1.height();
		var he2=box2.height();
		if(he1<=he2){
			self.remove().appendTo(box1);
		}else{
			self.remove().appendTo(box2);
		}
	});
}
//添加领奖扫描人员
function add_JUNgamingscanPrize(){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/JUNgamingscanPrize/addmsg',
		success: function (data){
			process.close();
			art.dialog({
				title: '添加信息',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//修改领奖扫描人员弹窗
function edit_JUNgamingscanPrize(obj){
	process=showloading('loading...');
	var id = $(obj).attr('scanPrize_id');
	$.ajax({
		url: '/index.php/JUNgamingscanPrize/edit_scanPrize/id/'+id,
		success: function (data){
			process.close();
			art.dialog({
				title: '编辑信息',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}  
//删除领奖扫描人员
function del_JUNgamingscanPrize(obj){
	var box=$(obj).parents('li');
	var foo=confirm('您确定要删除该人员信息吗？');
	var id = $(obj).attr('scanPrize_id');
	if(foo){
		process=showloading('loading...');
		$.ajax({
			url: '/index.php/JUNgamingscanPrize/del_scanPrize/',
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
//添加签到人员
function add_JUNgamingscan(){
	process=showloading('loading...');
	$.ajax({
		url: '/index.php/JUNgamingscan/addmsg',
		success: function (data){
			process.close();
			art.dialog({
				title: '添加信息',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}
//删除签到人员
function del_JUNgamingscan(obj){
	var box=$(obj).parents('li');
	var foo=confirm('您确定要删除该人员信息吗？');
	var id = $(obj).attr('scan_id');
	if(foo){
		process=showloading('loading...');
		$.ajax({
			url: '/index.php/JUNgamingscan/del_scan/',
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
//修改签到人员弹窗
function edit_JUNgamingscan(obj){
	process=showloading('loading...');
	var id = $(obj).attr('scan_id');
	$.ajax({
		url: '/index.php/JUNgamingscan/edit_scan/id/'+id,
		success: function (data){
			process.close();
			art.dialog({
				title: '编辑信息',
				content:data,
				lock:true
			});
		},
		cache: false
	});
}  
//修改抽奖活动开始结束时间
function edit_JUNgaminglottery(obj,type,round){
	var box=$(obj).parents('li');
	var foo=confirm('确定'+type+'该活动？');
	var id = $(obj).attr('lottery_id');
	if(foo){
		process=showloading('loading...');
		$.ajax({
			url: '/index.php/JUNgaminglottery/edit_state/',
			data:'id='+id+'&type='+type+'&round='+round,
			dataType:'json',
			type:'post',
			success: function (data){
				process.close();
				if(data.status){
					alert(type+'成功');
					location.href = location.href;
				}else{
					alert(type+'失败');
				}
			},
			cache: false
		});
	}
}


$(".add_product").find("input[name='news_type']").live("click",function(){
    if($(this).val() == 5){
        $(".upload-img").show();
    }else{
        $(".upload-img").hide();
    }
})
//删除自动应答
function del_response(obj,id){
	var box=$(obj).parents('li');
	var foo=confirm('您确定要删除该应答消息？');
	if(foo){
		process=showloading('loading...');
		$.ajax({
			url: '/index.php/autoresponse/del_response/',
			data:'id='+id,
			dataType:'json',
			type:'post',
			success: function (data){
				process.close();
				if(data.status){
					alert('删除成功');
					/*box.slideUp(1000,function(){
						$(this).remove();
					});*/
					location.href="/index.php/autoresponse/autoresponse_manage";
				}else{
					alert('删除失败');
				}
			},
			cache: false
		});
	}
}
