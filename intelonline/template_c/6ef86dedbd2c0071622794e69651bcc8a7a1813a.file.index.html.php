<?php /* Smarty version Smarty-3.1.12, created on 2016-07-29 17:26:52
         compiled from "/Users/lixiaoyan/Documents/workspacephp/intelonline/template/admin/login/index.html" */ ?>
<?php /*%%SmartyHeaderCode:1584905117579b1e2795be20-44337894%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6ef86dedbd2c0071622794e69651bcc8a7a1813a' => 
    array (
      0 => '/Users/lixiaoyan/Documents/workspacephp/intelonline/template/admin/login/index.html',
      1 => 1469784274,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1584905117579b1e2795be20-44337894',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_579b1e27b7b9d6_10460556',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_579b1e27b7b9d6_10460556')) {function content_579b1e27b7b9d6_10460556($_smarty_tpl) {?><!DOCTYPE HTML>
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
		<img src="../../../images/logo.jpg" alt="" />
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
				action="/index.php/login/doLogin/">
				<p>
					<!-- <label class="for_user" for="username"> 账户：</label> -->
					<!-- <span><i class="fa fa-user"></i></span> -->
					<!-- 	"intelonline/images/icon3.png"
						"intelonline/template/admin/login/index.html" -->
					<!-- 	http://localhost:8080/intelonline/index.php/login/index
					这个需要两次
						http://localhost:8080/intelonline/index.php/login/index/
						对于要用到的../个数有影响 这个需要3次
						 -->
					<span><img src="../../../images/icon3.png" alt=""></span>
					<input type="text" id="username" name="username" placeholder="账户" />
				</p>
				<p>
					<!-- <label class="for_pwd" for="password">密码</label> -->
					<!-- <span><i class="fa fa-lock"></i></span> -->
					<span><img src="../../../images/icon2.png" alt=""></span>
					<input type="password" id="password" name="password"
						placeholder="密码" />
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