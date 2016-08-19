<?php

//创建项目的应用目录 将应用目录与index.php应用入口文件分离
define("APP_PATH","../");

//设置开发模式为调试模式
//若生产模式false runtime开启生产模式有缓存
define("APP_DEBUG", true); 

//引用项目的thinkphp框架
require '../../ThinkPHP/ThinkPHP.php';


//IS_CGI 是否是通用网管接口
//IS_CLI 是否终端命令行模式 还是 浏览器模式
//thinkphp执行流程 在页面看到效果就是控制器被实例化然后调用相关方法的结果
/*执行流程 
 * thinkphp/common/runtime.php 
 * 声明许多常量信息 
 * 加载系统核心类文件 
 * 自动创建应用目录
 * build_app_dir()
 *Think::Start()
 *ThinkPHP/lib/Core/Think.class.php
 * static function start(){};
 * Think::buildApp()引入相关配置文件
 * App::run()
 * ThinkPHP/lib/Core/App.class.php
 * App::init 
 * Dispatcher类静态方法
 * 分析路由（控制器MODULE_NAME 方法ACTION_NAME）index.php?c=控制器&a=方法 
 * 控制器调用方法是PHP里面的反射机制？？？
 * App::exec()
 * */




/*
 * 丰富应用   使用框架 可以加快开发速度 节约工作量
 * 开发index 开发model数据 开发view视图
 * 根据业务特点 把控制器制作出来(User Goods)
 * pathinfo模式访问 nginx有问题
 * http://localhost:8080/thinkphp_3/Application3/go/index.php/User/login
 *伪静态隐藏index.php
 *普通方式 使用
 *localhost:8080/thinkphp_3/Application3/go/index.php?m=User&a=login
 * */
 
/*
 * file not found 服务器documentroot的配置 和 目录权限的访问配置;
 *file not found url中的php脚本文件名确定没写错？
 * 无法加载模块 thinkphp mvc默认的控制器的后缀名.class.php
 * * 无法加载模块 thinkphp 确定url路径没出错 
 * 比如应用入口文件go/index.php后有分组模块名之后才接控制器名方法名还有参数列表
 * 无法加载控制器 thinkphp 类文件 需要添加命名空间 以帮助框架找到控制器
 * */






