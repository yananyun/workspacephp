<?php /* Smarty version Smarty-3.1.12, created on 2016-07-29 17:28:36
         compiled from "/Users/lixiaoyan/Documents/workspacephp/intelonline/template/admin/statistical/index.html" */ ?>
<?php /*%%SmartyHeaderCode:1942452753579b21c4867a53-13545471%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'df3ad7948b0a9112377b4b25422d422a94b62329' => 
    array (
      0 => '/Users/lixiaoyan/Documents/workspacephp/intelonline/template/admin/statistical/index.html',
      1 => 1469778073,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1942452753579b21c4867a53-13545471',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_579b21c490d1d9_74772719',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_579b21c490d1d9_74772719')) {function content_579b21c490d1d9_74772719($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('admin/public/head.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

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