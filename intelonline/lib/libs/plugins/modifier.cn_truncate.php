<?php
/**
 * 中文字符串截取函数
 * @param type $string
 * @param type $length
 * @param type $etc
 * @param type $keep_first_style
 * @return type
 * 用法：
 * {$item.title|cn_truncate:18:"...":true}
 * 第1个参数 18 表示截取 18 个汉字
 * 第2个参数 ... 表示，如果多余18个汉字，则显示 ...
 * 第3个参数 true 表示保留文字的初始颜色。 false 表示去掉颜色。
 */
function smarty_modifier_cn_truncate($string, $length, $etc = '...', $keep_first_style = true) {
	$result = '';
	
	$string = html_entity_decode ( trim ( strip_tags ( $string ) ), ENT_QUOTES, 'UTF-8' );
	// $string = trim($string);
	$strlen = strlen ( $string );
	
	for($i = 0; (($i < $strlen) && ($length > 0)); $i ++) {
		if ($number = strpos ( str_pad ( decbin ( ord ( substr ( $string, $i, 1 ) ) ), 8, '0', STR_PAD_LEFT ), '0' )) {
			if ($length < 1.0) {
				break;
			}
			
			$result .= substr ( $string, $i, $number );
			
			$length -= 1.0;
			
			$i += $number - 1;
		} else {
			$result .= substr ( $string, $i, 1 );
			
			$length -= 0.5;
		}
	}
	
	$result = htmlspecialchars ( $result, ENT_QUOTES, 'UTF-8' );
	
	if ($i < $strlen) {
		$result .= $etc;
	}
	
	return $result;
}

?>