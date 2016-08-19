<?php

/**
 * class CurlItems Curl请求类
 * Demo:
 * ********自定义传输方式**********
 * function http
 * $url = "http://31.buzzopt.com/index.php/User/test";
 * $method = "POST";
 * $postfields = serialize(array("test_1"=>"test+value1","test_2"=>"test_value2"));
 * $response = CurlItems::http($url,$method,$postfields);
 * 或者
 * $method = "GET";
 * $postfields = array("test_1"=>"test+value1","test_2"=>"test_value2");//参数数组格式
 * 或者
 * $postfields = "test_1"="test+value1"&"test_2"="test_value2";//url参数格式
 * $response = CurlItems::http($url,$method,$postfields);
 *
 *
 * ********使用post方式********
 * function post
 * $url = "http://31.buzzopt.com/index.php/User/test";
 * $postfields = serialize(array("test_1"=>"test+value1","test_2"=>"test_value2")); //各种数据都需要序列化后作为参数
 * $response = CurlItems::post($url,$postfields);
 *
 * ********使用get方式*********
 * function get
 * $url = "http://31.buzzopt.com/index.php/User/test";
 * $postfields = array("test_1"=>"test+value1","test_2"=>"test_value2");//参数数组格式
 * 或者
 * $postfields = "test_1"="test+value1"&"test_2"="test_value2";//url参数格式
 * $response = CurlItems::http($url,$method,$postfields);
 * 成功时返回响应信息，失败返回错误信息
 *
 *
 */
class CurlItems {
	
	/**
	 * 发送一个HTTP请求
	 *
	 * @param string $url
	 *        	想要请求的url地址
	 * @param string $method
	 *        	请求方式 一般POST 或 DELETE
	 * @param mixed $postfields
	 *        	要传输的参数内容 POST 方式请使用序列化后的字符串作为 $postfields , GET 方式请使用paramName=paramValue&paramName1=paramValue1这种格式或参数数组传递
	 * @param string $headers
	 *        	要设置的头文件信息
	 * @param int $timeout
	 *        	设置cURL允许执行的最长秒数
	 * @return string API results
	 * @author Ice_Tuzki
	 */
	function http($url, $method, $postfields = NULL, $headers = array('Expect:'), $timeout = 30) {
		$ci = curl_init ();
		/* Curl settings */
		curl_setopt ( $ci, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)" );
		curl_setopt ( $ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 ); // 设置HTTP 版本 CURL_HTTP_VERSION_1_0 强制使用 HTTP/1.0
		                                                               // curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);//在HTTP请求中包含一个"User-Agent: "头的字符串。$this->useragent :Sae T OAuth2 v0.1
		curl_setopt ( $ci, CURLOPT_CONNECTTIMEOUT, 5 );
		curl_setopt ( $ci, CURLOPT_TIMEOUT, $timeout ); // 设置cURL允许执行的最长秒数。 //$this->timeout 30
		curl_setopt ( $ci, CURLOPT_RETURNTRANSFER, true ); // 将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt ( $ci, CURLOPT_ENCODING, "" ); // HTTP请求头中"Accept-Encoding: "的值。支持的编码有"identity"，"deflate"和"gzip"。如果为空字符串""，请求头会发送所有支持的编码类型
		/* * ******************值有所修改，直接设定为false****************** */
		curl_setopt ( $ci, CURLOPT_SSL_VERIFYPEER, false ); // 禁用后cURL将终止从服务端进行验证。使用CURLOPT_CAINFO选项设置证书使用CURLOPT_CAPATH选项设置证书目录 如果CURLOPT_SSL_VERIFYPEER(默认值为2)被启用，CURLOPT_SSL_VERIFYHOST需要被设置成TRUE否则设置为FALSE
		                                                 // curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		                                                 // curl_setopt($ci, CURLOPT_HEADER, FALSE);
		                                                 // test($url);
		switch ($method) {
			case 'POST' :
				curl_setopt ( $ci, CURLOPT_POST, TRUE );
				if (is_string ( $postfields )) {
					if (! empty ( $postfields )) {
						curl_setopt ( $ci, CURLOPT_POSTFIELDS, $postfields );
					}
				} else {
					return array (
							"error" => "参数类型错误" 
					);
				}
				break;
			case 'GET' :
				curl_setopt ( $ci, CURLOPT_HTTPGET, true );
				if (is_array ( $postfields )) {
					$postfields = http_build_query ( $postfields );
				}
				if (is_string ( $postfields )) {
					if (! empty ( $postfields )) {
						$url = "{$url}?{$postfields}";
					}
				} else {
					return array (
							"error" => "参数类型错误" 
					);
				}
				break;
		}
		/*
		 * if ( isset($this->access_token) && $this->access_token )
		 * $headers[] = "Authorization: OAuth2 ".$this->access_token;
		 */
		
		$headers [] = "API-RemoteIP: " . $_SERVER ['REMOTE_ADDR'];
		curl_setopt ( $ci, CURLOPT_URL, $url );
		curl_setopt ( $ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $ci, CURLINFO_HEADER_OUT, TRUE );
		
		$response = curl_exec ( $ci );
		$http_code = curl_getinfo ( $ci, CURLINFO_HTTP_CODE );
		// $http_info = array_merge($http_info, curl_getinfo($ci));
		$http_info = $http_code;
		
		/*
		 * if ($debug) {
		 * echo "=====post data======\r\n";
		 * var_dump($postfields);
		 *
		 * echo '=====info====='."\r\n";
		 * print_r( curl_getinfo($ci) );
		 *
		 * echo '=====$response====='."\r\n";
		 * print_r( $response );
		 * }
		 */
		
		if ($response !== false) {
			$result = $response;
		} else {
			// 2013-3-4JIATAN
			$result = json_encode ( array (
					"error" => curl_error ( $ci ),
					"error_code" => curl_errno ( $ci ),
					"result" => false 
			) );
		}
		curl_close ( $ci );
		return $result;
	}
	
	/**
	 * 发送一个post方式的HTTP请求
	 *
	 * @param string $url
	 *        	想要请求的url地址
	 * @param mixed $postfields
	 *        	要传输的参数内容 请使用序列化后的字符串
	 * @param string $headers
	 *        	要设置的头文件信息
	 * @param int $timeout
	 *        	设置cURL允许执行的最长秒数
	 * @return string API results
	 * @author Ice_Tuzki
	 */
	public function post($url, $postfields = NULL, $headers = array(), $timeout = 30) {
		$method = "POST";
		$response = CurlItems::http ( $url, $method, $postfields, $headers = array (), $timeout = 30 );
		return $response;
	}
	
	/**
	 * 发送一个get方式的HTTP请求
	 *
	 * @param string $url
	 *        	想要请求的url地址
	 * @param mixed $postfields
	 *        	要传输的参数内容 请使用参数数组或者paramName=paramValue&paramName1=paramValue1这种格式
	 * @param string $headers
	 *        	要设置的头文件信息
	 * @param int $timeout
	 *        	设置cURL允许执行的最长秒数
	 * @return string API results
	 * @author Ice_Tuzki
	 */
	public function get($url, $postfields = NULL, $headers = array(), $timeout = 30) {
		$method = "GET";
		$response = CurlItems::http ( $url, $method, $postfields, $headers = array (), $timeout = 30 );
		return $response;
	}
}