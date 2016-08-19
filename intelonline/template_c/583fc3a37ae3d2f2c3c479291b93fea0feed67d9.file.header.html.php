<?php /* Smarty version Smarty-3.1.12, created on 2016-07-29 17:28:36
         compiled from "/Users/lixiaoyan/Documents/workspacephp/intelonline/template/admin/public/header.html" */ ?>
<?php /*%%SmartyHeaderCode:662258558579b21c495f2b2-02432213%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '583fc3a37ae3d2f2c3c479291b93fea0feed67d9' => 
    array (
      0 => '/Users/lixiaoyan/Documents/workspacephp/intelonline/template/admin/public/header.html',
      1 => 1469778073,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '662258558579b21c495f2b2-02432213',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'userinfo' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_579b21c4968742_65300716',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_579b21c4968742_65300716')) {function content_579b21c4968742_65300716($_smarty_tpl) {?><!-- <div class="relative">
	<h1 id="logo" >
		<div class="">
			<img src="/images/logo.gif" alt="" />
		</div>
	</h1>
	<p class="user_area">
		<span><?php echo $_smarty_tpl->tpl_vars['userinfo']->value['nickname'];?>
</span>,您好
		<a href="/index.php/login/logout/">退出</a>
	</p>
</div> -->

<div class="top">
	<img src="/images/logo.jpg" alt="" />
	<p>
		Intel China Online<br /> <em>微信管理后台</em>
	</p>
	<div class="topRight">
		<ul>
			<li>您好，<a href="/index.php/me/index"><?php echo $_smarty_tpl->tpl_vars['userinfo']->value['nickname'];?>
</a>&nbsp;&nbsp;|
			</li>
			<!-- <li>帮助&nbsp;|</li> -->
			<li><a href="/index.php/login/logout/">{退出}</a></li>
		</ul>
	</div>
</div>


<link rel="stylesheet" type="text/css" href="/css/main.css">
<link rel="stylesheet" type="text/css" href="/css/base.css">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<link rel="stylesheet" type="text/css" href="/css/skins/blue.css">
<link rel="stylesheet" href="/css/font-icon/iconfont.css">

<style>
#logo {
	border-bottom: 1px solid #ddd;
	background: #fff;
	padding: 0;
}

.leftNav p {
	font-size: 16px;
	color: #ccc;
	margin-bottom: 10px;
}

.leftNav p i {
	color: #ccc;
	margin-right: 6px;
}

.leftNav li a {
	color: #000;
}

a {
	color: #ccc;
}

#logo div {
	color: #006a68;
}

.user_area {
	color: #006a68;
}

.user_area span {
	color: #006a68;
}

.user_area a {
	color: #006a68;
}

.intelBtnDel {
	color: #FFF;
	xbackground: #01a8a5;
	background: #0066CB;
	border: none;
	height: 27px;
	line-height: 27px;
	xdisplay: block;
	cursor: pointer;
	text-indent: 0px;
	border-radius: 3px;
}

.intelListWrap, .intelConflictListWrap, .intelDefaultListWrap {
	border-top: 1px solid #01a8a5;
}

.m_r_msg p {
	background: #01a8a5;
}

.m_menu_l a {
	color: #333;
}

.tabWrap .currentTab {
	background-color: rgb(0, 106, 104);
}

.tabWrap a {
	background-color: rgb(1, 168, 165);
	color: white;
	margin-right: 10px;
}

.m_r_leftMenu ul li {
	width: 260px;
}

.global_del {
	background-color: #008dd4;
	color: #fff;
	padding: 0 5px;
}

.global_button {
	background-color: #01a8a5;
	padding: 4px;
	color: white;
	cursor: pointer;
}

.global_change {
	background-color: #01a8a5;
}

.global_del {
	background-color: #01a8a5;
}

.intelListWrap {
	border: none;
}

.search .frm_input_append {
	margin-right: 0;
}

.productInfo {
	width: 8%;
	text-align: center;
}

.intelListWrap li input[type="button"] {
	margin-left: 1%;
	xmargin-right: 6%;
}
</style><?php }} ?>