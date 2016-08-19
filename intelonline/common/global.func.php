<?php
include ('common.func.php');
function list_gifts($gifts = null) {
	$arr = json_decode_zh ( $gifts );
	return implode ( ";", $arr );
}

// 自动加载类
function __autoload($class_name) {
	$filePath = '';
	$pathArray = array (
			'actionClass' => ROOT_PATH . 'action/',
			'libClass' => ROOT_PATH . 'lib/',
			'modelClass' => ROOT_PATH . 'model/',
			'apiClass' => ROOT_PATH . 'api/' 
	);
	
	$prefix = substr ( $class_name, 0, 3 );
	switch ($prefix) {
		case 'wap' :
			$pathArray ['acctionClass'] = ROOT_PATH . 'action/wap/';
			$pathArray ['modelClass'] = ROOT_PATH . 'model/wap/';
			break;
	}
	
	// 多级目录处理
	$classPath = '';
	$array = explode ( '_', $class_name );
	foreach ( $array as $val ) {
		$classPath .= $val . '/';
	}
	$classPath = str_replace ( $val . '/', '', $classPath );
	$classPath = $classPath . $val;
	// echo $classPath.'<br>';
	foreach ( $pathArray as $dir ) {
		$file = $dir . $classPath . '.class.php';
		if (is_file ( $file )) {
			require_once ($file);
		}
	}
	// 引用smarty库
	if (strtolower ( substr ( $class_name, 0, 6 ) ) == "smarty" && strlen ( $class_name ) > 6) {
		$filePath = ROOT_PATH . 'lib/libs/sysplugins/' . strtolower ( $class_name ) . ".php";
		require_once ($filePath);
	}
}

// 加载模块配置文件
function load_module_config($module) {
	
	// 多文件目录处理 20121220 xiaofeng 如果一月内没出问题则删除注释
	// $configFile = ROOT_PATH . 'config/module/' . strtolower($module) . '_config.inc.php';;
	$configPathArray = explode ( "_", $module );
	foreach ( $configPathArray as $val ) {
		$configPath .= $val . '/';
	}
	$configPath = str_replace ( $val . '/', '', $configPath );
	$configFile = ROOT_PATH . 'config/module/' . $configPath . $val . '_config.inc.php';
	if (file_exists ( $configFile )) {
		return require $configFile;
	} else {
		return false;
	}
}
//redirect
/**
 * 加载系统类
 * 
 * @param type $className        	
 * @return boolean
 */
function load_sys_class($className) {
	$loadFile = LIB_PATH . $className . '.class.php';
	if (file_exists ( $loadFile )) {
		require_once $loadFile;
	} else {
		return false;
	}
}

/**
 * 加载模块的用函数
 * 
 * @param type $module        	
 * @return boolean
 */
function load_module_fun($module) {
	$loadFile = ROOT_PATH . 'common/function/' . $module . '.func.php';
	if (file_exists ( $loadFile )) {
		require_once $loadFile;
	} else {
		return false;
	}
}

// 格式输出
function dump($data) {
	echo "<pre>";
	var_dump ( $data );
	echo "</pre>";
}

// 计算脚本执行时间s
function load_runtime() {
	if (defined ( 'MICROTIME' ) === true) {
		return microtime ( true ) - MICROTIME;
	} else {
		return 0.0000;
	}
}

/**
 * 使用反斜线引用字符串,如果参数为数组则遍历
 *
 * @param mixed $string
 *        	待转换的字符
 * @return mixed
 */
function addslashes_deep($string) {
	return is_array ( $string ) ? array_map ( 'addslashes_deep', $string ) : addslashes ( $string );
}

/**
 * 使用反斜线引用字符串,如果参数为数组则深度遍历
 *
 * @param mixed $string
 *        	待转换的字符
 * @return mixed
 */
function new_addslashes($string) {
	if (! is_array ( $string )) {
		return addslashes ( $string );
	}
	foreach ( $string as $key => $val ) {
		$string [$key] = new_addslashes ( $val );
		return $string;
	}
}

/**
 * 使用反斜线引用字符串
 *
 * @param mixed $string
 *        	待转换的对象
 * @return mixed
 */
function addslashes_deep_obj($obj) {
	if (is_object ( $obj )) {
		foreach ( $obj as $key => $val ) {
			$obj->$key = addslashes_deep ( $val );
		}
	} else {
		$obj = addslashes_deep ( $obj );
	}
	return $obj;
}

/**
 * 去掉字符串中的反斜线
 *
 * @param mixed $string
 *        	待转换的字符
 * @return mixed
 */
function stripslashes_deep($string) {
	return is_array ( $string ) ? array_map ( 'stripslashes_deep', $string ) : stripslashes ( $string );
}
function safe_replace($string) {
	$string = trim ( $string );
	$string = str_replace ( '%20', '', $string );
	$string = str_replace ( '%27', '', $string );
	$string = str_replace ( '%2527', '', $string );
	$string = str_replace ( '*', '', $string );
	$string = str_replace ( '"', '&quot;', $string );
	$string = str_replace ( "'", '', $string );
	$string = str_replace ( '"', '', $string );
	$string = str_replace ( ';', '', $string );
	$string = str_replace ( '<', '&lt;', $string );
	$string = str_replace ( '>', '&gt;', $string );
	$string = str_replace ( "{", '', $string );
	$string = str_replace ( '}', '', $string );
	$string = str_replace ( '\\', '', $string );
	return $string;
}

/**
 * 生成返回信息
 *
 * @param string $result
 *        	数据
 * @param int $error_code        	
 * @param int $type
 *        	1：表示系统错误，2：表示微薄错误
 * @return array
 */
function apiData($result, $error_code, $type = 1) {
	echo 'apidata 方法 在 global.func.php中 190<br/>';
	global $ErrorCode, $WeiboErrorCode;
	if ($type == 2) {
		$message = $WeiboErrorCode [$error_code];
	} else {
		$message = $ErrorCode [$error_code];
	}
	$message = empty ( $message ) ? '系统错误' : $message;
	$data = array (
			'error' => $message,
			'error_code' => $error_code,
			'result' => $result 
	);
	return $data;
}

/**
 * 判断email格式是否正确
 * 
 * @param
 *        	$email
 */
function is_email($email) {
	return strlen ( $email ) > 6 && preg_match ( "/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email );
}

/**
 * 字符截取 支持UTF8/GBK
 * 
 * @param
 *        	$string
 * @param
 *        	$length
 * @param
 *        	$dot
 */
function str_cut($string, $length, $dot = '...') {
	$strlen = strlen ( $string );
	if ($strlen <= $length)
		return $string;
	$string = str_replace ( array (
			' ',
			'&nbsp;',
			'&amp;',
			'&quot;',
			'&#039;',
			'&ldquo;',
			'&rdquo;',
			'&mdash;',
			'&lt;',
			'&gt;',
			'&middot;',
			'&hellip;' 
	), array (
			'∵',
			' ',
			'&',
			'"',
			"'",
			'“',
			'”',
			'—',
			'<',
			'>',
			'·',
			'…' 
	), $string );
	$strcut = '';
	if (strtolower ( CHARSET ) == 'utf-8') {
		$length = intval ( $length - strlen ( $dot ) - $length / 3 );
		$n = $tn = $noc = 0;
		while ( $n < strlen ( $string ) ) {
			$t = ord ( $string [$n] );
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n ++;
				$noc ++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t <= 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n ++;
			}
			if ($noc >= $length) {
				break;
			}
		}
		if ($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr ( $string, 0, $n );
		$strcut = str_replace ( array (
				'∵',
				'&',
				'"',
				"'",
				'“',
				'”',
				'—',
				'<',
				'>',
				'·',
				'…' 
		), array (
				' ',
				'&amp;',
				'&quot;',
				'&#039;',
				'&ldquo;',
				'&rdquo;',
				'&mdash;',
				'&lt;',
				'&gt;',
				'&middot;',
				'&hellip;' 
		), $strcut );
	} else {
		$dotlen = strlen ( $dot );
		$maxi = $length - $dotlen - 1;
		$current_str = '';
		$search_arr = array (
				'&',
				' ',
				'"',
				"'",
				'“',
				'”',
				'—',
				'<',
				'>',
				'·',
				'…',
				'∵' 
		);
		$replace_arr = array (
				'&amp;',
				'&nbsp;',
				'&quot;',
				'&#039;',
				'&ldquo;',
				'&rdquo;',
				'&mdash;',
				'&lt;',
				'&gt;',
				'&middot;',
				'&hellip;',
				' ' 
		);
		$search_flip = array_flip ( $search_arr );
		for($i = 0; $i < $maxi; $i ++) {
			$current_str = ord ( $string [$i] ) > 127 ? $string [$i] . $string [++ $i] : $string [$i];
			if (in_array ( $current_str, $search_arr )) {
				$key = $search_flip [$current_str];
				$current_str = str_replace ( $search_arr [$key], $replace_arr [$key], $current_str );
			}
			$strcut .= $current_str;
		}
	}
	return $strcut . $dot;
}

/**
 * 格式化文本域内容
 *
 * @param $string 文本域内容        	
 * @return string
 */
function trim_textarea($string) {
	$string = nl2br ( str_replace ( ' ', '&nbsp;', $string ) );
	return $string;
}

/**
 * 发送邮件
 *
 * @param string $send_to_mail
 *        	目标邮件
 * @param stinrg $subject
 *        	主题
 * @param string $body
 *        	邮件内容
 * @param string $extra_hdrs
 *        	附加信息
 * @param string $username
 *        	收件人
 * @param init $port
 *        	端口号
 * @param string $replyname
 *        	回复人
 * @param string $replymail
 *        	回复地址
 * @return array(bealoon,string) 返回数组包括两个元素，bealoon表示是否成功，string为提示信息
 */
function SendMail($send_to_mail, $subject, $body, $extra_hdrs, $username = '', $port = 25, $replyname = "Buzzopt", $replymail = MAIL_USER) {
	$mail = new PHPMailer ();
	$mail->IsSMTP (); // 邮件发送方式
	$mail->Host = MAIL_HOST; // SMTP服务器主机地址
	$mail->SMTPAuth = true; // 是否为可信任的SMTP
	$mail->Username = MAIL_USER; // SMTP 用户名 注意：普通邮件认证不需要加 @域名
	$mail->Password = MAIL_PWD; // SMTP 用户密码
	$mail->From = MAIL_HOST; // 发件人邮件地址
	$mail->FromName = "Buzzopt"; // 发件人
	$mail->Port = $port;
	$mail->CharSet = "UTF-8"; // 指定字符集
	$mail->Encoding = "base64";
	$mail->AddAddress ( $send_to_mail, $username ); // 添加发送目标地址
	$mail->AddReplyTo ( $replymail, $replyname ); // 添加回复地址
	$mail->IsHTML ( true ); // 邮件类型为HTML格式
	$mail->Subject = "=?UTF-8?B?" . base64_encode ( $subject ) . "?="; // 邮件主题
	$mail->Body = $body; // 邮件内容
	                     // $mail->Send()为邮件发送函数,不成功时执行if内容
	if (@! $mail->Send ()) {
		$results = array (
				"result" => false,
				"message" => $mail->ErrorInfo 
		);
		return $results;
	} else {
		$results = array (
				"result" => true,
				"message" => "邮件已经发送到{$send_to_mail}！" 
		);
		return $results;
	}
}

/**
 * xml转数组
 *
 * @param type $contents
 *        	xml信息
 * @param type $get_attributes        	
 * @param type $priority        	
 * @return type
 */
function xml2array($contents, $get_attributes = 1, $priority = 'tag') {
	if (! $contents)
		return array ();
	
	if (! function_exists ( 'xml_parser_create' )) {
		// print "'xml_parser_create()' function not found!";
		return array ();
	}
	
	// Get the XML parser of PHP - PHP must have this module for the parser to work
	$parser = xml_parser_create ();
	xml_parser_set_option ( $parser, XML_OPTION_TARGET_ENCODING, "UTF-8" ); // http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	xml_parser_set_option ( $parser, XML_OPTION_CASE_FOLDING, 0 );
	xml_parser_set_option ( $parser, XML_OPTION_SKIP_WHITE, 1 );
	xml_parse_into_struct ( $parser, trim ( $contents ), $xml_values );
	xml_parser_free ( $parser );
	
	if (! $xml_values) {
		return; // Hmm...
	}
	$xml_array = array ();
	$parents = array ();
	$opened_tags = array ();
	$arr = array ();
	
	$current = &$xml_array; // Refference
	                        // Go through the tags.
	$repeated_tag_index = array (); // Multiple tags with same name will be turned into an array
	foreach ( $xml_values as $data ) {
		unset ( $attributes, $value ); // Remove existing values, or there will be trouble
		                            // This command will extract these variables into the foreach scope
		                            // tag(string), type(string), level(int), attributes(array).
		extract ( $data ); // We could use the array by itself, but this cooler.
		
		$result = $priority == 'tag' ? (empty ( $value ) ? '' : $value) : array (
				'name' => $value 
		);
		$attributes_data = array ();
		
		// Set the attributes too.
		if (isset ( $attributes ) and $get_attributes) {
			foreach ( $attributes as $attr => $val ) {
				if ($priority == 'tag')
					$attributes_data [$attr] = $val;
				else
					$result ['attr'] [$attr] = $val; // Set all the attributes in a array called 'attr'
			}
		}
		
		// See tag status and do the needed.
		if ($type == "open") { // The starting of the tag '<tag>'
			$parent [$level - 1] = &$current;
			if (! is_array ( $current ) or (! in_array ( $tag, array_keys ( $current ) ))) { // Insert New tag
				$current [$tag] = $result;
				if ($attributes_data)
					$current [$tag . '_attr'] = $attributes_data;
				$repeated_tag_index [$tag . '_' . $level] = 1;
				
				$current = &$current [$tag];
			} else { // There was another element with the same tag name
				if (isset ( $current [$tag] [0] )) { // If there is a 0th element it is already an array
					$current [$tag] [$repeated_tag_index [$tag . '_' . $level]] = $result;
					$repeated_tag_index [$tag . '_' . $level] ++;
				} else { // This section will make the value an array if multiple tags with the same name appear together
					$current [$tag] = array (
							$current [$tag],
							$result 
					); // This will combine the existing item and the new item together to make an array
					$repeated_tag_index [$tag . '_' . $level] = 2;
					
					if (isset ( $current [$tag . '_attr'] )) { // The attribute of the last(0th) tag must be moved as well
						$current [$tag] ['0_attr'] = $current [$tag . '_attr'];
						unset ( $current [$tag . '_attr'] );
					}
				}
				$last_item_index = $repeated_tag_index [$tag . '_' . $level] - 1;
				$current = &$current [$tag] [$last_item_index];
			}
		} elseif ($type == "complete") { // Tags that ends in 1 line '<tag />'
		                                 // See if the key is already taken.
			if (! isset ( $current [$tag] )) { // New Key
				$current [$tag] = $result;
				$repeated_tag_index [$tag . '_' . $level] = 1;
				if ($priority == 'tag' and $attributes_data)
					$current [$tag . '_attr'] = $attributes_data;
			} else { // If taken, put all things inside a list(array)
				if (isset ( $current [$tag] [0] ) and is_array ( $current [$tag] )) { // If it is already an array...
				                                                             // ...push the new element into that array.
					$current [$tag] [$repeated_tag_index [$tag . '_' . $level]] = $result;
					
					if ($priority == 'tag' and $get_attributes and $attributes_data) {
						$current [$tag] [$repeated_tag_index [$tag . '_' . $level] . '_attr'] = $attributes_data;
					}
					$repeated_tag_index [$tag . '_' . $level] ++;
				} else { // If it is not an array...
					$current [$tag] = array (
							$current [$tag],
							$result 
					); // ...Make it an array using using the existing value and the new value
					$repeated_tag_index [$tag . '_' . $level] = 1;
					if ($priority == 'tag' and $get_attributes) {
						if (isset ( $current [$tag . '_attr'] )) { // The attribute of the last(0th) tag must be moved as well
							$current [$tag] ['0_attr'] = $current [$tag . '_attr'];
							unset ( $current [$tag . '_attr'] );
						}
						
						if ($attributes_data) {
							$current [$tag] [$repeated_tag_index [$tag . '_' . $level] . '_attr'] = $attributes_data;
						}
					}
					$repeated_tag_index [$tag . '_' . $level] ++; // 0 and 1 index is already taken
				}
			}
		} elseif ($type == 'close') { // End of tag '</tag>'
			$current = &$parent [$level - 1];
		}
	}
	
	return ($xml_array);
}

/**
 * 获取接口返回的xml数据并转换成数组
 *
 * @param string $url        	
 * @return arr
 */
function ApiXmlToArray($url) {
	set_time_limit ( 0 );
	$xml = @file_get_contents ( $url );
	return Xml::decode ( $xml );
}

/**
 * 将数组生成XML
 *
 * @param array $array        	
 * @param string $root        	
 * @return xml
 */
function array2xml($array, $root = 'root') {
	$dom = new Xml ( $root );
	$dom->createNode ( $array );
	return $dom->saveXML ();
}

/**
 * xml文件内容生成数组
 *
 * @param string $file
 *        	文件地址
 * @param type $root
 *        	节点
 * @return array
 */
function file_get_xmlarray($file, $root = null) {
	if (false === ($contents = @file_get_contents ( $file )) || ! ($arr = xml2array ( $contents ))) {
		return array ();
	}
	return $root ? $arr [$root] : $arr;
}
function p($a) {
	echo '<pre>';
	print_r ( $a );
	echo '</pre>';
}

/**
 * 通过传入的区间节点、数据数组及对应区间节点的值，返回数组
 *
 *
 * $arr1=array(0,1,5,10,20,50,100);
 * $arr2=array('a'=>0,'b'=>4,'c'=>6,'d'=>30,'e'=>80,'f'=>125);
 * $arr3=array(2,3,4,5,61,7,88);
 * 
 * @param array $intervals        	
 * @param array $arr        	
 * @param array $intervals_value        	
 * @return array
 */
function getDataFromIntervalArr($intervals, $arr, $intervals_value = null) {
	$data = array ();
	$intervals_arr = array ();
	if ($intervals_value != null) {
		if (count ( $intervals ) > count ( $intervals_value )) {
			die ( '您提供的区间值过少' );
		}
	}
	// 如果给出的区间值为空则
	if (! is_null ( $intervals_value )) {
		
		foreach ( $arr as $k => $v ) {
			$data [$k] ['key'] = $k;
			$data [$k] ['val'] = $v;
			
			$intervalsnum = count ( $intervals );
			for($i = 0; $i < $intervalsnum; $i ++) {
				if ($i != $intervalsnum - 1) {
					if ($v >= $intervals [$i] && $v < $intervals [$i + 1]) {
						$data [$k] ['intervals'] = $intervals [$i] . '-' . $intervals [$i + 1];
						$data [$k] ['score'] = $intervals_value [$i];
					}
				} else {
					if ($v >= $intervals [$i]) {
						$data [$k] ['intervals'] = $intervals [$i] . '-以上';
						$data [$k] ['score'] = $intervals_value [$i];
					}
				}
			}
		}
	} else {
		foreach ( $arr as $k => $v ) {
			$data [$k] ['key'] = $k;
			$data [$k] ['val'] = $v;
			
			$intervalsnum = count ( $intervals );
			for($i = 0; $i < $intervalsnum; $i ++) {
				if ($i != $intervalsnum - 1) {
					if ($v >= $intervals [$i] && $v < $intervals [$i + 1]) {
						$data [$k] ['intervals'] = $intervals [$i] . '-' . $intervals [$i + 1];
						$data [$k] ['score'] = $i;
					}
				} else {
					if ($v >= $intervals [$i]) {
						$data [$k] ['intervals'] = $intervals [$i] . '-以上';
						$data [$k] ['score'] = $i;
					}
				}
			}
		}
	}
	return $data;
}

/**
 * 通过传入的区间节点、数据值及对应区间节点的值，返回数组
 *
 *
 * $arr1=array(0,1,5,10,20,50,100);
 * $int
 * $arr3=array(2,3,4,5,61,7,88);
 * 
 * @param array $intervals        	
 * @param int $int        	
 * @param array $intervals_value        	
 * @return int
 */
function getDataFromIntervalInt($intervals, $int, $intervals_value = null) {
	$data = array ();
	$intervals_arr = array ();
	if ($intervals_value != null) {
		if (count ( $intervals ) > count ( $intervals_value )) {
			die ( '您提供的区间值过少' );
		}
	}
	// 如果给出的区间值为空则
	if (! is_null ( $intervals_value )) {
		$intervalsnum = count ( $intervals );
		for($i = 0; $i < $intervalsnum; $i ++) {
			if ($i != $intervalsnum - 1) {
				if ($int >= $intervals [$i] && $int < $intervals [$i + 1]) {
					$data = $intervals_value [$i];
				}
			} else {
				if ($int >= $intervals [$i]) {
					$data = $intervals_value [$i];
				}
			}
		}
	} else {
		$intervalsnum = count ( $intervals );
		for($i = 0; $i < $intervalsnum; $i ++) {
			if ($i != $intervalsnum - 1) {
				if ($int >= $intervals [$i] && $int < $intervals [$i + 1]) {
					$data = $i;
				}
			} else {
				if ($int >= $intervals [$i]) {
					$data = $i;
				}
			}
		}
	}
	return $data;
}

/**
 * 根据给出的数组（索引和数值），符号，返回饼状图
 *
 * @param array $data        	
 * @param string $numberSuffix        	
 * @return string
 */
function getPie2DChart($data, $numberSuffix = '', $type = 0) {
	if ($type) {
		$resultstr = '<chart  xAxisName="" yAxisName="" decimalPrecision="0" decimals="2" formatNumberScale="0" baseFontSize="12" numberSuffix="' . $numberSuffix . '" showAboutMenuItem="0"　showLabels="0">';
	} else {
		$resultstr = '<chart caption="" palette="2" animation="1" subCaption="" YAxisName="" showValues="0" numberPrefix="" numberSuffix="' . $numberSuffix . '" formatNumberScale="0" showPercentInToolTip="0" showLabels="0" showLegend="1">';
	}
	
	foreach ( $data as $name => $num ) {
		$resultstr .= '<set label="' . $name . '" value="' . $num . '" isSliced="0" />';
	}
	$resultstr .= '</chart>';
	return $resultstr;
}

/**
 * 根据给出的数组（索引和数值），栏目名，系列名，返回曲线图
 *
 * @param array $data        	
 * @param string $flag        	
 * @param string $value        	
 * @return string
 */
function getMsLineChart($data, $flag = '', $value = '') {
	$resultstr = "<chart  bgColor='F7F7F7, E9E9E9' numVDivLines='10' divLineAlpha='30'  labelPadding ='10' yAxisValuesPadding ='10' showValues='1' rotateValues='1'  valuePosition='Below' canvaspadding='10'>";
	foreach ( $data as $k => $v ) {
		$flag .= "<category label='" . $k . "' />";
		$value .= "<set value='" . $v . "' />";
	}
	$resultstr .= "<categories>$flag</categories>";
	$resultstr .= "<dataset seriesName='' >$value</dataset>";
	$resultstr .= "</chart>";
	return $resultstr;
}

/**
 * 求数组的平均值
 *
 * 通过传入的一维数组，求算平均值
 *
 * @param array $data        	
 * @return double
 */
function getAverageFromArr($arr) {
	$value = 0;
	if (is_array ( $arr )) {
		$sum = array_sum ( $arr );
		$count = count ( $arr );
		$value = $sum / $count;
	}
	return $value;
}

/**
 * 求标准差
 *
 * 通过传入的一维数组，求算标准差
 *
 * @param array $arr        	
 * @param bool $flag
 *        	是否为抽样数据
 * @return int
 */
function getStandardDeviation($arr, $flag = false) {
	$value = 0;
	if (is_array ( $arr )) {
		// 求平均值
		$average = getAverageFromArr ( $arr );
		// 元素个数
		$count = count ( $arr );
		if ($count == 1) {
			return $arr [0];
		}
		// 标准差开根之前的总和
		$sum_average = 0;
		if ($flag) {
			foreach ( $arr as $v ) {
				$sum_average += ($v - $average) * ($v - $average) / ($count - 1);
			}
		} else {
			foreach ( $arr as $v ) {
				$sum_average += ($v - $average) * ($v - $average) / $count;
			}
		}
		
		// 标准差
		$value = sqrt ( $sum_average );
	}
	return $value;
}

/**
 * +----------------------------------------------------------
 * Ajax方式返回数据到客户端
 * +----------------------------------------------------------
 * 
 * @access protected
 *         +----------------------------------------------------------
 * @param mixed $data
 *        	要返回的数据
 * @param String $info
 *        	提示信息
 * @param boolean $status
 *        	返回状态
 * @param String $status
 *        	ajax返回类型 JSON XML
 *        	+----------------------------------------------------------
 * @return void +----------------------------------------------------------
 */
function ajaxReturn($data, $info = '', $status = 1, $type = '') {
	$result = array ();
	$result ['status'] = $status;
	$result ['info'] = $info;
	$result ['data'] = $data;
	// 扩展ajax返回数据, 在Action中定义function ajaxAssign(&$result){} 方法 扩展ajax返回数据。
	if (method_exists ( $this, 'ajaxAssign' ))
		$this->ajaxAssign ( $result );
	if (empty ( $type ))
		$type = DEFAULT_AJAX_RETURN;
	if (strtoupper ( $type ) == 'JSON') {
		// 返回JSON数据格式到客户端 包含状态信息
		header ( 'Content-Type:text/html; charset=utf-8' );
		exit ( json_encode ( $result ) );
	} elseif (strtoupper ( $type ) == 'XML') {
		// 返回xml格式数据
		header ( 'Content-Type:text/xml; charset=utf-8' );
		exit ( xml_encode ( $result ) );
	} elseif (strtoupper ( $type ) == 'EVAL') {
		// 返回可执行的js脚本
		header ( 'Content-Type:text/html; charset=utf-8' );
		exit ( $data );
	} else {
		// TODO 增加其它格式
	}
}

/**
 * 从缓存中读取地区信息
 *
 * @param type $apicode        	
 * @return type
 */
function getCity($apicode = 0) {
	$cacheFile = new CacheFile ();
	$cacheFile->init ( array (
			'dir' => 'cache/system',
			'depth' => 1 
	) );
	$city = $cacheFile->get ( "city" );
	if (! $city) {
		$db = new user ();
		$db->db->tableName = "bz_location";
		$result = $db->select ( '*' );
		if ($result) {
			foreach ( $result as $key => $val ) {
				$city [$val ['id']] = $val;
			}
		}
		$cacheFile->set ( "city", $city );
		getCity ( $apicode );
	}
	$city_array = array ();
	foreach ( $city as $key => $val ) {
		if ($val ['provincecode'] == $apicode) {
			$city_array [$key] = $val;
		}
	}
	return $city_array;
}

/**
 * 计数统计
 *
 * @param unknown_type $fileanme        	
 */
function CountStatistics($fileanme = 'all_count') {
	$baseinfo = ROOT_PATH . 'cache/system/counts/';
	$fileanme = $baseinfo . $fileanme . '.txt';
	if (! is_dir ( $baseinfo )) {
		@mkdir ( $baseinfo );
	}
	
	if (! is_file ( $fileanme )) {
		file_put_contents ( $fileanme, 0 );
	} else {
		$count = file_get_contents ( $fileanme );
		$count = intval ( $count );
		$count += 1;
		file_put_contents ( $fileanme, $count );
	}
}

/**
 * 把微博内容转化成weibo.com里面的样式
 *
 * @param type $str        	
 * @return type
 */
function magicweibo($str) {
	$str = d2l ( $str ); // 短链
	$str = topic ( $str ); // 话题 ##
	$str = mention ( $str ); // 提及我 @
	$str = e2u ( $str ); // 表情
	return $str;
}

/**
 * 短链替换
 *
 * @param type $str        	
 * @return type
 */
function d2l($str) {
	$zz = '/http:\/\/t.cn\/\w+/i';
	if (preg_match_all ( $zz, $str, $return )) {
		foreach ( $return [0] as $s ) {
			$replace [] = "<a href=" . $s . " target='__blank'>" . $s . "</a>";
		}
		$return = array_unique ( $return [0] );
		$replace = array_unique ( $replace );
		$str = str_replace ( $return, $replace, $str );
	}
	return $str;
}

/**
 * 话题替换(#)
 *
 * @param type $str        	
 * @return type
 */
function topic($str) {
	$huati = "/http:\/\/huati.weibo.com\/#[\x80-\xff0-9a-zA-Z_-]+#/";
	if (preg_match_all ( $huati, $str, $return )) {
		foreach ( $return [0] as $s ) {
			$s = trim ( $s, '#' );
			$replace [] = "<a href=" . $s . " target='__blank'>" . $s . "</a>";
		}
		$return = array_unique ( $return [0] );
		$replace = array_unique ( $replace );
		$str = str_replace ( $return, $replace, $str );
	}
	return $str;
}

/**
 * 将@的微薄名称增加链接
 *
 * @param type $str        	
 * @return type
 */
function mention($str) {
	$tihuan = '/http:\/\/weibo.com\/@[\x80-\xff0-9a-zA-Z_-]+/';
	if (preg_match_all ( $tihuan, $str, $return )) {
		foreach ( $return [0] as $s ) {
			$s = trim ( $s, '@' );
			$replace [] = "<a href=" . $s . " target='__blank'>" . $s . "</a>";
		}
		$return = array_unique ( $return [0] );
		$replace = array_unique ( $replace );
		$str = str_replace ( $return, $replace, $str );
	}
	return $str;
}

/**
 * 表情替换
 *
 * @param type $str        	
 * @return type
 */
function e2u($str) {
	$bt = facePath ();
	return strtr ( $str, $bt );
}

/**
 * 表情路径
 *
 * @return string
 */
function facePath($str) {
	$arr = array (
			"[织]" => '<img type="face" title="[织]" src="/images/emotions/855.gif"/>',
			"[神马]" => '<img type="face" title="[神马]" src="/images/emotions/857.gif"/>',
			"[浮云]" => '<img type="face" title="[浮云]" src="/images/emotions/858.gif"/>',
			"[给力]" => '<img type="face" title="[给力]" src="/images/emotions/859.gif"/>',
			"[围观]" => '<img type="face" title="[围观]" src="/images/emotions/863.gif"/>',
			"[威武]" => '<img type="face" title="[威武]" src="/images/emotions/866.gif"/>',
			"[熊猫]" => '<img type="face" title="[熊猫]" src="/images/emotions/861.gif"/>',
			"[兔子]" => '<img type="face" title="[兔子]" src="/images/emotions/856.gif"/>',
			"[奥特曼]" => '<img type="face" title="[奥特曼]" src="/images/emotions/865.gif"/>',
			"[囧]" => '<img type="face" title="[囧]" src="/images/emotions/869.gif"/>',
			"[互粉]" => '<img type="face" title="[互粉]" src="/images/emotions/862.gif"/>',
			"[礼物]" => '<img type="face" title="[礼物]" src="/images/emotions/716.gif"/>',
			"[呵呵]" => '<img type="face" title="[呵呵]" src="/images/emotions/525.gif"/>',
			"[嘻嘻]" => '<img type="face" title="[嘻嘻]" src="/images/emotions/518.gif"/>',
			"[哈哈]" => '<img type="face" title="[哈哈]" src="/images/emotions/476.gif"/>',
			"[可爱]" => '<img type="face" title="[可爱]" src="/images/emotions/517.gif"/>',
			"[可怜]" => '<img type="face" title="[可怜]" src="/images/emotions/471.gif"/>',
			"[挖鼻屎]" => '<img type="face" title="[挖鼻屎]" src="/images/emotions/519.gif"/>',
			"[吃惊]" => '<img type="face" title="[吃惊]" src="/images/emotions/487.gif"/>',
			"[害羞]" => '<img type="face" title="[害羞]" src="/images/emotions/523.gif"/>',
			"[挤眼]" => '<img type="face" title="[挤眼]" src="/images/emotions/462.gif"/>',
			"[闭嘴]" => '<img type="face" title="[闭嘴]" src="/images/emotions/488.gif"/>',
			"[鄙视]" => '<img type="face" title="[鄙视]" src="/images/emotions/485.gif"/>',
			"[爱你]" => '<img type="face" title="[爱你]" src="/images/emotions/526.gif"/>',
			"[泪]" => '<img type="face" title="[泪]" src="/images/emotions/524.gif"/>',
			"[偷笑]" => '<img type="face" title="[偷笑]" src="/images/emotions/513.gif"/>',
			"[亲亲]" => '<img type="face" title="[亲亲]" src="/images/emotions/463.gif"/>',
			"[生病]" => '<img type="face" title="[生病]" src="/images/emotions/468.gif"/>',
			"[太开心]" => '<img type="face" title="[太开心]" src="/images/emotions/465.gif"/>',
			"[懒得理你]" => '<img type="face" title="[懒得理你]" src="/images/emotions/466.gif"/>',
			"[右哼哼]" => '<img type="face" title="[右哼哼]" src="/images/emotions/478.gif"/>',
			"[左哼哼]" => '<img type="face" title="[左哼哼]" src="/images/emotions/479.gif"/>',
			"[嘘]" => '<img type="face" title="[嘘]" src="/images/emotions/477.gif"/>',
			"[衰]" => '<img type="face" title="[衰]" src="/images/emotions/489.gif"/>',
			"[委屈]" => '<img type="face" title="[委屈]" src="/images/emotions/474.gif"/>',
			"[吐]" => '<img type="face" title="[吐]" src="/images/emotions/473.gif"/>',
			"[打哈欠]" => '<img type="face" title="[打哈欠]" src="/images/emotions/467.gif"/>',
			"[抱抱]" => '<img type="face" title="[抱抱]" src="/images/emotions/510.gif"/>',
			"[怒]" => '<img type="face" title="[怒]" src="/images/emotions/511.gif"/>',
			"[疑问]" => '<img type="face" title="[疑问]" src="/images/emotions/480.gif"/>',
			"[馋嘴]" => '<img type="face" title="[馋嘴]" src="/images/emotions/505.gif"/>',
			"[拜拜]" => '<img type="face" title="[拜拜]" src="/images/emotions/486.gif"/>',
			"[思考]" => '<img type="face" title="[思考]" src="/images/emotions/475.gif"/>',
			"[汗]" => '<img type="face" title="[汗]" src="/images/emotions/520.gif"/>',
			"[困]" => '<img type="face" title="[困]" src="/images/emotions/521.gif"/>',
			"[睡觉]" => '<img type="face" title="[睡觉]" src="/images/emotions/522.gif"/>',
			"[钱]" => '<img type="face" title="[钱]" src="/images/emotions/483.gif"/>',
			"[失望]" => '<img type="face" title="[失望]" src="/images/emotions/470.gif"/>',
			"[酷]" => '<img type="face" title="[酷]" src="/images/emotions/492.gif"/>',
			"[花心]" => '<img type="face" title="[花心]" src="/images/emotions/509.gif"/>',
			"[哼]" => '<img type="face" title="[哼]" src="/images/emotions/508.gif"/>',
			"[鼓掌]" => '<img type="face" title="[鼓掌]" src="/images/emotions/504.gif"/>',
			"[晕]" => '<img type="face" title="[晕]" src="/images/emotions/507.gif"/>',
			"[悲伤]" => '<img type="face" title="[悲伤]" src="/images/emotions/484.gif"/>',
			"[抓狂]" => '<img type="face" title="[抓狂]" src="/images/emotions/514.gif"/>',
			"[黑线]" => '<img type="face" title="[黑线]" src="/images/emotions/472.gif"/>',
			"[阴险]" => '<img type="face" title="[阴险]" src="/images/emotions/481.gif"/>',
			"[怒骂]" => '<img type="face" title="[怒骂]" src="/images/emotions/464.gif"/>',
			"[心]" => '<img type="face" title="[心]" src="/images/emotions/506.gif"/>',
			"[伤心]" => '<img type="face" title="[伤心]" src="/images/emotions/867.gif"/>',
			"[猪头]" => '<img type="face" title="[猪头]" src="/images/emotions/873.gif"/>',
			"[ok]" => '<img type="face" title="[ok]" src="/images/emotions/497.gif"/>',
			"[耶]" => '<img type="face" title="[耶]" src="/images/emotions/502.gif"/>',
			"[good]" => '<img type="face" title="[good]" src="/images/emotions/494.gif"/>',
			"[不要]" => '<img type="face" title="[不要]" src="/images/emotions/496.gif"/>',
			"[赞]" => '<img type="face" title="[赞]" src="/images/emotions/501.gif"/>',
			"[来]" => '<img type="face" title="[来]" src="/images/emotions/493.gif"/>',
			"[弱]" => '<img type="face" title="[弱]" src="/images/emotions/499.gif"/>',
			"[蜡烛]" => '<img type="face" title="[蜡烛]" src="/images/emotions/782.gif"/>',
			"[蛋糕]" => '<img type="face" title="[蛋糕]" src="/images/emotions/717.gif"/>',
			"[钟]" => '<img type="face" title="[钟]" src="/images/emotions/741.gif"/>',
			"[话筒]" => '<img type="face" title="[话筒]" src="/images/emotions/753.gif"/>',
			"[微博三岁啦]" => '<img type="face" title="[微博三岁啦]" src="/images/emotions/187.gif"/>',
			"[微博三周年]" => '<img type="face" title="[微博三周年]" src="/images/emotions/790.gif"/>',
			"[笑哈哈]" => '<img type="face" title="[笑哈哈]" src="/images/emotions/104.gif"/>',
			"[ali扑倒]" => '<img type="face" title="[ali扑倒]" src="/images/emotions/1314.gif"/>',
			"[群体围观]" => '<img type="face" title="[群体围观]" src="/images/emotions/113.gif"/>',
			"[亲一口]" => '<img type="face" title="[亲一口]" src="/images/emotions/141.gif"/>',
			"[皇小冠]" => '<img type="face" title="[皇小冠]" src="/images/emotions/773.gif"/>',
			"[din推撞]" => '<img type="face" title="[din推撞]" src="/images/emotions/290.gif"/>',
			"[bed凌乱]" => '<img type="face" title="[bed凌乱]" src="/images/emotions/590.gif"/>',
			"[g头晕]" => '<img type="face" title="[g头晕]" src="/images/emotions/555.gif"/>',
			"[lb味]" => '<img type="face" title="[lb味]" src="/images/emotions/341.gif"/>',
			"[lt羞]" => '<img type="face" title="[lt羞]" src="/images/emotions/396.gif"/>',
			"[好爱哦]" => '<img type="face" title="[好爱哦]" src="/images/emotions/127.gif"/>',
			"[中箭]" => '<img type="face" title="[中箭]" src="/images/emotions/160.gif"/>',
			"[玫瑰]" => '<img type="face" title="[玫瑰]" src="/images/emotions/175.gif"/>',
			"[羞嗒嗒]" => '<img type="face" title="[羞嗒嗒]" src="/images/emotions/115.gif"/>',
			"[七夕]" => '<img type="face" title="[七夕]" src="/images/emotions/184.gif"/>',
			"[国旗]" => '<img type="face" title="[国旗]" src="/images/emotions/1506.gif"/>',
			"[lt阴险]" => '<img type="face" title="[lt阴险]" src="/images/emotions/394.gif"/>',
			"[达人一周年]" => '<img type="face" title="[达人一周年]" src="/images/emotions/772.gif"/>',
			"[洪水]" => '<img type="face" title="[洪水]" src="/images/emotions/698.gif"/>',
			"[雨伞]" => '<img type="face" title="[雨伞]" src="/images/emotions/738.gif"/>',
			"[下雨]" => '<img type="face" title="[下雨]" src="/images/emotions/707.gif"/>',
			"[得意地笑]" => '<img type="face" title="[得意地笑]" src="/images/emotions/105.gif"/>',
			"[泪流满面]" => '<img type="face" title="[泪流满面]" src="/images/emotions/108.gif"/>',
			"[cai开心]" => '<img type="face" title="[cai开心]" src="/images/emotions/270.gif"/>',
			"[cai肚腩]" => '<img type="face" title="[cai肚腩]" src="/images/emotions/276.gif"/>',
			"[加油啊]" => '<img type="face" title="[加油啊]" src="/images/emotions/140.gif"/>',
			"[bobo纠结]" => '<img type="face" title="[bobo纠结]" src="/images/emotions/669.gif"/>',
			"[转发]" => '<img type="face" title="[转发]" src="/images/emotions/103.gif"/>',
			"[噢耶]" => '<img type="face" title="[噢耶]" src="/images/emotions/106.gif"/>',
			"[偷乐]" => '<img type="face" title="[偷乐]" src="/images/emotions/107.gif"/>',
			"[巨汗]" => '<img type="face" title="[巨汗]" src="/images/emotions/109.gif"/>',
			"[抠鼻屎]" => '<img type="face" title="[抠鼻屎]" src="/images/emotions/110.gif"/>',
			"[求关注]" => '<img type="face" title="[求关注]" src="/images/emotions/111.gif"/>',
			"[真V5]" => '<img type="face" title="[真V5]" src="/images/emotions/112.gif"/>',
			"[hold住]" => '<img type="face" title="[hold住]" src="/images/emotions/114.gif"/>',
			"[非常汗]" => '<img type="face" title="[非常汗]" src="/images/emotions/116.gif"/>',
			"[许愿]" => '<img type="face" title="[许愿]" src="/images/emotions/117.gif"/>',
			"[崩溃]" => '<img type="face" title="[崩溃]" src="/images/emotions/118.gif"/>',
			"[好囧]" => '<img type="face" title="[好囧]" src="/images/emotions/119.gif"/>',
			"[震惊]" => '<img type="face" title="[震惊]" src="/images/emotions/120.gif"/>',
			"[别烦我]" => '<img type="face" title="[别烦我]" src="/images/emotions/121.gif"/>',
			"[不好意思]" => '<img type="face" title="[不好意思]" src="/images/emotions/122.gif"/>',
			"[纠结]" => '<img type="face" title="[纠结]" src="/images/emotions/123.gif"/>',
			"[拍手]" => '<img type="face" title="[拍手]" src="/images/emotions/124.gif"/>',
			"[给劲]" => '<img type="face" title="[给劲]" src="/images/emotions/125.gif"/>',
			"[好喜欢]" => '<img type="face" title="[好喜欢]" src="/images/emotions/126.gif"/>',
			"[路过这儿]" => '<img type="face" title="[路过这儿]" src="/images/emotions/128.gif"/>',
			"[悲催]" => '<img type="face" title="[悲催]" src="/images/emotions/129.gif"/>',
			"[不想上班]" => '<img type="face" title="[不想上班]" src="/images/emotions/130.gif"/>',
			"[躁狂症]" => '<img type="face" title="[躁狂症]" src="/images/emotions/131.gif"/>',
			"[甩甩手]" => '<img type="face" title="[甩甩手]" src="/images/emotions/132.gif"/>',
			"[瞧瞧]" => '<img type="face" title="[瞧瞧]" src="/images/emotions/133.gif"/>',
			"[同意]" => '<img type="face" title="[同意]" src="/images/emotions/134.gif"/>',
			"[喝多了]" => '<img type="face" title="[喝多了]" src="/images/emotions/135.gif"/>',
			"[啦啦啦啦]" => '<img type="face" title="[啦啦啦啦]" src="/images/emotions/136.gif"/>',
			"[杰克逊]" => '<img type="face" title="[杰克逊]" src="/images/emotions/137.gif"/>',
			"[雷锋]" => '<img type="face" title="[雷锋]" src="/images/emotions/138.gif"/>',
			"[传火炬]" => '<img type="face" title="[传火炬]" src="/images/emotions/139.gif"/>',
			"[放假啦]" => '<img type="face" title="[放假啦]" src="/images/emotions/142.gif"/>',
			"[立志青年]" => '<img type="face" title="[立志青年]" src="/images/emotions/143.gif"/>',
			"[下班]" => '<img type="face" title="[下班]" src="/images/emotions/144.gif"/>',
			"[困死了]" => '<img type="face" title="[困死了]" src="/images/emotions/145.gif"/>',
			"[好棒]" => '<img type="face" title="[好棒]" src="/images/emotions/146.gif"/>',
			"[有鸭梨]" => '<img type="face" title="[有鸭梨]" src="/images/emotions/147.gif"/>',
			"[膜拜了]" => '<img type="face" title="[膜拜了]" src="/images/emotions/148.gif"/>',
			"[互相膜拜]" => '<img type="face" title="[互相膜拜]" src="/images/emotions/149.gif"/>',
			"[拍砖]" => '<img type="face" title="[拍砖]" src="/images/emotions/150.gif"/>',
			"[互相拍砖]" => '<img type="face" title="[互相拍砖]" src="/images/emotions/151.gif"/>',
			"[采访]" => '<img type="face" title="[采访]" src="/images/emotions/152.gif"/>',
			"[发表言论]" => '<img type="face" title="[发表言论]" src="/images/emotions/153.gif"/>',
			"[愚人节]" => '<img type="face" title="[愚人节]" src="/images/emotions/154.gif"/>',
			"[复活节]" => '<img type="face" title="[复活节]" src="/images/emotions/155.gif"/>',
			"[想一想]" => '<img type="face" title="[想一想]" src="/images/emotions/156.gif"/>',
			"[放电抛媚]" => '<img type="face" title="[放电抛媚]" src="/images/emotions/157.gif"/>',
			"[霹雳]" => '<img type="face" title="[霹雳]" src="/images/emotions/158.gif"/>',
			"[被电]" => '<img type="face" title="[被电]" src="/images/emotions/159.gif"/>',
			"[丘比特]" => '<img type="face" title="[丘比特]" src="/images/emotions/161.gif"/>',
			"[牛]" => '<img type="face" title="[牛]" src="/images/emotions/162.gif"/>',
			"[推荐]" => '<img type="face" title="[推荐]" src="/images/emotions/163.gif"/>',
			"[赞啊]" => '<img type="face" title="[赞啊]" src="/images/emotions/164.gif"/>',
			"[招财]" => '<img type="face" title="[招财]" src="/images/emotions/165.gif"/>',
			"[挤火车]" => '<img type="face" title="[挤火车]" src="/images/emotions/166.gif"/>',
			"[赶火车]" => '<img type="face" title="[赶火车]" src="/images/emotions/167.gif"/>',
			"[金元宝]" => '<img type="face" title="[金元宝]" src="/images/emotions/168.gif"/>',
			"[福到啦]" => '<img type="face" title="[福到啦]" src="/images/emotions/169.gif"/>',
			"[红包拿来]" => '<img type="face" title="[红包拿来]" src="/images/emotions/170.gif"/>',
			"[萌翻]" => '<img type="face" title="[萌翻]" src="/images/emotions/171.gif"/>',
			"[收藏]" => '<img type="face" title="[收藏]" src="/images/emotions/172.gif"/>',
			"[拜年了]" => '<img type="face" title="[拜年了]" src="/images/emotions/1594.gif"/>',
			"[龙啸]" => '<img type="face" title="[龙啸]" src="/images/emotions/174.gif"/>',
			"[放鞭炮]" => '<img type="face" title="[放鞭炮]" src="/images/emotions/176.gif"/>',
			"[发红包]" => '<img type="face" title="[发红包]" src="/images/emotions/177.gif"/>',
			"[大红灯笼]" => '<img type="face" title="[大红灯笼]" src="/images/emotions/178.gif"/>',
			"[耍花灯]" => '<img type="face" title="[耍花灯]" src="/images/emotions/179.gif"/>',
			"[元宵快乐]" => '<img type="face" title="[元宵快乐]" src="/images/emotions/180.gif"/>',
			"[吃汤圆]" => '<img type="face" title="[吃汤圆]" src="/images/emotions/181.gif"/>',
			"[喜得金牌]" => '<img type="face" title="[喜得金牌]" src="/images/emotions/182.gif"/>',
			"[奥运铜牌]" => '<img type="face" title="[奥运铜牌]" src="/images/emotions/183.gif"/>',
			"[冠军诞生]" => '<img type="face" title="[冠军诞生]" src="/images/emotions/185.gif"/>',
			"[德国队加油]" => '<img type="face" title="[德国队加油]" src="/images/emotions/186.gif"/>',
			"[奥运银牌]" => '<img type="face" title="[奥运银牌]" src="/images/emotions/188.gif"/>',
			"[夺冠感动]" => '<img type="face" title="[夺冠感动]" src="/images/emotions/189.gif"/>',
			"[葡萄牙队加油]" => '<img type="face" title="[葡萄牙队加油]" src="/images/emotions/190.gif"/>',
			"[西班牙队加油]" => '<img type="face" title="[西班牙队加油]" src="/images/emotions/191.gif"/>',
			"[奥运金牌]" => '<img type="face" title="[奥运金牌]" src="/images/emotions/192.gif"/>',
			"[意大利队加油]" => '<img type="face" title="[意大利队加油]" src="/images/emotions/193.gif"/>',
			"[xb自信]" => '<img type="face" title="[xb自信]" src="/images/emotions/194.gif"/>',
			"[xb转]" => '<img type="face" title="[xb转]" src="/images/emotions/195.gif"/>',
			"[xb转圈]" => '<img type="face" title="[xb转圈]" src="/images/emotions/196.gif"/>',
			"[xb指指]" => '<img type="face" title="[xb指指]" src="/images/emotions/197.gif"/>',
			"[xb招手]" => '<img type="face" title="[xb招手]" src="/images/emotions/198.gif"/>',
			"[xb照镜]" => '<img type="face" title="[xb照镜]" src="/images/emotions/199.gif"/>',
			"[xb雨]" => '<img type="face" title="[xb雨]" src="/images/emotions/200.gif"/>',
			"[xb坏笑]" => '<img type="face" title="[xb坏笑]" src="/images/emotions/201.gif"/>',
			"[xb疑惑]" => '<img type="face" title="[xb疑惑]" src="/images/emotions/202.gif"/>',
			"[xb摇摆]" => '<img type="face" title="[xb摇摆]" src="/images/emotions/203.gif"/>',
			"[xb眼镜]" => '<img type="face" title="[xb眼镜]" src="/images/emotions/204.gif"/>',
			"[xb压力]" => '<img type="face" title="[xb压力]" src="/images/emotions/205.gif"/>',
			"[xb星]" => '<img type="face" title="[xb星]" src="/images/emotions/206.gif"/>',
			"[xb兴奋]" => '<img type="face" title="[xb兴奋]" src="/images/emotions/207.gif"/>',
			"[xb喜欢]" => '<img type="face" title="[xb喜欢]" src="/images/emotions/208.gif"/>',
			"[xb小花]" => '<img type="face" title="[xb小花]" src="/images/emotions/209.gif"/>',
			"[xb无奈]" => '<img type="face" title="[xb无奈]" src="/images/emotions/210.gif"/>',
			"[xb捂脸]" => '<img type="face" title="[xb捂脸]" src="/images/emotions/211.gif"/>',
			"[xb天使]" => '<img type="face" title="[xb天使]" src="/images/emotions/212.gif"/>',
			"[xb太阳]" => '<img type="face" title="[xb太阳]" src="/images/emotions/213.gif"/>',
			"[xb睡觉]" => '<img type="face" title="[xb睡觉]" src="/images/emotions/214.gif"/>',
			"[xb甩葱]" => '<img type="face" title="[xb甩葱]" src="/images/emotions/215.gif"/>',
			"[xb生日]" => '<img type="face" title="[xb生日]" src="/images/emotions/216.gif"/>',
			"[xb扇子]" => '<img type="face" title="[xb扇子]" src="/images/emotions/217.gif"/>',
			"[xb伤心]" => '<img type="face" title="[xb伤心]" src="/images/emotions/218.gif"/>',
			"[xb揉]" => '<img type="face" title="[xb揉]" src="/images/emotions/219.gif"/>',
			"[xb求神]" => '<img type="face" title="[xb求神]" src="/images/emotions/220.gif"/>',
			"[xb青蛙]" => '<img type="face" title="[xb青蛙]" src="/images/emotions/221.gif"/>',
			"[xb期待]" => '<img type="face" title="[xb期待]" src="/images/emotions/222.gif"/>',
			"[xb泡澡]" => '<img type="face" title="[xb泡澡]" src="/images/emotions/223.gif"/>',
			"[xb怒]" => '<img type="face" title="[xb怒]" src="/images/emotions/224.gif"/>',
			"[xb努力]" => '<img type="face" title="[xb努力]" src="/images/emotions/225.gif"/>',
			"[xb拇指]" => '<img type="face" title="[xb拇指]" src="/images/emotions/226.gif"/>',
			"[xb喵]" => '<img type="face" title="[xb喵]" src="/images/emotions/227.gif"/>',
			"[xb喇叭]" => '<img type="face" title="[xb喇叭]" src="/images/emotions/228.gif"/>',
			"[xb哭]" => '<img type="face" title="[xb哭]" src="/images/emotions/229.gif"/>',
			"[xb看书]" => '<img type="face" title="[xb看书]" src="/images/emotions/230.gif"/>',
			"[xb开餐]" => '<img type="face" title="[xb开餐]" src="/images/emotions/231.gif"/>',
			"[xb举手]" => '<img type="face" title="[xb举手]" src="/images/emotions/232.gif"/>',
			"[xb奸笑]" => '<img type="face" title="[xb奸笑]" src="/images/emotions/233.gif"/>',
			"[xb昏]" => '<img type="face" title="[xb昏]" src="/images/emotions/234.gif"/>',
			"[xb挥手]" => '<img type="face" title="[xb挥手]" src="/images/emotions/235.gif"/>',
			"[xb欢乐]" => '<img type="face" title="[xb欢乐]" src="/images/emotions/236.gif"/>',
			"[xb喝茶]" => '<img type="face" title="[xb喝茶]" src="/images/emotions/237.gif"/>',
			"[xb汗]" => '<img type="face" title="[xb汗]" src="/images/emotions/238.gif"/>',
			"[xb害羞]" => '<img type="face" title="[xb害羞]" src="/images/emotions/239.gif"/>',
			"[xb害怕]" => '<img type="face" title="[xb害怕]" src="/images/emotions/240.gif"/>',
			"[xb风吹]" => '<img type="face" title="[xb风吹]" src="/images/emotions/241.gif"/>',
			"[xb风车]" => '<img type="face" title="[xb风车]" src="/images/emotions/242.gif"/>',
			"[xb恶魔]" => '<img type="face" title="[xb恶魔]" src="/images/emotions/243.gif"/>',
			"[xb打]" => '<img type="face" title="[xb打]" src="/images/emotions/244.gif"/>',
			"[xb大笑]" => '<img type="face" title="[xb大笑]" src="/images/emotions/245.gif"/>',
			"[xb呆]" => '<img type="face" title="[xb呆]" src="/images/emotions/246.gif"/>',
			"[xb触手]" => '<img type="face" title="[xb触手]" src="/images/emotions/247.gif"/>',
			"[xb吹]" => '<img type="face" title="[xb吹]" src="/images/emotions/248.gif"/>',
			"[xb吃糖]" => '<img type="face" title="[xb吃糖]" src="/images/emotions/249.gif"/>',
			"[xb吃饭]" => '<img type="face" title="[xb吃饭]" src="/images/emotions/250.gif"/>',
			"[xb吃包]" => '<img type="face" title="[xb吃包]" src="/images/emotions/251.gif"/>',
			"[xb唱歌]" => '<img type="face" title="[xb唱歌]" src="/images/emotions/252.gif"/>',
			"[xb摆手]" => '<img type="face" title="[xb摆手]" src="/images/emotions/253.gif"/>',
			"[cai走走]" => '<img type="face" title="[cai走走]" src="/images/emotions/254.gif"/>',
			"[cai揍人]" => '<img type="face" title="[cai揍人]" src="/images/emotions/255.gif"/>',
			"[cai撞墙]" => '<img type="face" title="[cai撞墙]" src="/images/emotions/256.gif"/>',
			"[cai正呀]" => '<img type="face" title="[cai正呀]" src="/images/emotions/257.gif"/>',
			"[cai嘻嘻]" => '<img type="face" title="[cai嘻嘻]" src="/images/emotions/258.gif"/>',
			"[cai羞羞]" => '<img type="face" title="[cai羞羞]" src="/images/emotions/259.gif"/>',
			"[cai无语]" => '<img type="face" title="[cai无语]" src="/images/emotions/260.gif"/>',
			"[cai脱光]" => '<img type="face" title="[cai脱光]" src="/images/emotions/261.gif"/>',
			"[cai偷摸]" => '<img type="face" title="[cai偷摸]" src="/images/emotions/262.gif"/>',
			"[cai太好了]" => '<img type="face" title="[cai太好了]" src="/images/emotions/263.gif"/>',
			"[cai庆祝]" => '<img type="face" title="[cai庆祝]" src="/images/emotions/264.gif"/>',
			"[cai钱]" => '<img type="face" title="[cai钱]" src="/images/emotions/265.gif"/>',
			"[cai潜水]" => '<img type="face" title="[cai潜水]" src="/images/emotions/266.gif"/>',
			"[cai怕羞]" => '<img type="face" title="[cai怕羞]" src="/images/emotions/267.gif"/>',
			"[cai落叶]" => '<img type="face" title="[cai落叶]" src="/images/emotions/268.gif"/>',
			"[cai哭]" => '<img type="face" title="[cai哭]" src="/images/emotions/269.gif"/>',
			"[cai惊吓]" => '<img type="face" title="[cai惊吓]" src="/images/emotions/271.gif"/>',
			"[cai奸笑]" => '<img type="face" title="[cai奸笑]" src="/images/emotions/272.gif"/>',
			"[cai晃头]" => '<img type="face" title="[cai晃头]" src="/images/emotions/273.gif"/>',
			"[cai哈喽]" => '<img type="face" title="[cai哈喽]" src="/images/emotions/274.gif"/>',
			"[cai飞吻]" => '<img type="face" title="[cai飞吻]" src="/images/emotions/275.gif"/>',
			"[cai打打]" => '<img type="face" title="[cai打打]" src="/images/emotions/277.gif"/>',
			"[cai扯脸]" => '<img type="face" title="[cai扯脸]" src="/images/emotions/278.gif"/>',
			"[cai插眼]" => '<img type="face" title="[cai插眼]" src="/images/emotions/279.gif"/>',
			"[cai鼻屎]" => '<img type="face" title="[cai鼻屎]" src="/images/emotions/280.gif"/>',
			"[cai崩溃]" => '<img type="face" title="[cai崩溃]" src="/images/emotions/281.gif"/>',
			"[cai拜拜]" => '<img type="face" title="[cai拜拜]" src="/images/emotions/282.gif"/>',
			"[cai啊]" => '<img type="face" title="[cai啊]" src="/images/emotions/283.gif"/>',
			"[din转转]" => '<img type="face" title="[din转转]" src="/images/emotions/284.gif"/>',
			"[din撞墙]" => '<img type="face" title="[din撞墙]" src="/images/emotions/285.gif"/>',
			"[din抓狂]" => '<img type="face" title="[din抓狂]" src="/images/emotions/286.gif"/>',
			"[din赞好]" => '<img type="face" title="[din赞好]" src="/images/emotions/287.gif"/>',
			"[din信息]" => '<img type="face" title="[din信息]" src="/images/emotions/288.gif"/>',
			"[din兴奋]" => '<img type="face" title="[din兴奋]" src="/images/emotions/289.gif"/>',
			"[din天哦]" => '<img type="face" title="[din天哦]" src="/images/emotions/291.gif"/>',
			"[din弹弹]" => '<img type="face" title="[din弹弹]" src="/images/emotions/292.gif"/>',
			"[din说话]" => '<img type="face" title="[din说话]" src="/images/emotions/293.gif"/>',
			"[din睡觉]" => '<img type="face" title="[din睡觉]" src="/images/emotions/294.gif"/>',
			"[din帅]" => '<img type="face" title="[din帅]" src="/images/emotions/295.gif"/>',
			"[din闪避]" => '<img type="face" title="[din闪避]" src="/images/emotions/296.gif"/>',
			"[din亲亲]" => '<img type="face" title="[din亲亲]" src="/images/emotions/297.gif"/>',
			"[din拍手]" => '<img type="face" title="[din拍手]" src="/images/emotions/298.gif"/>',
			"[din怒]" => '<img type="face" title="[din怒]" src="/images/emotions/299.gif"/>',
			"[din摸头]" => '<img type="face" title="[din摸头]" src="/images/emotions/300.gif"/>',
			"[din流血]" => '<img type="face" title="[din流血]" src="/images/emotions/301.gif"/>',
			"[din厉害]" => '<img type="face" title="[din厉害]" src="/images/emotions/302.gif"/>',
			"[din脸红]" => '<img type="face" title="[din脸红]" src="/images/emotions/303.gif"/>',
			"[din泪]" => '<img type="face" title="[din泪]" src="/images/emotions/304.gif"/>',
			"[din看看]" => '<img type="face" title="[din看看]" src="/images/emotions/305.gif"/>',
			"[din贱香]" => '<img type="face" title="[din贱香]" src="/images/emotions/306.gif"/>',
			"[din挥手]" => '<img type="face" title="[din挥手]" src="/images/emotions/307.gif"/>',
			"[din化妆]" => '<img type="face" title="[din化妆]" src="/images/emotions/308.gif"/>',
			"[din喝]" => '<img type="face" title="[din喝]" src="/images/emotions/309.gif"/>',
			"[din汗]" => '<img type="face" title="[din汗]" src="/images/emotions/310.gif"/>',
			"[din害羞]" => '<img type="face" title="[din害羞]" src="/images/emotions/311.gif"/>',
			"[din鬼脸]" => '<img type="face" title="[din鬼脸]" src="/images/emotions/312.gif"/>',
			"[din挂了]" => '<img type="face" title="[din挂了]" src="/images/emotions/313.gif"/>',
			"[din分身1]" => '<img type="face" title="[din分身1]" src="/images/emotions/314.gif"/>',
			"[din分身2]" => '<img type="face" title="[din分身2]" src="/images/emotions/315.gif"/>',
			"[din癫当]" => '<img type="face" title="[din癫当]" src="/images/emotions/316.gif"/>',
			"[din戴熊]" => '<img type="face" title="[din戴熊]" src="/images/emotions/317.gif"/>',
			"[din吃]" => '<img type="face" title="[din吃]" src="/images/emotions/318.gif"/>',
			"[din变身]" => '<img type="face" title="[din变身]" src="/images/emotions/319.gif"/>',
			"[din变脸]" => '<img type="face" title="[din变脸]" src="/images/emotions/320.gif"/>',
			"[din白旗]" => '<img type="face" title="[din白旗]" src="/images/emotions/321.gif"/>',
			"[din爱你]" => '<img type="face" title="[din爱你]" src="/images/emotions/322.gif"/>',
			"[lb装傻]" => '<img type="face" title="[lb装傻]" src="/images/emotions/323.gif"/>',
			"[lb咦]" => '<img type="face" title="[lb咦]" src="/images/emotions/324.gif"/>',
			"[lb嗯]" => '<img type="face" title="[lb嗯]" src="/images/emotions/325.gif"/>',
			"[lb糟糕]" => '<img type="face" title="[lb糟糕]" src="/images/emotions/326.gif"/>',
			"[lb嘿嘿]" => '<img type="face" title="[lb嘿嘿]" src="/images/emotions/327.gif"/>',
			"[lb鄙视]" => '<img type="face" title="[lb鄙视]" src="/images/emotions/328.gif"/>',
			"[lb戳]" => '<img type="face" title="[lb戳]" src="/images/emotions/329.gif"/>',
			"[lb摇头]" => '<img type="face" title="[lb摇头]" src="/images/emotions/330.gif"/>',
			"[lb惊]" => '<img type="face" title="[lb惊]" src="/images/emotions/331.gif"/>',
			"[lb欢乐]" => '<img type="face" title="[lb欢乐]" src="/images/emotions/332.gif"/>',
			"[lb雷]" => '<img type="face" title="[lb雷]" src="/images/emotions/333.gif"/>',
			"[lb呃]" => '<img type="face" title="[lb呃]" src="/images/emotions/334.gif"/>',
			"[lb蹭右]" => '<img type="face" title="[lb蹭右]" src="/images/emotions/335.gif"/>',
			"[lb蹭左]" => '<img type="face" title="[lb蹭左]" src="/images/emotions/336.gif"/>',
			"[lb啊]" => '<img type="face" title="[lb啊]" src="/images/emotions/337.gif"/>',
			"[lb哼]" => '<img type="face" title="[lb哼]" src="/images/emotions/338.gif"/>',
			"[lb撒欢]" => '<img type="face" title="[lb撒欢]" src="/images/emotions/339.gif"/>',
			"[lb爽]" => '<img type="face" title="[lb爽]" src="/images/emotions/340.gif"/>',
			"[lb厉害]" => '<img type="face" title="[lb厉害]" src="/images/emotions/342.gif"/>',
			"[lb帅]" => '<img type="face" title="[lb帅]" src="/images/emotions/343.gif"/>',
			"[lb哭]" => '<img type="face" title="[lb哭]" src="/images/emotions/344.gif"/>',
			"[lb呵]" => '<img type="face" title="[lb呵]" src="/images/emotions/345.gif"/>',
			"[lb嘻]" => '<img type="face" title="[lb嘻]" src="/images/emotions/346.gif"/>',
			"[lb讨厌]" => '<img type="face" title="[lb讨厌]" src="/images/emotions/347.gif"/>',
			"[lxhx刷牙]" => '<img type="face" title="[lxhx刷牙]" src="/images/emotions/348.gif"/>',
			"[lxhx问号]" => '<img type="face" title="[lxhx问号]" src="/images/emotions/349.gif"/>',
			"[lxhx病了]" => '<img type="face" title="[lxhx病了]" src="/images/emotions/350.gif"/>',
			"[lxhx惊]" => '<img type="face" title="[lxhx惊]" src="/images/emotions/351.gif"/>',
			"[lxhx汗]" => '<img type="face" title="[lxhx汗]" src="/images/emotions/352.gif"/>',
			"[lxhx泪目]" => '<img type="face" title="[lxhx泪目]" src="/images/emotions/353.gif"/>',
			"[lxhx无语]" => '<img type="face" title="[lxhx无语]" src="/images/emotions/354.gif"/>',
			"[lxhx吐]" => '<img type="face" title="[lxhx吐]" src="/images/emotions/355.gif"/>',
			"[lxhx喵]" => '<img type="face" title="[lxhx喵]" src="/images/emotions/356.gif"/>',
			"[lxhx失落]" => '<img type="face" title="[lxhx失落]" src="/images/emotions/357.gif"/>',
			"[lxhx顺毛]" => '<img type="face" title="[lxhx顺毛]" src="/images/emotions/358.gif"/>',
			"[lxhx划]" => '<img type="face" title="[lxhx划]" src="/images/emotions/359.gif"/>',
			"[lxhx逗上下]" => '<img type="face" title="[lxhx逗上下]" src="/images/emotions/360.gif"/>',
			"[lxhx喝奶]" => '<img type="face" title="[lxhx喝奶]" src="/images/emotions/361.gif"/>',
			"[lxhx狠]" => '<img type="face" title="[lxhx狠]" src="/images/emotions/362.gif"/>',
			"[lxhx奋斗]" => '<img type="face" title="[lxhx奋斗]" src="/images/emotions/363.gif"/>',
			"[lxhx滚过]" => '<img type="face" title="[lxhx滚过]" src="/images/emotions/364.gif"/>',
			"[lxhx都不给]" => '<img type="face" title="[lxhx都不给]" src="/images/emotions/365.gif"/>',
			"[lxhx蠕过]" => '<img type="face" title="[lxhx蠕过]" src="/images/emotions/366.gif"/>',
			"[lxhx无聊]" => '<img type="face" title="[lxhx无聊]" src="/images/emotions/367.gif"/>',
			"[lxhx听歌]" => '<img type="face" title="[lxhx听歌]" src="/images/emotions/368.gif"/>',
			"[lxhx狂欢]" => '<img type="face" title="[lxhx狂欢]" src="/images/emotions/369.gif"/>',
			"[lxhx喵喵]" => '<img type="face" title="[lxhx喵喵]" src="/images/emotions/370.gif"/>',
			"[lxhx懒腰]" => '<img type="face" title="[lxhx懒腰]" src="/images/emotions/371.gif"/>',
			"[lxhx得瑟]" => '<img type="face" title="[lxhx得瑟]" src="/images/emotions/372.gif"/>',
			"[lxhx得意]" => '<img type="face" title="[lxhx得意]" src="/images/emotions/373.gif"/>',
			"[lxhx走]" => '<img type="face" title="[lxhx走]" src="/images/emotions/374.gif"/>',
			"[lxhx躺中枪]" => '<img type="face" title="[lxhx躺中枪]" src="/images/emotions/375.gif"/>',
			"[lxhx撒欢]" => '<img type="face" title="[lxhx撒欢]" src="/images/emotions/376.gif"/>',
			"[lxhx求表扬]" => '<img type="face" title="[lxhx求表扬]" src="/images/emotions/377.gif"/>',
			"[lxhx不爽]" => '<img type="face" title="[lxhx不爽]" src="/images/emotions/378.gif"/>',
			"[lxhx转体]" => '<img type="face" title="[lxhx转体]" src="/images/emotions/379.gif"/>',
			"[lxhx逗左右]" => '<img type="face" title="[lxhx逗左右]" src="/images/emotions/380.gif"/>',
			"[lxhx睡觉]" => '<img type="face" title="[lxhx睡觉]" src="/images/emotions/381.gif"/>',
			"[lxhx讨厌]" => '<img type="face" title="[lxhx讨厌]" src="/images/emotions/382.gif"/>',
			"[lxhx扫灰]" => '<img type="face" title="[lxhx扫灰]" src="/images/emotions/383.gif"/>',
			"[lxhx打地鼠]" => '<img type="face" title="[lxhx打地鼠]" src="/images/emotions/384.gif"/>',
			"[lxhx啄地]" => '<img type="face" title="[lxhx啄地]" src="/images/emotions/385.gif"/>',
			"[lxhx转头]" => '<img type="face" title="[lxhx转头]" src="/images/emotions/386.gif"/>',
			"[lxhx掀桌]" => '<img type="face" title="[lxhx掀桌]" src="/images/emotions/387.gif"/>',
			"[lxhx奔跑]" => '<img type="face" title="[lxhx奔跑]" src="/images/emotions/388.gif"/>',
			"[lxhx逗转圈]" => '<img type="face" title="[lxhx逗转圈]" src="/images/emotions/389.gif"/>',
			"[lxhx怨念]" => '<img type="face" title="[lxhx怨念]" src="/images/emotions/390.gif"/>',
			"[lxhx画圈]" => '<img type="face" title="[lxhx画圈]" src="/images/emotions/391.gif"/>',
			"[lxhx跳跃]" => '<img type="face" title="[lxhx跳跃]" src="/images/emotions/392.gif"/>',
			"[lt五一]" => '<img type="face" title="[lt五一]" src="/images/emotions/393.gif"/>',
			"[lt摇摆]" => '<img type="face" title="[lt摇摆]" src="/images/emotions/395.gif"/>',
			"[lt闪瞎]" => '<img type="face" title="[lt闪瞎]" src="/images/emotions/397.gif"/>',
			"[lt拍手]" => '<img type="face" title="[lt拍手]" src="/images/emotions/398.gif"/>',
			"[lt蛋疼]" => '<img type="face" title="[lt蛋疼]" src="/images/emotions/399.gif"/>',
			"[lt撒花]" => '<img type="face" title="[lt撒花]" src="/images/emotions/400.gif"/>',
			"[lt母亲节]" => '<img type="face" title="[lt母亲节]" src="/images/emotions/401.gif"/>',
			"[lt挖鼻]" => '<img type="face" title="[lt挖鼻]" src="/images/emotions/402.gif"/>',
			"[lt哈欠]" => '<img type="face" title="[lt哈欠]" src="/images/emotions/403.gif"/>',
			"[lt泪目]" => '<img type="face" title="[lt泪目]" src="/images/emotions/404.gif"/>',
			"[lt雷]" => '<img type="face" title="[lt雷]" src="/images/emotions/405.gif"/>',
			"[lt中枪]" => '<img type="face" title="[lt中枪]" src="/images/emotions/406.gif"/>',
			"[lt耳朵]" => '<img type="face" title="[lt耳朵]" src="/images/emotions/407.gif"/>',
			"[lt顶]" => '<img type="face" title="[lt顶]" src="/images/emotions/408.gif"/>',
			"[lt潜水]" => '<img type="face" title="[lt潜水]" src="/images/emotions/409.gif"/>',
			"[lt拍桌大笑]" => '<img type="face" title="[lt拍桌大笑]" src="/images/emotions/410.gif"/>',
			"[lt黑线]" => '<img type="face" title="[lt黑线]" src="/images/emotions/411.gif"/>',
			"[lt喷血]" => '<img type="face" title="[lt喷血]" src="/images/emotions/412.gif"/>',
			"[lt巨汗]" => '<img type="face" title="[lt巨汗]" src="/images/emotions/413.gif"/>',
			"[lt疑惑]" => '<img type="face" title="[lt疑惑]" src="/images/emotions/414.gif"/>',
			"[lt浮云]" => '<img type="face" title="[lt浮云]" src="/images/emotions/415.gif"/>',
			"[lt笑话]" => '<img type="face" title="[lt笑话]" src="/images/emotions/416.gif"/>',
			"[lt喷]" => '<img type="face" title="[lt喷]" src="/images/emotions/417.gif"/>',
			"[lt雪]" => '<img type="face" title="[lt雪]" src="/images/emotions/418.gif"/>',
			"[lt转发]" => '<img type="face" title="[lt转发]" src="/images/emotions/419.gif"/>',
			"[lt偷窥]" => '<img type="face" title="[lt偷窥]" src="/images/emotions/420.gif"/>',
			"[lt惊吓]" => '<img type="face" title="[lt惊吓]" src="/images/emotions/421.gif"/>',
			"[lt囧]" => '<img type="face" title="[lt囧]" src="/images/emotions/422.gif"/>',
			"[lt灰飞烟灭]" => '<img type="face" title="[lt灰飞烟灭]" src="/images/emotions/423.gif"/>',
			"[lt冰封]" => '<img type="face" title="[lt冰封]" src="/images/emotions/424.gif"/>',
			"[lt吐]" => '<img type="face" title="[lt吐]" src="/images/emotions/425.gif"/>',
			"[lt吹泡泡]" => '<img type="face" title="[lt吹泡泡]" src="/images/emotions/426.gif"/>',
			"[lt吓]" => '<img type="face" title="[lt吓]" src="/images/emotions/427.gif"/>',
			"[j疯了]" => '<img type="face" title="[j疯了]" src="/images/emotions/428.gif"/>',
			"[j撒娇]" => '<img type="face" title="[j撒娇]" src="/images/emotions/429.gif"/>',
			"[j吐血]" => '<img type="face" title="[j吐血]" src="/images/emotions/430.gif"/>',
			"[j浪笑]" => '<img type="face" title="[j浪笑]" src="/images/emotions/431.gif"/>',
			"[j作揖]" => '<img type="face" title="[j作揖]" src="/images/emotions/432.gif"/>',
			"[j哎呀]" => '<img type="face" title="[j哎呀]" src="/images/emotions/433.gif"/>',
			"[j挂了]" => '<img type="face" title="[j挂了]" src="/images/emotions/434.gif"/>',
			"[j扭秧歌]" => '<img type="face" title="[j扭秧歌]" src="/images/emotions/435.gif"/>',
			"[j媚眼]" => '<img type="face" title="[j媚眼]" src="/images/emotions/436.gif"/>',
			"[j来嘛]" => '<img type="face" title="[j来嘛]" src="/images/emotions/437.gif"/>',
			"[j蹭]" => '<img type="face" title="[j蹭]" src="/images/emotions/438.gif"/>',
			"[xyj年年有鱼]" => '<img type="face" title="[xyj年年有鱼]" src="/images/emotions/1605.gif"/>',
			"[xyj红包]" => '<img type="face" title="[xyj红包]" src="/images/emotions/1604.gif"/>',
			"[xyj拜年]" => '<img type="face" title="[xyj拜年]" src="/images/emotions/1603.gif"/>',
			"[抓沙发]" => '<img type="face" title="[抓沙发]" src="/images/emotions/442.gif"/>',
			"[震撼]" => '<img type="face" title="[震撼]" src="/images/emotions/443.gif"/>',
			"[晕晕]" => '<img type="face" title="[晕晕]" src="/images/emotions/444.gif"/>',
			"[瞎眼]" => '<img type="face" title="[瞎眼]" src="/images/emotions/445.gif"/>',
			"[为难]" => '<img type="face" title="[为难]" src="/images/emotions/446.gif"/>',
			"[舔]" => '<img type="face" title="[舔]" src="/images/emotions/447.gif"/>',
			"[流汗]" => '<img type="face" title="[流汗]" src="/images/emotions/448.gif"/>',
			"[冷]" => '<img type="face" title="[冷]" src="/images/emotions/449.gif"/>',
			"[老大]" => '<img type="face" title="[老大]" src="/images/emotions/450.gif"/>',
			"[瞌睡]" => '<img type="face" title="[瞌睡]" src="/images/emotions/451.gif"/>',
			"[可怜的]" => '<img type="face" title="[可怜的]" src="/images/emotions/452.gif"/>',
			"[咖啡咖啡]" => '<img type="face" title="[咖啡咖啡]" src="/images/emotions/453.gif"/>',
			"[坏笑]" => '<img type="face" title="[坏笑]" src="/images/emotions/454.gif"/>',
			"[顶啊]" => '<img type="face" title="[顶啊]" src="/images/emotions/455.gif"/>',
			"[好得意]" => '<img type="face" title="[好得意]" src="/images/emotions/456.gif"/>',
			"[冲啊]" => '<img type="face" title="[冲啊]" src="/images/emotions/457.gif"/>',
			"[吃西瓜]" => '<img type="face" title="[吃西瓜]" src="/images/emotions/458.gif"/>',
			"[不要啊]" => '<img type="face" title="[不要啊]" src="/images/emotions/459.gif"/>',
			"[飙泪中]" => '<img type="face" title="[飙泪中]" src="/images/emotions/460.gif"/>',
			"[爱你哦]" => '<img type="face" title="[爱你哦]" src="/images/emotions/461.gif"/>',
			"[书呆子]" => '<img type="face" title="[书呆子]" src="/images/emotions/469.gif"/>',
			"[顶]" => '<img type="face" title="[顶]" src="/images/emotions/482.gif"/>',
			"[愤怒]" => '<img type="face" title="[愤怒]" src="/images/emotions/490.gif"/>',
			"[感冒]" => '<img type="face" title="[感冒]" src="/images/emotions/491.gif"/>',
			"[haha]" => '<img type="face" title="[haha]" src="/images/emotions/495.gif"/>',
			"[拳头]" => '<img type="face" title="[拳头]" src="/images/emotions/498.gif"/>',
			"[握手]" => '<img type="face" title="[握手]" src="/images/emotions/500.gif"/>',
			"[最差]" => '<img type="face" title="[最差]" src="/images/emotions/503.gif"/>',
			"[右抱抱]" => '<img type="face" title="[右抱抱]" src="/images/emotions/512.gif"/>',
			"[打哈气]" => '<img type="face" title="[打哈气]" src="/images/emotions/515.gif"/>',
			"[左抱抱]" => '<img type="face" title="[左抱抱]" src="/images/emotions/516.gif"/>',
			"[g思考]" => '<img type="face" title="[g思考]" src="/images/emotions/527.gif"/>',
			"[g震惊]" => '<img type="face" title="[g震惊]" src="/images/emotions/528.gif"/>',
			"[g狂笑]" => '<img type="face" title="[g狂笑]" src="/images/emotions/529.gif"/>',
			"[g脸红]" => '<img type="face" title="[g脸红]" src="/images/emotions/530.gif"/>',
			"[g发愣]" => '<img type="face" title="[g发愣]" src="/images/emotions/531.gif"/>',
			"[g话痨]" => '<img type="face" title="[g话痨]" src="/images/emotions/532.gif"/>',
			"[g吹发]" => '<img type="face" title="[g吹发]" src="/images/emotions/533.gif"/>',
			"[g爆哭]" => '<img type="face" title="[g爆哭]" src="/images/emotions/534.gif"/>',
			"[g伤心]" => '<img type="face" title="[g伤心]" src="/images/emotions/535.gif"/>',
			"[g得瑟]" => '<img type="face" title="[g得瑟]" src="/images/emotions/536.gif"/>',
			"[g魅眼]" => '<img type="face" title="[g魅眼]" src="/images/emotions/537.gif"/>',
			"[g无辜]" => '<img type="face" title="[g无辜]" src="/images/emotions/538.gif"/>',
			"[g挑眉]" => '<img type="face" title="[g挑眉]" src="/images/emotions/539.gif"/>',
			"[g墨镜1]" => '<img type="face" title="[g墨镜1]" src="/images/emotions/540.gif"/>',
			"[g墨镜2]" => '<img type="face" title="[g墨镜2]" src="/images/emotions/541.gif"/>',
			"[g变脸]" => '<img type="face" title="[g变脸]" src="/images/emotions/542.gif"/>',
			"[g扇笑]" => '<img type="face" title="[g扇笑]" src="/images/emotions/543.gif"/>',
			"[g扣鼻]" => '<img type="face" title="[g扣鼻]" src="/images/emotions/544.gif"/>',
			"[g扣鼻2]" => '<img type="face" title="[g扣鼻2]" src="/images/emotions/545.gif"/>',
			"[g瀑汗]" => '<img type="face" title="[g瀑汗]" src="/images/emotions/546.gif"/>',
			"[g汗滴]" => '<img type="face" title="[g汗滴]" src="/images/emotions/547.gif"/>',
			"[g咀嚼]" => '<img type="face" title="[g咀嚼]" src="/images/emotions/548.gif"/>',
			"[g阴影]" => '<img type="face" title="[g阴影]" src="/images/emotions/549.gif"/>',
			"[g鼻血]" => '<img type="face" title="[g鼻血]" src="/images/emotions/550.gif"/>',
			"[g呕吐]" => '<img type="face" title="[g呕吐]" src="/images/emotions/551.gif"/>',
			"[g噴血]" => '<img type="face" title="[g噴血]" src="/images/emotions/552.gif"/>',
			"[g泪滴]" => '<img type="face" title="[g泪滴]" src="/images/emotions/553.gif"/>',
			"[g惊讶1]" => '<img type="face" title="[g惊讶1]" src="/images/emotions/554.gif"/>',
			"[g闪牙1]" => '<img type="face" title="[g闪牙1]" src="/images/emotions/556.gif"/>',
			"[g闪牙2]" => '<img type="face" title="[g闪牙2]" src="/images/emotions/557.gif"/>',
			"[g巨汗]" => '<img type="face" title="[g巨汗]" src="/images/emotions/558.gif"/>',
			"[g鼓掌]" => '<img type="face" title="[g鼓掌]" src="/images/emotions/559.gif"/>',
			"[g招呼]" => '<img type="face" title="[g招呼]" src="/images/emotions/560.gif"/>',
			"[g鼓掌2]" => '<img type="face" title="[g鼓掌2]" src="/images/emotions/561.gif"/>',
			"[g无所谓]" => '<img type="face" title="[g无所谓]" src="/images/emotions/562.gif"/>',
			"[g雷击]" => '<img type="face" title="[g雷击]" src="/images/emotions/563.gif"/>',
			"[g邪笑]" => '<img type="face" title="[g邪笑]" src="/images/emotions/564.gif"/>',
			"[g裸奔1]" => '<img type="face" title="[g裸奔1]" src="/images/emotions/565.gif"/>',
			"[g裸奔2]" => '<img type="face" title="[g裸奔2]" src="/images/emotions/566.gif"/>',
			"[g裸奔3]" => '<img type="face" title="[g裸奔3]" src="/images/emotions/567.gif"/>',
			"[g举刀]" => '<img type="face" title="[g举刀]" src="/images/emotions/568.gif"/>',
			"[g喝茶]" => '<img type="face" title="[g喝茶]" src="/images/emotions/569.gif"/>',
			"[g摇手]" => '<img type="face" title="[g摇手]" src="/images/emotions/570.gif"/>',
			"[g病了]" => '<img type="face" title="[g病了]" src="/images/emotions/571.gif"/>',
			"[g冻上]" => '<img type="face" title="[g冻上]" src="/images/emotions/572.gif"/>',
			"[g好冷]" => '<img type="face" title="[g好冷]" src="/images/emotions/573.gif"/>',
			"[g委屈]" => '<img type="face" title="[g委屈]" src="/images/emotions/574.gif"/>',
			"[g发飘]" => '<img type="face" title="[g发飘]" src="/images/emotions/575.gif"/>',
			"[g卖萌]" => '<img type="face" title="[g卖萌]" src="/images/emotions/576.gif"/>',
			"[g唱歌]" => '<img type="face" title="[g唱歌]" src="/images/emotions/577.gif"/>',
			"[g吃糖]" => '<img type="face" title="[g吃糖]" src="/images/emotions/578.gif"/>',
			"[g桂宝]" => '<img type="face" title="[g桂宝]" src="/images/emotions/579.gif"/>',
			"[g汪汪]" => '<img type="face" title="[g汪汪]" src="/images/emotions/580.gif"/>',
			"[g吐舌]" => '<img type="face" title="[g吐舌]" src="/images/emotions/581.gif"/>',
			"[g骨头]" => '<img type="face" title="[g骨头]" src="/images/emotions/582.gif"/>',
			"[g口水]" => '<img type="face" title="[g口水]" src="/images/emotions/583.gif"/>',
			"[g惊讶2]" => '<img type="face" title="[g惊讶2]" src="/images/emotions/584.gif"/>',
			"[g爆哭2]" => '<img type="face" title="[g爆哭2]" src="/images/emotions/585.gif"/>',
			"[g激动]" => '<img type="face" title="[g激动]" src="/images/emotions/586.gif"/>',
			"[bed蹬腿]" => '<img type="face" title="[bed蹬腿]" src="/images/emotions/587.gif"/>',
			"[bed弹跳]" => '<img type="face" title="[bed弹跳]" src="/images/emotions/588.gif"/>',
			"[bed扯]" => '<img type="face" title="[bed扯]" src="/images/emotions/589.gif"/>',
			"[bed奔跑]" => '<img type="face" title="[bed奔跑]" src="/images/emotions/591.gif"/>',
			"[bed仰卧起坐]" => '<img type="face" title="[bed仰卧起坐]" src="/images/emotions/592.gif"/>',
			"[bed出浴]" => '<img type="face" title="[bed出浴]" src="/images/emotions/593.gif"/>',
			"[bed练腰]" => '<img type="face" title="[bed练腰]" src="/images/emotions/594.gif"/>',
			"[bed皮]" => '<img type="face" title="[bed皮]" src="/images/emotions/595.gif"/>',
			"[bed挠痒]" => '<img type="face" title="[bed挠痒]" src="/images/emotions/596.gif"/>',
			"[bed啦啦啦]" => '<img type="face" title="[bed啦啦啦]" src="/images/emotions/597.gif"/>',
			"[bed举哑铃]" => '<img type="face" title="[bed举哑铃]" src="/images/emotions/598.gif"/>',
			"[bed飘忽]" => '<img type="face" title="[bed飘忽]" src="/images/emotions/599.gif"/>',
			"[bed拍手]" => '<img type="face" title="[bed拍手]" src="/images/emotions/600.gif"/>',
			"[bed嘿哈]" => '<img type="face" title="[bed嘿哈]" src="/images/emotions/601.gif"/>',
			"[bed踏步]" => '<img type="face" title="[bed踏步]" src="/images/emotions/602.gif"/>',
			"[bed揉眼]" => '<img type="face" title="[bed揉眼]" src="/images/emotions/603.gif"/>',
			"[bed转圈]" => '<img type="face" title="[bed转圈]" src="/images/emotions/604.gif"/>',
			"[bed飞吻]" => '<img type="face" title="[bed飞吻]" src="/images/emotions/605.gif"/>',
			"[bed跳]" => '<img type="face" title="[bed跳]" src="/images/emotions/606.gif"/>',
			"[bed巴掌]" => '<img type="face" title="[bed巴掌]" src="/images/emotions/607.gif"/>',
			"[bed撒娇]" => '<img type="face" title="[bed撒娇]" src="/images/emotions/608.gif"/>',
			"[bed拍脸]" => '<img type="face" title="[bed拍脸]" src="/images/emotions/609.gif"/>',
			"[bed好饱]" => '<img type="face" title="[bed好饱]" src="/images/emotions/610.gif"/>',
			"[bed跑]" => '<img type="face" title="[bed跑]" src="/images/emotions/611.gif"/>',
			"[bed兴奋]" => '<img type="face" title="[bed兴奋]" src="/images/emotions/612.gif"/>',
			"[c帅]" => '<img type="face" title="[c帅]" src="/images/emotions/613.gif"/>',
			"[c窃喜]" => '<img type="face" title="[c窃喜]" src="/images/emotions/614.gif"/>',
			"[c迷糊]" => '<img type="face" title="[c迷糊]" src="/images/emotions/615.gif"/>',
			"[c面瘫]" => '<img type="face" title="[c面瘫]" src="/images/emotions/616.gif"/>',
			"[c囧]" => '<img type="face" title="[c囧]" src="/images/emotions/617.gif"/>',
			"[c汗]" => '<img type="face" title="[c汗]" src="/images/emotions/618.gif"/>',
			"[c高明]" => '<img type="face" title="[c高明]" src="/images/emotions/619.gif"/>',
			"[c大笑]" => '<img type="face" title="[c大笑]" src="/images/emotions/620.gif"/>',
			"[c变脸]" => '<img type="face" title="[c变脸]" src="/images/emotions/621.gif"/>',
			"[c左右看]" => '<img type="face" title="[c左右看]" src="/images/emotions/622.gif"/>',
			"[c坏笑]" => '<img type="face" title="[c坏笑]" src="/images/emotions/623.gif"/>',
			"[c看热闹]" => '<img type="face" title="[c看热闹]" src="/images/emotions/624.gif"/>',
			"[c开心]" => '<img type="face" title="[c开心]" src="/images/emotions/625.gif"/>',
			"[c关注]" => '<img type="face" title="[c关注]" src="/images/emotions/626.gif"/>',
			"[c娇羞]" => '<img type="face" title="[c娇羞]" src="/images/emotions/627.gif"/>',
			"[c无语]" => '<img type="face" title="[c无语]" src="/images/emotions/628.gif"/>',
			"[c疑惑]" => '<img type="face" title="[c疑惑]" src="/images/emotions/629.gif"/>',
			"[c正经]" => '<img type="face" title="[c正经]" src="/images/emotions/630.gif"/>',
			"[c无聊]" => '<img type="face" title="[c无聊]" src="/images/emotions/631.gif"/>',
			"[c挖鼻孔]" => '<img type="face" title="[c挖鼻孔]" src="/images/emotions/632.gif"/>',
			"[c期待]" => '<img type="face" title="[c期待]" src="/images/emotions/633.gif"/>',
			"[c摇头看]" => '<img type="face" title="[c摇头看]" src="/images/emotions/634.gif"/>',
			"[c亲亲]" => '<img type="face" title="[c亲亲]" src="/images/emotions/635.gif"/>',
			"[c羞涩]" => '<img type="face" title="[c羞涩]" src="/images/emotions/636.gif"/>',
			"[c悲催]" => '<img type="face" title="[c悲催]" src="/images/emotions/637.gif"/>',
			"[c得瑟]" => '<img type="face" title="[c得瑟]" src="/images/emotions/638.gif"/>',
			"[c冷眼]" => '<img type="face" title="[c冷眼]" src="/images/emotions/639.gif"/>',
			"[c惊讶]" => '<img type="face" title="[c惊讶]" src="/images/emotions/640.gif"/>',
			"[c委屈]" => '<img type="face" title="[c委屈]" src="/images/emotions/641.gif"/>',
			"[c甩舌头]" => '<img type="face" title="[c甩舌头]" src="/images/emotions/642.gif"/>',
			"[c摇头萌]" => '<img type="face" title="[c摇头萌]" src="/images/emotions/643.gif"/>',
			"[c抓狂]" => '<img type="face" title="[c抓狂]" src="/images/emotions/644.gif"/>',
			"[c发火]" => '<img type="face" title="[c发火]" src="/images/emotions/645.gif"/>',
			"[c卖萌]" => '<img type="face" title="[c卖萌]" src="/images/emotions/646.gif"/>',
			"[c伤心]" => '<img type="face" title="[c伤心]" src="/images/emotions/647.gif"/>',
			"[c捂脸]" => '<img type="face" title="[c捂脸]" src="/images/emotions/648.gif"/>',
			"[c震惊哭]" => '<img type="face" title="[c震惊哭]" src="/images/emotions/649.gif"/>',
			"[c摇摆]" => '<img type="face" title="[c摇摆]" src="/images/emotions/650.gif"/>',
			"[c得意笑]" => '<img type="face" title="[c得意笑]" src="/images/emotions/651.gif"/>',
			"[c烦躁]" => '<img type="face" title="[c烦躁]" src="/images/emotions/652.gif"/>',
			"[c得意]" => '<img type="face" title="[c得意]" src="/images/emotions/653.gif"/>',
			"[c脸红]" => '<img type="face" title="[c脸红]" src="/images/emotions/654.gif"/>',
			"[toto拜年]" => '<img type="face" title="[toto拜年]" src="/images/emotions/1607.gif"/>',
			"[bobo拜年]" => '<img type="face" title="[bobo拜年]" src="/images/emotions/1606.gif"/>',
			"[toto无聊]" => '<img type="face" title="[toto无聊]" src="/images/emotions/657.gif"/>',
			"[toto我最摇滚]" => '<img type="face" title="[toto我最摇滚]" src="/images/emotions/658.gif"/>',
			"[toto数落]" => '<img type="face" title="[toto数落]" src="/images/emotions/659.gif"/>',
			"[toto睡觉]" => '<img type="face" title="[toto睡觉]" src="/images/emotions/660.gif"/>',
			"[toto甩头发]" => '<img type="face" title="[toto甩头发]" src="/images/emotions/661.gif"/>',
			"[toto飘过]" => '<img type="face" title="[toto飘过]" src="/images/emotions/662.gif"/>',
			"[toto狂汗]" => '<img type="face" title="[toto狂汗]" src="/images/emotions/663.gif"/>',
			"[toto好累]" => '<img type="face" title="[toto好累]" src="/images/emotions/664.gif"/>',
			"[bobo抓狂]" => '<img type="face" title="[bobo抓狂]" src="/images/emotions/665.gif"/>',
			"[bobo疑问]" => '<img type="face" title="[bobo疑问]" src="/images/emotions/666.gif"/>',
			"[bobo抛媚眼]" => '<img type="face" title="[bobo抛媚眼]" src="/images/emotions/667.gif"/>',
			"[bobo膜拜]" => '<img type="face" title="[bobo膜拜]" src="/images/emotions/668.gif"/>',
			"[bobo不要啊]" => '<img type="face" title="[bobo不要啊]" src="/images/emotions/670.gif"/>',
			"[bobo不理你]" => '<img type="face" title="[bobo不理你]" src="/images/emotions/671.gif"/>',
			"[有爱]" => '<img type="face" title="[有爱]" src="/images/emotions/672.gif"/>',
			"[气死了]" => '<img type="face" title="[气死了]" src="/images/emotions/673.gif"/>',
			"[我爱听]" => '<img type="face" title="[我爱听]" src="/images/emotions/674.gif"/>',
			"[怒火]" => '<img type="face" title="[怒火]" src="/images/emotions/675.gif"/>',
			"[擂鼓]" => '<img type="face" title="[擂鼓]" src="/images/emotions/676.gif"/>',
			"[讥笑]" => '<img type="face" title="[讥笑]" src="/images/emotions/677.gif"/>',
			"[抛钱]" => '<img type="face" title="[抛钱]" src="/images/emotions/678.gif"/>',
			"[变花]" => '<img type="face" title="[变花]" src="/images/emotions/679.gif"/>',
			"[飙泪]" => '<img type="face" title="[飙泪]" src="/images/emotions/680.gif"/>',
			"[藏猫猫]" => '<img type="face" title="[藏猫猫]" src="/images/emotions/681.gif"/>',
			"[淘气]" => '<img type="face" title="[淘气]" src="/images/emotions/682.gif"/>',
			"[生闷气]" => '<img type="face" title="[生闷气]" src="/images/emotions/683.gif"/>',
			"[忍]" => '<img type="face" title="[忍]" src="/images/emotions/684.gif"/>',
			"[泡泡糖]" => '<img type="face" title="[泡泡糖]" src="/images/emotions/685.gif"/>',
			"[好的]" => '<img type="face" title="[好的]" src="/images/emotions/686.gif"/>',
			"[Hi]" => '<img type="face" title="[Hi]" src="/images/emotions/687.gif"/>',
			"[飞吻]" => '<img type="face" title="[飞吻]" src="/images/emotions/688.gif"/>',
			"[我爱西瓜]" => '<img type="face" title="[我爱西瓜]" src="/images/emotions/689.gif"/>',
			"[吓一跳]" => '<img type="face" title="[吓一跳]" src="/images/emotions/690.gif"/>',
			"[吃饭]" => '<img type="face" title="[吃饭]" src="/images/emotions/691.gif"/>',
			"[雾]" => '<img type="face" title="[雾]" src="/images/emotions/692.gif"/>',
			"[台风]" => '<img type="face" title="[台风]" src="/images/emotions/693.gif"/>',
			"[沙尘暴]" => '<img type="face" title="[沙尘暴]" src="/images/emotions/694.gif"/>',
			"[晴转多云]" => '<img type="face" title="[晴转多云]" src="/images/emotions/695.gif"/>',
			"[流星]" => '<img type="face" title="[流星]" src="/images/emotions/696.gif"/>',
			"[龙卷风]" => '<img type="face" title="[龙卷风]" src="/images/emotions/697.gif"/>',
			"[风]" => '<img type="face" title="[风]" src="/images/emotions/699.gif"/>',
			"[多云转晴]" => '<img type="face" title="[多云转晴]" src="/images/emotions/700.gif"/>',
			"[彩虹]" => '<img type="face" title="[彩虹]" src="/images/emotions/701.gif"/>',
			"[冰雹]" => '<img type="face" title="[冰雹]" src="/images/emotions/702.gif"/>',
			"[微风]" => '<img type="face" title="[微风]" src="/images/emotions/703.gif"/>',
			"[阳光]" => '<img type="face" title="[阳光]" src="/images/emotions/704.gif"/>',
			"[雪]" => '<img type="face" title="[雪]" src="/images/emotions/705.gif"/>',
			"[闪电]" => '<img type="face" title="[闪电]" src="/images/emotions/706.gif"/>',
			"[阴天]" => '<img type="face" title="[阴天]" src="/images/emotions/708.gif"/>',
			"[鞭炮]" => '<img type="face" title="[鞭炮]" src="/images/emotions/709.gif"/>',
			"[让红包飞]" => '<img type="face" title="[让红包飞]" src="/images/emotions/710.gif"/>',
			"[围脖]" => '<img type="face" title="[围脖]" src="/images/emotions/711.gif"/>',
			"[温暖帽子]" => '<img type="face" title="[温暖帽子]" src="/images/emotions/712.gif"/>',
			"[手套]" => '<img type="face" title="[手套]" src="/images/emotions/713.gif"/>',
			"[红包]" => '<img type="face" title="[红包]" src="/images/emotions/714.gif"/>',
			"[喜]" => '<img type="face" title="[喜]" src="/images/emotions/715.gif"/>',
			"[钻戒]" => '<img type="face" title="[钻戒]" src="/images/emotions/718.gif"/>',
			"[钻石]" => '<img type="face" title="[钻石]" src="/images/emotions/719.gif"/>',
			"[大巴]" => '<img type="face" title="[大巴]" src="/images/emotions/720.gif"/>',
			"[飞机]" => '<img type="face" title="[飞机]" src="/images/emotions/721.gif"/>',
			"[汽车]" => '<img type="face" title="[汽车]" src="/images/emotions/722.gif"/>',
			"[自行车]" => '<img type="face" title="[自行车]" src="/images/emotions/723.gif"/>',
			"[手机]" => '<img type="face" title="[手机]" src="/images/emotions/724.gif"/>',
			"[照相机]" => '<img type="face" title="[照相机]" src="/images/emotions/725.gif"/>',
			"[电脑]" => '<img type="face" title="[电脑]" src="/images/emotions/726.gif"/>',
			"[药]" => '<img type="face" title="[药]" src="/images/emotions/727.gif"/>',
			"[手纸]" => '<img type="face" title="[手纸]" src="/images/emotions/728.gif"/>',
			"[落叶]" => '<img type="face" title="[落叶]" src="/images/emotions/729.gif"/>',
			"[圣诞树]" => '<img type="face" title="[圣诞树]" src="/images/emotions/730.gif"/>',
			"[圣诞帽]" => '<img type="face" title="[圣诞帽]" src="/images/emotions/731.gif"/>',
			"[圣诞老人]" => '<img type="face" title="[圣诞老人]" src="/images/emotions/732.gif"/>',
			"[圣诞铃铛]" => '<img type="face" title="[圣诞铃铛]" src="/images/emotions/733.gif"/>',
			"[圣诞袜]" => '<img type="face" title="[圣诞袜]" src="/images/emotions/734.gif"/>',
			"[电视机]" => '<img type="face" title="[电视机]" src="/images/emotions/735.gif"/>',
			"[电话]" => '<img type="face" title="[电话]" src="/images/emotions/736.gif"/>',
			"[西瓜]" => '<img type="face" title="[西瓜]" src="/images/emotions/737.gif"/>',
			"[太阳]" => '<img type="face" title="[太阳]" src="/images/emotions/739.gif"/>',
			"[哨子]" => '<img type="face" title="[哨子]" src="/images/emotions/1510.gif"/>',
			"[印迹]" => '<img type="face" title="[印迹]" src="/images/emotions/742.gif"/>',
			"[钢琴]" => '<img type="face" title="[钢琴]" src="/images/emotions/743.gif"/>',
			"[叶子]" => '<img type="face" title="[叶子]" src="/images/emotions/744.gif"/>',
			"[星]" => '<img type="face" title="[星]" src="/images/emotions/745.gif"/>',
			"[茶]" => '<img type="face" title="[茶]" src="/images/emotions/746.gif"/>',
			"[干杯]" => '<img type="face" title="[干杯]" src="/images/emotions/747.gif"/>',
			"[音乐]" => '<img type="face" title="[音乐]" src="/images/emotions/748.gif"/>',
			"[档案]" => '<img type="face" title="[档案]" src="/images/emotions/749.gif"/>',
			"[风扇]" => '<img type="face" title="[风扇]" src="/images/emotions/750.gif"/>',
			"[花]" => '<img type="face" title="[花]" src="/images/emotions/751.gif"/>',
			"[鲜花]" => '<img type="face" title="[鲜花]" src="/images/emotions/752.gif"/>',
			"[足球]" => '<img type="face" title="[足球]" src="/images/emotions/1513.gif"/>',
			"[房子]" => '<img type="face" title="[房子]" src="/images/emotions/755.gif"/>',
			"[冰棍]" => '<img type="face" title="[冰棍]" src="/images/emotions/756.gif"/>',
			"[唱歌]" => '<img type="face" title="[唱歌]" src="/images/emotions/757.gif"/>',
			"[月亮]" => '<img type="face" title="[月亮]" src="/images/emotions/758.gif"/>',
			"[电影]" => '<img type="face" title="[电影]" src="/images/emotions/759.gif"/>',
			"[帽子]" => '<img type="face" title="[帽子]" src="/images/emotions/760.gif"/>',
			"[工作]" => '<img type="face" title="[工作]" src="/images/emotions/761.gif"/>',
			"[植树节]" => '<img type="face" title="[植树节]" src="/images/emotions/762.gif"/>',
			"[酒]" => '<img type="face" title="[酒]" src="/images/emotions/763.gif"/>',
			"[悼念乔布斯]" => '<img type="face" title="[悼念乔布斯]" src="/images/emotions/764.gif"/>',
			"[首发]" => '<img type="face" title="[首发]" src="/images/emotions/765.gif"/>',
			"[音乐盒]" => '<img type="face" title="[音乐盒]" src="/images/emotions/766.gif"/>',
			"[上海志愿者]" => '<img type="face" title="[上海志愿者]" src="/images/emotions/767.gif"/>',
			"[iPhone]" => '<img type="face" title="[iPhone]" src="/images/emotions/768.gif"/>',
			"[驯鹿]" => '<img type="face" title="[驯鹿]" src="/images/emotions/769.gif"/>',
			"[神龙]" => '<img type="face" title="[神龙]" src="/images/emotions/770.gif"/>',
			"[伦敦奥火]" => '<img type="face" title="[伦敦奥火]" src="/images/emotions/771.gif"/>',
			"[龙蛋]" => '<img type="face" title="[龙蛋]" src="/images/emotions/774.gif"/>',
			"[狗]" => '<img type="face" title="[狗]" src="/images/emotions/775.gif"/>',
			"[微博蛋糕]" => '<img type="face" title="[微博蛋糕]" src="/images/emotions/776.gif"/>',
			"[康乃馨]" => '<img type="face" title="[康乃馨]" src="/images/emotions/777.gif"/>',
			"[脚印]" => '<img type="face" title="[脚印]" src="/images/emotions/778.gif"/>',
			"[巧克力]" => '<img type="face" title="[巧克力]" src="/images/emotions/779.gif"/>',
			"[满月]" => '<img type="face" title="[满月]" src="/images/emotions/780.gif"/>',
			"[月饼]" => '<img type="face" title="[月饼]" src="/images/emotions/781.gif"/>',
			"[酒壶]" => '<img type="face" title="[酒壶]" src="/images/emotions/783.gif"/>',
			"[万圣节]" => '<img type="face" title="[万圣节]" src="/images/emotions/784.gif"/>',
			"[糖果]" => '<img type="face" title="[糖果]" src="/images/emotions/785.gif"/>',
			"[粉蛋糕]" => '<img type="face" title="[粉蛋糕]" src="/images/emotions/786.gif"/>',
			"[咖啡]" => '<img type="face" title="[咖啡]" src="/images/emotions/787.gif"/>',
			"[图片]" => '<img type="face" title="[图片]" src="/images/emotions/788.gif"/>',
			"[火炬]" => '<img type="face" title="[火炬]" src="/images/emotions/789.gif"/>',
			"[ppbbibi]" => '<img type="face" title="[ppbbibi]" src="/images/emotions/791.gif"/>',
			"[ppb靠]" => '<img type="face" title="[ppb靠]" src="/images/emotions/792.gif"/>',
			"[ppb发狂]" => '<img type="face" title="[ppb发狂]" src="/images/emotions/793.gif"/>',
			"[ppb困]" => '<img type="face" title="[ppb困]" src="/images/emotions/794.gif"/>',
			"[ppb啊哈哈]" => '<img type="face" title="[ppb啊哈哈]" src="/images/emotions/795.gif"/>',
			"[ppb僵尸]" => '<img type="face" title="[ppb僵尸]" src="/images/emotions/796.gif"/>',
			"[ppb甩嘴]" => '<img type="face" title="[ppb甩嘴]" src="/images/emotions/797.gif"/>',
			"[ppb囧]" => '<img type="face" title="[ppb囧]" src="/images/emotions/798.gif"/>',
			"[ppb去死]" => '<img type="face" title="[ppb去死]" src="/images/emotions/799.gif"/>',
			"[ppb晴天霹雳]" => '<img type="face" title="[ppb晴天霹雳]" src="/images/emotions/800.gif"/>',
			"[ppb啊]" => '<img type="face" title="[ppb啊]" src="/images/emotions/801.gif"/>',
			"[ppb大哭]" => '<img type="face" title="[ppb大哭]" src="/images/emotions/802.gif"/>',
			"[ppb我砍]" => '<img type="face" title="[ppb我砍]" src="/images/emotions/803.gif"/>',
			"[ppb扫射]" => '<img type="face" title="[ppb扫射]" src="/images/emotions/804.gif"/>',
			"[ppb杀啊]" => '<img type="face" title="[ppb杀啊]" src="/images/emotions/805.gif"/>',
			"[ppb啊呜]" => '<img type="face" title="[ppb啊呜]" src="/images/emotions/806.gif"/>',
			"[ppb蝙蝠侠]" => '<img type="face" title="[ppb蝙蝠侠]" src="/images/emotions/807.gif"/>',
			"[ppb滚]" => '<img type="face" title="[ppb滚]" src="/images/emotions/808.gif"/>',
			"[ppb欢迎欢迎]" => '<img type="face" title="[ppb欢迎欢迎]" src="/images/emotions/809.gif"/>',
			"[ppb狂吃]" => '<img type="face" title="[ppb狂吃]" src="/images/emotions/810.gif"/>',
			"[ppb讨厌]" => '<img type="face" title="[ppb讨厌]" src="/images/emotions/811.gif"/>',
			"[ppb爱你哟]" => '<img type="face" title="[ppb爱你哟]" src="/images/emotions/812.gif"/>',
			"[ppb卖萌]" => '<img type="face" title="[ppb卖萌]" src="/images/emotions/813.gif"/>',
			"[ala扭啊扭]" => '<img type="face" title="[ala扭啊扭]" src="/images/emotions/814.gif"/>',
			"[ala吐舌头]" => '<img type="face" title="[ala吐舌头]" src="/images/emotions/815.gif"/>',
			"[ala么么]" => '<img type="face" title="[ala么么]" src="/images/emotions/816.gif"/>',
			"[ala嘿嘿嘿]" => '<img type="face" title="[ala嘿嘿嘿]" src="/images/emotions/817.gif"/>',
			"[ala哼]" => '<img type="face" title="[ala哼]" src="/images/emotions/818.gif"/>',
			"[ala囧]" => '<img type="face" title="[ala囧]" src="/images/emotions/819.gif"/>',
			"[ala上火]" => '<img type="face" title="[ala上火]" src="/images/emotions/820.gif"/>',
			"[ala啊哈哈哈]" => '<img type="face" title="[ala啊哈哈哈]" src="/images/emotions/821.gif"/>',
			"[ala飘走]" => '<img type="face" title="[ala飘走]" src="/images/emotions/822.gif"/>',
			"[ala吃货]" => '<img type="face" title="[ala吃货]" src="/images/emotions/823.gif"/>',
			"[ala悲催]" => '<img type="face" title="[ala悲催]" src="/images/emotions/824.gif"/>',
			"[ala讨厌]" => '<img type="face" title="[ala讨厌]" src="/images/emotions/825.gif"/>',
			"[ala衰]" => '<img type="face" title="[ala衰]" src="/images/emotions/826.gif"/>',
			"[哎呦熊做面膜]" => '<img type="face" title="[哎呦熊做面膜]" src="/images/emotions/827.gif"/>',
			"[哎呦熊咒骂]" => '<img type="face" title="[哎呦熊咒骂]" src="/images/emotions/828.gif"/>',
			"[哎呦熊震惊]" => '<img type="face" title="[哎呦熊震惊]" src="/images/emotions/829.gif"/>',
			"[哎呦熊yes]" => '<img type="face" title="[哎呦熊yes]" src="/images/emotions/830.gif"/>',
			"[哎呦熊掩面]" => '<img type="face" title="[哎呦熊掩面]" src="/images/emotions/831.gif"/>',
			"[哎呦熊乌鸦]" => '<img type="face" title="[哎呦熊乌鸦]" src="/images/emotions/832.gif"/>',
			"[哎呦熊无奈]" => '<img type="face" title="[哎呦熊无奈]" src="/images/emotions/833.gif"/>',
			"[哎呦熊晚安]" => '<img type="face" title="[哎呦熊晚安]" src="/images/emotions/834.gif"/>',
			"[哎呦熊生日快乐]" => '<img type="face" title="[哎呦熊生日快乐]" src="/images/emotions/835.gif"/>',
			"[哎呦熊撒欢]" => '<img type="face" title="[哎呦熊撒欢]" src="/images/emotions/836.gif"/>',
			"[哎呦熊no]" => '<img type="face" title="[哎呦熊no]" src="/images/emotions/837.gif"/>',
			"[哎呦熊路过]" => '<img type="face" title="[哎呦熊路过]" src="/images/emotions/838.gif"/>',
			"[哎呦熊流汗]" => '<img type="face" title="[哎呦熊流汗]" src="/images/emotions/839.gif"/>',
			"[哎呦熊流鼻血]" => '<img type="face" title="[哎呦熊流鼻血]" src="/images/emotions/840.gif"/>',
			"[哎呦熊雷死]" => '<img type="face" title="[哎呦熊雷死]" src="/images/emotions/841.gif"/>',
			"[哎呦熊泪奔]" => '<img type="face" title="[哎呦熊泪奔]" src="/images/emotions/842.gif"/>',
			"[哎呦熊哭泣]" => '<img type="face" title="[哎呦熊哭泣]" src="/images/emotions/843.gif"/>',
			"[哎呦熊开心]" => '<img type="face" title="[哎呦熊开心]" src="/images/emotions/844.gif"/>',
			"[哎呦熊开饭咯]" => '<img type="face" title="[哎呦熊开饭咯]" src="/images/emotions/845.gif"/>',
			"[哎呦熊纠结]" => '<img type="face" title="[哎呦熊纠结]" src="/images/emotions/846.gif"/>',
			"[哎呦熊害羞]" => '<img type="face" title="[哎呦熊害羞]" src="/images/emotions/847.gif"/>',
			"[哎呦熊鼓掌]" => '<img type="face" title="[哎呦熊鼓掌]" src="/images/emotions/848.gif"/>',
			"[哎呦熊感动]" => '<img type="face" title="[哎呦熊感动]" src="/images/emotions/849.gif"/>',
			"[哎呦熊浮云]" => '<img type="face" title="[哎呦熊浮云]" src="/images/emotions/850.gif"/>',
			"[哎呦熊飞吻]" => '<img type="face" title="[哎呦熊飞吻]" src="/images/emotions/851.gif"/>',
			"[哎呦熊打招呼]" => '<img type="face" title="[哎呦熊打招呼]" src="/images/emotions/852.gif"/>',
			"[哎呦熊补妆]" => '<img type="face" title="[哎呦熊补妆]" src="/images/emotions/853.gif"/>',
			"[哎呦熊崩溃]" => '<img type="face" title="[哎呦熊崩溃]" src="/images/emotions/854.gif"/>',
			"[萌]" => '<img type="face" title="[萌]" src="/images/emotions/860.gif"/>',
			"[扔鸡蛋]" => '<img type="face" title="[扔鸡蛋]" src="/images/emotions/864.gif"/>',
			"[热吻]" => '<img type="face" title="[热吻]" src="/images/emotions/868.gif"/>',
			"[orz]" => '<img type="face" title="[orz]" src="/images/emotions/870.gif"/>',
			"[宅]" => '<img type="face" title="[宅]" src="/images/emotions/871.gif"/>',
			"[帅]" => '<img type="face" title="[帅]" src="/images/emotions/872.gif"/>',
			"[实习]" => '<img type="face" title="[实习]" src="/images/emotions/874.gif"/>',
			"[骷髅]" => '<img type="face" title="[骷髅]" src="/images/emotions/875.gif"/>',
			"[便便]" => '<img type="face" title="[便便]" src="/images/emotions/876.gif"/>',
			"[黄牌]" => '<img type="face" title="[黄牌]" src="/images/emotions/1511.gif"/>',
			"[红牌]" => '<img type="face" title="[红牌]" src="/images/emotions/1512.gif"/>',
			"[跳舞花]" => '<img type="face" title="[跳舞花]" src="/images/emotions/879.gif"/>',
			"[礼花]" => '<img type="face" title="[礼花]" src="/images/emotions/880.gif"/>',
			"[打针]" => '<img type="face" title="[打针]" src="/images/emotions/881.gif"/>',
			"[叹号]" => '<img type="face" title="[叹号]" src="/images/emotions/882.gif"/>',
			"[问号]" => '<img type="face" title="[问号]" src="/images/emotions/883.gif"/>',
			"[句号]" => '<img type="face" title="[句号]" src="/images/emotions/884.gif"/>',
			"[逗号]" => '<img type="face" title="[逗号]" src="/images/emotions/885.gif"/>',
			"[闪]" => '<img type="face" title="[闪]" src="/images/emotions/886.gif"/>',
			"[啦啦]" => '<img type="face" title="[啦啦]" src="/images/emotions/887.gif"/>',
			"[吼吼]" => '<img type="face" title="[吼吼]" src="/images/emotions/888.gif"/>',
			"[庆祝]" => '<img type="face" title="[庆祝]" src="/images/emotions/889.gif"/>',
			"[嘿]" => '<img type="face" title="[嘿]" src="/images/emotions/890.gif"/>',
			"[00]" => '<img type="face" title="[00]" src="/images/emotions/891.gif"/>',
			"[1]" => '<img type="face" title="[1]" src="/images/emotions/892.gif"/>',
			"[2]" => '<img type="face" title="[2]" src="/images/emotions/893.gif"/>',
			"[3]" => '<img type="face" title="[3]" src="/images/emotions/894.gif"/>',
			"[4]" => '<img type="face" title="[4]" src="/images/emotions/895.gif"/>',
			"[5]" => '<img type="face" title="[5]" src="/images/emotions/896.gif"/>',
			"[6]" => '<img type="face" title="[6]" src="/images/emotions/897.gif"/>',
			"[7]" => '<img type="face" title="[7]" src="/images/emotions/898.gif"/>',
			"[8]" => '<img type="face" title="[8]" src="/images/emotions/899.gif"/>',
			"[9]" => '<img type="face" title="[9]" src="/images/emotions/900.gif"/>',
			"[a]" => '<img type="face" title="[a]" src="/images/emotions/901.gif"/>',
			"[b]" => '<img type="face" title="[b]" src="/images/emotions/902.gif"/>',
			"[c]" => '<img type="face" title="[c]" src="/images/emotions/903.gif"/>',
			"[d]" => '<img type="face" title="[d]" src="/images/emotions/904.gif"/>',
			"[e]" => '<img type="face" title="[e]" src="/images/emotions/905.gif"/>',
			"[f]" => '<img type="face" title="[f]" src="/images/emotions/906.gif"/>',
			"[g]" => '<img type="face" title="[g]" src="/images/emotions/907.gif"/>',
			"[h]" => '<img type="face" title="[h]" src="/images/emotions/908.gif"/>',
			"[i]" => '<img type="face" title="[i]" src="/images/emotions/909.gif"/>',
			"[j]" => '<img type="face" title="[j]" src="/images/emotions/910.gif"/>',
			"[k]" => '<img type="face" title="[k]" src="/images/emotions/911.gif"/>',
			"[l]" => '<img type="face" title="[l]" src="/images/emotions/912.gif"/>',
			"[m]" => '<img type="face" title="[m]" src="/images/emotions/913.gif"/>',
			"[n]" => '<img type="face" title="[n]" src="/images/emotions/914.gif"/>',
			"[o]" => '<img type="face" title="[o]" src="/images/emotions/915.gif"/>',
			"[p]" => '<img type="face" title="[p]" src="/images/emotions/916.gif"/>',
			"[q]" => '<img type="face" title="[q]" src="/images/emotions/917.gif"/>',
			"[r]" => '<img type="face" title="[r]" src="/images/emotions/918.gif"/>',
			"[s]" => '<img type="face" title="[s]" src="/images/emotions/919.gif"/>',
			"[t]" => '<img type="face" title="[t]" src="/images/emotions/920.gif"/>',
			"[u]" => '<img type="face" title="[u]" src="/images/emotions/921.gif"/>',
			"[v]" => '<img type="face" title="[v]" src="/images/emotions/922.gif"/>',
			"[w]" => '<img type="face" title="[w]" src="/images/emotions/923.gif"/>',
			"[x]" => '<img type="face" title="[x]" src="/images/emotions/924.gif"/>',
			"[y]" => '<img type="face" title="[y]" src="/images/emotions/925.gif"/>',
			"[z]" => '<img type="face" title="[z]" src="/images/emotions/926.gif"/>',
			"[团]" => '<img type="face" title="[团]" src="/images/emotions/927.gif"/>',
			"[圆]" => '<img type="face" title="[圆]" src="/images/emotions/928.gif"/>',
			"[男孩儿]" => '<img type="face" title="[男孩儿]" src="/images/emotions/929.gif"/>',
			"[女孩儿]" => '<img type="face" title="[女孩儿]" src="/images/emotions/930.gif"/>',
			"[22]" => '<img type="face" title="[22]" src="/images/emotions/931.gif"/>',
			"[点]" => '<img type="face" title="[点]" src="/images/emotions/932.gif"/>',
			"[鸭梨]" => '<img type="face" title="[鸭梨]" src="/images/emotions/933.gif"/>',
			"[省略号]" => '<img type="face" title="[省略号]" src="/images/emotions/934.gif"/>',
			"[kiss]" => '<img type="face" title="[kiss]" src="/images/emotions/935.gif"/>',
			"[雪人]" => '<img type="face" title="[雪人]" src="/images/emotions/936.gif"/>',
			"[小丑]" => '<img type="face" title="[小丑]" src="/images/emotions/937.gif"/>',
			"[km问号]" => '<img type="face" title="[km问号]" src="/images/emotions/938.gif"/>',
			"[km爱你]" => '<img type="face" title="[km爱你]" src="/images/emotions/939.gif"/>',
			"[km白块旋转]" => '<img type="face" title="[km白块旋转]" src="/images/emotions/940.gif"/>',
			"[km黑块旋转]" => '<img type="face" title="[km黑块旋转]" src="/images/emotions/941.gif"/>',
			"[km花痴]" => '<img type="face" title="[km花痴]" src="/images/emotions/942.gif"/>',
			"[km可爱]" => '<img type="face" title="[km可爱]" src="/images/emotions/943.gif"/>',
			"[km切]" => '<img type="face" title="[km切]" src="/images/emotions/944.gif"/>',
			"[km亲亲]" => '<img type="face" title="[km亲亲]" src="/images/emotions/945.gif"/>',
			"[km亲亲白块]" => '<img type="face" title="[km亲亲白块]" src="/images/emotions/946.gif"/>',
			"[km亲亲黑块]" => '<img type="face" title="[km亲亲黑块]" src="/images/emotions/947.gif"/>',
			"[km挖鼻屎]" => '<img type="face" title="[km挖鼻屎]" src="/images/emotions/948.gif"/>',
			"[km哇哇哭]" => '<img type="face" title="[km哇哇哭]" src="/images/emotions/949.gif"/>',
			"[km围观]" => '<img type="face" title="[km围观]" src="/images/emotions/950.gif"/>',
			"[km委屈]" => '<img type="face" title="[km委屈]" src="/images/emotions/951.gif"/>',
			"[km羞]" => '<img type="face" title="[km羞]" src="/images/emotions/952.gif"/>',
			"[kmFL]" => '<img type="face" title="[kmFL]" src="/images/emotions/953.gif"/>',
			"[km侦探]" => '<img type="face" title="[km侦探]" src="/images/emotions/954.gif"/>',
			"[km嘻嘻]" => '<img type="face" title="[km嘻嘻]" src="/images/emotions/955.gif"/>',
			"[km呜呜1]" => '<img type="face" title="[km呜呜1]" src="/images/emotions/956.gif"/>',
			"[km冷笑]" => '<img type="face" title="[km冷笑]" src="/images/emotions/957.gif"/>',
			"[km邮件]" => '<img type="face" title="[km邮件]" src="/images/emotions/958.gif"/>',
			"[km闹钟]" => '<img type="face" title="[km闹钟]" src="/images/emotions/959.gif"/>',
			"[km哼]" => '<img type="face" title="[km哼]" src="/images/emotions/960.gif"/>',
			"[km无语]" => '<img type="face" title="[km无语]" src="/images/emotions/961.gif"/>',
			"[km黑块不淡定]" => '<img type="face" title="[km黑块不淡定]" src="/images/emotions/962.gif"/>',
			"[km害怕]" => '<img type="face" title="[km害怕]" src="/images/emotions/963.gif"/>',
			"[km呜呜88]" => '<img type="face" title="[km呜呜88]" src="/images/emotions/964.gif"/>',
			"[km透亮]" => '<img type="face" title="[km透亮]" src="/images/emotions/965.gif"/>',
			"[km唔]" => '<img type="face" title="[km唔]" src="/images/emotions/966.gif"/>',
			"[km侠盗]" => '<img type="face" title="[km侠盗]" src="/images/emotions/967.gif"/>',
			"[km醉]" => '<img type="face" title="[km醉]" src="/images/emotions/968.gif"/>',
			"[km丽莎2]" => '<img type="face" title="[km丽莎2]" src="/images/emotions/969.gif"/>',
			"[km酷2]" => '<img type="face" title="[km酷2]" src="/images/emotions/970.gif"/>',
			"[km憨]" => '<img type="face" title="[km憨]" src="/images/emotions/971.gif"/>',
			"[km中毒]" => '<img type="face" title="[km中毒]" src="/images/emotions/972.gif"/>',
			"[km电视]" => '<img type="face" title="[km电视]" src="/images/emotions/973.gif"/>',
			"[km困]" => '<img type="face" title="[km困]" src="/images/emotions/974.gif"/>',
			"[km高兴]" => '<img type="face" title="[km高兴]" src="/images/emotions/975.gif"/>',
			"[km幺鸡猫]" => '<img type="face" title="[km幺鸡猫]" src="/images/emotions/976.gif"/>',
			"[km黑化笑]" => '<img type="face" title="[km黑化笑]" src="/images/emotions/977.gif"/>',
			"[km花猫]" => '<img type="face" title="[km花猫]" src="/images/emotions/978.gif"/>',
			"[km好吃]" => '<img type="face" title="[km好吃]" src="/images/emotions/979.gif"/>',
			"[kmAI]" => '<img type="face" title="[kmAI]" src="/images/emotions/980.gif"/>',
			"[km黑化唠叨]" => '<img type="face" title="[km黑化唠叨]" src="/images/emotions/981.gif"/>',
			"[km好吃惊]" => '<img type="face" title="[km好吃惊]" src="/images/emotions/982.gif"/>',
			"[km唠叨]" => '<img type="face" title="[km唠叨]" src="/images/emotions/983.gif"/>',
			"[km眼镜]" => '<img type="face" title="[km眼镜]" src="/images/emotions/984.gif"/>',
			"[km闪]" => '<img type="face" title="[km闪]" src="/images/emotions/985.gif"/>',
			"[kmV]" => '<img type="face" title="[kmV]" src="/images/emotions/986.gif"/>',
			"[km不淡定]" => '<img type="face" title="[km不淡定]" src="/images/emotions/987.gif"/>',
			"[km鼻血1]" => '<img type="face" title="[km鼻血1]" src="/images/emotions/988.gif"/>',
			"[km好饿]" => '<img type="face" title="[km好饿]" src="/images/emotions/989.gif"/>',
			"[km上传]" => '<img type="face" title="[km上传]" src="/images/emotions/990.gif"/>',
			"[km黑化]" => '<img type="face" title="[km黑化]" src="/images/emotions/991.gif"/>',
			"[km鼻血]" => '<img type="face" title="[km鼻血]" src="/images/emotions/992.gif"/>',
			"[km酷]" => '<img type="face" title="[km酷]" src="/images/emotions/993.gif"/>',
			"[km愁]" => '<img type="face" title="[km愁]" src="/images/emotions/994.gif"/>',
			"[km相机]" => '<img type="face" title="[km相机]" src="/images/emotions/995.gif"/>',
			"[km喜]" => '<img type="face" title="[km喜]" src="/images/emotions/996.gif"/>',
			"[km得意]" => '<img type="face" title="[km得意]" src="/images/emotions/997.gif"/>',
			"[km怒]" => '<img type="face" title="[km怒]" src="/images/emotions/998.gif"/>',
			"[km生气]" => '<img type="face" title="[km生气]" src="/images/emotions/999.gif"/>',
			"[kmDW]" => '<img type="face" title="[kmDW]" src="/images/emotions/1000.gif"/>',
			"[km呜血泪]" => '<img type="face" title="[km呜血泪]" src="/images/emotions/1001.gif"/>',
			"[kmPS]" => '<img type="face" title="[kmPS]" src="/images/emotions/1002.gif"/>',
			"[km馋]" => '<img type="face" title="[km馋]" src="/images/emotions/1003.gif"/>',
			"[km下载]" => '<img type="face" title="[km下载]" src="/images/emotions/1004.gif"/>',
			"[kmX]" => '<img type="face" title="[kmX]" src="/images/emotions/1005.gif"/>',
			"[km情书]" => '<img type="face" title="[km情书]" src="/images/emotions/1006.gif"/>',
			"[km骷髅]" => '<img type="face" title="[km骷髅]" src="/images/emotions/1007.gif"/>',
			"[km丽莎]" => '<img type="face" title="[km丽莎]" src="/images/emotions/1008.gif"/>',
			"[km禁]" => '<img type="face" title="[km禁]" src="/images/emotions/1009.gif"/>',
			"[km晕]" => '<img type="face" title="[km晕]" src="/images/emotions/1010.gif"/>',
			"[km热]" => '<img type="face" title="[km热]" src="/images/emotions/1011.gif"/>',
			"[km冷]" => '<img type="face" title="[km冷]" src="/images/emotions/1012.gif"/>',
			"[km猫]" => '<img type="face" title="[km猫]" src="/images/emotions/1013.gif"/>',
			"[bofu吐舌头]" => '<img type="face" title="[bofu吐舌头]" src="/images/emotions/1014.gif"/>',
			"[bofu拜年]" => '<img type="face" title="[bofu拜年]" src="/images/emotions/1601.gif"/>',
			"[bofu淫笑]" => '<img type="face" title="[bofu淫笑]" src="/images/emotions/1016.gif"/>',
			"[bofu压力山大]" => '<img type="face" title="[bofu压力山大]" src="/images/emotions/1017.gif"/>',
			"[bofu心灰意冷]" => '<img type="face" title="[bofu心灰意冷]" src="/images/emotions/1018.gif"/>',
			"[bofu心动]" => '<img type="face" title="[bofu心动]" src="/images/emotions/1019.gif"/>',
			"[bofu咸蛋超人]" => '<img type="face" title="[bofu咸蛋超人]" src="/images/emotions/1020.gif"/>',
			"[bofu食神]" => '<img type="face" title="[bofu食神]" src="/images/emotions/1021.gif"/>',
			"[bofu票子快来]" => '<img type="face" title="[bofu票子快来]" src="/images/emotions/1022.gif"/>',
			"[bofu怒]" => '<img type="face" title="[bofu怒]" src="/images/emotions/1023.gif"/>',
			"[bofu扭]" => '<img type="face" title="[bofu扭]" src="/images/emotions/1024.gif"/>',
			"[bofu梦遗]" => '<img type="face" title="[bofu梦遗]" src="/images/emotions/1025.gif"/>',
			"[bofu累]" => '<img type="face" title="[bofu累]" src="/images/emotions/1026.gif"/>',
			"[bofu啃西瓜]" => '<img type="face" title="[bofu啃西瓜]" src="/images/emotions/1027.gif"/>',
			"[bofu给力]" => '<img type="face" title="[bofu给力]" src="/images/emotions/1028.gif"/>',
			"[bofu发愤图强]" => '<img type="face" title="[bofu发愤图强]" src="/images/emotions/1029.gif"/>',
			"[bofu抖骚]" => '<img type="face" title="[bofu抖骚]" src="/images/emotions/1030.gif"/>',
			"[bofu得瑟]" => '<img type="face" title="[bofu得瑟]" src="/images/emotions/1031.gif"/>',
			"[bofu打飞机]" => '<img type="face" title="[bofu打飞机]" src="/images/emotions/1032.gif"/>',
			"[bofu变脸]" => '<img type="face" title="[bofu变脸]" src="/images/emotions/1033.gif"/>',
			"[bofu蹦极]" => '<img type="face" title="[bofu蹦极]" src="/images/emotions/1034.gif"/>',
			"[bofu暴躁]" => '<img type="face" title="[bofu暴躁]" src="/images/emotions/1035.gif"/>',
			"[萌萌星星眼]" => '<img type="face" title="[萌萌星星眼]" src="/images/emotions/1036.gif"/>',
			"[萌萌打滚]" => '<img type="face" title="[萌萌打滚]" src="/images/emotions/1037.gif"/>',
			"[萌萌甩帽]" => '<img type="face" title="[萌萌甩帽]" src="/images/emotions/1038.gif"/>',
			"[萌萌摔瓶]" => '<img type="face" title="[萌萌摔瓶]" src="/images/emotions/1039.gif"/>',
			"[萌萌扭屁股]" => '<img type="face" title="[萌萌扭屁股]" src="/images/emotions/1040.gif"/>',
			"[萌萌惊讶]" => '<img type="face" title="[萌萌惊讶]" src="/images/emotions/1041.gif"/>',
			"[萌萌懒得理]" => '<img type="face" title="[萌萌懒得理]" src="/images/emotions/1042.gif"/>',
			"[萌萌偷乐]" => '<img type="face" title="[萌萌偷乐]" src="/images/emotions/1043.gif"/>',
			"[萌萌鄙视]" => '<img type="face" title="[萌萌鄙视]" src="/images/emotions/1044.gif"/>',
			"[萌萌哈欠]" => '<img type="face" title="[萌萌哈欠]" src="/images/emotions/1045.gif"/>',
			"[萌萌石化]" => '<img type="face" title="[萌萌石化]" src="/images/emotions/1046.gif"/>',
			"[萌萌敲鼓]" => '<img type="face" title="[萌萌敲鼓]" src="/images/emotions/1047.gif"/>',
			"[萌萌叹气]" => '<img type="face" title="[萌萌叹气]" src="/images/emotions/1048.gif"/>',
			"[萌萌捶地笑]" => '<img type="face" title="[萌萌捶地笑]" src="/images/emotions/1049.gif"/>',
			"[萌萌捂脸]" => '<img type="face" title="[萌萌捂脸]" src="/images/emotions/1050.gif"/>',
			"[萌萌流汗]" => '<img type="face" title="[萌萌流汗]" src="/images/emotions/1051.gif"/>',
			"[萌萌抠鼻]" => '<img type="face" title="[萌萌抠鼻]" src="/images/emotions/1052.gif"/>',
			"[萌萌泪奔]" => '<img type="face" title="[萌萌泪奔]" src="/images/emotions/1053.gif"/>',
			"[萌萌献花]" => '<img type="face" title="[萌萌献花]" src="/images/emotions/1054.gif"/>',
			"[欢欢]" => '<img type="face" title="[欢欢]" src="/images/emotions/1055.gif"/>',
			"[乐乐]" => '<img type="face" title="[乐乐]" src="/images/emotions/1056.gif"/>',
			"[管不着爱]" => '<img type="face" title="[管不着爱]" src="/images/emotions/1057.gif"/>',
			"[爱]" => '<img type="face" title="[爱]" src="/images/emotions/1058.gif"/>',
			"[了不起爱]" => '<img type="face" title="[了不起爱]" src="/images/emotions/1059.gif"/>',
			"[gbz真穿越]" => '<img type="face" title="[gbz真穿越]" src="/images/emotions/1060.gif"/>',
			"[gbz再睡会]" => '<img type="face" title="[gbz再睡会]" src="/images/emotions/1061.gif"/>',
			"[gbz呜呜]" => '<img type="face" title="[gbz呜呜]" src="/images/emotions/1062.gif"/>',
			"[gbz委屈]" => '<img type="face" title="[gbz委屈]" src="/images/emotions/1063.gif"/>',
			"[gbz晚安了]" => '<img type="face" title="[gbz晚安了]" src="/images/emotions/1064.gif"/>',
			"[gbz祈福]" => '<img type="face" title="[gbz祈福]" src="/images/emotions/1065.gif"/>',
			"[gbz祈福了]" => '<img type="face" title="[gbz祈福了]" src="/images/emotions/1066.gif"/>',
			"[gbz窃笑]" => '<img type="face" title="[gbz窃笑]" src="/images/emotions/1067.gif"/>',
			"[gbz起床啦]" => '<img type="face" title="[gbz起床啦]" src="/images/emotions/1068.gif"/>',
			"[gbz困]" => '<img type="face" title="[gbz困]" src="/images/emotions/1069.gif"/>',
			"[gbz加班]" => '<img type="face" title="[gbz加班]" src="/images/emotions/1070.gif"/>',
			"[gbz加班中]" => '<img type="face" title="[gbz加班中]" src="/images/emotions/1071.gif"/>',
			"[gbz饿]" => '<img type="face" title="[gbz饿]" src="/images/emotions/1072.gif"/>',
			"[gbz饿晕]" => '<img type="face" title="[gbz饿晕]" src="/images/emotions/1073.gif"/>',
			"[gbz得意]" => '<img type="face" title="[gbz得意]" src="/images/emotions/1074.gif"/>',
			"[gbz大笑]" => '<img type="face" title="[gbz大笑]" src="/images/emotions/1075.gif"/>',
			"[gbz穿越了]" => '<img type="face" title="[gbz穿越了]" src="/images/emotions/1076.gif"/>',
			"[有点困]" => '<img type="face" title="[有点困]" src="/images/emotions/1077.gif"/>',
			"[yes]" => '<img type="face" title="[yes]" src="/images/emotions/1078.gif"/>',
			"[咽回去了]" => '<img type="face" title="[咽回去了]" src="/images/emotions/1079.gif"/>',
			"[鸭梨很大]" => '<img type="face" title="[鸭梨很大]" src="/images/emotions/1080.gif"/>',
			"[羞羞]" => '<img type="face" title="[羞羞]" src="/images/emotions/1081.gif"/>',
			"[喜欢你]" => '<img type="face" title="[喜欢你]" src="/images/emotions/1082.gif"/>',
			"[小便屁]" => '<img type="face" title="[小便屁]" src="/images/emotions/1083.gif"/>',
			"[无奈]" => '<img type="face" title="[无奈]" src="/images/emotions/1084.gif"/>',
			"[兔兔]" => '<img type="face" title="[兔兔]" src="/images/emotions/1085.gif"/>',
			"[吐舌头]" => '<img type="face" title="[吐舌头]" src="/images/emotions/1086.gif"/>',
			"[头晕]" => '<img type="face" title="[头晕]" src="/images/emotions/1087.gif"/>',
			"[听音乐]" => '<img type="face" title="[听音乐]" src="/images/emotions/1088.gif"/>',
			"[睡大觉]" => '<img type="face" title="[睡大觉]" src="/images/emotions/1089.gif"/>',
			"[闪闪紫]" => '<img type="face" title="[闪闪紫]" src="/images/emotions/1090.gif"/>',
			"[闪闪绿]" => '<img type="face" title="[闪闪绿]" src="/images/emotions/1091.gif"/>',
			"[闪闪灰]" => '<img type="face" title="[闪闪灰]" src="/images/emotions/1092.gif"/>',
			"[闪闪红]" => '<img type="face" title="[闪闪红]" src="/images/emotions/1093.gif"/>',
			"[闪闪粉]" => '<img type="face" title="[闪闪粉]" src="/images/emotions/1094.gif"/>',
			"[咆哮]" => '<img type="face" title="[咆哮]" src="/images/emotions/1095.gif"/>',
			"[摸头]" => '<img type="face" title="[摸头]" src="/images/emotions/1096.gif"/>',
			"[真美好]" => '<img type="face" title="[真美好]" src="/images/emotions/1097.gif"/>',
			"[脸红自爆]" => '<img type="face" title="[脸红自爆]" src="/images/emotions/1098.gif"/>',
			"[哭泣女]" => '<img type="face" title="[哭泣女]" src="/images/emotions/1099.gif"/>',
			"[哭泣男]" => '<img type="face" title="[哭泣男]" src="/images/emotions/1100.gif"/>',
			"[空]" => '<img type="face" title="[空]" src="/images/emotions/1101.gif"/>',
			"[尽情玩]" => '<img type="face" title="[尽情玩]" src="/images/emotions/1102.gif"/>',
			"[惊喜]" => '<img type="face" title="[惊喜]" src="/images/emotions/1103.gif"/>',
			"[惊呆]" => '<img type="face" title="[惊呆]" src="/images/emotions/1104.gif"/>',
			"[胡萝卜]" => '<img type="face" title="[胡萝卜]" src="/images/emotions/1105.gif"/>',
			"[欢腾去爱]" => '<img type="face" title="[欢腾去爱]" src="/images/emotions/1106.gif"/>',
			"[感冒了]" => '<img type="face" title="[感冒了]" src="/images/emotions/1107.gif"/>',
			"[怒了]" => '<img type="face" title="[怒了]" src="/images/emotions/1108.gif"/>',
			"[我要奋斗]" => '<img type="face" title="[我要奋斗]" src="/images/emotions/1109.gif"/>',
			"[发芽]" => '<img type="face" title="[发芽]" src="/images/emotions/1110.gif"/>',
			"[春暖花开]" => '<img type="face" title="[春暖花开]" src="/images/emotions/1111.gif"/>',
			"[抽烟]" => '<img type="face" title="[抽烟]" src="/images/emotions/1112.gif"/>',
			"[昂]" => '<img type="face" title="[昂]" src="/images/emotions/1113.gif"/>',
			"[啊]" => '<img type="face" title="[啊]" src="/images/emotions/1114.gif"/>',
			"[自插双目]" => '<img type="face" title="[自插双目]" src="/images/emotions/1115.gif"/>',
			"[咦]" => '<img type="face" title="[咦]" src="/images/emotions/1116.gif"/>',
			"[嘘嘘]" => '<img type="face" title="[嘘嘘]" src="/images/emotions/1117.gif"/>',
			"[我吃]" => '<img type="face" title="[我吃]" src="/images/emotions/1118.gif"/>',
			"[喵呜]" => '<img type="face" title="[喵呜]" src="/images/emotions/1119.gif"/>',
			"[v5]" => '<img type="face" title="[v5]" src="/images/emotions/1120.gif"/>',
			"[调戏]" => '<img type="face" title="[调戏]" src="/images/emotions/1121.gif"/>',
			"[打牙]" => '<img type="face" title="[打牙]" src="/images/emotions/1122.gif"/>',
			"[手贱]" => '<img type="face" title="[手贱]" src="/images/emotions/1123.gif"/>',
			"[色]" => '<img type="face" title="[色]" src="/images/emotions/1124.gif"/>',
			"[喷]" => '<img type="face" title="[喷]" src="/images/emotions/1125.gif"/>',
			"[你懂的]" => '<img type="face" title="[你懂的]" src="/images/emotions/1126.gif"/>',
			"[喵]" => '<img type="face" title="[喵]" src="/images/emotions/1127.gif"/>',
			"[美味]" => '<img type="face" title="[美味]" src="/images/emotions/1128.gif"/>',
			"[惊恐]" => '<img type="face" title="[惊恐]" src="/images/emotions/1129.gif"/>',
			"[感动]" => '<img type="face" title="[感动]" src="/images/emotions/1130.gif"/>',
			"[放开]" => '<img type="face" title="[放开]" src="/images/emotions/1131.gif"/>',
			"[痴呆]" => '<img type="face" title="[痴呆]" src="/images/emotions/1132.gif"/>',
			"[扯脸]" => '<img type="face" title="[扯脸]" src="/images/emotions/1133.gif"/>',
			"[不知所措]" => '<img type="face" title="[不知所措]" src="/images/emotions/1134.gif"/>',
			"[白眼]" => '<img type="face" title="[白眼]" src="/images/emotions/1135.gif"/>',
			"[cc疯掉]" => '<img type="face" title="[cc疯掉]" src="/images/emotions/1136.gif"/>',
			"[cc吃货]" => '<img type="face" title="[cc吃货]" src="/images/emotions/1137.gif"/>',
			"[cc疑问]" => '<img type="face" title="[cc疑问]" src="/images/emotions/1138.gif"/>',
			"[cc老爷]" => '<img type="face" title="[cc老爷]" src="/images/emotions/1139.gif"/>',
			"[cc开心]" => '<img type="face" title="[cc开心]" src="/images/emotions/1140.gif"/>',
			"[cc怕怕]" => '<img type="face" title="[cc怕怕]" src="/images/emotions/1141.gif"/>',
			"[cc哎呦喂]" => '<img type="face" title="[cc哎呦喂]" src="/images/emotions/1142.gif"/>',
			"[cc鼻血]" => '<img type="face" title="[cc鼻血]" src="/images/emotions/1143.gif"/>',
			"[cc没有]" => '<img type="face" title="[cc没有]" src="/images/emotions/1144.gif"/>',
			"[cc晕菜]" => '<img type="face" title="[cc晕菜]" src="/images/emotions/1145.gif"/>',
			"[cc媚眼]" => '<img type="face" title="[cc媚眼]" src="/images/emotions/1146.gif"/>',
			"[cc鄙视]" => '<img type="face" title="[cc鄙视]" src="/images/emotions/1147.gif"/>',
			"[cc委屈]" => '<img type="face" title="[cc委屈]" src="/images/emotions/1148.gif"/>',
			"[cc革命]" => '<img type="face" title="[cc革命]" src="/images/emotions/1149.gif"/>',
			"[cc撞墙]" => '<img type="face" title="[cc撞墙]" src="/images/emotions/1150.gif"/>',
			"[cc穿越]" => '<img type="face" title="[cc穿越]" src="/images/emotions/1151.gif"/>',
			"[cc嘿嘿]" => '<img type="face" title="[cc嘿嘿]" src="/images/emotions/1152.gif"/>',
			"[cc不行]" => '<img type="face" title="[cc不行]" src="/images/emotions/1153.gif"/>',
			"[cc大哭]" => '<img type="face" title="[cc大哭]" src="/images/emotions/1154.gif"/>',
			"[cc耍赖]" => '<img type="face" title="[cc耍赖]" src="/images/emotions/1155.gif"/>',
			"[cc激动]" => '<img type="face" title="[cc激动]" src="/images/emotions/1156.gif"/>',
			"[cc哭泣]" => '<img type="face" title="[cc哭泣]" src="/images/emotions/1157.gif"/>',
			"[cc亲亲]" => '<img type="face" title="[cc亲亲]" src="/images/emotions/1158.gif"/>',
			"[cc心虚]" => '<img type="face" title="[cc心虚]" src="/images/emotions/1159.gif"/>',
			"[cc舞动]" => '<img type="face" title="[cc舞动]" src="/images/emotions/1160.gif"/>',
			"[cc数钱]" => '<img type="face" title="[cc数钱]" src="/images/emotions/1161.gif"/>',
			"[cc抱抱]" => '<img type="face" title="[cc抱抱]" src="/images/emotions/1162.gif"/>',
			"[cc睡觉]" => '<img type="face" title="[cc睡觉]" src="/images/emotions/1163.gif"/>',
			"[cc僵尸]" => '<img type="face" title="[cc僵尸]" src="/images/emotions/1164.gif"/>',
			"[cc我踩]" => '<img type="face" title="[cc我踩]" src="/images/emotions/1165.gif"/>',
			"[cc运动]" => '<img type="face" title="[cc运动]" src="/images/emotions/1166.gif"/>',
			"[cc恭喜]" => '<img type="face" title="[cc恭喜]" src="/images/emotions/1167.gif"/>',
			"[cc歌唱]" => '<img type="face" title="[cc歌唱]" src="/images/emotions/1168.gif"/>',
			"[cc无语]" => '<img type="face" title="[cc无语]" src="/images/emotions/1169.gif"/>',
			"[cc郁闷]" => '<img type="face" title="[cc郁闷]" src="/images/emotions/1170.gif"/>',
			"[cc祈祷]" => '<img type="face" title="[cc祈祷]" src="/images/emotions/1171.gif"/>',
			"[cc思考]" => '<img type="face" title="[cc思考]" src="/images/emotions/1172.gif"/>',
			"[cc惊讶]" => '<img type="face" title="[cc惊讶]" src="/images/emotions/1173.gif"/>',
			"[cc得瑟]" => '<img type="face" title="[cc得瑟]" src="/images/emotions/1174.gif"/>',
			"[cc不嘛]" => '<img type="face" title="[cc不嘛]" src="/images/emotions/1175.gif"/>',
			"[cc生气]" => '<img type="face" title="[cc生气]" src="/images/emotions/1176.gif"/>',
			"[cc乞讨]" => '<img type="face" title="[cc乞讨]" src="/images/emotions/1177.gif"/>',
			"[cc呼啦]" => '<img type="face" title="[cc呼啦]" src="/images/emotions/1178.gif"/>',
			"[cc偷乐]" => '<img type="face" title="[cc偷乐]" src="/images/emotions/1179.gif"/>',
			"[cc无奈]" => '<img type="face" title="[cc无奈]" src="/images/emotions/1180.gif"/>',
			"[cc蒙面]" => '<img type="face" title="[cc蒙面]" src="/images/emotions/1181.gif"/>',
			"[cc色色]" => '<img type="face" title="[cc色色]" src="/images/emotions/1182.gif"/>',
			"[cc哈哈]" => '<img type="face" title="[cc哈哈]" src="/images/emotions/1183.gif"/>',
			"[nono卖帅]" => '<img type="face" title="[nono卖帅]" src="/images/emotions/1184.gif"/>',
			"[nono摇手指]" => '<img type="face" title="[nono摇手指]" src="/images/emotions/1185.gif"/>',
			"[nono来呀来呀]" => '<img type="face" title="[nono来呀来呀]" src="/images/emotions/1186.gif"/>',
			"[nono哭]" => '<img type="face" title="[nono哭]" src="/images/emotions/1187.gif"/>',
			"[nono挑逗]" => '<img type="face" title="[nono挑逗]" src="/images/emotions/1188.gif"/>',
			"[nono娇羞]" => '<img type="face" title="[nono娇羞]" src="/images/emotions/1189.gif"/>',
			"[nono生病]" => '<img type="face" title="[nono生病]" src="/images/emotions/1190.gif"/>',
			"[nono开心]" => '<img type="face" title="[nono开心]" src="/images/emotions/1191.gif"/>',
			"[nono看不见我]" => '<img type="face" title="[nono看不见我]" src="/images/emotions/1192.gif"/>',
			"[nono眨眼]" => '<img type="face" title="[nono眨眼]" src="/images/emotions/1193.gif"/>',
			"[nono大礼包]" => '<img type="face" title="[nono大礼包]" src="/images/emotions/1194.gif"/>',
			"[nono水汪汪]" => '<img type="face" title="[nono水汪汪]" src="/images/emotions/1195.gif"/>',
			"[nonokiss]" => '<img type="face" title="[nonokiss]" src="/images/emotions/1196.gif"/>',
			"[nono圣诞节]" => '<img type="face" title="[nono圣诞节]" src="/images/emotions/1197.gif"/>',
			"[nono跳舞]" => '<img type="face" title="[nono跳舞]" src="/images/emotions/1198.gif"/>',
			"[nono害羞]" => '<img type="face" title="[nono害羞]" src="/images/emotions/1199.gif"/>',
			"[nono无语]" => '<img type="face" title="[nono无语]" src="/images/emotions/1200.gif"/>',
			"[nono放屁]" => '<img type="face" title="[nono放屁]" src="/images/emotions/1201.gif"/>',
			"[nono晕]" => '<img type="face" title="[nono晕]" src="/images/emotions/1202.gif"/>',
			"[nono悠哉跑]" => '<img type="face" title="[nono悠哉跑]" src="/images/emotions/1203.gif"/>',
			"[nono打哈欠]" => '<img type="face" title="[nono打哈欠]" src="/images/emotions/1204.gif"/>',
			"[nono扭]" => '<img type="face" title="[nono扭]" src="/images/emotions/1205.gif"/>',
			"[nonomua]" => '<img type="face" title="[nonomua]" src="/images/emotions/1206.gif"/>',
			"[nono尴尬]" => '<img type="face" title="[nono尴尬]" src="/images/emotions/1207.gif"/>',
			"[nono跑步]" => '<img type="face" title="[nono跑步]" src="/images/emotions/1208.gif"/>',
			"[nono转圈圈]" => '<img type="face" title="[nono转圈圈]" src="/images/emotions/1209.gif"/>',
			"[nono心心眼]" => '<img type="face" title="[nono心心眼]" src="/images/emotions/1210.gif"/>',
			"[nono睡觉]" => '<img type="face" title="[nono睡觉]" src="/images/emotions/1211.gif"/>',
			"[nono星星眼]" => '<img type="face" title="[nono星星眼]" src="/images/emotions/1212.gif"/>',
			"[nono抛小球]" => '<img type="face" title="[nono抛小球]" src="/images/emotions/1213.gif"/>',
			"[dino求人]" => '<img type="face" title="[dino求人]" src="/images/emotions/1214.gif"/>',
			"[dino泪奔]" => '<img type="face" title="[dino泪奔]" src="/images/emotions/1215.gif"/>',
			"[dino害羞]" => '<img type="face" title="[dino害羞]" src="/images/emotions/1216.gif"/>',
			"[dino等人]" => '<img type="face" title="[dino等人]" src="/images/emotions/1217.gif"/>',
			"[dino囧]" => '<img type="face" title="[dino囧]" src="/images/emotions/1218.gif"/>',
			"[dino抠鼻]" => '<img type="face" title="[dino抠鼻]" src="/images/emotions/1219.gif"/>',
			"[dino心碎]" => '<img type="face" title="[dino心碎]" src="/images/emotions/1220.gif"/>',
			"[dino撒花]" => '<img type="face" title="[dino撒花]" src="/images/emotions/1221.gif"/>',
			"[dino电筒]" => '<img type="face" title="[dino电筒]" src="/images/emotions/1222.gif"/>',
			"[dino热]" => '<img type="face" title="[dino热]" src="/images/emotions/1223.gif"/>',
			"[dino坏笑]" => '<img type="face" title="[dino坏笑]" src="/images/emotions/1224.gif"/>',
			"[dino礼物]" => '<img type="face" title="[dino礼物]" src="/images/emotions/1225.gif"/>',
			"[dino晕倒]" => '<img type="face" title="[dino晕倒]" src="/images/emotions/1226.gif"/>',
			"[dino诡异]" => '<img type="face" title="[dino诡异]" src="/images/emotions/1227.gif"/>',
			"[dino瞌睡]" => '<img type="face" title="[dino瞌睡]" src="/images/emotions/1228.gif"/>',
			"[dino安慰]" => '<img type="face" title="[dino安慰]" src="/images/emotions/1229.gif"/>',
			"[dino再见]" => '<img type="face" title="[dino再见]" src="/images/emotions/1230.gif"/>',
			"[dino甜筒]" => '<img type="face" title="[dino甜筒]" src="/images/emotions/1231.gif"/>',
			"[dino不屑]" => '<img type="face" title="[dino不屑]" src="/images/emotions/1232.gif"/>',
			"[dino早安]" => '<img type="face" title="[dino早安]" src="/images/emotions/1233.gif"/>',
			"[dino高兴]" => '<img type="face" title="[dino高兴]" src="/images/emotions/1234.gif"/>',
			"[dino投降]" => '<img type="face" title="[dino投降]" src="/images/emotions/1235.gif"/>',
			"[dino鬼脸]" => '<img type="face" title="[dino鬼脸]" src="/images/emotions/1236.gif"/>',
			"[dino吃饭]" => '<img type="face" title="[dino吃饭]" src="/images/emotions/1237.gif"/>',
			"[dino失望]" => '<img type="face" title="[dino失望]" src="/images/emotions/1238.gif"/>',
			"[dino数钱]" => '<img type="face" title="[dino数钱]" src="/images/emotions/1239.gif"/>',
			"[dino打你]" => '<img type="face" title="[dino打你]" src="/images/emotions/1240.gif"/>',
			"[dino狂叫]" => '<img type="face" title="[dino狂叫]" src="/images/emotions/1241.gif"/>',
			"[dino吐血]" => '<img type="face" title="[dino吐血]" src="/images/emotions/1242.gif"/>',
			"[dino委屈]" => '<img type="face" title="[dino委屈]" src="/images/emotions/1243.gif"/>',
			"[dino划圈]" => '<img type="face" title="[dino划圈]" src="/images/emotions/1244.gif"/>',
			"[dino发怒]" => '<img type="face" title="[dino发怒]" src="/images/emotions/1245.gif"/>',
			"[dino吃惊]" => '<img type="face" title="[dino吃惊]" src="/images/emotions/1246.gif"/>',
			"[dino喝酒]" => '<img type="face" title="[dino喝酒]" src="/images/emotions/1247.gif"/>',
			"[dino咬手帕]" => '<img type="face" title="[dino咬手帕]" src="/images/emotions/1248.gif"/>',
			"[dino臭美]" => '<img type="face" title="[dino臭美]" src="/images/emotions/1249.gif"/>',
			"[dino困惑]" => '<img type="face" title="[dino困惑]" src="/images/emotions/1250.gif"/>',
			"[dino许愿]" => '<img type="face" title="[dino许愿]" src="/images/emotions/1251.gif"/>',
			"[dino打滚]" => '<img type="face" title="[dino打滚]" src="/images/emotions/1252.gif"/>',
			"[yz我倒]" => '<img type="face" title="[yz我倒]" src="/images/emotions/1253.gif"/>',
			"[yz撞玻璃]" => '<img type="face" title="[yz撞玻璃]" src="/images/emotions/1254.gif"/>',
			"[yz淋浴]" => '<img type="face" title="[yz淋浴]" src="/images/emotions/1255.gif"/>',
			"[yz纳尼]" => '<img type="face" title="[yz纳尼]" src="/images/emotions/1256.gif"/>',
			"[yz欢呼]" => '<img type="face" title="[yz欢呼]" src="/images/emotions/1257.gif"/>',
			"[yz拍桌子]" => '<img type="face" title="[yz拍桌子]" src="/images/emotions/1258.gif"/>',
			"[yz光棍]" => '<img type="face" title="[yz光棍]" src="/images/emotions/1259.gif"/>',
			"[yz哇哇叫]" => '<img type="face" title="[yz哇哇叫]" src="/images/emotions/1260.gif"/>',
			"[yz求你了]" => '<img type="face" title="[yz求你了]" src="/images/emotions/1261.gif"/>',
			"[yz翻滚]" => '<img type="face" title="[yz翻滚]" src="/images/emotions/1262.gif"/>',
			"[yz偷着笑]" => '<img type="face" title="[yz偷着笑]" src="/images/emotions/1263.gif"/>',
			"[yzye]" => '<img type="face" title="[yzye]" src="/images/emotions/1264.gif"/>',
			"[yz投降]" => '<img type="face" title="[yz投降]" src="/images/emotions/1265.gif"/>',
			"[yz抽风]" => '<img type="face" title="[yz抽风]" src="/images/emotions/1266.gif"/>',
			"[yzoye]" => '<img type="face" title="[yzoye]" src="/images/emotions/1267.gif"/>',
			"[yz撒花]" => '<img type="face" title="[yz撒花]" src="/images/emotions/1268.gif"/>',
			"[yz抱枕头]" => '<img type="face" title="[yz抱枕头]" src="/images/emotions/1269.gif"/>',
			"[yz甩手绢]" => '<img type="face" title="[yz甩手绢]" src="/images/emotions/1270.gif"/>',
			"[yz右边亮了]" => '<img type="face" title="[yz右边亮了]" src="/images/emotions/1271.gif"/>',
			"[yz人呢]" => '<img type="face" title="[yz人呢]" src="/images/emotions/1272.gif"/>',
			"[yz傻兮兮]" => '<img type="face" title="[yz傻兮兮]" src="/images/emotions/1273.gif"/>',
			"[yz砸]" => '<img type="face" title="[yz砸]" src="/images/emotions/1274.gif"/>',
			"[yz招财猫]" => '<img type="face" title="[yz招财猫]" src="/images/emotions/1275.gif"/>',
			"[yz扇扇子]" => '<img type="face" title="[yz扇扇子]" src="/images/emotions/1276.gif"/>',
			"[yz不呢]" => '<img type="face" title="[yz不呢]" src="/images/emotions/1277.gif"/>',
			"[yz拍屁股]" => '<img type="face" title="[yz拍屁股]" src="/images/emotions/1278.gif"/>',
			"[yz委屈哭]" => '<img type="face" title="[yz委屈哭]" src="/images/emotions/1279.gif"/>',
			"[yz听歌]" => '<img type="face" title="[yz听歌]" src="/images/emotions/1280.gif"/>',
			"[yz吃瓜]" => '<img type="face" title="[yz吃瓜]" src="/images/emotions/1281.gif"/>',
			"[yz好哇]" => '<img type="face" title="[yz好哇]" src="/images/emotions/1282.gif"/>',
			"[yz来看看]" => '<img type="face" title="[yz来看看]" src="/images/emotions/1283.gif"/>',
			"[yz焦糖舞]" => '<img type="face" title="[yz焦糖舞]" src="/images/emotions/1284.gif"/>',
			"[yz放屁]" => '<img type="face" title="[yz放屁]" src="/images/emotions/1285.gif"/>',
			"[yz吃苹果]" => '<img type="face" title="[yz吃苹果]" src="/images/emotions/1286.gif"/>',
			"[yz太好了]" => '<img type="face" title="[yz太好了]" src="/images/emotions/1287.gif"/>',
			"[yz好紧张]" => '<img type="face" title="[yz好紧张]" src="/images/emotions/1288.gif"/>',
			"[ali做鬼脸]" => '<img type="face" title="[ali做鬼脸]" src="/images/emotions/1289.gif"/>',
			"[ali追]" => '<img type="face" title="[ali追]" src="/images/emotions/1290.gif"/>',
			"[ali转圈哭]" => '<img type="face" title="[ali转圈哭]" src="/images/emotions/1291.gif"/>',
			"[ali转]" => '<img type="face" title="[ali转]" src="/images/emotions/1292.gif"/>',
			"[ali郁闷]" => '<img type="face" title="[ali郁闷]" src="/images/emotions/1293.gif"/>',
			"[ali元宝]" => '<img type="face" title="[ali元宝]" src="/images/emotions/1294.gif"/>',
			"[ali摇晃]" => '<img type="face" title="[ali摇晃]" src="/images/emotions/1295.gif"/>',
			"[ali嘘嘘嘘]" => '<img type="face" title="[ali嘘嘘嘘]" src="/images/emotions/1296.gif"/>',
			"[ali羞]" => '<img type="face" title="[ali羞]" src="/images/emotions/1297.gif"/>',
			"[ali笑死了]" => '<img type="face" title="[ali笑死了]" src="/images/emotions/1298.gif"/>',
			"[ali笑]" => '<img type="face" title="[ali笑]" src="/images/emotions/1299.gif"/>',
			"[ali掀桌子]" => '<img type="face" title="[ali掀桌子]" src="/images/emotions/1300.gif"/>',
			"[ali献花]" => '<img type="face" title="[ali献花]" src="/images/emotions/1301.gif"/>',
			"[ali想]" => '<img type="face" title="[ali想]" src="/images/emotions/1302.gif"/>',
			"[ali吓]" => '<img type="face" title="[ali吓]" src="/images/emotions/1303.gif"/>',
			"[ali哇]" => '<img type="face" title="[ali哇]" src="/images/emotions/1304.gif"/>',
			"[ali吐血]" => '<img type="face" title="[ali吐血]" src="/images/emotions/1305.gif"/>',
			"[ali偷看]" => '<img type="face" title="[ali偷看]" src="/images/emotions/1306.gif"/>',
			"[ali送礼物]" => '<img type="face" title="[ali送礼物]" src="/images/emotions/1307.gif"/>',
			"[ali睡]" => '<img type="face" title="[ali睡]" src="/images/emotions/1308.gif"/>',
			"[ali甩手]" => '<img type="face" title="[ali甩手]" src="/images/emotions/1309.gif"/>',
			"[ali摔]" => '<img type="face" title="[ali摔]" src="/images/emotions/1310.gif"/>',
			"[ali撒钱]" => '<img type="face" title="[ali撒钱]" src="/images/emotions/1311.gif"/>',
			"[ali亲一个]" => '<img type="face" title="[ali亲一个]" src="/images/emotions/1312.gif"/>',
			"[ali欠揍]" => '<img type="face" title="[ali欠揍]" src="/images/emotions/1313.gif"/>',
			"[ali扑]" => '<img type="face" title="[ali扑]" src="/images/emotions/1315.gif"/>',
			"[ali飘过]" => '<img type="face" title="[ali飘过]" src="/images/emotions/1316.gif"/>',
			"[ali飘]" => '<img type="face" title="[ali飘]" src="/images/emotions/1317.gif"/>',
			"[ali喷嚏]" => '<img type="face" title="[ali喷嚏]" src="/images/emotions/1318.gif"/>',
			"[ali拍拍手]" => '<img type="face" title="[ali拍拍手]" src="/images/emotions/1319.gif"/>',
			"[ali你]" => '<img type="face" title="[ali你]" src="/images/emotions/1320.gif"/>',
			"[ali挠墙]" => '<img type="face" title="[ali挠墙]" src="/images/emotions/1321.gif"/>',
			"[ali摸摸头]" => '<img type="face" title="[ali摸摸头]" src="/images/emotions/1322.gif"/>',
			"[ali溜]" => '<img type="face" title="[ali溜]" src="/images/emotions/1323.gif"/>',
			"[ali赖皮]" => '<img type="face" title="[ali赖皮]" src="/images/emotions/1324.gif"/>',
			"[ali来吧]" => '<img type="face" title="[ali来吧]" src="/images/emotions/1325.gif"/>',
			"[ali揪]" => '<img type="face" title="[ali揪]" src="/images/emotions/1326.gif"/>',
			"[ali囧]" => '<img type="face" title="[ali囧]" src="/images/emotions/1327.gif"/>',
			"[ali惊]" => '<img type="face" title="[ali惊]" src="/images/emotions/1328.gif"/>',
			"[ali加油]" => '<img type="face" title="[ali加油]" src="/images/emotions/1329.gif"/>',
			"[ali僵尸跳]" => '<img type="face" title="[ali僵尸跳]" src="/images/emotions/1330.gif"/>',
			"[ali呼拉圈]" => '<img type="face" title="[ali呼拉圈]" src="/images/emotions/1331.gif"/>',
			"[ali画圈圈]" => '<img type="face" title="[ali画圈圈]" src="/images/emotions/1332.gif"/>',
			"[ali欢呼]" => '<img type="face" title="[ali欢呼]" src="/images/emotions/1333.gif"/>',
			"[ali坏笑]" => '<img type="face" title="[ali坏笑]" src="/images/emotions/1334.gif"/>',
			"[ali跪求]" => '<img type="face" title="[ali跪求]" src="/images/emotions/1335.gif"/>',
			"[ali风筝]" => '<img type="face" title="[ali风筝]" src="/images/emotions/1336.gif"/>',
			"[ali飞]" => '<img type="face" title="[ali飞]" src="/images/emotions/1337.gif"/>',
			"[ali翻白眼]" => '<img type="face" title="[ali翻白眼]" src="/images/emotions/1338.gif"/>',
			"[ali顶起]" => '<img type="face" title="[ali顶起]" src="/images/emotions/1339.gif"/>',
			"[ali点头]" => '<img type="face" title="[ali点头]" src="/images/emotions/1340.gif"/>',
			"[ali得瑟]" => '<img type="face" title="[ali得瑟]" src="/images/emotions/1341.gif"/>',
			"[ali打篮球]" => '<img type="face" title="[ali打篮球]" src="/images/emotions/1342.gif"/>',
			"[ali打滚]" => '<img type="face" title="[ali打滚]" src="/images/emotions/1343.gif"/>',
			"[ali大吃]" => '<img type="face" title="[ali大吃]" src="/images/emotions/1344.gif"/>',
			"[ali踩]" => '<img type="face" title="[ali踩]" src="/images/emotions/1345.gif"/>',
			"[ali不耐烦]" => '<img type="face" title="[ali不耐烦]" src="/images/emotions/1346.gif"/>',
			"[ali不嘛]" => '<img type="face" title="[ali不嘛]" src="/images/emotions/1347.gif"/>',
			"[ali别吵]" => '<img type="face" title="[ali别吵]" src="/images/emotions/1348.gif"/>',
			"[ali鞭炮]" => '<img type="face" title="[ali鞭炮]" src="/images/emotions/1349.gif"/>',
			"[ali抱一抱]" => '<img type="face" title="[ali抱一抱]" src="/images/emotions/1350.gif"/>',
			"[ali拜年]" => '<img type="face" title="[ali拜年]" src="/images/emotions/1351.gif"/>',
			"[ali88]" => '<img type="face" title="[ali88]" src="/images/emotions/1352.gif"/>',
			"[狂笑]" => '<img type="face" title="[狂笑]" src="/images/emotions/1353.gif"/>',
			"[冤]" => '<img type="face" title="[冤]" src="/images/emotions/1354.gif"/>',
			"[蜷]" => '<img type="face" title="[蜷]" src="/images/emotions/1355.gif"/>',
			"[美好]" => '<img type="face" title="[美好]" src="/images/emotions/1356.gif"/>',
			"[乐和]" => '<img type="face" title="[乐和]" src="/images/emotions/1357.gif"/>',
			"[揪耳朵]" => '<img type="face" title="[揪耳朵]" src="/images/emotions/1358.gif"/>',
			"[晃]" => '<img type="face" title="[晃]" src="/images/emotions/1359.gif"/>',
			"[high]" => '<img type="face" title="[high]" src="/images/emotions/1360.gif"/>',
			"[蹭]" => '<img type="face" title="[蹭]" src="/images/emotions/1361.gif"/>',
			"[抱枕]" => '<img type="face" title="[抱枕]" src="/images/emotions/1362.gif"/>',
			"[不公平]" => '<img type="face" title="[不公平]" src="/images/emotions/1363.gif"/>',
			"[lm招财猫]" => '<img type="face" title="[lm招财猫]" src="/images/emotions/1364.gif"/>',
			"[lm贼笑]" => '<img type="face" title="[lm贼笑]" src="/images/emotions/1365.gif"/>',
			"[lm严肃]" => '<img type="face" title="[lm严肃]" src="/images/emotions/1366.gif"/>',
			"[lm小地主]" => '<img type="face" title="[lm小地主]" src="/images/emotions/1367.gif"/>',
			"[lm无奈]" => '<img type="face" title="[lm无奈]" src="/images/emotions/1368.gif"/>',
			"[lm挖鼻屎]" => '<img type="face" title="[lm挖鼻屎]" src="/images/emotions/1369.gif"/>',
			"[lm天然呆]" => '<img type="face" title="[lm天然呆]" src="/images/emotions/1370.gif"/>',
			"[lm生病了]" => '<img type="face" title="[lm生病了]" src="/images/emotions/1371.gif"/>',
			"[lm扑克脸]" => '<img type="face" title="[lm扑克脸]" src="/images/emotions/1372.gif"/>',
			"[lm瀑布汗]" => '<img type="face" title="[lm瀑布汗]" src="/images/emotions/1373.gif"/>',
			"[lm磨牙]" => '<img type="face" title="[lm磨牙]" src="/images/emotions/1374.gif"/>',
			"[lm没听见]" => '<img type="face" title="[lm没听见]" src="/images/emotions/1375.gif"/>',
			"[lm没事吧]" => '<img type="face" title="[lm没事吧]" src="/images/emotions/1376.gif"/>',
			"[lm茫然]" => '<img type="face" title="[lm茫然]" src="/images/emotions/1377.gif"/>',
			"[lm泪流满面]" => '<img type="face" title="[lm泪流满面]" src="/images/emotions/1378.gif"/>',
			"[lm囧汗]" => '<img type="face" title="[lm囧汗]" src="/images/emotions/1379.gif"/>',
			"[lm惊恐]" => '<img type="face" title="[lm惊恐]" src="/images/emotions/1380.gif"/>',
			"[lm惊呆]" => '<img type="face" title="[lm惊呆]" src="/images/emotions/1381.gif"/>',
			"[lm警察]" => '<img type="face" title="[lm警察]" src="/images/emotions/1382.gif"/>',
			"[lm混乱中]" => '<img type="face" title="[lm混乱中]" src="/images/emotions/1383.gif"/>',
			"[lm花痴]" => '<img type="face" title="[lm花痴]" src="/images/emotions/1384.gif"/>',
			"[lm喝水]" => '<img type="face" title="[lm喝水]" src="/images/emotions/1385.gif"/>',
			"[lm嘿嘿]" => '<img type="face" title="[lm嘿嘿]" src="/images/emotions/1386.gif"/>',
			"[lm哈哈哈]" => '<img type="face" title="[lm哈哈哈]" src="/images/emotions/1387.gif"/>',
			"[lm干笑]" => '<img type="face" title="[lm干笑]" src="/images/emotions/1388.gif"/>',
			"[lm疯了]" => '<img type="face" title="[lm疯了]" src="/images/emotions/1389.gif"/>',
			"[lm恶心]" => '<img type="face" title="[lm恶心]" src="/images/emotions/1390.gif"/>',
			"[lm嘟嘟嘴]" => '<img type="face" title="[lm嘟嘟嘴]" src="/images/emotions/1391.gif"/>',
			"[lm滴蜡]" => '<img type="face" title="[lm滴蜡]" src="/images/emotions/1392.gif"/>',
			"[lm点头]" => '<img type="face" title="[lm点头]" src="/images/emotions/1393.gif"/>',
			"[lm大怒]" => '<img type="face" title="[lm大怒]" src="/images/emotions/1394.gif"/>',
			"[lm大惊失色]" => '<img type="face" title="[lm大惊失色]" src="/images/emotions/1395.gif"/>',
			"[lm呆笑]" => '<img type="face" title="[lm呆笑]" src="/images/emotions/1396.gif"/>',
			"[lm搭错线]" => '<img type="face" title="[lm搭错线]" src="/images/emotions/1397.gif"/>',
			"[lm大便]" => '<img type="face" title="[lm大便]" src="/images/emotions/1398.gif"/>',
			"[lm不]" => '<img type="face" title="[lm不]" src="/images/emotions/1399.gif"/>',
			"[lm鼻涕虫]" => '<img type="face" title="[lm鼻涕虫]" src="/images/emotions/1400.gif"/>',
			"[lm暴雨汗]" => '<img type="face" title="[lm暴雨汗]" src="/images/emotions/1401.gif"/>',
			"[lm啊呜啊呜]" => '<img type="face" title="[lm啊呜啊呜]" src="/images/emotions/1402.gif"/>',
			"[lm爱爱爱]" => '<img type="face" title="[lm爱爱爱]" src="/images/emotions/1403.gif"/>',
			"[mk拜年]" => '<img type="face" title="[mk拜年]" src="/images/emotions/1611.gif"/>',
			"[真淡定]" => '<img type="face" title="[真淡定]" src="/images/emotions/1405.gif"/>',
			"[运气中]" => '<img type="face" title="[运气中]" src="/images/emotions/1406.gif"/>',
			"[嗯]" => '<img type="face" title="[嗯]" src="/images/emotions/1407.gif"/>',
			"[一头竖线]" => '<img type="face" title="[一头竖线]" src="/images/emotions/1408.gif"/>',
			"[星星眼儿]" => '<img type="face" title="[星星眼儿]" src="/images/emotions/1409.gif"/>',
			"[笑眯眯]" => '<img type="face" title="[笑眯眯]" src="/images/emotions/1410.gif"/>',
			"[小地主]" => '<img type="face" title="[小地主]" src="/images/emotions/1411.gif"/>',
			"[我错了]" => '<img type="face" title="[我错了]" src="/images/emotions/1412.gif"/>',
			"[喂]" => '<img type="face" title="[喂]" src="/images/emotions/1413.gif"/>',
			"[伸舌头]" => '<img type="face" title="[伸舌头]" src="/images/emotions/1414.gif"/>',
			"[天然呆]" => '<img type="face" title="[天然呆]" src="/images/emotions/1415.gif"/>',
			"[陶醉了]" => '<img type="face" title="[陶醉了]" src="/images/emotions/1416.gif"/>',
			"[生气了]" => '<img type="face" title="[生气了]" src="/images/emotions/1417.gif"/>',
			"[生病鸟]" => '<img type="face" title="[生病鸟]" src="/images/emotions/1418.gif"/>',
			"[忍不了]" => '<img type="face" title="[忍不了]" src="/images/emotions/1419.gif"/>',
			"[扑克脸]" => '<img type="face" title="[扑克脸]" src="/images/emotions/1420.gif"/>',
			"[瀑布汗]" => '<img type="face" title="[瀑布汗]" src="/images/emotions/1421.gif"/>',
			"[你没事吧]" => '<img type="face" title="[你没事吧]" src="/images/emotions/1422.gif"/>',
			"[内牛满面]" => '<img type="face" title="[内牛满面]" src="/images/emotions/1423.gif"/>',
			"[没听见]" => '<img type="face" title="[没听见]" src="/images/emotions/1424.gif"/>',
			"[哭死啦]" => '<img type="face" title="[哭死啦]" src="/images/emotions/1425.gif"/>',
			"[囧汗]" => '<img type="face" title="[囧汗]" src="/images/emotions/1426.gif"/>',
			"[惊恐中]" => '<img type="face" title="[惊恐中]" src="/images/emotions/1427.gif"/>',
			"[混乱中]" => '<img type="face" title="[混乱中]" src="/images/emotions/1428.gif"/>',
			"[花痴闪闪]" => '<img type="face" title="[花痴闪闪]" src="/images/emotions/1429.gif"/>',
			"[嘿嘿嘿]" => '<img type="face" title="[嘿嘿嘿]" src="/images/emotions/1430.gif"/>',
			"[哈哈哈哈]" => '<img type="face" title="[哈哈哈哈]" src="/images/emotions/1431.gif"/>',
			"[干笑中]" => '<img type="face" title="[干笑中]" src="/images/emotions/1432.gif"/>',
			"[恶心死]" => '<img type="face" title="[恶心死]" src="/images/emotions/1433.gif"/>',
			"[嘟嘟嘴]" => '<img type="face" title="[嘟嘟嘴]" src="/images/emotions/1434.gif"/>',
			"[大怒]" => '<img type="face" title="[大怒]" src="/images/emotions/1435.gif"/>',
			"[大惊失色]" => '<img type="face" title="[大惊失色]" src="/images/emotions/1436.gif"/>',
			"[呆呆]" => '<img type="face" title="[呆呆]" src="/images/emotions/1437.gif"/>',
			"[搭错线]" => '<img type="face" title="[搭错线]" src="/images/emotions/1438.gif"/>',
			"[鼻涕虫]" => '<img type="face" title="[鼻涕虫]" src="/images/emotions/1439.gif"/>',
			"[暴雨汗]" => '<img type="face" title="[暴雨汗]" src="/images/emotions/1440.gif"/>',
			"[啊呜啊呜]" => '<img type="face" title="[啊呜啊呜]" src="/images/emotions/1441.gif"/>',
			"[哇]" => '<img type="face" title="[哇]" src="/images/emotions/1442.gif"/>',
			"[爱爱爱]" => '<img type="face" title="[爱爱爱]" src="/images/emotions/1443.gif"/>',
			"[猥琐]" => '<img type="face" title="[猥琐]" src="/images/emotions/1444.gif"/>',
			"[挑眉]" => '<img type="face" title="[挑眉]" src="/images/emotions/1445.gif"/>',
			"[挑逗]" => '<img type="face" title="[挑逗]" src="/images/emotions/1446.gif"/>',
			"[亲耳朵]" => '<img type="face" title="[亲耳朵]" src="/images/emotions/1447.gif"/>',
			"[媚眼]" => '<img type="face" title="[媚眼]" src="/images/emotions/1448.gif"/>',
			"[冒个泡]" => '<img type="face" title="[冒个泡]" src="/images/emotions/1449.gif"/>',
			"[囧耳朵]" => '<img type="face" title="[囧耳朵]" src="/images/emotions/1450.gif"/>',
			"[鬼脸]" => '<img type="face" title="[鬼脸]" src="/images/emotions/1451.gif"/>',
			"[放电]" => '<img type="face" title="[放电]" src="/images/emotions/1452.gif"/>',
			"[悲剧]" => '<img type="face" title="[悲剧]" src="/images/emotions/1453.gif"/>',
			"[抚摸]" => '<img type="face" title="[抚摸]" src="/images/emotions/1454.gif"/>',
			"[大汗]" => '<img type="face" title="[大汗]" src="/images/emotions/1455.gif"/>',
			"[大惊]" => '<img type="face" title="[大惊]" src="/images/emotions/1456.gif"/>',
			"[惊哭]" => '<img type="face" title="[惊哭]" src="/images/emotions/1457.gif"/>',
			"[星星眼]" => '<img type="face" title="[星星眼]" src="/images/emotions/1458.gif"/>',
			"[好困]" => '<img type="face" title="[好困]" src="/images/emotions/1459.gif"/>',
			"[呕吐]" => '<img type="face" title="[呕吐]" src="/images/emotions/1460.gif"/>',
			"[加我一个]" => '<img type="face" title="[加我一个]" src="/images/emotions/1461.gif"/>',
			"[痞痞兔耶]" => '<img type="face" title="[痞痞兔耶]" src="/images/emotions/1462.gif"/>',
			"[mua]" => '<img type="face" title="[mua]" src="/images/emotions/1463.gif"/>',
			"[面抽]" => '<img type="face" title="[面抽]" src="/images/emotions/1464.gif"/>',
			"[大笑]" => '<img type="face" title="[大笑]" src="/images/emotions/1465.gif"/>',
			"[揉]" => '<img type="face" title="[揉]" src="/images/emotions/1466.gif"/>',
			"[痞痞兔囧]" => '<img type="face" title="[痞痞兔囧]" src="/images/emotions/1467.gif"/>',
			"[哈尼兔耶]" => '<img type="face" title="[哈尼兔耶]" src="/images/emotions/1468.gif"/>',
			"[开心]" => '<img type="face" title="[开心]" src="/images/emotions/1469.gif"/>',
			"[咬手帕]" => '<img type="face" title="[咬手帕]" src="/images/emotions/1470.gif"/>',
			"[去]" => '<img type="face" title="[去]" src="/images/emotions/1471.gif"/>',
			"[晕死了]" => '<img type="face" title="[晕死了]" src="/images/emotions/1472.gif"/>',
			"[大哭]" => '<img type="face" title="[大哭]" src="/images/emotions/1473.gif"/>',
			"[扇子遮面]" => '<img type="face" title="[扇子遮面]" src="/images/emotions/1474.gif"/>',
			"[怒气]" => '<img type="face" title="[怒气]" src="/images/emotions/1475.gif"/>',
			"[886]" => '<img type="face" title="[886]" src="/images/emotions/1476.gif"/>',
			"[白羊]" => '<img type="face" title="[白羊]" src="/images/emotions/1477.gif"/>',
			"[射手]" => '<img type="face" title="[射手]" src="/images/emotions/1478.gif"/>',
			"[双鱼]" => '<img type="face" title="[双鱼]" src="/images/emotions/1479.gif"/>',
			"[双子]" => '<img type="face" title="[双子]" src="/images/emotions/1480.gif"/>',
			"[天秤]" => '<img type="face" title="[天秤]" src="/images/emotions/1481.gif"/>',
			"[天蝎]" => '<img type="face" title="[天蝎]" src="/images/emotions/1482.gif"/>',
			"[水瓶]" => '<img type="face" title="[水瓶]" src="/images/emotions/1483.gif"/>',
			"[处女]" => '<img type="face" title="[处女]" src="/images/emotions/1484.gif"/>',
			"[金牛]" => '<img type="face" title="[金牛]" src="/images/emotions/1485.gif"/>',
			"[巨蟹]" => '<img type="face" title="[巨蟹]" src="/images/emotions/1486.gif"/>',
			"[狮子]" => '<img type="face" title="[狮子]" src="/images/emotions/1487.gif"/>',
			"[摩羯]" => '<img type="face" title="[摩羯]" src="/images/emotions/1488.gif"/>',
			"[天蝎座]" => '<img type="face" title="[天蝎座]" src="/images/emotions/1489.gif"/>',
			"[天秤座]" => '<img type="face" title="[天秤座]" src="/images/emotions/1490.gif"/>',
			"[双子座]" => '<img type="face" title="[双子座]" src="/images/emotions/1491.gif"/>',
			"[双鱼座]" => '<img type="face" title="[双鱼座]" src="/images/emotions/1492.gif"/>',
			"[射手座]" => '<img type="face" title="[射手座]" src="/images/emotions/1493.gif"/>',
			"[水瓶座]" => '<img type="face" title="[水瓶座]" src="/images/emotions/1494.gif"/>',
			"[摩羯座]" => '<img type="face" title="[摩羯座]" src="/images/emotions/1495.gif"/>',
			"[狮子座]" => '<img type="face" title="[狮子座]" src="/images/emotions/1496.gif"/>',
			"[巨蟹座]" => '<img type="face" title="[巨蟹座]" src="/images/emotions/1497.gif"/>',
			"[金牛座]" => '<img type="face" title="[金牛座]" src="/images/emotions/1498.gif"/>',
			"[处女座]" => '<img type="face" title="[处女座]" src="/images/emotions/1499.gif"/>',
			"[白羊座]" => '<img type="face" title="[白羊座]" src="/images/emotions/1500.gif"/>',
			"[爱心传递]" => '<img type="face" title="[爱心传递]" src="/images/emotions/1501.gif"/>',
			"[绿丝带]" => '<img type="face" title="[绿丝带]" src="/images/emotions/1502.gif"/>',
			"[粉红丝带]" => '<img type="face" title="[粉红丝带]" src="/images/emotions/1503.gif"/>',
			"[红丝带]" => '<img type="face" title="[红丝带]" src="/images/emotions/1504.gif"/>',
			"[加油]" => '<img type="face" title="[加油]" src="/images/emotions/1505.gif"/>',
			"[金牌]" => '<img type="face" title="[金牌]" src="/images/emotions/1507.gif"/>',
			"[银牌]" => '<img type="face" title="[银牌]" src="/images/emotions/1508.gif"/>',
			"[铜牌]" => '<img type="face" title="[铜牌]" src="/images/emotions/1509.gif"/>',
			"[篮球]" => '<img type="face" title="[篮球]" src="/images/emotions/1514.gif"/>',
			"[黑8]" => '<img type="face" title="[黑8]" src="/images/emotions/1515.gif"/>',
			"[排球]" => '<img type="face" title="[排球]" src="/images/emotions/1516.gif"/>',
			"[游泳]" => '<img type="face" title="[游泳]" src="/images/emotions/1517.gif"/>',
			"[乒乓球]" => '<img type="face" title="[乒乓球]" src="/images/emotions/1518.gif"/>',
			"[投篮]" => '<img type="face" title="[投篮]" src="/images/emotions/1519.gif"/>',
			"[羽毛球]" => '<img type="face" title="[羽毛球]" src="/images/emotions/1520.gif"/>',
			"[射门]" => '<img type="face" title="[射门]" src="/images/emotions/1521.gif"/>',
			"[射箭]" => '<img type="face" title="[射箭]" src="/images/emotions/1522.gif"/>',
			"[举重]" => '<img type="face" title="[举重]" src="/images/emotions/1523.gif"/>',
			"[微微笑]" => '<img type="face" title="[微微笑]" src="/images/emotions/1524.gif"/>',
			"[特委屈]" => '<img type="face" title="[特委屈]" src="/images/emotions/1525.gif"/>',
			"[我吐]" => '<img type="face" title="[我吐]" src="/images/emotions/1526.gif"/>',
			"[很生气]" => '<img type="face" title="[很生气]" src="/images/emotions/1527.gif"/>',
			"[流鼻涕]" => '<img type="face" title="[流鼻涕]" src="/images/emotions/1528.gif"/>',
			"[默默哭泣]" => '<img type="face" title="[默默哭泣]" src="/images/emotions/1529.gif"/>',
			"[小盒汗]" => '<img type="face" title="[小盒汗]" src="/images/emotions/1530.gif"/>',
			"[发呆中]" => '<img type="face" title="[发呆中]" src="/images/emotions/1531.gif"/>',
			"[不理你]" => '<img type="face" title="[不理你]" src="/images/emotions/1532.gif"/>',
			"[强烈鄙视]" => '<img type="face" title="[强烈鄙视]" src="/images/emotions/1533.gif"/>',
			"[烦躁]" => '<img type="face" title="[烦躁]" src="/images/emotions/1534.gif"/>',
			"[呲牙]" => '<img type="face" title="[呲牙]" src="/images/emotions/1535.gif"/>',
			"[有钱]" => '<img type="face" title="[有钱]" src="/images/emotions/1536.gif"/>',
			"[微笑]" => '<img type="face" title="[微笑]" src="/images/emotions/1537.gif"/>',
			"[帅爆]" => '<img type="face" title="[帅爆]" src="/images/emotions/1538.gif"/>',
			"[生气]" => '<img type="face" title="[生气]" src="/images/emotions/1539.gif"/>',
			"[生病了]" => '<img type="face" title="[生病了]" src="/images/emotions/1540.gif"/>',
			"[色眯眯]" => '<img type="face" title="[色眯眯]" src="/images/emotions/1541.gif"/>',
			"[疲劳]" => '<img type="face" title="[疲劳]" src="/images/emotions/1542.gif"/>',
			"[瞄]" => '<img type="face" title="[瞄]" src="/images/emotions/1543.gif"/>',
			"[哭]" => '<img type="face" title="[哭]" src="/images/emotions/1544.gif"/>',
			"[好可怜]" => '<img type="face" title="[好可怜]" src="/images/emotions/1545.gif"/>',
			"[紧张]" => '<img type="face" title="[紧张]" src="/images/emotions/1546.gif"/>',
			"[惊讶]" => '<img type="face" title="[惊讶]" src="/images/emotions/1547.gif"/>',
			"[激动]" => '<img type="face" title="[激动]" src="/images/emotions/1548.gif"/>',
			"[见钱]" => '<img type="face" title="[见钱]" src="/images/emotions/1549.gif"/>',
			"[汗了]" => '<img type="face" title="[汗了]" src="/images/emotions/1550.gif"/>',
			"[奋斗]" => '<img type="face" title="[奋斗]" src="/images/emotions/1551.gif"/>',
			"[小人得志]" => '<img type="face" title="[小人得志]" src="/images/emotions/1552.gif"/>',
			"[哇哈哈]" => '<img type="face" title="[哇哈哈]" src="/images/emotions/1553.gif"/>',
			"[叹气]" => '<img type="face" title="[叹气]" src="/images/emotions/1554.gif"/>',
			"[冻结]" => '<img type="face" title="[冻结]" src="/images/emotions/1555.gif"/>',
			"[切]" => '<img type="face" title="[切]" src="/images/emotions/1556.gif"/>',
			"[拍照]" => '<img type="face" title="[拍照]" src="/images/emotions/1557.gif"/>',
			"[怕怕]" => '<img type="face" title="[怕怕]" src="/images/emotions/1558.gif"/>',
			"[怒吼]" => '<img type="face" title="[怒吼]" src="/images/emotions/1559.gif"/>',
			"[膜拜]" => '<img type="face" title="[膜拜]" src="/images/emotions/1560.gif"/>',
			"[路过]" => '<img type="face" title="[路过]" src="/images/emotions/1561.gif"/>',
			"[泪奔]" => '<img type="face" title="[泪奔]" src="/images/emotions/1562.gif"/>',
			"[脸变色]" => '<img type="face" title="[脸变色]" src="/images/emotions/1563.gif"/>',
			"[亲]" => '<img type="face" title="[亲]" src="/images/emotions/1564.gif"/>',
			"[恐怖]" => '<img type="face" title="[恐怖]" src="/images/emotions/1565.gif"/>',
			"[交给我吧]" => '<img type="face" title="[交给我吧]" src="/images/emotions/1566.gif"/>',
			"[欢欣鼓舞]" => '<img type="face" title="[欢欣鼓舞]" src="/images/emotions/1567.gif"/>',
			"[高兴]" => '<img type="face" title="[高兴]" src="/images/emotions/1568.gif"/>',
			"[尴尬]" => '<img type="face" title="[尴尬]" src="/images/emotions/1569.gif"/>',
			"[发嗲]" => '<img type="face" title="[发嗲]" src="/images/emotions/1570.gif"/>',
			"[犯错]" => '<img type="face" title="[犯错]" src="/images/emotions/1571.gif"/>',
			"[得意]" => '<img type="face" title="[得意]" src="/images/emotions/1572.gif"/>',
			"[吵闹]" => '<img type="face" title="[吵闹]" src="/images/emotions/1573.gif"/>',
			"[冲锋]" => '<img type="face" title="[冲锋]" src="/images/emotions/1574.gif"/>',
			"[抽耳光]" => '<img type="face" title="[抽耳光]" src="/images/emotions/1575.gif"/>',
			"[差得远呢]" => '<img type="face" title="[差得远呢]" src="/images/emotions/1576.gif"/>',
			"[被砸]" => '<img type="face" title="[被砸]" src="/images/emotions/1577.gif"/>',
			"[拜托]" => '<img type="face" title="[拜托]" src="/images/emotions/1578.gif"/>',
			"[必胜]" => '<img type="face" title="[必胜]" src="/images/emotions/1579.gif"/>',
			"[不关我事]" => '<img type="face" title="[不关我事]" src="/images/emotions/1580.gif"/>',
			"[上火]" => '<img type="face" title="[上火]" src="/images/emotions/1581.gif"/>',
			"[不倒翁]" => '<img type="face" title="[不倒翁]" src="/images/emotions/1582.gif"/>',
			"[不错哦]" => '<img type="face" title="[不错哦]" src="/images/emotions/1583.gif"/>',
			"[yeah]" => '<img type="face" title="[yeah]" src="/images/emotions/1584.gif"/>',
			"[喜欢]" => '<img type="face" title="[喜欢]" src="/images/emotions/1585.gif"/>',
			"[心动]" => '<img type="face" title="[心动]" src="/images/emotions/1586.gif"/>',
			"[无聊]" => '<img type="face" title="[无聊]" src="/images/emotions/1587.gif"/>',
			"[手舞足蹈]" => '<img type="face" title="[手舞足蹈]" src="/images/emotions/1588.gif"/>',
			"[搞笑]" => '<img type="face" title="[搞笑]" src="/images/emotions/1589.gif"/>',
			"[痛哭]" => '<img type="face" title="[痛哭]" src="/images/emotions/1590.gif"/>',
			"[爆发]" => '<img type="face" title="[爆发]" src="/images/emotions/1591.gif"/>',
			"[发奋]" => '<img type="face" title="[发奋]" src="/images/emotions/1592.gif"/>',
			"[不屑]" => '<img type="face" title="[不屑]" src="/images/emotions/1593.gif"/>',
			"[cc拜年]" => '<img type="face" title="[cc拜年]" src="/images/emotions/1595.gif"/>',
			"[brd拜年]" => '<img type="face" title="[brd拜年]" src="/images/emotions/1596.gif"/>',
			"[brd谨]" => '<img type="face" title="[brd谨]" src="/images/emotions/1597.gif"/>',
			"[brd贺]" => '<img type="face" title="[brd贺]" src="/images/emotions/1598.gif"/>',
			"[brd新]" => '<img type="face" title="[brd新]" src="/images/emotions/1599.gif"/>',
			"[brd年]" => '<img type="face" title="[brd年]" src="/images/emotions/1600.gif"/>',
			"[yz拜年]" => '<img type="face" title="[yz拜年]" src="/images/emotions/1602.gif"/>',
			"[dx拜年]" => '<img type="face" title="[dx拜年]" src="/images/emotions/1614.gif"/>',
			"[nono拜年]" => '<img type="face" title="[nono拜年]" src="/images/emotions/1609.gif"/>',
			"[mtjj拜年]" => '<img type="face" title="[mtjj拜年]" src="/images/emotions/1610.gif"/>',
			"[km拜年]" => '<img type="face" title="[km拜年]" src="/images/emotions/1612.gif"/>',
			"[alt拜年]" => '<img type="face" title="[alt拜年]" src="/images/emotions/1613.gif"/>',
			"[dx炸弹]" => '<img type="face" title="[dx炸弹]" src="/images/emotions/1615.gif"/>',
			"[dx洗澡]" => '<img type="face" title="[dx洗澡]" src="/images/emotions/1616.gif"/>',
			"[dx握爪]" => '<img type="face" title="[dx握爪]" src="/images/emotions/1617.gif"/>',
			"[dx数落]" => '<img type="face" title="[dx数落]" src="/images/emotions/1618.gif"/>',
			"[dx刷牙]" => '<img type="face" title="[dx刷牙]" src="/images/emotions/1619.gif"/>',
			"[dx傻]" => '<img type="face" title="[dx傻]" src="/images/emotions/1620.gif"/>',
			"[dx晒]" => '<img type="face" title="[dx晒]" src="/images/emotions/1621.gif"/>',
			"[dx抛媚眼]" => '<img type="face" title="[dx抛媚眼]" src="/images/emotions/1622.gif"/>',
			"[dx拍拍手]" => '<img type="face" title="[dx拍拍手]" src="/images/emotions/1623.gif"/>',
			"[dx耶]" => '<img type="face" title="[dx耶]" src="/images/emotions/1624.gif"/>',
			"[dx扭]" => '<img type="face" title="[dx扭]" src="/images/emotions/1625.gif"/>',
			"[dx没有]" => '<img type="face" title="[dx没有]" src="/images/emotions/1626.gif"/>',
			"[dx卖萌]" => '<img type="face" title="[dx卖萌]" src="/images/emotions/1627.gif"/>',
			"[dx脸红]" => '<img type="face" title="[dx脸红]" src="/images/emotions/1628.gif"/>',
			"[dx泪奔]" => '<img type="face" title="[dx泪奔]" src="/images/emotions/1629.gif"/>',
			"[dx加油]" => '<img type="face" title="[dx加油]" src="/images/emotions/1630.gif"/>',
			"[dx脚踏车]" => '<img type="face" title="[dx脚踏车]" src="/images/emotions/1631.gif"/>',
			"[dx花心]" => '<img type="face" title="[dx花心]" src="/images/emotions/1632.gif"/>',
			"[dx欢乐]" => '<img type="face" title="[dx欢乐]" src="/images/emotions/1633.gif"/>',
			"[dx滑板]" => '<img type="face" title="[dx滑板]" src="/images/emotions/1634.gif"/>',
			"[dx倒]" => '<img type="face" title="[dx倒]" src="/images/emotions/1635.gif"/>',
			"[dx超人]" => '<img type="face" title="[dx超人]" src="/images/emotions/1636.gif"/>',
			"[dx饱]" => '<img type="face" title="[dx饱]" src="/images/emotions/1637.gif"/>',
			"[dx哎]" => '<img type="face" title="[dx哎]" src="/images/emotions/1638.gif"/>',
			"[眨眨眼]" => '<img type="face" title="[眨眨眼]" src="/images/emotions/1639.gif"/>',
			"[杂技]" => '<img type="face" title="[杂技]" src="/images/emotions/1640.gif"/>',
			"[多问号]" => '<img type="face" title="[多问号]" src="/images/emotions/1641.gif"/>',
			"[跳绳]" => '<img type="face" title="[跳绳]" src="/images/emotions/1642.gif"/>',
			"[强吻]" => '<img type="face" title="[强吻]" src="/images/emotions/1643.gif"/>',
			"[不活了]" => '<img type="face" title="[不活了]" src="/images/emotions/1644.gif"/>',
			"[磕头]" => '<img type="face" title="[磕头]" src="/images/emotions/1645.gif"/>',
			"[呜呜]" => '<img type="face" title="[呜呜]" src="/images/emotions/1646.gif"/>',
			"[不]" => '<img type="face" title="[不]" src="/images/emotions/1647.gif"/>' 
	);
	return strtr ( $str, $arr );
}

/**
 * 验证提交信息是不是为空
 * 
 * @param array $data
 *        	提交上来的信息
 */
function submitcheck($data) {
	$flog = false;
	if (! empty ( $data )) {
		$flog = true;
	}
	return $flog;
}

/**
 * URL重定向
 * 
 * @param string $url
 *        	跳转地址
 * @param int $time
 *        	时间
 * @param stirng $msg
 *        	提示信息
 */
function redirect($url, $time = 0, $msg = '') {
	// 多行URL地址支持
	echo '这里注意修改要跳转的时的重定向<br/>';

	$url = str_replace ( array (
			"\n",
			"\r" 
	), '', $url );
	if (empty ( $msg ))
		$msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (! headers_sent ()) {
		// redirect
		if (0 === $time) {
		
			header ( 'Location:http://testintelpro.com:8080' . $url );
		} else {
	
			header ( "refresh:{$time};url={$url}" );
			echo ($msg);
		}
		exit ();
	} else {
// 	   $url='/intelonl' . $url;
		$str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0)
			$str .= $msg;
		echo $str;
		exit ( $str );
	}
}

/**
 * 获取请求ip
 *
 * @return ip地址
 */
function ip() {
	if (getenv ( 'HTTP_CLIENT_IP' ) && strcasecmp ( getenv ( 'HTTP_CLIENT_IP' ), 'unknown' )) {
		$ip = getenv ( 'HTTP_CLIENT_IP' );
	} elseif (getenv ( 'HTTP_X_FORWARDED_FOR' ) && strcasecmp ( getenv ( 'HTTP_X_FORWARDED_FOR' ), 'unknown' )) {
		$ip = getenv ( 'HTTP_X_FORWARDED_FOR' );
	} elseif (getenv ( 'REMOTE_ADDR' ) && strcasecmp ( getenv ( 'REMOTE_ADDR' ), 'unknown' )) {
		$ip = getenv ( 'REMOTE_ADDR' );
	} elseif (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], 'unknown' )) {
		$ip = $_SERVER ['REMOTE_ADDR'];
	}
	return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>'http://www.phpcms.cn'));
 * 
 * @param string $msg
 *        	提示信息
 * @param mixed(string/array) $url_forward
 *        	跳转地址
 * @param int $ms
 *        	跳转等待时间 (秒)
 */
function showmessage($msg, $url_forward = 'goback', $ms = 3) {
	$header = <<<EOD
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>提示信息</title>
    <link rel="stylesheet" type="text/css" href="/css/showmessage.css" />
    <script type="text/javaScript" src="/js/common/jquery.js"></script>
    <script language="JavaScript" src="/js/common/showmessage.js"></script>
    </head>
    <body>
    <div class="showMsg" style="text-align:center">
            <h5>提示信息</h5>
        <div class="content guery" style="display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline; max-width:280px">
EOD;
	
	$content = '</div> <div class="bottom">';
	if ($url_forward == 'goback' || $url_forward == '') {
		$content .= '<a href="javascript:history.back();" >[点这里返回上一页]</a>';
	} else if ($url_forward == "close") {
		$content .= '<input type="button" name="close" value=" 关闭 " onClick="window.close();">';
	} else if ($url_forward == "blank") {
	} else if ($url_forward) {
		$content .= '<a href="' . $url_forward . '">如果您的浏览器没有自动跳转，请点击这里</a><script language="javascript">setTimeout("redirect(\'' . $url_forward . '\');",' . $ms * 1000 . ')</script> </div></div>';
	}
	$content .= '</body></html>';
	echo $header . $msg . $content;
	exit ();
}

/**
 * 语言文件处理
 * 以数组的形式保存的语言包 默认是当前模块下的语言包
 * 
 * @param string $language
 *        	数组中的下标
 * @param string $model
 *        	模型名称
 * @return string
 */
function L($language, $model = '') {
	if (empty ( $model )) {
		$model = System::$module;
	}
	$fileName = ROOT_PATH . "common/language/" . $model . ".lang.php";
	require_once ROOT_PATH . "common/language/system.lang.php";
	if (file_exists ( $fileName )) {
		include $fileName;
	}
	return $LANG [$language];
}

/**
 * 合并两个数组
 * 
 * @param array $arrayData1        	
 * @param array $arrayData2        	
 * @param string $arrayData1_key        	
 * @param string $arrayData2_key        	
 * @return array
 */
function merger_array($arrayData1, $arrayData2, $arrayData1_key, $arrayData2_key) {
	$array_data = array ();
	if (is_array ( $arrayData1 ) && is_array ( $arrayData2 )) { // 判断两个是否为数组
		foreach ( $arrayData1 as $key => $val ) {
			if (array_key_exists ( $arrayData1_key, $val )) { // $arrayData1_key 是否在$arrayData1数组中
				foreach ( $arrayData2 as $k => $v ) {
					if (array_key_exists ( $arrayData2_key, $v )) { // $arrayData2_key 是否在$arrayData2数组中
						if ($v [$arrayData2_key] == $val [$arrayData1_key]) {
							/*
							 * 合并两个数组如果输入的数组中有相同的字符串键名，则该键名后面的值将覆盖前一个值。
							 * 然而，如果数组包含数字键名，后面的值将不会覆盖原来的值，而是附加到后面。
							 * 如果只给了一个数组并且该数组是数字索引的，则键名会以连续方式重新索引。
							 */
							$array_data [$key] = array_merge ( $v, $val );
						}
					}
				}
			}
		}
	}
	return $array_data;
}

/**
 * 从缓存中读取行业信息
 * 
 * @return array
 */
function getIndustry() {
	$cacheFile = new CacheFile ();
	$cacheFile->init ( array (
			'dir' => 'cache/system',
			'depth' => 0 
	) );
	$industry = $cacheFile->get ( "industry" );
	if (! $industry) {
		$db = new user ();
		$db->db->tableName = "bz_industry";
		$result = $db->select ( '*' );
		if ($result) {
			$cacheFile->set ( "industry", $result );
		}
		$industry = $cacheFile->get ( "industry" );
	}
	return $industry;
}

/**
 * 得到所有的ID信息
 * 二维数级中有ID时只取ID信息将转换成一维数组
 * 
 * @param type $resultUsertask
 *        	转换的二维数组
 * @return array
 */
function foreachArray($resultUsertask) {
	$array = array ();
	foreach ( $resultUsertask as $val ) {
		$array [] = $val ['id'];
	}
	return $array;
}

/*
 * *
 * 信息拼接展示
 *
 *
 */
function show_list($list) {
	if (empty ( $list )) {
		return '';
	}
	if (is_string ( $list )) {
		return $list;
	}
	$str = '<div class="wbTop">';
	$str .= '<span class="wbDataSource">';
	$source = 0;
	if ($list ['status'] == 6) {
		$str .= '转发我的微薄';
		$source = 1;
	} elseif ($list ['status'] == 7) {
		$str .= '评论我的微薄';
		$source = 5;
	} elseif ($list ['status'] == 8) {
		$str .= '提及我的微博';
		$source = 2;
	} elseif ($list ['status'] == 9) {
		$str .= '回复我的评论';
		$source = 3;
	}
	if ($list ['source_type'] == 2) {
		$source = 4;
	}
	if ($list ['source_type'] == 2 && $list ['flag'] == 2) {
		$str .= '关键人';
	}
	if ($list ['source_type'] == 2 && $list ['flag'] == 3) {
		$str .= '关键字';
	}
	$str .= '</span>';
	$str .= '<span class="timeSpent">累积耗时：';
	// 如果已删除 已完成 以转移时 信息处理时间计时停止
	if ($list ['current_status'] == 4 || $list ['current_status'] == 7 || $list ['current_status'] == 8) {
		$str .= time_Format ( $list ['created_at'], 1, $list ['utime'] );
	} else {
		$str .= time_Format ( $list ['created_at'] );
	}
	$str .= '</span>';
	if ($list ['degree'] == 3) {
		$str .= '<span class="wbUrgentTag rush" title="重要"></span>';
	}
	$str .= '<span class="wbTaskType">';
	if ($list ['current_status'] == 1 && $list ['prev_status'] == 1) {
		$curr_status = "未分配";
	}
	if ($list ['current_status'] == 1 && $list ['prev_status'] == 4) {
		$curr_status = "已恢复";
	}
	if ($list ['current_status'] == 2) {
		$curr_status = "待处理";
	}
	if ($list ['current_status'] == 3) {
		$curr_status = "待审核";
	}
	if ($list ['current_status'] == 4) {
		$curr_status = "已静默";
	}
	if ($list ['current_status'] == 5) {
		$curr_status = "驳回";
	}
	if ($list ['operater_id'] == $list ['assigner_id'] && $list ['assigner_id'] != 10000) {
		if ($list ['current_status'] == 1 || $list ['current_status'] == 2) {
			$curr_status = "退回";
		}
	}
	if ($list ['current_status'] == 6) {
		if ($list ['send_error_num'] >= SEND_ERROR_NUM) {
			$curr_status = "发送失败";
		} else {
			$curr_status = "发送中";
		}
	}
	if ($list ['current_status'] == 7) {
		$curr_status = "已完成";
	}
	if ($list ['current_status'] == 8) {
		$curr_status = "转移";
	}
	$str .= $curr_status;
	$str .= '</span>';
	$str .= '</div>';
	$str .= '<div class="wbInfo">';
	$user = json_decode_zh ( $list ['user'], TRUE );
	$str .= "<p class='userinfo_" . $user ['idstr'] . "' style='display:none'>" . json_encode_zh ( $user ) . "</p>";
	$str .= '<a  target="_blank"  href="http://weibo.com/u/' . $user ['idstr'] . '" class="wbName" id=""><span class="firstName">' . $user ['screen_name'] . '</span>';
	$str .= verified ( intval ( $user ['verified_type'] ) );
	$str .= '</a>';
	$str .= checkCustomerInfo ( $user ['idstr'], $source );
	$str .= '</div>';
	$str .= '<div class="wbContent">';
	$str .= '<p title="' . $list ['text'] . '">';
	$str .= sinaContentRegx ( $list ['text'] );
	// $str .= '<a class="wbLink" href="javascript:void(0);">http://t.cn/zjGvBI5</a> by <a class="wbAt" href="javascript:void(0);">@kimi丁伟峰</a>';
	$str .= '</p>';
	if ($list ['thumbnail_pic']) {
		$str .= '<p class="wbImg">
				<img class="lazy" src="/images/loading.gif" data-original="' . $list ['thumbnail_pic'] . '" alt="" id="" />
            	<img class="full" src="/images/loading.gif" data-original="' . $list ['bmiddle_pic'] . '" alt="" id="" />
			</p>';
	}
	if ($list ['retweeted_status']) { // 转发或评论啥的
		$retweeted = json_decode_zh ( $list ['retweeted_status'], TRUE );
		$str .= '<blockquote>';
		if (! $retweeted ['deleted']) { // 当原微博呗删除时走下边的方法
			$str .= '<div class="wbInfo">';
			$str .= '<a  target="_blank" href="http://weibo.com/u/' . $retweeted ['user'] ['idstr'] . '" class="wbName" id=""><span class="twoName">' . $retweeted ['user'] ['screen_name'] . '</span>';
			$str .= verified ( intval ( $retweeted ['user'] ['verified_type'] ) );
			$str .= '</a>：';
			$str .= '</div>';
			$str .= '<p>' . sinaContentRegx ( $retweeted ['text'] ) . '</p>';
			if ($retweeted ['thumbnail_pic']) {
				$str .= '<p class="wbImg">';
				$str .= '<img class="lazy" src="/images/loading.gif" data-original="' . $retweeted ['thumbnail_pic'] . '" alt="" id="" />';
				$str .= '<img class="full" src="/images/loading.gif" data-original="' . $retweeted ['bmiddle_pic'] . '" alt="" id="" />';
				$str .= '</p>';
			}
			$str .= '<div class="resourceInfo">';
			$str .= '<div class="resourceInfoLeft">';
			$str .= '<a href="http://weibo.com/' . $retweeted ['user'] ['idstr'] . '/' . midToStr ( $retweeted ['mid'] ) . '" target = "_blank">' . date ( 'Y-m-d H:i:s', strtotime ( $retweeted ['created_at'] ) ) . '</a> ';
			// var_dump($retweeted['source']);
			// var_dump(str_replace('rel',' target="_blank" rel',$retweeted['source']));die;
			$str .= '<span>来自</span> ' . str_replace ( 'rel', ' target="_blank" rel', $retweeted ['source'] ) . '</div>';
			$str .= '<div class="resourceInfoRight">';
			$str .= '<a href="http://weibo.com/' . $retweeted ['user'] ['idstr'] . '/' . midToStr ( $retweeted ['mid'] ) . '?type=repost" target = "_blank">转发(' . $retweeted ['reposts_count'] . ')</a>';
			$str .= '<i>|</i>';
			$str .= '<a href="http://weibo.com/' . $retweeted ['user'] ['idstr'] . '/' . midToStr ( $retweeted ['mid'] ) . '" target = "_blank">评论(' . $retweeted ['comments_count'] . ')</a>';
			$str .= '</div>';
			$str .= '</div>';
		} else { // 当原始微博被删掉时走这里
			$str .= '<p>' . sinaContentRegx ( $retweeted ['text'] ) . '</p>';
		}
		$str .= '</blockquote>';
	}
	$str .= '<div class="resourceInfo">';
	$str .= '<div class="resourceInfoLeft">';
	$str .= '<a href="http://weibo.com/' . $user ['idstr'] . '/' . midToStr ( $list ['mid'] ) . '" target="_blank">';
	$str .= date ( 'Y-m-d H:i:s', $list ['created_at'] );
	$str .= '</a>';
	preg_match_all ( '/href="(.*?)"/', $list ['source'], $presult );
	$str .= '<span>来自</span> ' . str_replace ( 'rel', ' target="_blank" rel', $list ['source'] ) . '</div>';
	$str .= '<div class="resourceInfoRight">';
	$str .= '<a href="http://weibo.com/' . $user ['idstr'] . '/' . midToStr ( $list ['mid'] ) . '?type=repost" target = "_blank">转发(' . $list ['reposts_count'] . ')</a>';
	$str .= '<i>|</i>';
	$str .= '<a href="http://weibo.com/' . $user ['idstr'] . '/' . midToStr ( $list ['mid'] ) . '" target="_blank" >评论(' . $list ['comments_count'] . ')</a>';
	$str .= '</div>';
	$str .= '</div>';
	$str .= '</div>';
	echo $str;
}

/**
 * 验证是否存在SCRM中
 * 
 * @param string $weiboUserId
 *        	用户的微博ID
 * @param int $source
 *        	信息来源
 * @return string $str SCRM按钮
 */
function checkCustomerInfo($weiboUserId, $source) {
	load_module_fun ( 'scrm' );
	$sid = Cookie::get ( 'sid' );
	$array = array (
			'weiboUserId' => $weiboUserId,
			'sid' => $sid 
	);
	$param = array (
			'm' => 'steam',
			'a' => 'checkStOperaterPer',
			'sid' => $sid,
			'operater_id' => Cookie::get ( 'id' ),
			'forward' => 1 
	) // 处理权
;
	// 验证是否有相关权限
	$result = checkPermissionButton ( $param );
	if ($result) {
		$str = '<a onclick="disScrm(this,' . $weiboUserId . ',' . $source . ')" id="" class="wbScrmTag noData" href="javascript:void(0);">SCRM</a>';
		// 查询是否在SCRM中
		$data = checkCustomer ( $array );
		if ($data ['error_code'] == 1000) {
			$str = '<a onclick="disScrm(this,' . $weiboUserId . ',' . $source . ')" id="" class="wbScrmTag" href="javascript:void(0);">SCRM</a>';
		}
	} else {
		$str = '';
	}
	return $str;
}
function weiboAvatar($list) {
	$arr = json_decode_zh ( $list, TRUE );
	echo '<a href="http://weibo.com/u/' . $arr ['id'] . '" target = "_blank" ><img src="' . $arr ['profile_image_url'] . '" class="wbFace" title="' . $arr ['screen_name'] . '" alt="" /></a>';
}

// 格式化时间，贾坦2013/1/17添加参数$type=1,兼容本函数之前使用方法和功能
// 添加功能 第三个参数 最后结束时间 如此时间不为空 标示格式化时间时结束时间为此时间
function time_Format($date, $type = 1, $utime = '') {
	$time = $utime ? $utime : time ();
	if ($type == 1) {
		$cle = $time - $date;
	} elseif ($type == 2) {
		$cle = $date - $time;
	}
	$d = intval ( $cle / 86400 );
	$h = intval ( ($cle % 86400) / 3600 );
	$m = intval ( (($cle % 86400) % 3600) / 60 );
	if (! empty ( $d )) {
		$t .= $d . "天";
	}
	if (! empty ( $h ) || ! empty ( $d )) {
		$t .= $h . "时";
	}
	$t .= $m . "分";
	return $t;
}

// 通过mid值转新浪微博的地址
function midToStr($mid) {
	settype ( $mid, 'string' );
	$mid_length = strlen ( $mid );
	$url = '';
	$str = strrev ( $mid );
	$str = str_split ( $str, 7 );
	
	foreach ( $str as $v ) {
		$char = intTo62 ( strrev ( $v ) );
		$char = str_pad ( $char, 4, "0" );
		$url .= $char;
	}
	
	$url_str = strrev ( $url );
	
	return ltrim ( $url_str, '0' );
}

/* url 10 进制 转62进制 */
function intTo62($int10) {
	$s62 = '';
	$r = 0;
	while ( $int10 != 0 ) {
		$r = $int10 % 62;
		$s62 .= str62keys_int_62 ( $r );
		$int10 = floor ( $int10 / 62 );
	}
	
	return $s62;
}

/**
 * 62进制字典
 */
function str62keys_int_62($key) {
	$str62keys = array (
			"0",
			"1",
			"2",
			"3",
			"4",
			"5",
			"6",
			"7",
			"8",
			"9",
			"a",
			"b",
			"c",
			"d",
			"e",
			"f",
			"g",
			"h",
			"i",
			"j",
			"k",
			"l",
			"m",
			"n",
			"o",
			"p",
			"q",
			"r",
			"s",
			"t",
			"u",
			"v",
			"w",
			"x",
			"y",
			"z",
			"A",
			"B",
			"C",
			"D",
			"E",
			"F",
			"G",
			"H",
			"I",
			"J",
			"K",
			"L",
			"M",
			"N",
			"O",
			"P",
			"Q",
			"R",
			"S",
			"T",
			"U",
			"V",
			"W",
			"X",
			"Y",
			"Z" 
	);
	return $str62keys [$key];
}
function sinaContentRegx($resu) {
	$resu = facePath ( $resu );
	
	// $resu = preg_replace( "/ *@([\x{4e00}-\x{9fa5}A-Za-z0-9_]*) ?/u", " <a href=\"http://t.sina.com.cn/n/\\1\" target=\"_blank\">@\\1</a> ", $resu);
	$regex = '/#(.*)#/U';
	if (preg_match_all ( $regex, $resu, $array )) {
		foreach ( $array [1] as $key => $val ) {
			$arr [0] [$key] = '/' . $array [0] [$key] . '/U';
			$arr [1] [$key] = "<a href='http://huati.weibo.com/k/" . $val . "'  target = '_blank'>" . $array [0] [$key] . '</a>';
		}
		$resu = @preg_replace ( @array_unique ( $arr [0] ), @array_unique ( $arr [1] ), $resu );
	}
	$resu = preg_replace ( "/ *@([\x{4e00}-\x{9fa5}A-Za-z0-9_-]*) ?/u", " <a href=\"http://t.sina.com.cn/n/\\1\" target=\"_blank\">@\\1</a> ", $resu );
	$regex = '/http:\/\/(t\.cn\/[a-zA-Z0-9]*?)|(w{3}\.\s*\.\s{3}\/.*)/U';
	if (preg_match_all ( $regex, $resu, $array )) {
		foreach ( $array [0] as $key => $val ) {
			$arr [0] [$key] = '$' . $array [0] [$key] . '$U';
			$arr [1] [$key] = "<a href='" . $array [0] [$key] . "' target = '_blank'>" . $array [0] [$key] . '</a>';
		}
		$resu = @preg_replace ( @array_unique ( $arr [0] ), @array_unique ( $arr [1] ), $resu );
	}
	return $resu;
}

/**
 * utf-8内码转中文,将类似\u123a这样的编码转换为中文
 * 
 * @param string $str
 *        	传入包含json_encode处理的字符串,其中包含中文utf8编码字符(格式为\uabcd之类的编码)
 * @return string 返回经过处理的内容,其中所有的utf8的编码全部变回中文
 */
function jsonToZh($str) {
	return preg_replace ( "#\\\u([0-9a-f]{4})#ie", "mb_convert_encoding(pack('H4', '\\1'), 'UTF-8', 'UCS-2')", $str );
}

/**
 * 数组进行json_encode处理,保持其中的多字节字符不变,并实体化
 * 
 * @param array $arr
 *        	传递进来一个需要json_encode的数组
 * @return string 返回经过json_encode处理之后的字符串,其中中文保持不变
 */
function json_encode_zh($arr) {
	$arr = arrReplace ( $arr );
	return jsonToZh ( json_encode ( $arr ) );
}

/**
 * 对json格式的字符串进行解码,并返回处理结果,保持多字节字符的使用稳定
 * 
 * @param string $str
 *        	需要解码的字符串,该字符串应为json_encode处理的结果,最好是经过函数json_encode_zh函数处理的结果
 * @param boolean $assoc
 *        	解码之后返回什么样的结果,默认值为true,解码为array
 * @return type
 */
function json_decode_zh($str, $assoc = true, $decode = false) {
	// 所有多字节字符经过json_encode之后,经过处理返回相应的原字符,例如还原成中文
	$str = jsonToZh ( $str );
	$str = str_replace ( "\n", ' ', $str );
	$str = str_replace ( "\r", ' ', $str );
	$str = str_replace ( '\\', '\\\\', $str );
	$str = str_replace ( '\/', '/', $str );
	// 对实体进行反转
	if ($decode)
		$str = html_entity_decode ( $str, ENT_QUOTES, 'UTF-8' );
		// 对json格式字符串进行解码
	$arr = json_decode ( $str, $assoc );
	return $arr;
}

/**
 * 预处理需要json_encode的数组,处理之后数组结构不变
 * 其中html转化为实体,双引号改为单引号
 * 
 * @param array $arr
 *        	需要处理的数组
 * @return array 返回处理的结果
 */
function arrReplace($arr) {
	static $num = 0;
	$num ++;
	// 如果传进来的参数是数组,则进行数组的处理,否则直接替换之后返回
	if (is_array ( $arr )) {
		foreach ( $arr as $k => $str ) {
			// 如果循环出来的当前的键依然是数组则调用自己,否则直接处理
			if (is_array ( $arr [$k] )) {
				$arr [$k] = arrReplace ( $arr [$k] );
			} else {
				if ($k == 'source') {
					// 如果是来源字段,直接去除双引号
					$arr [$k] = str_replace ( '"', "", $arr [$k] );
				} else {
					// 普通的字段直接进行转化为HTML实体
					$arr [$k] = htmlentities ( $arr [$k], ENT_QUOTES, "utf-8" );
					$arr [$k] = str_replace ( '\n', ' ', $arr [$k] );
				}
			}
		}
	} else {
		$arr = str_replace ( '"', "", $arr );
	}
	return $arr;
}

/**
 * 获取微博用户的认证类型
 * 
 * @param int $i
 *        	认证的类型的数字
 * @return string 返回对应的中文描述
 */
function dgj_get_vert($i) {
	$vert = array (
			'0' => '名人',
			'1' => '政府',
			'2' => '企业',
			'3' => '媒体',
			'4' => '校园',
			'5' => '网站',
			'6' => '应用',
			'7' => '团体（机构）',
			'8' => '待审企业',
			'10' => '微博女郎',
			'200' => '初级达人',
			'220' => '中高级达人',
			'400' => '已故V用户',
			'-1' => '普通用户' 
	);
	return $vert [$i];
}

/**
 * 远程Curl方式访问module中的方法的函数
 * 
 * @param array $param
 *        	远程Curl方式调用的相关参数数组
 * @return array 返回
 */
function curlRequest($param, $zh = false) {
	/*
	 * 参数param实例:
	 * $param = array(
	 * 'module' => 'test',//指定本地模块名,用来载入本地配置文件,如果不传,则自动获得m的值来使用
	 * 'm' => 'release',//指定远程module名
	 * 'a' => 'commentsReply',//指定需要调用的方法
	 * 'uid' => 'abc',//需要传递的参数,参数可选多个,只需要继续创建数组的键值对即可
	 * 'test' => 123,//另外一个需要传递的参数
	 * );
	 * 配置文件中的API字段指定远程访问的应用的API接口
	 * 'API' => 'http://cloud.buzzopt.com/index.php/api/index/',
	 */
	$param ['module'] = $param ['module'] ? $param ['module'] : $param ['m'];
	// print_r($param);
	$user_config = load_module_config ( $param ['module'] );
	
	$curl = new CurlItems ();
	$api = $user_config ['API'];
	
	// return $param;
	// test($param);
	$data = $curl->post ( $api, json_encode ( $param ) );
	// print_r($data);
	// test($data);
	if ($zh) {
		$data = jsonToZh ( $data );
	}
	if ($data) {
		$result = json_decode ( $data, true );
	}
	return $result;
}

/**
 * 格式化输入内容
 * 此函数主要用来做调试
 * 
 * @param mixed $var
 *        	需要输出的值,可为PHP支持的八种数据类型中的其中任意一种
 * @param bool $is_end
 *        	是否终止脚本的运行,默认值为true,终止脚本执行
 */
function test($var = '测试断点', $is_end = true) {
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
	echo '<pre>';
	var_dump ( $var );
	if ($is_end == true)
		exit ();
}

/**
 * 获取服务器端的IP并转化为整形
 * 此函数针对IPV4有效,IPV6之后需要修改此函数
 *
 * @return int 返回服务器端的IP转化为整形的结果
 */
function serverIpToInt() {
	// 获取服务器端IP地址;
	$ip = $_SERVER ['SERVER_ADDR'];
	// 将IP转化为数组
	$iparr = explode ( '.', $ip );
	// 将IP转化为整形数字
	return $iparr [0] * pow ( 2, 24 ) + $iparr [1] * pow ( 2, 16 ) + $iparr [2] * pow ( 2, 8 ) + $iparr [3];
}

/**
 * 获取客户端(浏览器端)的IP并转化为整形
 * 此函数针对IPV4有效,IPV6之后需要修改此函数
 *
 * @return int 返回服务器端的IP转化为整形的结果
 */
function browserIpToInt() {
	// 获取客户端IP
	$ip = ip ();
	// 将IP转化为数组
	$iparr = explode ( '.', $ip );
	// 将IP转化为整形数字
	return $iparr [0] * pow ( 2, 24 ) + $iparr [1] * pow ( 2, 16 ) + $iparr [2] * pow ( 2, 8 ) + $iparr [3];
}

/**
 * 根据akey产生skey
 * 
 * @param int|string $akey
 *        	数字或者数字组成的字符串,代表授权应用的编号
 * @return string 返回生成的skey
 */
function createSkey($akey) {
	$str = md5 ( $akey );
	$str = md5 ( $str . time () );
	$str = sha1 ( $str . time () );
	return VERSION . $str;
}

/**
 * 根据key把后一个数组附到前一个数据中
 *
 * @param unknown_type $array1        	
 * @param unknown_type $array2        	
 * @param unknown_type $key1        	
 * @param unknown_type $key2        	
 */
function attachArr($array1, $array2, $key1, $key2) {
	if ($array2) {
		foreach ( $array2 as $k => $v ) {
			$array_keys2 [] = $v [$key2];
			$data [$v [$key2]] = $v;
		}
	}
	foreach ( $array1 as $key => $value ) {
		if (empty ( $array_keys2 )) {
			$array_keys2 = array ();
		}
		if (in_array ( $value [$key1], $array_keys2 )) {
			$array1 [$key] ['attach'] = $data [$value [$key1]];
		}
	}
	return $array1;
}

/**
 * 微博等级图标
 *
 * @param int $ver        	
 */
function verified($ver) {
	switch ($ver) {
		case 0 : // 黄V 名人
			return '<i class="wbApprove wbPerson"></i>';
			break;
		case 1 : // 政府
		case 2 : // 企业
		case 3 : // 媒体
		case 4 : // 校园
		case 5 : // 网站
		case 6 : // 应用
		case 7 : // 团体(机构)
			return '<i class="wbApprove wbCo"></i>';
			break;
		case 8 : // 待审企业
		case 10 : // 微博女郎
		case 200 : // 初级达人
		case 220 : // 高级达人
		case 400 : // 已故V用户
			return '';
			break;
		default : // -1普通用户
			return '';
	}
}

/**
 * 将文件名拆分成 名子和后缀
 *
 * @param string $file_name
 *        	@
 */
function get_exname($file_name) {
	if (empty ( $file_name )) {
		return false;
	}
	return pathinfo ( $file_name );
}

/**
 * 请求云API
 * 
 * @param type $param        	
 * @return type
 */
function curlRequestCloud($param) {
	$curl = new CurlItems ();
	$api = 'http://cloud.buzzopt.com/index.php/api/index/';
	$data = $curl->post ( $api, json_encode ( $param ) );
	$result = json_decode ( $data, true );
	return $result;
}

/**
 * 根据错误码提取中文解释
 *
 * @param int $v        	
 * @return str
 */
function weibo_err_code($v) {
	global $WeiboErrorCode;
	return $WeiboErrorCode [$v];
}

/**
 * 二维数组排序
 *
 * @param unknown_type $multArray        	
 * @param unknown_type $sortField        	
 * @param unknown_type $desc        	
 * @return array
 */
function sortByField($multArray, $sortField, $desc = true) {
	$tmpKey = '';
	$ResArray = array ();
	
	$maIndex = array_keys ( $multArray );
	$maSize = count ( $multArray ) - 1;
	
	for($i = 0; $i < $maSize; $i ++) {
		
		$minElement = $i;
		$tempMin = $multArray [$maIndex [$i]] [$sortField];
		$tmpKey = $maIndex [$i];
		
		for($j = $i + 1; $j <= $maSize; $j ++)
			if ($multArray [$maIndex [$j]] [$sortField] < $tempMin) {
				$minElement = $j;
				$tmpKey = $maIndex [$j];
				$tempMin = $multArray [$maIndex [$j]] [$sortField];
			}
		$maIndex [$minElement] = $maIndex [$i];
		$maIndex [$i] = $tmpKey;
	}
	
	if ($desc)
		for($j = 0; $j <= $maSize; $j ++)
			$ResArray [$maIndex [$j]] = $multArray [$maIndex [$j]];
	else
		for($j = $maSize; $j >= 0; $j --)
			$ResArray [$maIndex [$j]] = $multArray [$maIndex [$j]];
	
	return array_values ( $ResArray );
}

/**
 * 获取URL地址的页面头信息
 *
 * @param unknown_type $curl        	
 * @param unknown_type $url        	
 * @param unknown_type $index        	
 * @return unknown
 */
function getUrlPageHeader($curl, $url = '', $index = null, $utf82gbk = false) {
	if ($url == '' || ! is_object ( $curl )) {
		return '';
	}
	$store = $curl->http ( $url, 'get' );
	if ($utf82gbk) {
		$store = iconv ( 'gbk', 'utf-8', $store );
	}
	
	$pageinfo = array ();
	// charset
	if ($pageinfo [charset] == '') {
		preg_match ( '@<meta.+charset=([\w\-]+)[^>]*>@i', $store, $matches );
		$pageinfo [charset] = trim ( $matches [1] );
	}
	
	// desctiption
	preg_match ( '@<meta\s+name=\"*description\"*\s+content\s*=\s*([^/>]+)/*>@i', $store, $matches );
	// print_r($matches);
	$desc = trim ( $matches [1] );
	$pageinfo [description] = str_replace ( "\"", '', $desc );
	
	preg_match ( '@<meta\s+name=\"*keywords\"*\s+content\s*=\s*([^/>]+)/*>@i', $store, $matches );
	// print_r($matches);
	$keywords = trim ( $matches [1] );
	$pageinfo [keywords] = str_replace ( "\"", '', $keywords );
	
	preg_match ( "/<title>(.*)<\/title>/smUi", $store, $matches );
	$pageinfo [title] = trim ( $matches [1] );
	
	if ($index) {
		return $pageinfo [$index];
	}
	return $pageinfo;
}
function getApiResultJsonToArray($url, $format = false) {
	set_time_limit ( 0 );
	$curl = new CurlItems ();
	$json = $curl->http ( $url, 'get' );
	
	$json = trim ( $json );
	
	if ($format) {
		$json = str_replace ( 'data:[', '"data":[', $json );
		$json = gbk2utf8 ( $json );
	}
	return json_decode ( $json, true );
}
function gbk2utf8($data) {
	if (is_array ( $data )) {
		return array_map ( 'gbk2utf8', $data );
	}
	return iconv ( 'gbk', 'utf-8', $data );
}
function iconv2utf8($data) {
	if (is_array ( $data )) {
		return array_map ( 'iconv2utf8', $data );
	}
	
	$type = TestUtf8 ( $data );
	if ($type == 2) {
		return $data;
	}
	return iconv ( 'gbk', 'utf-8', $data );
}

/**
 * test utf8
 * 
 * @param unknown_type $text        	
 * @return boolean|number //返回 1 表示纯 ASCII(即是所有字符都不大于127) 返回 2 表示UTF8 返回 0 表示正常gb编码
 */
function TestUtf8($text) {
	if (strlen ( $text ) < 3)
		return false;
	$lastch = 0;
	$begin = 0;
	$BOM = true;
	$BOMchs = array (
			0xEF,
			0xBB,
			0xBF 
	);
	$good = 0;
	$bad = 0;
	$notAscii = 0;
	for($i = 0; $i < strlen ( $text ); $i ++) {
		$ch = ord ( $text [$i] );
		if ($begin < 3) {
			$BOM = ($BOMchs [$begin] == $ch);
			$begin += 1;
			continue;
		}
		
		if ($begin == 4 && $BOM)
			break;
		
		if ($ch >= 0x80)
			$notAscii ++;
		
		if (($ch & 0xC0) == 0x80) {
			if (($lastch & 0xC0) == 0xC0) {
				$good += 1;
			} else if (($lastch & 0x80) == 0) {
				$bad += 1;
			}
		} else if (($lastch & 0xC0) == 0xC0) {
			$bad += 1;
		}
		$lastch = $ch;
	}
	if ($begin == 4 && $BOM) {
		return 2;
	} else if ($notAscii == 0) {
		return 1;
	} else if ($good >= $bad) {
		return 2;
	} else {
		return 0;
	}
}
function curl_file_get_contents($url, $postFields = null) {
	set_time_limit ( 0 );
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 100 );
	curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:26.0) Gecko/20100101 Firefox/26.0' );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	if ($postFields) {
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
	}
	$r = curl_exec ( $ch );
	curl_close ( $ch );
	return $r;
}

/**
 * 获取人群分类
 * 
 * @return multitype:|mixed
 */
function getClassification() {
	$file = ROOT_PATH . 'cache/classification.txt';
	if (! is_file ( $file )) {
		return array ();
	}
	$data = file_get_contents ( $file );
	return json_decode ( $data, true );
}

/**
 * 创建多级目录
 * 
 * @param unknown_type $dir        	
 * @return boolean
 */
function mkdirs($dir) {
	if (! is_dir ( $dir )) {
		if (! mkdirs ( dirname ( $dir ) )) {
			return false;
		}
		if (! mkdir ( $dir, 0777 )) {
			return false;
		}
	}
	return true;
}

/**
 * 解压缩zip包
 * 
 * @param string $orginal_path        	
 * @param string $savepath        	
 * @return multitype:
 */
function unzip($orginal_path, $savepath) {
	if (! is_dir ( $savepath )) {
		mkdirs ( ROOT_PATH . $savepath, 0777 );
	}
	$result = array ();
	$zip = zip_open ( $orginal_path );
	if ($zip) {
		$seed = 0;
		while ( $zip_entry = zip_read ( $zip ) ) {
			if (zip_entry_open ( $zip, $zip_entry, "r" )) {
				$buf = zip_entry_read ( $zip_entry, zip_entry_filesize ( $zip_entry ) ); // File content
				                                                                   // $filename = $savepath.zip_entry_name($zip_entry);
				$name = zip_entry_name ( $zip_entry );
				
				$filename = $savepath . time () . $seed . substr ( $name, strrpos ( $name, '.' ) );
				$seed ++;
				
				if (zip_entry_filesize ( $zip_entry ) != 0) {
					$fp = fopen ( ROOT_PATH . $filename, 'wb' );
					fwrite ( $fp, $buf );
					fclose ( $fp );
					zip_entry_close ( $zip_entry );
					$name = gbk2utf8 ( $name );
					$result [$name] = $filename;
				} else {
					mkdir ( ROOT_PATH . $filename, 0777 );
				}
			}
		}
		zip_close ( $zip );
		return $result;
	}
}

/**
 * 两个数组之间的排列组合
 * 
 * @param array $A        	
 * @param array $B        	
 * @return multitype:string
 */
function combination($A, $B) {
	$result = array ();
	$fliter = false;
	if (count ( $A ) < count ( $B )) {
		$C = $A;
		$A = $B;
		$B = $C;
		$fliter = true;
	}
	
	if (count ( $A ) == 0) {
		$A = array (
				'' 
		);
	}
	if (count ( $B ) == 0) {
		$B = array (
				'' 
		);
	}
	
	if (is_array ( $A ) && is_array ( $B ) && $A && $B) {
		$seed = 0;
		foreach ( $A as $v ) {
			foreach ( $B as $w ) {
				if ($fliter) {
					$result [$seed] = $w . '_' . $v;
				} else {
					$result [$seed] = $v . '_' . $w;
				}
				
				$seed ++;
			}
		}
	} else if (is_array ( $A ) && $A) {
		return $A;
	} else if (is_array ( $B ) && $A) {
		return $B;
	}
	return $result;
}

/**
 * 显示人群分类的中文名称
 * 
 * @param string $name        	
 * @return string
 */
function showname($name) {
	$file = ROOT_PATH . 'cache/classification_names.txt';
	$cname = array ();
	if (is_file ( $file )) {
		$classes = file_get_contents ( $file );
		$classes = json_decode ( trim ( $classes ), true );
		$tmp = explode ( ',', $name );
		foreach ( $tmp as $c ) {
			if (isset ( $classes [$c] )) {
				$n = $classes [$c];
				
				array_push ( $cname, $n );
			}
		}
	}
	return implode ( ',', $cname );
}

/**
 * 从数组中根据键值取数据
 * 
 * @param unknown_type $resource        	
 * @param unknown_type $key        	
 * @return multitype:
 */
function pickUpArrFromArr($resource, $key) {
	$result = array ();
	if (is_array ( $resource )) {
		foreach ( $resource as $v ) {
			if (isset ( $v [$key] )) {
				array_push ( $result, $v [$key] );
			}
		}
	}
	return $result;
}

/**
 * 密码加密处理方法
 * 根据参数处理密码需要加密的密码并返回处理之后的密码
 * 
 * @param string $password
 *        	需要加密的密码
 * @param int $level
 *        	加密级别,取值范围为1-10
 * @param int $median
 *        	加密之后的位数,只接受32-72位之间的数字
 * @param boolean $complex
 *        	是否采用复合加密,默认启用,为采用md5+sha1加密,否则使用sha1和md5分别加密
 * @return type
 */
function passwordProcess($password, $level = 3, $median = 50, $complex = true) {
	// 限定level参数大校
	$level = $level < 1 ? 1 : $level > 10 ? 10 : $level;
	$median = $median < 32 ? 32 : $median > 72 ? 72 : $median;
	$sha1 = $password;
	$md5 = $password;
	for($i = 0; $i < $level; $i ++) {
		$sha1 = $complex ? sha1 ( md5 ( $sha1 ) ) : sha1 ( $sha1 );
		
		$md5 = $complex ? md5 ( sha1 ( $md5 ) ) : md5 ( $md5 );
	}
	$num = $median / 2;
	$password = $median % 2 ? (substr ( $md5, 0, ceil ( $num ) - 1 - 4 ) . substr ( $sha1, 0, ceil ( $num ) + 4 )) : (substr ( $md5, 0, $num - 4 ) . substr ( $sha1, 0, $num + 4 ));
	return $password;
}

/**
 * 格式化时间
 * 
 * @param unknown $date        	
 * @param number $type
 *        	0 年月日时分秒 非0 年月日
 */
function dateformat($date, $type = 0) {
	$date = intval ( $date );
	if ($type) {
		return date ( 'Y-m-d', $date );
	}
	return date ( 'Y-m-d H:i:s', $date );
}

/**
 * 格式化输出产品概要参数
 * 
 * @param string $summary        	
 * @return string
 */
function showSummary($summary = '', $nums = 100) {
	$data = explode ( '|', $summary );
	$strHtml = '';
	if (is_array ( $data )) {
		foreach ( $data as $k => $v ) {
			if ($k < $nums) {
				$tmp = explode ( ':', $v );
				$strHtml .= '<p class="itemParam"><span class="leftSpan">' . $tmp [0] . '：</span>' . $tmp [1] . '</p>';
			}
		}
	}
	return $strHtml;
}

/**
 * 显示详细参数
 * 
 * @param string $items        	
 * @param string $wap
 *        	false 后台使用 true 前台使用
 * @return string
 */
function showDetailParameter($items = null, $wap = false, $smallTypeName = null) {
	$strHtml = '';
	
	$useParam = array (
			'CPU' => array (
					'核心类型',
					'接口',
					'核心数量',
					'主频',
					'三级缓存' 
			),
			'主板' => array (
					'芯片组',
					'芯片(组)',
					'CPU插槽',
					'内存插槽',
					'支持内存类型',
					'硬盘接口',
					'扩展插槽',
					'PCI Express插槽' 
			),
			'固态硬盘SSD' => array (
					'容量',
					'接口类型',
					'硬盘尺寸' 
			),
			'固态硬盘' => array (
					'容量',
					'接口类型',
					'硬盘尺寸' 
			),
			'SSD' => array (
					'容量',
					'接口类型',
					'硬盘尺寸' 
			),
			'笔记本' => array (
					'处理器',
					'内存大小',
					'硬盘容量',
					'硬盘类型',
					'显卡芯片',
					'屏幕大小',
					'分辨率',
					'重量' 
			),
			'超极本' => array (
					'处理器',
					'内存大小',
					'硬盘容量',
					'硬盘类型',
					'分辨率',
					'重量' 
			),
			'手机' => array (
					'系统',
					'CPU',
					'主屏尺寸',
					'屏幕分辨率',
					'像素',
					'运行内存',
					'内置容量',
					'电池规格' 
			),
			'平板电脑' => array (
					'处理器',
					'操作系统',
					'屏幕大小',
					'分辨率',
					'内存容量',
					'硬盘容量',
					'内置摄像头' 
			) 
	);
	if ($items) {
		$data = json_decode_zh ( $items, true );
		foreach ( $data as $v ) {
			$key = trim ( $v ['key'] );
			$value = trim ( $v ['value'] );
			
			if ($smallTypeName) {
				if (! in_array ( $key, $useParam [$smallTypeName] )) {
					continue;
				}
			}
			
			if ($value == '') {
				$value = trim ( $v ['nValue'] );
			}
			
			if ($wap) {
				$strHtml .= '<p><span class="leftSpan">';
			} else {
				$strHtml .= '<p><span class="leftCol">';
			}
			$strHtml .= $key . '：</span>' . $value . '</p>';
		}
	}
	return $strHtml;
}

/**
 * 显示品类列表
 * 
 * @return Ambigous <multitype:, multitype:2 , 查询结果, boolean, NULL, string, number>
 */
function showCateList() {
	$category = new category ();
	$list = $category->getCateList ();
	return $list;
}

/**
 * 店面区域级联
 * 
 * @param number $area        	
 * @param number $province        	
 * @param number $city        	
 * @return string
 */
function storeRegion($area = 0, $province = 0, $city = 0) {
	$strhtml = '<select id="storeArea" onchange="loadProvince()">';
	$strhtml .= regionList ( 0, 0, $area );
	$strhtml .= '</select>';
	$strhtml .= '<select id="storeProvince" onchange="loadCity()">';
	
	if ($province) {
		$strhtml .= regionList ( 1, $area, $province );
	} else {
		$strhtml .= '<option value="0">全部</option>';
	}
	$strhtml .= '</select>';
	$strhtml .= '<select id="storeCity">';
	if ($city) {
		$strhtml .= regionList ( 2, $province, $city );
	} else {
		$strhtml .= '<option value="0">全部</option>';
	}
	$strhtml .= '</select>';
	return $strhtml;
}

/**
 * 用户省市缓存
 * 
 * @param number $area        	
 * @param number $province        	
 * @param number $city        	
 * @return string
 */
function userProvinceCity($province = 0, $city = 0) {
	$strhtml = '<select id="storeProvince" onchange="loadCity()">';
	
	$strhtml .= '<option value="0">所在省市</option>';
	
	$strhtml .= provinceList ( 3, '', $province );
	
	$strhtml .= '</select>';
	$strhtml .= '<select id="storeCity">';
	if ($city) {
		$strhtml .= provinceList ( 2, $province );
	} else {
		$strhtml .= '<option value="0">所在城市</option>';
	}
	$strhtml .= '</select>';
	return $strhtml;
}

/**
 * 区域调用函数
 * 
 * @param number $type        	
 * @param string $key        	
 * @return string
 */
function provinceList($type = 0, $key = null, $seleckedKey = null) {
	$region = new region ();
	$data = $region->getList ( $type, $key );
	$strhtml = '';
	foreach ( $data as $v ) {
		switch ($type) {
			case 3 :
				$strhtml .= '<option value="' . $v ['province'] . '"';
				if ($v ['province'] == strval ( $seleckedKey )) {
					$strhtml .= ' selected="selected"';
				}
				$strhtml .= '>' . $v ['province'] . '</option>';
				break;
			case 2 :
				$strhtml .= '<option value="' . $v ['city'] . '"';
				if ($v ['city'] == strval ( $seleckedKey )) {
					$strhtml .= ' selected="selected"';
				}
				$strhtml .= '>' . $v ['city'] . '</option>';
				break;
		}
	}
	return $strhtml;
}

/**
 * 店面区域级联
 * 
 * @param number $area        	
 * @param number $province        	
 * @param number $city        	
 * @return string
 */
function rspStoreRegion($area = 0, $province = 0, $city = 0) {
	$strhtml = '<select id="storeArea" onchange="loadProvince()">';
	$strhtml .= regionList ( 0, 0, $area );
	$strhtml .= '</select>';
	$strhtml .= '<select id="storeProvince" onchange="loadCity()">';
	
	if ($province) {
		$strhtml .= regionList ( 1, $area, $province );
	} else {
		$strhtml .= '<option value="0">全部</option>';
	}
	$strhtml .= '</select>';
	$strhtml .= '<select id="storeCity" onchange="loadStore()">';
	if ($city) {
		$strhtml .= regionList ( 2, $province, $city );
	} else {
		$strhtml .= '<option value="0">全部</option>';
	}
	$strhtml .= '</select>';
	return $strhtml;
}

/**
 * 店面区域级联,RSP信息修改
 * 
 * @param number $area        	
 * @param number $province        	
 * @param number $city        	
 * @return string
 */
function rspStoreRegionEdit($area = 0, $province = 0, $city = 0) {
	$strhtml = '<select id="storeArea" onchange="loadProvince()">';
	$strhtml .= regionList ( 0, 0, $area );
	$strhtml .= '</select>';
	$strhtml .= '<select id="storeProvince" onchange="loadCity()">';
	
	if ($province) {
		$strhtml .= regionList ( 1, $area, $province );
	} else {
		$strhtml .= '<option value="0">全部</option>';
	}
	$strhtml .= '</select>';
	$strhtml .= '<select id="storeCity" onchange="loadStore()">';
	if ($city) {
		$strhtml .= regionList ( 2, $province, $city );
	} else {
		$strhtml .= '<option value="0">全部</option>';
	}
	$strhtml .= '</select>';
	return $strhtml;
}

/**
 * 区域调用函数
 * 
 * @param number $type        	
 * @param string $key        	
 * @return string
 */
function regionList($type = 0, $key = null, $seleckedKey = null, $single = 0) {
	$region = new region ();
	$data = $region->getList ( $type, $key );
	$strhtml = '<option value="0">全部</option>';
	if ($single) {
		$strhtml = '<option value="0">所在城市</option>';
	}
	foreach ( $data as $v ) {
		switch ($type) {
			case 0 :
				
				$strhtml .= '<option value="' . $v ['area'] . '"';
				if ($v ['area'] == strval ( $seleckedKey )) {
					$strhtml .= ' selected="selected"';
				}
				$strhtml .= '>' . $v ['partition'] . '</option>';
				break;
			case 1 :
				$strhtml .= '<option value="' . $v ['province'] . '"';
				if ($v ['province'] == strval ( $seleckedKey )) {
					$strhtml .= ' selected="selected"';
				}
				$strhtml .= '>' . $v ['province'] . '</option>';
				break;
			case 2 :
				$strhtml .= '<option value="' . $v ['city'] . '"';
				if ($v ['city'] == strval ( $seleckedKey )) {
					$strhtml .= ' selected="selected"';
				}
				$strhtml .= '>' . $v ['city'] . '</option>';
				break;
		}
	}
	return $strhtml;
}

/**
 * 会员省市联动
 * 
 * @return string
 */
function memberRegion() {
	$location = new location ();
	$data = $location->getList ();
	$strhtml = '<select id="memberProvince" onchange="loadmemberCity()">';
	$strhtml .= locationList ( 0 );
	$strhtml .= '</select>';
	$strhtml .= '<select id="memberCity" onchange="countMemberNum()">';
	$strhtml .= '<option value="0">全部</option>';
	$strhtml .= '</select>';
	return $strhtml;
}

/**
 * 会员地区列表
 * 
 * @param unknown $fid        	
 * @return string
 */
function locationList($fid) {
	$location = new location ();
	$data = $location->getList ( $fid );
	$strhtml = '<option value="0">全部</option>';
	foreach ( $data as $v ) {
		$strhtml .= '<option value="' . $v ['id'] . '">' . $v ['name'] . '</option>';
	}
	return $strhtml;
}

/**
 * json数据监测方法
 *
 * 直接输出错误类型
 * 
 * @param string $json        	
 */
function jsonTest($json) {
	json_decode ( $json );
	switch (json_last_error ()) {
		case JSON_ERROR_NONE :
			echo ' - No errors';
			break;
		case JSON_ERROR_DEPTH :
			echo ' - Maximum stack depth exceeded';
			break;
		case JSON_ERROR_STATE_MISMATCH :
			echo ' - Underflow or the modes mismatch';
			break;
		case JSON_ERROR_CTRL_CHAR :
			echo ' - Unexpected control character found';
			break;
		case JSON_ERROR_SYNTAX :
			echo ' - Syntax error, malformed JSON';
			break;
		case JSON_ERROR_UTF8 :
			echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
			break;
		default :
			echo ' - Unknown error';
			break;
	}
	echo PHP_EOL;
	exit ();
}

/**
 * 根据详细地址获取坐标信息
 * 
 * @param unknown $address        	
 * @return number
 */
function location($address) {
	$result ['lng'] = '';
	$result ['lat'] = '';
	$result ['precise'] = 0;
	$result ['confidence'] = 0;
	
	$address = trim ( $address );
	$address = preg_replace ( '/\s|\r|\n|\t|[ ]|#/', '', $address );
	if (empty ( $address )) {
		return $result;
	}
	$url = 'http://api.map.baidu.com/geocoder/v2/?address=' . $address . '&output=json&ak=81231E65D090e62ea493d00d1861bf61&qq-pf-to=pcqq.c2c';
	$location = curl_file_get_contents ( $url );
	$location = json_decode ( $location, true );
	if ($location ['result']) {
		$result ['lng'] = $location ['result'] ['location'] ['lng'];
		$result ['lat'] = $location ['result'] ['location'] ['lat'];
		$result ['precise'] = $location ['result'] ['precise'];
		$result ['confidence'] = $location ['result'] ['confidence'];
	}
	return $result;
}

/**
 * 根据坐标取信息
 * 
 * @param unknown $location        	
 * @return string
 */
/*
 * function location_r($point){
 * $city = '';
 * if($point){
 * $url = 'http://api.map.baidu.com/geocoder?location='.$point.'&output=json&key=81231E65D090e62ea493d00d1861bf61';
 * $data = curl_file_get_contents($url);
 * $info = json_decode($data,true);
 * if($info['status'] == 'OK'){
 * $city = $info['result']['addressComponent']['city'];
 * }
 * }
 * return $city;
 * }
 */
function location_r($point) {
	$location = '';
	if ($point) {
		$url = 'http://api.map.baidu.com/geocoder?location=' . $point . '&output=json&key=81231E65D090e62ea493d00d1861bf61';
		$data = curl_file_get_contents ( $url );
		$info = json_decode ( $data, true );
		if ($info ['status'] == 'OK') {
			$location = $info ['result'] ['addressComponent'];
		}
	}
	return $location;
}

/**
 * 根据会员ID得到 省份区域 和相关坐标
 * 
 * @param type $openid
 *        	会员ＩＤ
 * @return array
 */
function getLocation($openid = 0) {
	$info = array ();
	$member = new member ();
	$user = $member->getInfoByOpenid ( $openid );
	$location = $user ['lastlocation'];
	$location = json_decode ( $location, true );
	
	$point = '';
	if ($location) {
		$lat = $location ['Latitude'];
		$lng = $location ['Longitude'];
		$point = $lat . ',' . $lng;
	}
	
	$location = location_r ( $point );
	$province = $location ['province'];
	$city = $location ['city'];
	
	if (empty ( $city )) {
		$address = $user ['province'] . $user ['city'];
		$location = location ( $address );
		
		$lat = $location ['lat'];
		$lng = $location ['lng'];
		
		$point = $lat . ',' . $lng;
		$location = location_r ( $point );
		$province = $location ['province'];
		$city = $location ['city'];
	}
	if (empty ( $city )) {
		$city = '北京市';
		$location = location ( $city );
		$lat = $location ['lat'];
		$lng = $location ['lng'];
	}
	$info ['lat'] = $lat;
	$info ['lng'] = $lng;
	$info ['province'] = $province;
	$info ['city'] = $city;
	
	return $info;
}

/**
 * 获取电商信息
 *
 * @return array
 */
function getBusinessInfo() {
	$business = new business ();
	$datainfo = array ();
	$datainfo = $business->businesslist_action ();
	foreach ( $datainfo as $value ) {
		$id = $value ['id'];
		$data [$id] = $value ['business_name'];
	}
	return $data;
}

/**
 * 评测文章跳转
 */
function redirctArticle($url) {
	if ($url) {
		return APP_PATH . 'index.php/article/article_detail?url=' . urlencode ( $url );
	}
	return '';
}
function firstChar($str) {
	$char = '';
	if ($str) {
		$pinyin = new Pinyin ();
		$str = iconv ( 'utf-8', 'gbk', $str );
		$pin = $pinyin->get ( $str );
		$char = strtoupper ( substr ( $pin, 0, 1 ) );
	}
	return $char;
}

/**
 * 给定两个坐标，返回这两个点的直线距离
 *
 * @param type $earthX        	
 * @param type $earthY        	
 * @param type $myLocationX        	
 * @param type $myLocationY        	
 * @return type
 */
function getfar($earthX, $earthY, $myLocationX, $myLocationY) {
	return round ( sqrt ( pow ( abs ( $earthX - $myLocationX ), 2 ) + pow ( abs ( $earthY - $myLocationY ), 2 ) ), 10 );
}

/**
 * 获取店面优惠
 * 
 * @param int $id
 *        	店面表Id
 */
function getStoreGifts($id) {
	$store = new store ();
	// 是否有优惠
	$gift = $store->getAvaliableGifts ( $id );
	
	$strhtml = '';
	if ($gift) {
		$strhtml = '<a href="/index.php/wapGift/coupons_detail/id/' . $gift [0] ['gid'] . '"><span class="compaignIcon">惠</span></a>';
	}
	return $strhtml;
}

/**
 * 店面的优惠图标
 * 
 * @param number $gid        	
 * @return string
 */
function storeGifts($gid = 0) {
	$strhtml = '';
	if ($gid) {
		$strhtml = '<a href="/index.php/wapGift/coupons_detail/id/' . $gid . '"><span class="compaignIcon">惠</span></a>';
	}
	return $strhtml;
}

/**
 * 获取品类的优惠
 * 
 * @param unknown $id        	
 */
function getProductGifts($smallTypeId) {
	$product = new product ();
	// 是否有优惠
	$gift = $product->getRelationGift ( $smallTypeId );
	$strhtml = '';
	if ($gift) {
		$strhtml = '<a href="/index.php/wapGift/index/brand/' . $smallTypeId . '"><span class="compaignIcon">惠</span></a>';
	}
	return $strhtml;
}

/**
 * 品类的优惠图标
 * 
 * @param unknown $id        	
 */
function smallTypeGifts($smallTypeId, $gid = 0) {
	$strhtml = '';
	if ($smallTypeId && $gid) {
		$strhtml = '<a href="/index.php/wapGift/index/brand/' . $smallTypeId . '"><span class="compaignIcon">惠</span></a>';
	}
	return $strhtml;
}
function rad($d) {
	return $d * M_PI / 180.0;
}

/**
 * 获取两个坐标点之间的距离，单位km，小数点后2位
 */
function GetDistance($lat1, $lng1, $lat2, $lng2) {
	$EARTH_RADIUS = 6378.137;
	$radLat1 = rad ( $lat1 );
	$radLat2 = rad ( $lat2 );
	$a = $radLat1 - $radLat2;
	$b = rad ( $lng1 ) - rad ( $lng2 );
	$s = 2 * asin ( sqrt ( pow ( sin ( $a / 2 ), 2 ) + cos ( $radLat1 ) * cos ( $radLat2 ) * pow ( sin ( $b / 2 ), 2 ) ) );
	$s = $s * $EARTH_RADIUS;
	$s = round ( $s * 10000 ) / 10000;
	return $s;
}

/**
 * 检查产品的评测文章是否存在
 * 
 * @param unknown $url        	
 * @return boolean
 */
function checkProductArticleExists($url) {
	phpQuery::newDocumentFile ( $url );
	$doms = pq ( '.body .artWrap' );
	$articleTitle = $doms->find ( '.artTitle' )->html ();
	if ($articleTitle) {
		return true;
	}
	return false;
}

/**
 * 验证电话号和手机号
 * 
 * @param unknown $tel        	
 * @return Ambigous <string, mixed>
 */
function checkStoreTel($tel) {
	$tel = trim ( $tel ); // 去除号码两边空白　
	$tel = str_replace ( 'O', '0', $tel ); // 将Ｏ变成0
	$tels = explode ( '/', $tel ); // 将多个号码的分隔开，取第一个
	$tel = $tels [0];
	$intTel = intval ( $tel ); // 转成数判断是否为0
	$flag = false;
	if ($intTel) {
		$flag = pregTP ( $tel );
		if (! $flag) {
			$flag = pregPN ( $tel );
		}
	}
	return $flag ? $tel : '';
}

/**
 * 电话号码匹配
 * 电话号码规则：
 * 区号：3到5位，大部分都是四位，北京(010)和上海市(021)三位，西藏有部分五位，可以包裹在括号内也可以没有
 * 如果有区号由括号包裹，则在区号和号码之间可以有0到1个空格，如果区号没有由括号包裹，则区号和号码之间可以有两位长度的 或者-
 * 号码：7到8位的数字
 * 例如：(010) 12345678 或者 (010)12345678 或者 010 12345678 或者 010--12345678 或者　1234567
 */
function pregTP($test) {
	$rule = '/^(([0-9]{7,8})|\(((010)|(021)|(0\d{3,4}))\)( ?)([0-9]{7,8}))|((010|021|0\d{3,4}))([- ]{1,2})([0-9]{7,8})$/A';
	$flag = preg_match ( $rule, $test );
	return $flag;
}

/**
 * 匹配手机号码
 * 规则：
 * 手机号码基本格式：
 * 前面三位为：
 * 移动：134-139 147 150-152 157-159 182 187 188
 * 联通：130-132 155-156 185 186
 * 电信：133 153 180 189
 * 后面八位为：
 * 0-9位的数字
 */
function pregPN($test) {
	$rule = "/^((13[0-9])|147|(15[0-35-9])|180|182|(18[5-9]))[0-9]{8}$/A";
	$flag = preg_match ( $rule, $test, $result );
	return $flag;
}

/**
 * 匹配中文
 */
function pregName($test) {
	return preg_match ( "/^[\x{4e00}-\x{9fa5}]{2,5}$/u", $test ) ? true : false;
}
/**
 * email正则表达式
 */
function pregEmail($test) {
	$rule = "/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/";
	$flag = preg_match ( $rule, $test );
	return $flag;
}

/**
 * 列表店面处理
 * 
 * @param type $data        	
 * @param type $num
 *        	显示城市个数
 */
function showStore($data, $num = 2) {
	// if (count($data) > 2) {
	$i = 1;
	if (! $data) {
		return '全国';
	}
	foreach ( $data as $key => $val ) {
		$array [$val ['city']] = $val ['city'];
	}
	foreach ( $array as $k => $v ) {
		$arrayData [] ['city'] = $v;
		if ($num == $i) {
			break;
		}
		$i ++;
	}
	$arrayDataStr = '';
	foreach ( $arrayData as $ks => $vs ) {
		$arrayDataStr .= $vs ['city'] . " ";
	}
	if (count ( $data ) > 2) {
		$arrayDataStr = substr ( $arrayDataStr, 0, - 1 ) . '...';
	}
	$arrayData = $arrayDataStr;
	// } else {
	// $arrayDataStr = '';
	// foreach ($data as $key => $val) {
	// $arrayDataStr .= $val['province'] . ' ' . $val['city'] . ' ' . $val['stor_nm'] . '<br>';
	// $arrayDataStr .= $val['stor_nm'] . '<br>';
	// }
	// $arrayDataStr = substr($arrayDataStr, 0, -4);
	// $arrayData = $arrayDataStr;
	// }
	echo $arrayData;
}
function getShopName($relationId) {
	$award = new award ();
	$awardData = $award->getAwardList ( 'id = ' . $relationId );
	$busiessArray = getBusinessInfo ();
	$buss = $busiessArray [$awardData [0] ['onlinetype']];
	echo $buss;
}
function scanAddressTime() {
	return microtime ( true ) * 10000;
}

/**
 * 英特尔加密码算法
 * 
 * @param unknown $password        	
 * @param string $password_salt        	
 */
function intel_pwd_encode($password, $password_salt = '') {
	$p = $password . $password_salt;
	for($i = 1; $i <= 20; $i ++) {
		$p = hash ( 'sha512', $p );
	}
	return $p;
}
/**
 * 获取帖子的回复数量
 * 
 * @param number $id        	
 * @return unknown
 */
function topicReplyCount($id = 0) {
	$forum = new forum ();
	$count = $forum->replyCount ( $id );
	return $count;
}

/**
 * 获取表名（带前缀的）
 * 
 * @param varchar $tablename        	
 * @return varchar
 */
function gtn($tablename) {
	return (defined ( 'TABLE_PREFIX' ) ? TABLE_PREFIX . $tablename : $tablename);
}

/**
 * 实例化MYSQL基础模型类
 * 
 * @param unknown $table        	
 * @return Mysql
 */
function M($table) {
	global $dbConfig;
	$dbConfig = $dbConfig ['default'];
	return new Mysql ( $dbConfig, gtn ( $table ) );
}

/**
 * 自动创建多级目录
 * 
 * @param type $path        	
 * @example mkpath('a/b/c');
 */
function mkpath($mkpath, $mode = 0777) {
	$path_arr = explode ( '/', $mkpath );
	foreach ( $path_arr as $value ) {
		if (! empty ( $value )) {
			if (empty ( $path ))
				$path = $value;
			else
				$path .= '/' . $value;
			is_dir ( $path ) or mkdir ( $path, $mode );
		}
	}
	if (is_dir ( $mkpath ))
		return true;
	return false;
}

/**
 * 创建像这样的查询: "IN('a','b')";
 *
 * @access public
 * @param mix $item_list
 *        	列表数组或字符串
 * @param string $field_name
 *        	字段名称
 *        	
 * @return void
 */
function db_create_in($item_list, $field_name = '') {
	if (empty ( $item_list )) {
		return $field_name . " IN ('') ";
	} else {
		if (! is_array ( $item_list )) {
			$item_list = explode ( ',', $item_list );
		}
		$item_list = array_unique ( $item_list );
		$item_list_tmp = '';
		foreach ( $item_list as $item ) {
			if ($item !== '') {
				$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
			}
		}
		if (empty ( $item_list_tmp )) {
			return $field_name . " IN ('') ";
		} else {
			return $field_name . ' IN (' . $item_list_tmp . ') ';
		}
	}
}

/**
 * 过滤昵称中的图表
 */
function filterImage($str) {
	$arr = array ();
	$pattern = '/([a-zA-Z0-9\x{4e00}-\x{9fa5}])+/u';
	preg_match_all ( $pattern, $str, $arr );
	$res = implode ( '-', $arr [0] );
	return $res;
}

// 检索身份证是否正确
function isCreditNo($vStr) {
	$vCity = array (
			'11',
			'12',
			'13',
			'14',
			'15',
			'21',
			'22',
			'23',
			'31',
			'32',
			'33',
			'34',
			'35',
			'36',
			'37',
			'41',
			'42',
			'43',
			'44',
			'45',
			'46',
			'50',
			'51',
			'52',
			'53',
			'54',
			'61',
			'62',
			'63',
			'64',
			'65',
			'71',
			'81',
			'82',
			'91' 
	);
	
	if (! preg_match ( '/^([\\d]{17}[xX\\d]|[\\d]{15})$/', $vStr ))
		return false; // 执行一个正则
	if (! in_array ( substr ( $vStr, 0, 2 ), $vCity ))
		return false;
	
	$vStr = preg_replace ( '/[xX]$/i', 'a', $vStr );
	$vLength = strlen ( $vStr );
	
	if ($vLength == 18) {
		$vBirthday = substr ( $vStr, 6, 4 ) . '-' . substr ( $vStr, 10, 2 ) . '-' . substr ( $vStr, 12, 2 );
	} else {
		$vBirthday = '19' . substr ( $vStr, 6, 2 ) . '-' . substr ( $vStr, 8, 2 ) . '-' . substr ( $vStr, 10, 2 );
	}
	
	if (date ( 'Y-m-d', strtotime ( $vBirthday ) ) != $vBirthday)
		return false;
	if ($vLength == 18) {
		$vSum = 0;
		
		for($i = 17; $i >= 0; $i --) {
			$vSubStr = substr ( $vStr, 17 - $i, 1 );
			$vSum += (pow ( 2, $i ) % 11) * (($vSubStr == 'a') ? 10 : intval ( $vSubStr, 11 ));
		}
		if ($vSum % 11 != 1)
			return false;
	}
	
	return true;
}
function NumToStr($num) {
	if (stripos ( $num, 'e' ) === false)
		return $num;
	$num = trim ( preg_replace ( '/[=\'"]/', '', $num, 1 ), '"' ); // 出现科学计数法，还原成字符串
	$result = "";
	while ( $num > 0 ) {
		$v = $num - floor ( $num / 10 ) * 10;
		$num = floor ( $num / 10 );
		$result = $v . $result;
	}
	return $result;
}
    