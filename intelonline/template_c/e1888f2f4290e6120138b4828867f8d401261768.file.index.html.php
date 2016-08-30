<?php /* Smarty version Smarty-3.1.12, created on 2016-08-30 13:46:10
         compiled from "/Users/lixiaoyan/Documents/workspacephp/workspacephp/intelonline/template/admin/statistical/index.html" */ ?>
<?php /*%%SmartyHeaderCode:14853997657c51da2338262-23452930%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e1888f2f4290e6120138b4828867f8d401261768' => 
    array (
      0 => '/Users/lixiaoyan/Documents/workspacephp/workspacephp/intelonline/template/admin/statistical/index.html',
      1 => 1469778073,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14853997657c51da2338262-23452930',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_57c51da244c519_96052815',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57c51da244c519_96052815')) {function content_57c51da244c519_96052815($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('admin/public/head.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<link rel="stylesheet" type="text/css" href="/css/index.css" />
<script type="text/javascript" src="/js/highcharts.js"></script>
<script type="text/javascript">
    function AjaxDataChart(obj,type,action) {
        $.ajax({
            "url":'/index.php/statistical/' + action+'/type/'+type+'/obj/'+obj,
            "success":function(data){
                $("#"+obj).html(data);
            }
        })
    }
</script>
<body>
	<div class="container">
		<?php echo $_smarty_tpl->getSubTemplate ('admin/public/header.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<div class="main">
			<?php echo $_smarty_tpl->getSubTemplate ('admin/public/left_menu.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			<div class="mainWrap statisWrap"></div>
		</div>


	</div>

</body>
</html><?php }} ?>