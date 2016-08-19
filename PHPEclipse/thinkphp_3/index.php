<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
/*
 * 调试模式下的优势
 开启日志记录，任何错误信息和调试信息都会详细记录，便于调试；
关闭模板缓存，模板修改可以即时生效；
记录SQL日志，方便分析SQL；
关闭字段缓存，数据表字段修改不受缓存影响；
严格检查文件大小写（即使是Windows平台），帮助你提前发现Linux部署问题；
可以方便用于开发过程的不同阶段，包括开发、测试和演示等任何需要的情况，不同的应用模式可以配置独立的项目配置文件；
*/
define('APP_DEBUG',True);
//扩展thinkphp支持的配置文件的格式种类
define('CONF_EXT','.ini');
//// 类文件后缀 在thinkphp中定义了框架支持的类文件后缀名
//const EXT               =   '.class.php'; 
// 定义应用目录
define('APP_PATH','./Application/');
// 定义运行时目录
define('RUNTIME_PATH','./Application/Runtime/');


// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单
