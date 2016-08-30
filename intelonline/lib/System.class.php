<?php

/**
 * url处理、控制器的实例化，方法的调用
 * @author xiaofeng Doe <276355449@ZeverTech.com>
 * @copyright 2012 (c) ZeverTech
 * @version $Id$
 */
class System {
	public static $module; // 模块名称
	public static $action; // 操作名称
	
	/**
	 * 初始化功能主要是对url的处理
	 * 
	 * @param type $argv        	
	 */
	function init($argv = false) {
		// 提交信息过滤
		echo '<br/>init system success<br>';
		$filter = new Filter ();
		$filter->input ();
		// 网址解析
		if (URL_REWRITE_ON == 'true') {
			$this->_parseUrl (); // 解析模块和操作
			                    // 兼容命令行模式
			self::$module = ! empty ( self::$module ) ? self::$module : ($_SERVER ['argv'] [1] ? $_SERVER ['argv'] [1] : $_GET ['m']);
			self::$action = ! empty ( self::$action ) ? self::$action : ($_SERVER ['argv'] [2] ? $_SERVER ['argv'] [2] : $_GET ['a']);
			// 命令行模式下设置$_GET数组
			if (count ( $argv ) > 3) {
				$argv_num = (count ( $argv ) - 3) / 2;
				for($ai = 0; $ai < $argv_num; $ai ++) {
					$_GET [$argv [3 + $ai * 2]] = $argv [4 + $ai * 2];
				}
			}
		} else {
			
			self::$module = $_GET ['m'] ? $_GET ['m'] : $_SERVER ['argv'] [1];
			self::$action = $_GET ['a'] ? $_GET ['a'] : $_SERVER ['argv'] [2];
		}
		// 模块或操作为空，则设置默认值
		self::$module = empty ( self::$module ) ? "index" : self::$module;
		self::$action = empty ( self::$action ) ? "index" : self::$action;
		echo $object . '<br/>';
		echo $action . '<br/>';
		$this->_execute ();
	}
	
	/**
	 * 网址解析
	 */
	private function _parseUrl() {
		echo "parse url success<br/>";
		$script_name = $_SERVER ["SCRIPT_NAME"]; // 获取当前文件的路径
		$url = $_SERVER ["REQUEST_URI"]; // 获取完整的路径，包含"?"之后的字符串
		echo $script_name . '当前文件的路径<br/>';
		echo $url . '获取完整的路径包含?之后的字符串<br/>';
		// 去除url包含的当前文件的路径信息
		/*
		 * 返回字符串在另一字符串中第一次出现的位置，如果没有找到字符串则返回 FALSE。
		 * 注释：字符串位置从 0 开始，不是从 1 开始。
		 */
		if ($url && @strpos ( $url, $script_name, 0 ) !== false) {
			/*
			 * strpos(string,find,start) string规定要搜索的字符串 find 规定要查找的字符串 start 规定从何处开始查找
			 * $script_name = $_SERVER["SCRIPT_NAME"];
			 * 获取当前文件的路径 这里就是以下要截取的脚本文件名
			 * 别返回全路径就好（/intelonline/index.php?login）
			 * 应该返回（/intelonline/index.php） 需要检查服务器配置
			 * /intelonline/index.php当前文件的路径
			 * /intelonline/index.php/login/index当前文件的路径
			 * /intelonline/index.php/login/index?home=111&name=222
			 * 获取完整的路径包含?之后的字符串
			 *
			 *
			 */
			// stripos 如果没有找到字符串则返回 FALSE。
			$pos = stripos ( $script_name, 'index.php', 0 );
			if ($pos) {
				echo strlen ( $script_name ) . '<br/>';
				// 发现服务器返回的scriptname 有问题 把模块名 和 方法名也返回过来了
				$script_name = substr ( $script_name, 0, $pos + strlen ( 'index.php' ) );
				echo $pos . '<br/>';
				echo $script_name . '<br/>';
			}
			
			/* substr(string,start,length) string 规定要搜索的字符串 start 规定从哪里开始查找要查找的字符串 length 规定被返回字符串的长度 */
			$url = substr ( $url, strlen ( $script_name ), strlen ( $url ) - strlen ( $script_name ) );
			echo $url . '---1111<br/>';
			
			/*
			 * 这里有截断地址栏中的url 来获取要传递的参数 以确定要进入的模块 和执行的方法
			 * /intelonline/index.php?login获取完整的路径包含?之后的字符串
			 */
		} else {
			$script_name = str_replace ( basename ( $_SERVER ["SCRIPT_NAME"] ), '', $_SERVER ["SCRIPT_NAME"] );
			echo $script_name . '---2222<br/>';
			if ($url && @strpos ( $url, $script_name, 0 ) !== false) {
				$url = substr ( $url, strlen ( $script_name ) );
			}
		}
		// 第一个字符是'/'，则去掉
		if ($url [0] == '/') {
			echo $url . '---2222<br/>';
			$url = substr ( $url, 1 );
			echo $url . '---2222<br/>';
		}
		
		// 去除问号后面的查询字符串
		if ($url && false !== ($pos = @strrpos ( $url, '?' ))) {
			echo $url . '---3333<br/>';
			echo $pos . 'substr<br/>';
			$url = substr ( $url, 0, $pos );
			echo $url . '---3333<br/>';
		}
		
		// 去除后缀
		if ($url && ($pos = strrpos ( $url, URL_HTML_SUFFIX )) > 0) {
			echo $url . '---4444<br/>';
			$url = substr ( $url, 0, $pos );
			echo $url . '---4444<br/>';
		}
		
		$flag = 0;
		// 获取模块名称
		if ($url && ($pos = @strpos ( $url, URL_MODULE_DEPR, 1 )) > 0) {
			/* substr(string,start,length) string 规定要搜索的字符串 start 规定从哪里开始查找要查找的字符串 length 规定被返回字符串的长度 */
			self::$module = substr ( $url, 0, $pos ); // 模块
			echo self::$module . '模块名<br/>';
			$url = substr ( $url, $pos + 1 ); // 除去模块名称，剩下的url字符串(没有保留了方法名前的/分隔符 所以加1)
			echo $url . '除去模块名后的url字符串<br/>';
			$flag = 1; // 标志可以正常查找到模块
		} else { // 如果找不到模块分隔符，以当前网址为模块名
			self::$module = $url;
		}
		
		$flag2 = 0; // 用来表示是否需要解析参数
		            // 获取操作方法名称
		/*
		 * define('URL_MODULE_DEPR', '/'); //模块分隔符，一般不需要修改
		 * define('URL_ACTION_DEPR', '/'); //操作分隔符，一般不需要修改
		 * define('URL_PARAM_DEPR', '/'); //参数分隔符，一般不需要修改
		 * define('URL_HTML_SUFFIX', '.html'); //伪静态后缀设置，，例如 .html ，一般不需
		 */
		/*
		 * 我们这里彻底重写路由更改 访问服务器的url的拼写规则 模块名 方法名 参数列表 都用'/'来分割 否则访问出错
		 * http://localhost:8080/intelonline/index.php/login/index/home=111/name=222
		 */
		if ($url && ($pos = strpos ( $url, URL_ACTION_DEPR, 1 ))) {
			/* substr(string,start,length) string 规定要搜索的字符串 start 规定从哪里开始查找要查找的字符串 length 规定被返回字符串的长度 */
			self::$action = substr ( $url, 0, $pos ); // 模块
			echo self::$action . '方法名';
			$url = substr ( $url, $pos + 1 );
			$flag2 = 1; // 表示需要解析参数
		} else {
			// 只有可以正常查找到模块之后，才能把剩余的当作操作来处理
			// 因为不能找不到模块，已经把剩下的网址当作模块处理了
			echo 'error----方法名';
			if ($flag) {
				self::$action = $url;
			}
		}
		// 解析参数
		if ($flag2) {
			$param = explode ( URL_PARAM_DEPR, $url );
			$param_count = count ( $param );
			for($i = 0; $i < $param_count; $i = $i + 2) {
				if (isset ( $param [$i + 1] )) {
					if (! is_numeric ( $param [$i] )) {
						$_GET [$param [$i]] = $param [$i + 1];
					}
				}
			}
		}
	}
	
	/**
	 * 执行操作
	 */
	private function _execute() {
		echo 'test execute <br/>';
		
		if (self::$module == 'api') {
			echo self::$module . '<br/>';
			echo self::$action . '<br/>';
			$control = 'main';
		} else {
			echo self::$module . '<br/>';
			echo self::$action . '<br/>';
			$control = self::$module . "Action";
		}
		if (! class_exists ( $control )) {
		   echo self::$module . '<br/>';
			echo self::$action . '<br/>';
			apiData ( "此类不存在！", 1027, '/system' );
			die ();
		}
		echo self::$module . '模块名 具体文件在action文件夹下<br/>';
		echo self::$action . '方法名 具体文件在action文件夹下<br/>';
		
		$object = new $control (); // 实例化模块对象
		
		$action = self::$action;
		
		if (! method_exists ( $object, $action )) {
			$action = "index";
		} else {
			$action = self::$action;
		}
		
		$object->$action ();
	}
}
/*index php intel online11
intel online3
intel online2
/Users/lixiaoyan/Documents/workspacephp/workspacephp/intelonline/我要知道项目的根路径documentroot

init system success
parse url success
/index.php当前文件的路径
/index.php/login/index获取完整的路径包含?之后的字符串
10
1
/index.php
/login/index---1111
/login/index---2222
login/index---2222
login模块名
index除去模块名后的url字符串
error----方法名

test execute 


模块名 具体文件在action文件夹下
方法名 具体文件在action文件夹下
array(1) { ["default"]=> array(2) { ["Master"]=> array(4) { ["DBhost"]=> string(9) "localhost" ["DBport"]=> string(4) "3306" ["DBuser"]=> string(4) "root" ["DBpws"]=> string(9) "dyn123456" } ["DBname"]=> string(11) "intelonline" } } array(2) { ["Master"]=> array(4) { ["DBhost"]=> string(9) "localhost" ["DBport"]=> string(4) "3306" ["DBuser"]=> string(4) "root" ["DBpws"]=> string(9) "dyn123456" } ["DBname"]=> string(11) "intelonline" } array(2) { ["Master"]=> array(4) { ["DBhost"]=> string(9) "localhost" ["DBport"]=> string(4) "3306" ["DBuser"]=> string(4) "root" ["DBpws"]=> string(9) "dyn123456" } ["DBname"]=> string(11) "intelonline" } array(2) { ["Master"]=> array(4) { ["DBhost"]=> string(9) "localhost" ["DBport"]=> string(4) "3306" ["DBuser"]=> string(4) "root" ["DBpws"]=> string(9) "dyn123456" } ["DBname"]=> string(11) "intelonline" } intelonline
connect
主库连接 : SQLSTATE[HY000] [2002] No such file or directory
 * */
?>
