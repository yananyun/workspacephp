<?php /* Smarty version Smarty-3.1.12, created on 2016-07-29 17:28:36
         compiled from "/Users/lixiaoyan/Documents/workspacephp/intelonline/template/admin/public/left_menu.html" */ ?>
<?php /*%%SmartyHeaderCode:1601569814579b21c496b699-94103118%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd4a67bac86d4850b0f6dd9e36bdf1767dc56f1f3' => 
    array (
      0 => '/Users/lixiaoyan/Documents/workspacephp/intelonline/template/admin/public/left_menu.html',
      1 => 1469778073,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1601569814579b21c496b699-94103118',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'module' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_579b21c4991ba0_50994176',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_579b21c4991ba0_50994176')) {function content_579b21c4991ba0_50994176($_smarty_tpl) {?><!--<div class="mainLeft">
	<ul class="leftNav">
            <p><a href="/index.php/statistical/index">首页</a></p>
		<p>基础功能</p>
			<li><i></i><a href="/index.php/reply/list_manage">自动回复</a></li>
			<li><i></i><a href="/index.php/menu/menu_manage">自定义菜单</a></li>
			<li><i></i><a href="/index.php/groupMessage/list_manage">消息发布</a></li>
			<li><i></i><a href="/index.php/message/list_manage">消息管理</a></li>
			<li><i></i><a href="/index.php/group/list_manage">用户组管理</a></li>
		<p>素材管理</p>
			<li><i></i><a href="/index.php/material/pic_txt_msg">图文素材</a></li>
			<li><i></i><a href="/index.php/material/pic_msg">图片管理</a></li>
		<p><a href="/index.php/member/member_manage">微信用户管理</a></p>
		<p><a href="/index.php/user/index">成员管理</a></p>					
		<p><a href="/index.php/me/index">个人中心</a></p>

	</ul>
</div>-->
<div class="leftDiv">
	<ul id="navigation">
		<li><a href="/index.php/member/member_manage"<?php if ($_smarty_tpl->tpl_vars['module']->value=='member'||$_smarty_tpl->tpl_vars['module']->value=='group'){?> class="nowRight"<?php }?>
				>微信用户管理</a></li>
		<!-- <li><a href="/index.php/group/list_manage">微信用户分组管理</a></li> -->
		<li><a href="/index.php/tag/list_manage"<?php if ($_smarty_tpl->tpl_vars['module']->value=='tag'){?> class="nowRight"<?php }?> >标签管理</a></li>
		<li><a href="/index.php/information/index"<?php if ($_smarty_tpl->tpl_vars['module']->value=='information'){?> class="nowRight"<?php }?>>最新资讯管理</a></li>
		<li><a href="/index.php/activepush/weixin_msg_list"<?php if ($_smarty_tpl->tpl_vars['module']->value=='activepush'){?> class="nowRight"<?php }?>>主动发送管理</a></li>
		<!--<li><a href="/index.php/reply/list_manage" <?php if ($_smarty_tpl->tpl_vars['module']->value=='reply'){?> class="nowRight"<?php }?>>客服应答</a></li>-->
		<li><a href="/index.php/autoresponse/autoresponse_manage"<?php if ($_smarty_tpl->tpl_vars['module']->value=='autoresponse'){?> class="nowRight"<?php }?>>客服应答</a></li>
		<li><a href="/index.php/datamanage/index"<?php if ($_smarty_tpl->tpl_vars['module']->value=='datamanage'){?> class="nowRight"<?php }?>>数据管理</a></li>
		<!-- <li><a href="/index.php/material/pic_txt_msg"  <?php if ($_smarty_tpl->tpl_vars['module']->value=='material'&&($_smarty_tpl->tpl_vars['action']->value=='pic_txt_msg'||$_smarty_tpl->tpl_vars['action']->value=='creat_pic_txt'||$_smarty_tpl->tpl_vars['action']->value=='creat_pic_txt_multi')){?> class="nowRight"<?php }?>>图文素材</a></li> -->
		<li><a href="/index.php/material/txt_msg"<?php if ($_smarty_tpl->tpl_vars['module']->value=='material'){?> class="nowRight"<?php }?>>素材库</a></li>
		<!-- <li><a href="/index.php/material/pic_msg">图片管理</a></li> -->
	</ul>
</div>
<?php }} ?>