<?php

/**
 * 过滤类
 * 主要过滤POST、GET、COOKIE、REQUEST 传过来的数据。
 * @author xiaofeng
 * @copyright 2012 (c) ZeverTech
 * @version $Id$
 */
class Filter {
	
	/**
	 * 非过滤HTML标记
	 *
	 * @var _allowtags
	 */
	private static $_allowtags = 'p|br|b|strong|hr|a|img|object|param|form|input|label|dl|dt|dd|div|font';
	
	/**
	 * 非过滤HTML标记中的属性
	 *
	 * @var _allowattrs
	 */
	private static $_allowattrs = 'id|class|align|valign|src|border|href|target|width|height|title|alt|name|action|method|value|type';
	
	/**
	 * 过滤HTML标记属性
	 *
	 * @var _disallowattrvals
	 */
	private static $_disallowattrvals = 'expression|javascript:|behaviour:|vbscript:|mocha:|livescript:';
	
	/**
	 * 构造方法 初始化过滤设置
	 *
	 * @param string $allowtags        	
	 * @param string $allowattrs        	
	 * @param string $disallowattrvals        	
	 */
	function __construct($allowtags = null, $allowattrs = null, $disallowattrvals = null) {
		if ($allowtags) {
			self::$_allowtags = $allowtags;
		}
		if ($allowattrs) {
			self::$_allowattrs = $allowattrs;
		}
		if ($disallowattrvals) {
			self::$_disallowattrvals = $disallowattrvals;
		}
	}
	
	/**
	 * 对提交到服务器的数据进行过滤
	 *
	 * @param int $cleanxss
	 *        	xss开关
	 */
	static function input($cleanxss = 1) {
		if (get_magic_quotes_gpc ()) { // 获取当前的配置设置magic_quotes_gpc的
			$_POST = stripslashes_deep ( $_POST );
			
			$_GET = stripslashes_deep ( $_GET );
			
			$_COOKIE = stripslashes_deep ( $_COOKIE );
			
			$_REQUEST = stripslashes_deep ( $_REQUEST );
		}
		
		if ($cleanxss) {
			// 分别外理
			$_POST = self::xss ( $_POST );
			
			$_GET = self::xss ( $_GET );
			
			$_COOKIE = self::xss ( $_COOKIE );
			
			$_REQUEST = self::xss ( $_REQUEST );
		}
	}
	
	/**
	 * xss攻击处理
	 *
	 * @param string|array $stirng
	 *        	过滤字符串
	 * @return string 返回过滤后的字符串
	 */
	static function xss($string) {
		if (is_array ( $string )) {
			
			$string = array_map ( array (
					'self',
					'xss' 
			), $string ); // 将回调函数作用到给定数组的单元上,每个数组元素分别处理
		} else {
			
			if (strlen ( $string ) > 20) {
				
				$string = self::_strip_tags ( $string );
			}
		}
		
		return $string;
	}
	
	/**
	 * 标签过滤
	 *
	 * @param string $string
	 *        	字符
	 * @return string 返回过滤后的字符串
	 */
	static function _strip_tags($string) {
		return preg_replace_callback ( "|(<)(/?)(\w+)([^>]*)(>)|", array (
				'self',
				'_strip_attrs' 
		), $string ); // 用回调函数执行正则表达式的搜索和替换
	}
	
	/**
	 * 标签属性过滤
	 *
	 * @param array $matches        	
	 * @return string 返回过滤后的字符串
	 */
	static function _strip_attrs($matches) {
		if (preg_match ( "/^(" . self::$_allowtags . ")$/", $matches [3] )) {
			
			if ($matches [4]) {
				
				preg_match_all ( "/\s(" . self::$_allowattrs . ")\s*=\s*(['\"]?)(.*?)\\2/i", $matches [4], $m, PREG_SET_ORDER );
				
				$matches [4] = '';
				
				foreach ( $m as $k => $v ) {
					
					if (! preg_match ( "/(" . self::$_disallowattrvals . ")/", $v [3] )) {
						
						$matches [4] .= $v [0];
					}
				}
			}
		} else {
			
			$matches [1] = '&lt;';
			
			$matches [5] = '&gt;';
		}
		
		unset ( $matches [0] );
		
		return implode ( '', $matches );
	}
}