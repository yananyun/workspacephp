<?php

  /**
  * 
  */
  class Smarty 
  {
  	
  	private $vars = array();
  	
  	function assign ($key,$value)
  	{
  		$this->vars[$key] = $value;
  	}

 	function display($tplfile)
 	{
 		/*对模版文件 和 组合后的文件 进行分离 便于管理*/
 		// $tplfile = './tpls/'.$tplfile;
        
 		$confile = 'con_'.'$tplfile'.'php';

 		// $confile = './components/'.$confile;

/*检查 组合后的模版文件是否存在  检查组合后的模版文件 修改时间 是否小于 模版文件修改时间*/
     if(!file_exists($confile)||filemtime($confile)<filemtime($tplfile))
     {
     	$content = file_get_contents($tplfile);
 		/*需要保存到新的文件 中 准备 替换*/
 		/*正则替换 替换变量 替换子模版 这里只做替换变量*/
 		$zz = array(
 			/*
			模版中使用的定界符是大括号
			这里使用正则   区分大小写 不加i
			定界符是 正则 元字符 需要转义
			允许定界符后有空格 \s*
			变量调用符 $ 也是元字符 需要转义
			要作为缓冲区取出来 需要加圆括号

 			*/
 			'/\{\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*\}/',

 			);

 		$rep = array (

 			    '<?php
				echo $this->vars["${1}"];
 			    ?>',
 			);

 	$recontent = preg_replace($zz, $rep, $content);
      
      file_put_contents($confile, $recontent);


     }


     /*
     	流行的php框架 会整合进 smarty

     	smarty 将php程序 与 html 模版
     	组合成新的文件  这个过程我们可以称它为编译过程

     	这个过程主要考虑文件是否存在 以及 模版文件 是否修改

     	访问php后 php 连接数据库 获取动态数据

     	smarty 缓存
     */
     include $confile;
 		




 	}
  }