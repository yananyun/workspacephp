<?php
define ( 'MEC_PREFIX', 'intelreport_' );
define ( 'LIB_PATH', ROOT_PATH . 'lib/' );
define ( 'MODEL_PATH', ROOT_PATH . 'model/' );
define ( 'API_PATH', ROOT_PATH . 'api/' );
define ( 'MICROTIME', microtime ( true ) );
define ( 'ERROR_REPORT', true ); // 错误报告，false 不启用，true 启用
define ( 'SMARTY_DIR', LIB_PATH . 'libs/' ); // smarty 库路径
define ( "CHARSET", 'utf-8' );
define ( 'SYS_TIME', time () );
define ( "APP_PATH", 'http://testintelpro.com:8080/' ); // 页面跳转地址
define ( "SHOWMSG_PATH", 'public/showmsg.html' ); // 前台信息提示模板
define ( "MANAGE_CENTER", 'http://testintelpro.com:8080/' ); // 管理中心地址
define ( "CSS_PATH", APP_PATH . "css/" );
define ( "IMG_PATH", APP_PATH . "images/" );
define ( "JS_PATH", APP_PATH . "js/" );
// define('TABLE_PREFIX', 'intelonline_'); // 表前缀
define ( 'TABLE_PREFIX', 'sys_' ); // 表前缀
define ( 'URL_REWRITE_ON', 'true' ); // 是否开启重写，true开启重写,false关闭重写
define ( 'URL_MODULE_DEPR', '/' ); // 模块分隔符，一般不需要修改
define ( 'URL_ACTION_DEPR', '/' ); // 操作分隔符，一般不需要修改
define ( 'URL_PARAM_DEPR', '/' ); // 参数分隔符，一般不需要修改
define ( 'URL_HTML_SUFFIX', '.html' ); // 伪静态后缀设置，，例如 .html ，一般不需
                                    
// 微信相关配置
define ( 'AOPENID', 'gh_ac518c1b2445' ); // 公众号的openid
define ( 'APPID', 'wx4de191f10d4ca90f' ); // 公众号的APPID
define ( 'APPSECRET', 'b9d9b344b246c15e7a15a5dc8d0a546e' ); // 公众号的APPSECRET
define ( 'TOKEN', 'JMLKJkhkjhkJKLjhkughyuT76tbhIOu' ); // 公众号的TOKEN

/* Cookie设置 */
define ( 'COOKIE_EXPIRE', 0 ); // Coodie有效期
define ( 'COOKIE_DOMAIN', '' ); // Cookie有效域名
define ( 'COOKIE_PATH', '/' ); // Cookie路径
define ( 'COOKIE_PREFIX', 'intelonline' ); // Cookie前缀 避免冲突

/* Session设置 */
define ( 'SESSION_NAME', 'intelonline' ); // 默认Session_name
define ( 'SESSION_PATH', '' ); // 采用默认的Session save path
define ( 'SESSION_TYPE', 'File' ); // 默认Session类型 支持 DB 和 File
define ( 'SESSION_EXPIRE', '300000' ); // 默认Session有效期
define ( 'SESSION_CALLBACK', '' ); // 反序列化对象的回调方法

define ( 'ERROR_NUM', 5 ); // 用户登录错误次数
define ( "ERROR_TIME", 5 ); // 用户登录错误次数大于上面数量后多少时间可再次登录（分钟）
define ( "SENDFALURE_NUMBER", 10 ); // 用户发送失败次数
define ( "SENDFALURE_LIMIT", 10 ); // 每次轮发条数
define ( "SENDFALURE_TIME", 10 ); // 轮发时间sleep的时间
define ( "DUBGE", true );
define ( 'MEMCACHE_EXPIRE', 1800 ); // memcache过期时间
                                 // 默认数据格式
define ( 'DEFAULT_AJAX_RETURN', 'JSON' );

// 默认密码
define ( 'DEFAULT_PASSWORD', '666666' );

// 发送次数限制
define ( 'SEND_ERROR_NUM', 10 );
// 系统错误码
// 注意变量名格式，薛爱华修改
$ErrorCode = array (
		1000 => '操作成功',
		1001 => '发生未知错误',
		1026 => '非法操作',
		1027 => '参数错误',
		1028 => '操作失败',
		1029 => '系统错误',
		1030 => '访问的类不存在或类名不正确',
		1031 => '上传文件的大小超过限制',
		1032 => '数据库错误',
		1033 => '缺少参数',
		1034 => '找不到模板文件',
		1035 => '上传文件格式不正确',
		3004 => '没有数据返回',
		4321 => '请求错误',
		4322 => '没有权限访问对应的资源',
		4323 => '请求的资源不存在',
		4324 => '请求的ID无法被修改',
		5000 => '服务端脚本错误',
		- 1 => "系统繁忙",
		0 => "请求成功",
		40001 => "验证失败",
		40002 => "不合法的凭证类型",
		40003 => "不合法的OpenID",
		40004 => "不合法的媒体文件类型",
		40005 => "不合法的文件类型",
		40006 => "不合法的文件大小",
		40007 => "不合法的媒体文件id",
		40008 => "不合法的消息类型",
		40009 => "不合法的图片文件大小",
		40010 => "不合法的语音文件大小",
		40011 => "不合法的视频文件大小",
		40012 => "不合法的缩略图文件大小",
		40013 => "不合法的APPID",
		40014 => "不合法的access_token",
		40014 => "不合法的access_token",
		40015 => "不合法的菜单类型",
		40016 => "不合法的按钮个数",
		40017 => "不合法的按钮个数",
		40018 => "不合法的按钮名字长度",
		40019 => "不合法的按钮KEY长度",
		40020 => "不合法的按钮URL长度",
		40021 => "不合法的菜单版本号",
		40022 => "不合法的子菜单级数",
		40023 => "不合法的子菜单按钮个数",
		40024 => "不合法的子菜单按钮类型",
		40025 => "不合法的子菜单按钮名字长度",
		40026 => "不合法的子菜单按钮KEY长度",
		40027 => "不合法的子菜单按钮URL长度",
		40028 => "不合法的自定义菜单使用用户",
		41001 => "缺少access_token参数",
		41002 => "缺少appid参数",
		41003 => "缺少refresh_token参数",
		41004 => "缺少secret参数",
		41005 => "缺少多媒体文件数据",
		41006 => "缺少media_id参数",
		41007 => "缺少子菜单数据",
		42001 => "access_token超时",
		43001 => "需要GET请求",
		43002 => "需要POST请求",
		43003 => "需要HTTPS请求",
		44001 => "多媒体文件为空",
		44002 => "POST的数据包为空",
		44003 => "图文消息内容为空",
		45001 => "多媒体文件大小超过限制",
		45002 => "消息内容超过限制",
		5003 => "标题字段超过限制",
		45004 => "描述字段超过限制",
		45005 => "链接字段超过限制",
		45006 => "图片链接字段超过限制",
		45007 => "语音播放时间超过限制",
		45008 => "图文消息超过限制",
		45009 => "接口调用超过限制",
		45010 => "创建菜单个数超过限制",
		46001 => "不存在媒体数据",
		46002 => "不存在的菜单版本",
		46003 => "不存在的菜单数据",
		47001 => "解析JSON/XML内容错误 " 
);

// 数据库及缓存服务器配置
$dbConfig = array (
		// 默认数据库
		'default' => array (
				// 主数据库
				'Master' => array (
						'DBhost' => 'localhost', // 服务器地址 不能直接写127.0.0.1 为什么呢
						'DBport' => '3306', // 端口号
						'DBuser' => 'root', // 用户名
						'DBpws' => 'dyn123456' 
				) // 密码
,
				'DBname' => 'intelonline' 
		)
		// 'TABLE_PREFIX' => 'i_', // 表前缀
		 
);
// Memcache服务器配置
$memcConfig = array (
		// 默认的缓存服务器设置
		'default' => array (
				// '前缀'=>array('host'=>'服务器地址','port'=>'端口号'),
				'default' => array (
						'host' => '127.0.0.1',
						'port' => '3306' 
				) 
		) 
);

