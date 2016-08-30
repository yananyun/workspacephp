<?php
// define('ROOT_PATH', substr(__FILE__, 0, strlen(__FILE__) - 15));
// define('MAIL_HOST', '');//SMTP服务器主机地址
// define('SYS_MAIL_USER', '');//SMTP 用户名 注意：普通邮件认证不需要加 @域名
// define('SYS_MAIL_PWD', '');//SMTP 用户密码
// define('SYS_MAIL_FROM', '');//发件人邮件地址
// define('SYS_MAIL_FROM_NAME', '');//发件人
define ( 'ROOT_PATH', substr ( __FILE__, 0, strlen ( __FILE__ ) - 15 ) );

define ( 'WebFolder', 'intelonline' );

define ( 'MAIL_HOST', 'smtp.exmail.qq.com' ); // SMTP服务器主机地址
define ( 'SYS_MAIL_USER', '' ); // SMTP 用户名 注意：普通邮件认证不需要加 @域名
define ( 'SYS_MAIL_PWD', '' ); // SMTP 用户密码
define ( 'SYS_MAIL_FROM', '' ); // 发件人邮件地址
define ( 'SYS_MAIL_FROM_NAME', '英特尔官方微信系统' ); // 发件人
require_once ROOT_PATH . 'common/global.func.php';
// 引入配置文件
require_once ROOT_PATH . 'config/config.inc.php';

header ( "Content-Type: text/html; charset=" . CHARSET );
date_default_timezone_set ( "Asia/Shanghai" );
// 检查是否开启错误等级
defined ( 'ERROR_REPORT' ) ? '' : define ( 'ERROR_REPORT', false );
if (! ERROR_REPORT) {
	error_reporting ( 0 );
} else {
	// error_reporting(E_ALL ^ E_NOTICE);
	error_reporting ( E_ALL ^ (E_NOTICE | E_WARNING) );
}
require_once ROOT_PATH . 'common/commonArray.php';
echo ROOT_PATH . '<br/>我要知道项目的根路径documentroot<br/>';
session_start (); // 开启session
?>
