document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	WeixinJSBridge.call('hideToolbar');
});
//微信分享相关函数
function getStrFromTxtDom(selector) {
	var url = jQuery('#txt-' + selector)
			.html()
			.replace(/&lt;/g, '<')
			.replace(/&gt;/g, '>');
	return jQuery.trim(url);
}
function report(link, fakeid, action_type) {
	var parse_link = parseUrl(link);
	if (parse_link == null)
	{
		return;
	}
	var query_obj = parseParams(parse_link['query_str']);
	query_obj['action_type'] = action_type;
	query_obj['uin'] = fakeid;
	var report_url = '/mp/appmsg/show?' + jQuery.param(query_obj);
	jQuery.ajax({
		url: report_url,
		type: 'POST',
		timeout: 2000
	});
}

function share_scene(link, scene_type) {
	var parse_link = parseUrl(link);
	if (parse_link == null)
	{
		return link;
	}
	var query_obj = parseParams(parse_link['query_str']);
	query_obj['scene'] = scene_type;
	var share_url = 'http://' + parse_link['domain'] + parse_link['path'] + '?' + jQuery.param(query_obj) + (parse_link['sharp'] ? parse_link['sharp'] : '');
	return share_url;
}
		
function showProMenu(obj,box,eve){
	var flag=box.is(':visible');
	if(flag){
		$(obj).removeClass('currentOn');
		box.removeClass('show');
		$('.mask').hide();
		tracking('ProductListMenu_fold');
	}else{
		$(obj).addClass('currentOn');
		box.addClass('show');
		$('.mask').show();
		tracking('ProductListMenu_display');
	}
}

function showCommercialMenu(obj,box,eve){
	eve.stopPropagation();
	var flag=box.is(':visible');
	if(flag){
		box.hide();
		$('.mask').hide();
	}else{
		box.show();
		$('.mask').show();
	}
}
		
$(function(){
	var tailWrap=$('.tailWrap');
	var footerWrap=$('.footerWrap');
	$.ajax({
		url:'/event/share_tail.html',
		success:function(data){
			tailWrap.html(data);
		}
	});
	$.ajax({
		url:'/event/footer.html?safsaf11111111111111',
		success:function(data){
			footerWrap.html(data);
		}
	});
	//index background and slogan effect start
	var mainBg=$('.mainBg');
	var indexSlogan=$('.indexSlogan');
	var sloganPointer=$('.sloganPointer');
	var bgLi=mainBg.children('li');
	var sloganLi=indexSlogan.children('li');
	bgLi.eq(0).show();
	sloganLi.eq(0).show();
	var bgNum=bgLi.size();
	if(bgNum>1){
		var str='<li class="currentPointer"></li>';
		for(i=1;i<bgNum;i++){
			str+='<li></li>';
		}
		sloganPointer.html(str);
		var pointerLi=sloganPointer.children('li');
		var n=z=0;
		var tsx=0;
		var tex=0;
		var indexSlogan=indexSlogan[0];
		if(indexSlogan){
			indexSlogan.ontouchstart=function(e){
				tsx=e.changedTouches[0].clientX;
				e.preventDefault();
			}
			indexSlogan.ontouchend=function(e){
				e.preventDefault();
				tex=e.changedTouches[0].clientX;
				if(tsx>tex && (tsx-tex)>50){
					n++;
					if(n>=bgNum){n=0}
					sloganLi.eq(n).fadeIn(500).siblings('li').fadeOut(100);
					bgLi.eq(n).fadeIn(500).siblings('li').fadeOut(100);
					pointerLi.eq(n).addClass('currentPointer').siblings('li').removeClass('currentPointer');
				}else if(tsx<tex && (tsx-tex)<-50){
					n--;
					if(n<0){n=bgNum-1}
					sloganLi.eq(n).fadeIn(500).siblings('li').fadeOut(100);
					bgLi.eq(n).fadeIn(500).siblings('li').fadeOut(100);
					pointerLi.eq(n).addClass('currentPointer').siblings('li').removeClass('currentPointer');
				}
			}
		}
	}
	
	var avHeight=document.documentElement.clientHeight;
	$('.main').css('min-height',avHeight-80);
	$('.container').css('min-height',avHeight-80);
	
	//banner satart
	var x=0
	var avWidth=document.documentElement.clientWidth;
	var bannerBox=$('.bannerBox');
	var bannerBase=$('.bannerBase');
	var bannerWrap=$('.bannerWrap');
	var bannerWrapClone=$('.bannerWrapClone');
	bannerWrapClone.html(bannerWrap.html());
	var bannerLi=bannerBase.find('li');
	var bannerP=bannerBase.find('p');
	var bannerNum=bannerLi.size()/2;
	bannerBox.css({'height':avWidth/2});
	bannerLi.css({'width':avWidth});
	if(bannerNum>2){
		t=setInterval(function(){
			x++;
			bannerBase.stop(true,false).animate({left:-(avWidth*x)},500,function(){
				if(x==(bannerNum)){
					x=0;
					bannerBase.css({'left':0});
				}
			});
		},5000);
		var ts=0;
		var te=0;
		var bannerBox=bannerBox[0];
		if(bannerBox){
			bannerBox.ontouchstart=function(e){
				ts=e.changedTouches[0].clientX;
			}
			bannerBox.ontouchend=function(e){
				clearInterval(t);
				te=e.changedTouches[0].clientX;
				if(ts>te && (ts-te)>10){
					x++;
					bannerBase.stop(true,false).animate({left:-(avWidth*x)},500,function(){
						if(x==(bannerNum)){
							x=0;
							bannerBase.css({'left':0});
						}
					});
				}else if(ts<te && (ts-te)<-10){
					//couponsBase.stop().animate({scrollLeft:'0'},100);
				}
			}
		}
	}
	//banner end
	
	var slideBox=$('.slideBox');
	slideBox.each(function(){
		var box=$(this);
		var slideBase=box.find('.slideBase');
		var slideHolder=box.find('.slideHolder');
		var slideLi=slideBase.find('li');
		var slideLength=slideLi.width()+10;
		var touchBase=slideBase[0];
		slideHolder.css('width',slideLength*slideLi.size());
		var ts1=0;
		var te1=0;
		var i=0;
		if(touchBase){
			touchBase.ontouchstart=function(e){
				e.stopPropagation();
				ts1=e.changedTouches[0].clientX;
			}
			touchBase.ontouchend=function(e){
				e.stopPropagation();
				te1=e.changedTouches[0].clientX;
				if(ts1>te1 && (ts1-te1)>10){
					i++;
					if(i>=slideLi.size()-2){
						i=slideLi.size()-2;
					}
					slideBase.stop(true,false).animate({scrollLeft:slideLength*i},100);
				}else if(ts1<te1 && (ts1-te1)<-10){
					i--;
					if(i<=0){
						i=0;
					}
					slideBase.stop(true,false).animate({scrollLeft:slideLength*i},100);
				}
			}
		}
	});
	/* $(window).scroll(function(){
		hideDialog();
	}); */
	$('.mask').click(function(){
		hideDialog();
	});
	
	var urlStr=window.location;
	var nav=$('.slideMenuWrap').children('li').children('a');
	if(nav.size()>0){
		nav.each(function(){
			var a=$(this);
			var lnk=new RegExp(a.attr('href').replace(/#weixin.qq.com/,''));
			if(lnk.test(urlStr)){
				a.addClass('currentNav');
			}
		});
	}
	$('.slideMenuWrap').mouseover(function(e){
		e.stopPropagation();
	});
	$('.searchWrap').mouseover(function(e){
		e.stopPropagation();
	});
	$('#proDropMenu').mouseover(function(e){
		e.stopPropagation();
	});
});

function shareFans(imgUrl,link,title,desc){
	var onBridgeReady = function() {
		appId = '',
		fakeid = "MjA3NTIxMDU=",
		desc = desc || link;
		if ("1" == "0") {
			WeixinJSBridge.call("hideOptionMenu");
		}
		jQuery("#post-user").click(function() {
			WeixinJSBridge.invoke('profile', {'username': 'gh_f10ac97bb079', 'scene': '57'});
		})
		// 发送给好友; 
		WeixinJSBridge.on('menu:share:appmessage', function(argv) {
			WeixinJSBridge.invoke('sendAppMessage', {
				"appid": appId,
				"img_url": imgUrl,
				"img_width": "640",
				"img_height": "640",
				"link": share_scene(link, 1),
				"desc": desc,
				"title": title
			});
			_gaq.push(['_trackEvent', 'share', '个人消息']); //分享给朋友触发的方法
		});
		// 分享到朋友圈;
		WeixinJSBridge.on('menu:share:timeline', function(argv) {
			WeixinJSBridge.invoke('shareTimeline', {
				"img_url": imgUrl,
				"img_width": "640",
				"img_height": "640",
				"link": share_scene(link, 2),
				"desc": 'share',
				"title": title,
			});
			_gaq.push(['_trackEvent', 'share', '朋友圈']);//分享到朋友圈触发的方法 
		});
		// 分享到腾讯微博;
		WeixinJSBridge.on('menu:share:weibo', function(argv) {
			WeixinJSBridge.invoke('shareWeibo', {
				"img_url": imgUrl,
				"img_width": "640",
				"img_height": "640",
				"link": share_scene(link, 2),
				"desc": 'share',
				"title": title,
			});
			_gaq.push(['_trackEvent', 'share', '群']);//分享到微博触发的方法
		});
	};
	if (document.addEventListener) {
		document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
	} else if (document.attachEvent) {
		document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
		document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
	}

}

function setIncome(num){
	var newIncome=$('.newIncome');
	var exist=parseInt(newIncome.html());
	if(isNaN(exist) || exist=='undefined'){
		exist=0;
	}
	if(exist+num>0){
		newIncome.html(exist+num).css({'display':'block'});
		$.get("/index.php/wapGift/memberChooseAdd/" ,{num:num} );
	}
}

function setOutgo(num){
	var newIncome=$('.newIncome');
	var exist=parseInt(newIncome.html());
	if(isNaN(exist) || exist=='undefined'){
		exist=0;
	}
	if(exist-num>0){
		newIncome.html(exist+num).css({'display':'block'});
	}else{
		newIncome.html(0).css({'display':'none'});
	}
	$.get("/index.php/wapGift/memberChooseMinus/", {num:num} );
}

function hideDialog(){
	var menu=$('#proDropMenu');
	var commercialDrop=$('.commercialDrop');
	var box=$('.searchWrap');
	var dialogWrap=$('.dialogWrap');
	var slide=$('.slideMenuWrap');
	var IDFMenuWrap=$('.IDFMenuWrap');
	if($('.pageTtl').size()!=0 && slide.size()!=0){
		$('.pageTtl').removeClass('currentOn');
		menu.removeClass('show');
		tracking('ProductListMenu_fold');
	}
	if($('.pageTtl').size()!=0 && IDFMenuWrap.size()!=0){
		$('.pageTtl').removeClass('currentOn');
		menu.removeClass('show');
	}
	if(box.size()!=0){
		box.hide();
	}
	if(slide.size()!=0){
		slide.hide();
	}
	if(commercialDrop.size()!=0){
		commercialDrop.hide();
	}
	if(IDFMenuWrap.size()!=0){
		IDFMenuWrap.hide();
	}
	if(dialogWrap.size()!=0){
		dialogWrap.hide();
	}
	$('.mask').hide();
}

function cancelBubble(eve){
	eve.stopPropagation();
}

//加载CSS或者JS方法
function zaLoadjscssfile(filename,filetype){
    if(filetype == "js"){
        var fileref = document.createElement('script');
        fileref.setAttribute("type","text/javascript");
        fileref.setAttribute("src",filename);
    }else if(filetype == "css"){
        var fileref = document.createElement('link');
        fileref.setAttribute("rel","stylesheet");
        fileref.setAttribute("type","text/css");
        fileref.setAttribute("href",filename);
    }
   if(typeof fileref != "undefined"){
        document.getElementsByTagName("head")[0].appendChild(fileref);
    }
}

function giftExceed(obj,id,event,pos){
	event.stopPropagation();
	jAlert('抱歉，该礼券已被抢光了，立即去领取其他礼券吧！','温馨提示');
	if(pos==1){
		tracking('CouponListGet');
	}else if(pos==2){
		tracking('CouponDetailGet');
	}
}
function giftExceedAttention(obj,id,event,pos){
	event.stopPropagation();
	openDialog();
	if(pos==1){
		tracking('CouponListGet');
	}else if(pos==2){
		tracking('CouponDetailGet');
	}
}

function giftNotStart(obj,id,event,pos){
	event.stopPropagation();
	jAlert('您好，此优惠暂未开始，感谢您的关注！','温馨提示');
	if(pos==1){
		tracking('CouponListGet');
	}else if(pos==2){
		tracking('CouponDetailGet');
	}
}
function giftNotStartAttention(obj,id,event,pos){
	event.stopPropagation();
	openDialog();
	if(pos==1){
		tracking('CouponListGet');
	}else if(pos==2){
		tracking('CouponDetailGet');
	}
}
function showDetail(url,pos){
	if(pos==1){
		tracking('CouponListPic');
	}else if(pos==2){
		tracking('CouponDetailOtherPic');
	}else if(pos=='c1'){
		tracking('CouponDetailMore');
	}else if(pos==5){
		tracking('StoreListDetail');
	}else if(pos==8){
		tracking('ProductListPic');
	}else if(pos==20){
		tracking('MyCouponGotDetailOtherPic');
	}else if(pos==22){
		tracking('MyCouponUsedDetailOtherPic');
	}else if(pos==30){
		tracking('FollowedProductPic');
	}else if(pos==31){
		tracking('LatestPic');
	}
	window.location.href=url;
}
function overdue(obj,id,event){
	event.stopPropagation();
	jAlert('抱歉，该礼券已过期，立即去领取其他礼券吧！','温馨提示');

}

function goBack(){
	tracking('Back_button');
	var backurl=document.referrer;
	if(backurl=='http://intelweixin.buzzopt.com' || backurl==''){
		window.location.href = '/';
	}else{
		history.go(-1);
	}
}

function goBackTest(alt){
    jAlert(alt, '温馨提示', function(){
        history.go(-1);
    });
}

function exitTest(){
    jAlert('返回到考试列表', '温馨提示',function(){
        window.location.href = "/index.php/wapTrain/my_train/";
    });
}

function goTestBack(alt){
    if(alt){
	jAlert(alt,'温馨提示', function(){
            var backUrl = document.referrer;
            if(backUrl){
                window.location.href='/index.php/wapTrain/';
            }else{
                window.location.href='/index.php/wapTrain/';
            }
        });
    }
}

function receive(obj,id,event,type,dayNum,pos){
	event.stopPropagation();
	var msgStr ='';
	if(dayNum){
		msgStr ="请在"+dayNum+"天内使用此礼券，";
	}
	$.ajax({
		url: '/index.php/wapGift/receive/id/'+id,
		dataType:"json",
		success: function (data){
			if(data.data){
				setIncome(1);
				jAlert('恭喜您，礼券领取成功！'+msgStr+'您可在<a href="/index.php/wapMyGift/">个人中心-我的礼券</a>中查看','温馨提示');
				if(type==2){
					$(obj).html("<i class='iconBox receivedBtnIcon'></i>已领取");
					$(obj).removeClass('addPic')
					$(obj).addClass("disabledBtn");
				}else{
					$(obj).html("<i class='iconBox receivedIcon'></i>已领取");
				}
				$(obj).removeAttr("onClick");
			}else{
				jAlert('操作失败','温馨提示');
			}
		},
		cache: false
	});
	if(pos==1){
		tracking('CouponListGet');
	}else if(pos==2){
		tracking('CouponDetailGet');
	}else if(pos==3){
		tracking('CouponDetailOtherGet');
	}else if(pos==20){
		tracking('MyCouponGotDetailOtherGet');
	}else if(pos==22){
		tracking('MyCouponUsedDetailOtherGet');
	}
}

function receiveAttention(obj,id,event,type,dayNum,pos){
	event.stopPropagation();
	openDialog();
	if(pos==1){
		tracking('CouponListGet');
	}else if(pos==2){
		tracking('CouponDetailGet');
	}else if(pos==3){
		tracking('CouponDetailOtherGet');
	}else if(pos==20){
		tracking('MyCouponGotDetailOtherGet');
	}else if(pos==22){
		tracking('MyCouponUsedDetailOtherGet');
	}
}

function collection(obj,id,event,type,pos){
	event.stopPropagation();
	$.ajax({
		url: '/index.php/wapGift/collection/id/'+id,
		dataType:"json",
		success: function (data){
			if(data.data){
				jAlert('恭喜您，礼券收藏成功！您可在<a href="/index.php/wapMyGift/collected/">个人中心-我的礼券</a>中查看','温馨提示');
				if(type==2){
					$(obj).html("<i class='iconBox addedFavBtnIcon cancelBtnIcon'></i>取消收藏");
					$(obj).attr("onClick","cancelCollected(this,"+id+",event,2)");
				}else{
					$(obj).html("<i class='iconBox addedFavIcon'></i>取消收藏");
					$(obj).attr("onClick","cancelCollected(this,"+id+",event,1)");
				}
			}else{
				jAlert('操作失败','温馨提示');
			}
		},
		cache: false
	});
	if(pos==1){
		tracking('CouponListRemove_Favorite');
	}else if(pos==2){
		tracking('CouponDetailRemove_Favorite');
	}else if(pos==3){
		tracking('CouponDetailOtherAdd_Favorite');
	}else if(pos==4){
		tracking('MyCouponGotListAdd_Favorite');
	}else if(pos==20){
		tracking('MyCouponGotDetailOtherAdd_Favorite');
	}else if(pos==21){
		tracking('MyCouponUsedListAdd_Favorite');
	}else if(pos==22){
		tracking('MyCouponUsedDetailOtherAdd_Favorite');
	}
}


function collectionAttention(obj,id,event,type,pos){
	event.stopPropagation();
 	openDialog();
	if(pos==1){
		tracking('CouponListRemove_Favorite');
	}else if(pos==2){
		tracking('CouponDetailRemove_Favorite');
	}else if(pos==3){
		tracking('CouponDetailOtherAdd_Favorite');
	}else if(pos==4){
		tracking('MyCouponGotListAdd_Favorite');
	}else if(pos==20){
		tracking('MyCouponGotDetailOtherAdd_Favorite');
	}else if(pos==21){
		tracking('MyCouponUsedListAdd_Favorite');
	}else if(pos==22){
		tracking('MyCouponUsedDetailOtherAdd_Favorite');
	}
}



function cancelCollected(obj,id,event,type,pos){
	event.stopPropagation();
	jConfirm('您确定要取消该收藏？', '温馨提示', function(r){
		if(r===true){
			$.ajax({
				url: '/index.php/wapGift/delCollected/id/'+id,
				dataType:"json",
				type:"GET",			
				success: function (data){
					if(data.data){
						jAlert('收藏礼券取消成功！','温馨提示');
						if(type==2){
							$(obj).html("<i class='iconBox addedFavBtnIcon'></i>收藏");
							$(obj).attr("onClick","collection(this,"+id+",event,2)");
						}else{
							$(obj).html("<i class='iconBox addFavIcon'></i>收藏");
							$(obj).attr("onClick","collection(this,"+id+",event,1)");
						}
						
					}else{
						jAlert('收藏礼券取消失败！','温馨提示');
					}
				},
				cache: false
			});
		}
	});
	if(pos==1){
		tracking('CouponListRemove_Favorite');
	}else if(pos==2){
		tracking('CouponDetailRemove_Favorite');
	}else if(pos==3){
		tracking('CouponDetailOtherRemove_Favorite');
	}else if(pos==4){
		tracking('MyCouponGotListRemove_Favorite');
	}else if(pos==20){
		tracking('MyCouponGotDetailOtherRemove_Favorite');
	}else if(pos==21){
		tracking('MyCouponUsedListRemove_Favorite');
	}else if(pos==22){
		tracking('MyCouponUsedDetailOtherRemove_Favorite');
	}
}

function switchTab(obj,box,pos){
	$(obj).parent('li').addClass('currentTab').siblings('li').removeClass('currentTab');
	box.show().siblings('.tabContent').hide();
	if(pos){
		if(pos==5){
			tracking('ProductListFilterBrand');
		}else if(pos==6){
			tracking('ProductListFilterPrice');
		}else if(pos==11){
			tracking('ProductDetailPara');
		}else if(pos==12){
			tracking('ProductDetailArticle');
		}else if(pos==13){
			tracking('ProductDetailComment');
		}
	}
}

function toggleObj(obj){
	obj.toggleClass('show');
}

function getSpreadCode(uid)
{
	$.ajax({
		url: '/index.php/wapMyIndex/getSpreadQrcode/uid/'+uid,
		dataType:"json",
		success: function (data){
			if(data.data == 1)
			{
				toggleObj($('.qCodeWrap'));
			}else{
				alert('生成二维码失败，请稍后再试');
			}
		},
		cache: false
	});
}

function delReceive(obj,id,event,type,pos){
	event.stopPropagation();
	jConfirm('优惠礼券删除后将无法恢复，您确定要删除该领取的礼券？', '温馨提示', function(r){
		if(r===true){
			$.ajax({
				url: '/index.php/wapMyGift/delReceive/id/'+id,
				dataType:"json",
				success: function (data){
					if(data.data){
						if(type==2){
							jAlert('礼券删除成功！','温馨提示');
							 window.location.href = "/index.php/wapMyGift/index/";
						}else{
							jAlert('礼券删除成功！','温馨提示');
							$(obj).parents('li').remove();
							itemNumReload();
							geftNoData(1)
						}
					}else{
						jAlert('礼券删除失败！','温馨提示');
					}
				},
				cache: false
			});
		}
	});
	if(pos==4){
		tracking('MyCouponGotListDel');
	}else if(pos==5){
		tracking('MyCouponGotDetailDel');
	}
}

function delInvalid(obj,id,event,type,pos){
	event.stopPropagation();
	jConfirm('优惠礼券删除后将无法恢复，您确定要删除该礼券？', '温馨提示', function(r){
		if(r===true){
			$.ajax({
				url: '/index.php/wapMyGift/delInvalid/id/'+id,
				dataType:"json",
				success: function (data){
					if(data.data){
						if(type==2){
							jAlert('礼券删除成功！','温馨提示');
							 window.location.href = "/index.php/wapMyGift/invalid/";
						}else{
							jAlert('礼券删除成功！','温馨提示');
							$(obj).parents('li').remove();
							itemNumReload();
							geftNoData(2)
						}
					}else{
						jAlert('礼券删除失败！','温馨提示');
					}
				},
				cache: false
			});
		}
	});
	if(pos==21){
		tracking('MyCouponUsedListDel');
	}else if(pos==22){
		tracking('MyCouponUsedDetailDel');
	}
}
function delCollected(obj,id,event,pos){
	event.stopPropagation();
	jConfirm('您确定要取消该收藏？', '温馨提示', function(r){
		if(r===true){
			$.ajax({
				url: '/index.php/wapMyGift/delCollected/id/'+id,
				dataType:"json",
				success: function (data){
					if(data.data){
						jAlert('收藏礼券取消成功！','温馨提示');
						$(obj).parents('li').remove();
						itemNumReload();
						geftNoData(3);
					}else{
						jAlert('收藏礼券取消失败！','温馨提示');
					}
				},
				cache: false
			});
		}
	});
	if(pos==23){
		tracking('MyCouponFavoriteListDel');
	}
}
function geftNoData(type){
	var itemListWrap = $(".itemListWrap li").size();
	if(itemListWrap==0){
		if(type==3){
		 str = 	'<p class="noDataTip">您还没有领取任何礼券，立即去<a href="/index.php/wapGift/">收藏礼券</a>吧！</p>';
		}else{
		 str = 	'<p class="noDataTip">您还没有领取任何礼券，立即去<a href="/index.php/wapGift/">领取礼券</a>吧！</p>';
		}
		 $(".itemListWrap").html(str);
	}
	var itemNum = $('.itemNumContainer').html();	
	var strs=itemNum.split("/");
	if(strs[1]==0){
		$(".itemNum").remove();
	}

}

function itemNumReload(){
	var itemNum = $('.itemNumContainer').html();	
	var strs=itemNum.split("/");
	strs[0] = parseInt(strs[0]) - 1;
	strs[1] = parseInt(strs[1]) - 1;
	$('.itemNumContainer').html(strs.join('/'));
}

function getMapkilo(province,lng,lat,obj,x,y){
var map = new BMap.Map("allmap");
map.centerAndZoom(province,12);                   // 初始化地图,设置城市和地图级别。
var pointA = new BMap.Point(lng,lat);  // 创建点坐标A--大渡口区
var pointB = new BMap.Point(x,y);  // 创建点坐标B--江北区
obj.text(Math.ceil(map.getDistance(pointA,pointB))+"m");     //获取两点距离
}

function toAnswer(url){
    var arr = url.split('/');
    var t_id = arr[5];
//    goVerifyCode(t_id);
    
}

function verifyCode(obj,url){
    var codeInput = $("#codeInput").val();
    var arr = url.split('/');
    var t_id = arr[5];
    var t_type = arr[7];
    var errorTip = $('.errorTip');
    $.ajax({
        url:"/index.php/wapTrain/checkCode",
        dataType:'json',
        data:{
            t_id:t_id,
            t_type:t_type,
            codeInput:codeInput,
            rand:Math.random()
        },
        type:'post',
        success:function(data){
            if(data.status == true){
		jAlert(data.data,'温馨提示',function(){
                    window.location.href = url;
                });
                
            }else{
                errorTip.html(data.data);
            }
        },
        cache:false
    });
	
}

function verifyCodeTestSelf(url){
    jAlert('此试题无需答题编码，点击直接进入考试。','温馨提示',function(){
        window.location.href = url;
    });
}

function useGiftRsp(rsp,id,obj){
	$(obj).removeAttr("onClick");
	$(obj).text("提交中.."); 
	$.ajax({
		url:"/index.php/wapRsp/useGift/id/"+id+"/key/"+rsp+"/",
		dataType:"json",
		success:function(data){			
			 if(data.data){
				jAlert('操作成功','温馨提示');
				$('.use').remove();
			}else{
				jAlert('操作失败','温馨提示');
				$(obj).attr("onClick","useGiftRsp('"+rsp+"',"+id+",this)");
				$(obj).text("确认使用"); 
			}
		},
		cache:false
	});
}

function submitAllExam(tid){
    $('#submit').click();
}
function goVerifyCode(t_id){
    var url = '/index.php/wapTrain/verify_code/t_id/' + t_id;
    $.ajax({
        url:'/index.php/wapTrain/checkToAnswer',
        dataType:'json',
        type:'post',
        data:{
            t_id:t_id
        },
        success:function(data){
            if(data.status == true){
                window.location.href = url;
            }else{
		jAlert('考试尚未开始！','温馨提示');
            }
        }
    });
}

function goTrain(alt){
    jAlert('提交成功！','温馨提示', function(){
        window.location.href = "/index.php/wapTrain/";
    });
}

function rspAuthenticate(obj,obj2){
	var wid=obj.val();
        var textContent;
        var backurl = '/index.php/wapMyIndex/index/showRspMsg/1';
	if(wid==''){
		jAlert('请输入您的认证编号');
	}else{
		$(obj2).removeAttr("onclick");
		$(obj2).text("提交中...");
//		setTimeout(function(){
//			$(obj2).attr("onclick","rspAuthenticate($('[name=wechatid]'),"+obj2+")");
//			$(obj2).text("认证");
//		},1000);
//		alert(2323424)
		$.ajax({
			url:'/index.php/wapRspAuthenticate/becomeRsp',
			dataType:'json',
			type:'post',
			data:{
				wechat:wid
			},
			success:function(data){
				if(data == 1)
				{
					textContent = 'Sorry，认证失败，请核对您的认证信息，谢谢。';
				}else if(data == 2){
					textContent = '恭喜，RSP认证成功！';
				}else if(data == 3){
					textContent = 'Sorry，当前RSP已被认证，无法重复认证，谢谢。';
				}else if(data == 4){
					textContent = 'Sorry，您已经认证过了。';
				}
				jAlert(textContent,'',function(){
					window.location.href = backurl;
				});
			}
		});
	}
}

function reloadPage(){
	var axel = Math.random()+'';
	var a = axel * 10000000000000;
	tracking('Refresh_button');
	location.href=window.location.href+'?'+a;
}
function toggleSlide(wrap,mask){
	hideDialog();
	tracking('Menu_button');
	var flag=wrap.is(':visible');
	if(flag){
		wrap.animate({bottom:'50px'},100,function(){
			wrap.animate({bottom:'40px'},50,function(){
				wrap.slideUp(50);
				mask.hide();
			});
		});
	}else{
		wrap.slideDown(100,function(){
			wrap.animate({bottom:'50px'},50,function(){
				wrap.animate({bottom:'35px'},50,function(){
					wrap.animate({bottom:'40px'},100);
					mask.show();
				});
			});
		});
	}
}

function showSearch(wrap){
	tracking('SearchBar');
	var flag=wrap.is(':visible');
	if(flag){
		$('.mask').hide();
		wrap.animate({top:'50px'},100,function(){
			wrap.animate({top:'40px'},50,function(){
				wrap.slideUp(50);
			});
		});
	}else{
		$('.mask').show();
		wrap.slideDown(100,function(){
			wrap.animate({top:'50px'},50,function(){
				wrap.animate({top:'35px'},50,function(){
					wrap.animate({top:'40px'},100);
				});
			});
		});
	}
}

function shutBox(mask,wrap){
	mask.hide();
	wrap.hide()
}

function openDialog(){
	var msg='<p class="diaTtl">温馨提示：</p><p>尊敬的用户您好，由于您暂未关注英特尔中国微信公众账号，还不能参与礼券领取等相关优惠，关注后即可正常参与，感谢您的关注。</p><p class="diaTtl">如何关注：</p><p>进入微信通讯录，点击右上角→查看公众账号→查找：英特尔中国 点击“关注”，即可完成关注英特尔中国微信公众账号。</p>';
	var msk=$('.mask');
	var wrap=$('.dialogWrap');
	if(wrap.size()!=0){
		msk.show();
		wrap.slideDown(1000).children('.diaMsg').html(msg);
	}
}

function loadContent(obj,wrap,url){
	wrap_url = url;
	wrap.html('');
	$.ajax({
		url: url,
		success: function (data) {
			wrap.html(data);
		},
		cache: false
	});
}

function fnComingsoon(txt){
	jAlert(txt,'温馨提示');
}

function tracking(key){
	switch (key){
		case 'Copy_right':
		    dataLayer.push({'event': '/Home/Copy_right'});
		break;
		case 'MenuHP':
		    dataLayer.push({'event': '/Nav/Menu/HP'});
		break;
		case 'MenuCoupon':
		    dataLayer.push({'event': '/Nav/Menu/Coupon'});
		break;
		case 'MenuExpStore':
		    dataLayer.push({'event': '/Nav/Menu/ExpStore'});
		break;
		case 'MenuProductLib':
		    dataLayer.push({'event': '/Nav/Menu/ProductLib'});
		break;
		case 'MenuPCenter':
		    dataLayer.push({'event': '/Nav/Menu/PCenter'});
		break;
		case 'MenuLatest':
		    dataLayer.push({'event': '/Nav/Menu/Latest'});
		break;
		case 'MenuDisclaimer':
		    dataLayer.push({'event': '/Nav/Menu/Disclaimer'});
		break;
		case 'Menu_button':
		    dataLayer.push({'event': '/Nav/Menu_button'});
		break;
		case 'HP_button':
		    dataLayer.push({'event': '/Nav/HP_button'});
		break;
		case 'Pcenter_button':
		    dataLayer.push({'event': '/Nav/Pcenter_button'});
		break;
		case 'Back_button':
		    dataLayer.push({'event': '/Nav/Back_button'});
		break;
		case 'Refresh_button':
		    dataLayer.push({'event': '/Nav/Refresh_button'});
		break;
		case 'CouponListPic':
		    dataLayer.push({'event': '/CouponList/Pic'});
		break;
		case 'CouponListName':
		    dataLayer.push({'event': '/CouponList/Name'});
		break;
		case 'CouponListAdd_Favorite':
		    dataLayer.push({'event': '/CouponList/Add_Favorite'});
		break;
		case 'CouponListRemove_Favorite':
		    dataLayer.push({'event': '/CouponList/Remove_Favorite'});
		break;
		case 'CouponListGet':
		    dataLayer.push({'event': '/CouponList/Get'});
		break;
		case 'CouponListMore':
		    dataLayer.push({'event': '/CouponList/More'});
		break;
		case 'CouponDetailAdd_Favorite':
		    dataLayer.push({'event': '/CouponDetail/Add_Favorite'});
		break;
		case 'CouponDetailRemove_Favorite':
		    dataLayer.push({'event': '/CouponDetail/Remove_Favorite'});
		break;
		case 'CouponDetailGet':
		    dataLayer.push({'event': '/CouponDetail/Get'});
		break;
		case 'CouponDetailOtherPic':
		    dataLayer.push({'event': '/CouponDetail/Other/Pic'});
		break;
		case 'CouponDetailOtherName':
		    dataLayer.push({'event': '/CouponDetail/Other/Name'});
		break;
		case 'CouponDetailOtherAdd_Favorite':
		    dataLayer.push({'event': '/CouponDetail/Other/Add_Favorite'});
		break;
		case 'CouponDetailOtherRemove_Favorite':
		    dataLayer.push({'event': '/CouponDetail/Other/Remove_Favorite'});
		break;
		case 'CouponDetailOtherGet':
		    dataLayer.push({'event': '/CouponDetail/Other/Get'});
		break;
		case 'CouponDetailMore':
		    dataLayer.push({'event': '/CouponDetail/More'});
		break;
		case 'StoreListDetail':
		    dataLayer.push({'event': '/StoreList/Detail'});
		break;
		case 'StoreListMore':
		    dataLayer.push({'event': '/StoreList/More'});
		break;
		case 'ProductListMenu_display':
		    dataLayer.push({'event': '/ProductList/Menu_display'});
		break;
		case 'ProductListMenu_fold':
		    dataLayer.push({'event': '/ProductList/Menu_fold'});
		break;
		case 'ProductListFilterBrand':
		    dataLayer.push({'event': '/ProductList/Filter/Brand'});
		break;
		case 'ProductListFilterPrice':
		    dataLayer.push({'event': '/ProductList/Filter/Price'});
		break;
		case 'ProductListSortHot':
		    dataLayer.push({'event': '/ProductList/Sort/Hot'});
		break;
		case 'ProductListSortNew':
		    dataLayer.push({'event': '/ProductList/Sort/New'});
		break;
		case 'ProductListSortPrice':
		    dataLayer.push({'event': '/ProductList/Sort/Price'});
		break;
		case 'ProductListPic':
		    dataLayer.push({'event': '/ProductList/Pic'});
		break;
		case 'ProductListName':
		    dataLayer.push({'event': '/ProductList/Name'});
		break;
		case 'ProductDetailCouponIcon':
		    dataLayer.push({'event': '/ProductDetail/CouponIcon'});
		break;
		case 'ProductDetailCouponAvailable':
		    dataLayer.push({'event': '/ProductDetail/CouponAvailable'});
		break;
		case 'ProductDetailFollow':
		    dataLayer.push({'event': '/ProductDetail/Follow'});
		break;
		case 'ProductDetailPara':
		    dataLayer.push({'event': '/ProductDetail/Para'});
		break;
		case 'ProductDetailArticle':
		    dataLayer.push({'event': '/ProductDetail/Article'});
		break;
		case 'ProductDetailComment':
		    dataLayer.push({'event': '/ProductDetail/Comment'});
		break;
		case 'MessageDetailDel':
		    dataLayer.push({'event': '/PCenter/Message/Detail/Del'});
		break;
		case 'MyCouponGotListAdd_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Got/List/Add_Favorite'});
		break;
		case 'MyCouponGotListRemove_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Got/List/Remove_Favorite'});
		break;
		case 'MyCouponGotListDel':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Got/List/Del'});
		break;
		case 'MyCouponGotDetailDel':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Got/Detail/Del'});
		break;
		case 'MyCouponGotDetailOtherPic':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Got/Detail/Other/Pic'});
		break;
		case 'MyCouponGotDetailOtherName':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Got/Detail/Other/Name'});
		break;
		case 'MyCouponGotDetailOtherAdd_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Got/Detail/Other/Add_Favorite'});
		break;
		case 'MyCouponGotDetailOtherRemove_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Got/Detail/Other/Remove_Favorite'});
		break;
		case 'MyCouponGotDetailOtherGet':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Got/Detail/Other/Get'});
		break;
		case 'MyCouponUsedListAdd_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Used/List/Add_Favorite'});
		break;
		case 'MyCouponUsedListRemove_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Used/List/Remove_Favorite'});
		break;
		case 'MyCouponUsedListDel':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Used/List/Del'});
		break;
		case 'MyCouponUsedDetailDel':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Used/Detail/Del'});
		break;
		case 'MyCouponUsedDetailOtherPic':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Used/Detail/Other/Pic'});
		break;
		case 'MyCouponUsedDetailOtherName':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Used/Detail/Other/Name'});
		break;
		case 'MyCouponUsedDetailOtherAdd_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Used/Detail/Other/Add_Favorite'});
		break;
		case 'MyCouponUsedDetailOtherRemove_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Used/Detail/Other/Remove_Favorite'});
		break;
		case 'MyCouponUsedDetailOtherGet':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Used/Detail/Other/Get'});
		break;
		case 'MyCouponFavoriteListAdd_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Favorite/List/Add_Favorite'});
		break;
		case 'MyCouponFavoriteListRemove_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Favorite/List/Remove_Favorite'});
		break;
		case 'MyCouponFavoriteListDel':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Favorite/List/Del'});
		break;
		case 'MyCouponFavoriteDetailDel':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Favorite/Detail/Del'});
		break;
		case 'MyCouponFavoriteDetailOtherPic':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Favorite/Detail/Other/Pic'});
		break;
		case 'MyCouponFavoriteDetailOtherName':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Favorite/Detail/Other/Name'});
		break;
		case 'MyCouponFavoriteDetailOtherAdd_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Favorite/Detail/Other/Add_Favorite'});
		break;
		case 'MyCouponFavoriteDetailOtherRemove_Favorite':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Favorite/Detail/Other/Remove_Favorite'});
		break;
		case 'MyCouponFavoriteDetailOtherGet':
		    dataLayer.push({'event': '/PCenter/MyCoupon/Favorite/Detail/Other/Get'});
		break;
		case 'FollowedProductPic':
		    dataLayer.push({'event': '/PCenter/FollowedProduct/Pic'});
		break;
		case 'FollowedProductName':
		    dataLayer.push({'event': '/PCenter/FollowedProduct/Name'});
		break;
		case 'FollowedProductUnfollow':
		    dataLayer.push({'event': '/PCenter/FollowedProduct/Unfollow'});
		break;
		case 'LatestPic':
		    dataLayer.push({'event': '/Latest/Pic'});
		break;
		case 'LatestName':
		    dataLayer.push({'event': '/Latest/Name'});
		break;
		case 'ApplyVerify':
		    dataLayer.push({'event': '/Disclaimer/Apply/Verify'});
		break;	
		case 'SearchBar':
		    dataLayer.push({'event': '/SearchBar/'});
		break;	
	}
}