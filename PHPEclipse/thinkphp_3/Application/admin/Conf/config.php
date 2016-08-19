<?php
return array(
	//'配置项'=>'配置值'
		'URL_CASE_INSENSITIVE'  =>  true,//设置为true的时候表示URL地址不区分大小写
		'URL_MODEL'          => '1', //URL模式 pathinfo thinkphp文档采用的
		'SESSION_AUTO_START' => true, //是否开启session
		'URL_PATHINFO_FETCH' => 'ORIG_PATH_INFO,REDIRECT_URL,其他参数…'
// 		'MODULE_ALLOW_LIST'    =>    array('Home','Test','Admin'),
// 		'DEFAULT_MODULE'       =>    'Home',
// 		'MULTI_MODULE'          =>  true, // 是否允许多模块 如果为false 则必须设置 DEFAULT_MODULE
// 		'URL_MODULE_MAP'       =>    array('test'=>'Admin'),
);