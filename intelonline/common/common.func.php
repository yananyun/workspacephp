<?php

/**
 * 导出excel
 */
function makeSpreadExcel($all, $list) {
	set_time_limit ( 0 );
	error_reporting ( E_ALL );
	ini_set ( 'display_errors', TRUE );
	ini_set ( 'display_startup_errors', TRUE );
	require_once './lib/excel/PHPExcel.php';
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel ();
	// Set document properties
	$objPHPExcel->getProperties ()->setCreator ( "ZeverTech" )->setLastModifiedBy ( "ZeverTech" )->setTitle ( "ZeverTech" );
	// Add some data
	$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'A1', '二维码ID' )->setCellValue ( 'B1', '推广会员数量' );
	
	$i = 2;
	foreach ( $all as $log ) {
		// Miscellaneous glyphs, UTF-8
		$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'A' . $i, $log ['sid'] )->setCellValue ( 'B' . $i, $log ['count'] );
		$i ++;
	}
	// Rename worksheet
	$objPHPExcel->getActiveSheet ()->setTitle ( '二维码推广概况' );
	
	$objPHPExcel->createSheet ();
	// Add some data
	$objPHPExcel->setActiveSheetIndex ( 1 )->setCellValue ( 'A1', '二维码ID' )->setCellValue ( 'B1', '推广会员Open ID' )->setCellValue ( 'C1', '会员昵称' )->setCellValue ( 'D1', '性别' )->setCellValue ( 'E1', '国家' )->setCellValue ( 'F1', '城市' )->setCellValue ( 'G1', '地区' )->setCellValue ( 'H1', '推广时间' )->setCellValue ( 'I1', '城市级别' );
	$j = 2;
	$location = location_level ();
	foreach ( $list as $log ) {
		if ($log ['sex'] == 1) {
			$sex = '男';
		} elseif ($log ['sex'] == 2) {
			$sex = '女';
		} else {
			$sex = '未知';
		}
		$nickname = str_replace ( '=', '', $log ['nickname'] );
		
		if (! empty ( $log ['province'] ) && ! empty ( $log ['city'] )) {
			if ($log ['province'] != "其他" && $log ['city'] != "其他") {
				$cityArea = $log ['province'] . $log ['city'];
				// 获取城市级别
				if (! empty ( $location [$cityArea] )) {
					$cityLevel = $location [$cityArea];
				} else {
					$cityLevel = "0";
				}
			} else {
				$cityLevel = "0";
			}
		} else {
			$cityLevel = "0";
		}
		
		// Miscellaneous glyphs, UTF-8
		$objPHPExcel->setActiveSheetIndex ( 1 )->setCellValue ( 'A' . $j, $log ['sid'] )->setCellValue ( 'B' . $j, $log ['openid'] )->setCellValue ( 'C' . $j, $nickname )->setCellValue ( 'D' . $j, $sex )->setCellValue ( 'E' . $j, $log ['country'] )->setCellValue ( 'F' . $j, $log ['city'] )->setCellValue ( 'G' . $j, $log ['province'] )->setCellValue ( 'H' . $j, date ( 'Y-m-d H:i:s', $log ['sptime'] ) )->setCellValue ( 'I' . $j, $cityLevel );
		$j ++;
	}
	// Rename worksheet
	$objPHPExcel->getActiveSheet ()->setTitle ( '二维码推广详情' );
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex ();
	
	// $title = "推广数据" . date("YmdHis") .".xlsx";
	// Redirect output to a client’s web browser (Excel2007)
	header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
	header ( 'Content-Disposition: attachment;filename="推广数据' . date ( "YmdHis" ) . '.xlsx"' );
	header ( 'Cache-Control: max-age=0' );
	
	$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
	$objWriter->save ( 'php://output' );
	exit ();
}

/**
 * 格式化数组中的数值为小数点后两位
 * 
 * @param
 *        	mix 数组或数值
 * @return 返回值直接作用到了参数
 * @author Gason Wong <gaoshang_s@163.com>
 */
function formatNum(&$param) {
	if (is_array ( $param ) && $param) {
		foreach ( $param as &$v ) {
			formatNum ( $v );
		}
	} else {
		$param = sprintf ( '%.2f', $param );
	}
}
/**
 * 格式化Ueditor 编辑器内容
 * 
 * @param unknown $content        	
 */
function formatUEditor($content) {
	if ($content) {
		$content = str_replace ( array (
				'%0D%0A',
				'%0A',
				'%0D' 
		), '', urlencode ( $content ) );
		$content = urldecode ( $content );
		$content = htmlspecialchars_decode ( $content );
	}
	return $content;
}

// post方式提交数据
function http_post($url, $data, $ssl = FALSE) { // 模拟提交数据函数
	$curl = curl_init (); // 启动一个CURL会话
	curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
	if ($ssl) {
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 81 ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_SSLVERSION, 3 );
	}
	curl_setopt ( $curl, CURLOPT_USERAGENT, $_SERVER ['HTTP_USER_AGENT'] ); // 模拟用户使用的浏览器
	curl_setopt ( $curl, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
	curl_setopt ( $curl, CURLOPT_POST, 1 ); // 发送一个常规的Post请求
	curl_setopt ( $curl, CURLOPT_POSTFIELDS, $data ); // Post提交的数据包
	curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
	curl_setopt ( $curl, CURLOPT_HEADER, 0 ); // 显示返回的Header区域内容
	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
	$tmpInfo = curl_exec ( $curl ); // 执行操作
	if (curl_errno ( $curl )) {
		return FALSE;
	}
	curl_close ( $curl ); // 关闭CURL会话
	return $tmpInfo; // 返回数据
}