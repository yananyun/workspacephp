<?php
class Curl {
	function Curl() {
		return true;
	}
	function curlPost($url, array $post = NULL, array $options = array()) {
		$defaults = array (
				CURLOPT_POST => 1,
				CURLOPT_HEADER => 0,
				CURLOPT_URL => $url,
				CURLOPT_FRESH_CONNECT => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_FORBID_REUSE => 1,
				CURLOPT_TIMEOUT => 4,
				CURLOPT_POSTFIELDS => http_build_query ( $post ) 
		);
		
		$ch = curl_init ();
		curl_setopt_array ( $ch, ($options + $defaults) );
		if (! $result = curl_exec ( $ch )) {
			trigger_error ( curl_error ( $ch ) );
		}
		curl_close ( $ch );
		return $result;
	}
	function execute($method, $url, $fields = '', $userAgent = '', $httpHeaders = '', $username = '', $password = '') {
		$ch = Curl::create ();
		
		if (false === $ch) {
			return false;
		}
		
		if (is_string ( $url ) && strlen ( $url )) {
			$ret = curl_setopt ( $ch, CURLOPT_URL, $url );
		} else {
			return false;
		}
		
		// 是否显示头部信息
		curl_setopt ( $ch, CURLOPT_HEADER, false );
		//
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		
		if ($username != '') {
			curl_setopt ( $ch, CURLOPT_USERPWD, $username . ':' . $password );
		}
		
		$method = strtolower ( $method );
		if ('post' == $method) {
			curl_setopt ( $ch, CURLOPT_POST, true );
			if (is_array ( $fields )) {
				$sets = array ();
				foreach ( $fields as $key => $val ) {
					$sets [] = $key . '=' . urlencode ( $val );
				}
				$fields = implode ( '&', $sets );
			}
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
		} else if ('put' == $method) {
			curl_setopt ( $ch, CURLOPT_PUT, true );
		}
		
		// curl_setopt($ch, CURLOPT_PROGRESS, true);
		// curl_setopt($ch, CURLOPT_VERBOSE, true);
		// curl_setopt($ch, CURLOPT_MUTE, false);
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 3 ); // 设置curl超时秒数，例如将信息POST出去3秒钟后自动结束运行。
		
		if (strlen ( $userAgent )) {
			curl_setopt ( $ch, CURLOPT_USERAGENT, $userAgent );
		}
		
		if (is_array ( $httpHeaders )) {
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $httpHeaders );
		}
		
		$ret = curl_exec ( $ch );
		
		if (curl_errno ( $ch )) {
			curl_close ( $ch );
			return array (
					curl_error ( $ch ),
					curl_errno ( $ch ) 
			);
		} else {
			curl_close ( $ch );
			if (! is_string ( $ret ) || ! strlen ( $ret )) {
				return false;
			}
			return $ret;
		}
	}
	function post($url, $fields, $userAgent = '', $httpHeaders = '', $username = '', $password = '') {
		$ret = Curl::execute ( 'POST', $url, $fields, $userAgent, $httpHeaders, $username, $password );
		return $ret;
		if (false === $ret) {
			return false;
		}
		
		if (is_array ( $ret )) {
			return false;
		}
		return $ret;
	}
	function get($url, $userAgent = '', $httpHeaders = '', $username = '', $password = '') {
		$ret = Curl::execute ( 'GET', $url, '', $userAgent, $httpHeaders, $username, $password );
		if (false === $ret) {
			return false;
		}
		
		if (is_array ( $ret )) {
			return false;
		}
		return $ret;
	}
	function create() {
		$ch = null;
		if (! function_exists ( 'curl_init' )) {
			return false;
		}
		$ch = curl_init ();
		if (! is_resource ( $ch )) {
			return false;
		}
		return $ch;
	}
}
?>  