<?php

/**
 +------------------------------------------------------------------------------
 * Cookie管理类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Cookie.class.php 2702 2012-02-02 12:35:01Z liu21st $
 +------------------------------------------------------------------------------
 */
class Cookie {
	
	/**
	 * 判断Cookie是否存在
	 *
	 * @param string $name
	 *        	cookie名称
	 * @return type
	 */
	static function is_set($name) {
		return isset ( $_COOKIE [COOKIE_PREFIX . $name] );
	}
	
	/**
	 * 获取某个Cookie值
	 *
	 * @param string $name
	 *        	获取cookie名称
	 * @return type
	 */
	static function get($name) {
		$value = $_COOKIE [COOKIE_PREFIX . $name];
		$value = unserialize ( base64_decode ( $value ) );
		return $value;
	}
	
	/**
	 * 设置某个Cookie值
	 * 
	 * @param string $name
	 *        	设置cookie的名称
	 * @param string $value
	 *        	设置cookie的值
	 * @param int $expire
	 *        	有效时间
	 * @param string $path
	 *        	作用路径
	 * @param string $domain
	 *        	作用域
	 */
	static function set($name, $value, $expire = COOKIE_EXPIRE, $path = '', $domain = '') {
		// if ($expire == '') {
		// $expire = COOKIE_EXPIRE;
		// }
		if (empty ( $path )) {
			$path = COOKIE_PATH;
		}
		if (empty ( $domain )) {
			$domain = COOKIE_DOMAIN;
		}
		$expire = ! empty ( $expire ) ? time () + $expire : 0;
		$value = base64_encode ( serialize ( $value ) );
		setcookie ( COOKIE_PREFIX . $name, $value, $expire, $path, $domain );
		$_COOKIE [COOKIE_PREFIX . $name] = $value;
	}
	
	/**
	 * 删除某个Cookie值
	 *
	 * @param string $name        	
	 */
	static function delete($name) {
		Cookie::set ( $name, '', - 3600 );
		unset ( $_COOKIE [COOKIE_PREFIX . $name] );
	}
	
	/**
	 * 清空Cookie值
	 */
	static function clear() {
		unset ( $_COOKIE );
	}
}