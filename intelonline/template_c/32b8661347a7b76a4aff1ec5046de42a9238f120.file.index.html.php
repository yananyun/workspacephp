<?php 
/*
       * Smarty version Smarty-3.1.12, created on 2016-07-29 14:23:03
       * compiled from "/Users/thief/Documents/phpworkspace/extintelonline1/extintelonline/template/admin/login/index.html"
       */
?>
<?php
 
/* %%SmartyHeaderCode:2124132401579aebb45382d5-23480631%% */
if (! defined ( 'SMARTY_DIR' ))
	exit ( 'no direct access allowed' );
$_valid = $_smarty_tpl->decodeProperties ( array (
		'file_dependency' => array (
				'32b8661347a7b76a4aff1ec5046de42a9238f120' => array (
						0 => '/Users/thief/Documents/phpworkspace/extintelonline1/extintelonline/template/admin/login/index.html',
						1 => 1469773318,
						2 => 'file' 
				) 
		),
		'nocache_hash' => '2124132401579aebb45382d5-23480631',
		'function' => array (),
		'version' => 'Smarty-3.1.12',
		'unifunc' => 'content_579aebb4585558_32033050',
		'has_nocache_code' => false 
), false ); /* /%%SmartyHeaderCode%% */
?>
<?php if ($_valid && !is_callable('content_579aebb4585558_32033050')) {function content_579aebb4585558_32033050($_smarty_tpl) {?>
<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="">
<meta name="description" content="">
<title>微信公众号管理平台</title>

<link rel="stylesheet" type="text/css" href="/css/base.css">

<link rel="stylesheet" type="text/css" href="/css/skins/blue.css">
<link rel="stylesheet" href="/css/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/artDialog.js"></script>
<script type="text/javascript" src="/js/base.js"></script>
<script>
		$(function(){
			// 提交信息
			$(document).keydown(function(e) {
			    if (e.which == 13) {
			        $("#submit").trigger('click');
			    }
			});
		})
		</script>
</head>
<body>
	<div class="top">
		<img src="../../../../images/logo.jpg" alt="" />
		<p>
			Intel China Online<br /> <em>微信管理后台</em>
		</p>
		<!-- <div class="topRight">
			<ul>
				<li>admin&nbsp;|</li>
				<li>帮助&nbsp;|</li>
				<li><a href="">{退出}</a></li>
			</ul>
		</div> -->
	</div>
	<div class="loginCon">
		<div class="login_area">
			<h3>微信公众帐号管理平台</h3>
			<form onsubmit="loginCheck(this)" name="login" id="login"
				method="post"
				action="/extintelonline1/extintelonline/index.php/login/doLogin/">
				<p>
					<!-- <label class="for_user" for="username"> 账户：</label> -->
					<!-- <span><i class="fa fa-user"></i></span> -->
					<!-- 	"extintelonline/images/icon3.png"
						"extintelonline/template/admin/login/index.html" -->
					<span><img src="../../../../images/icon3.png" alt=""></span> <input
						type="text" id="username" name="username" placeholder="账户" />
				</p>
				<p>
					<!-- <label class="for_pwd" for="password">密码</label> -->
					<!-- <span><i class="fa fa-lock"></i></span> -->
					<span><img src="../../../../images/icon2.png" alt=""></span> <input
						type="password" id="password" name="password" placeholder="密码" />
				</p>
				<p class="for_submit">
					<input type="submit" id="submit" name="submit" value="登录" />
				</p>
			</form>
		</div>
	</div>
	<div class="footer">Intel China Online Wechat</div>
</body>
</html><?php }} ?>