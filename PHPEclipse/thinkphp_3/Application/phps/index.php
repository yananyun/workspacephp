<?php
phpinfo();

echo"thinkphp的入口文件 每个项目都有一个统一的入口文件（但不一定唯一），
	入口文件的内容包括 第一：定义框架的路径 项目路径 项目名称	
		第二：定义调试模式和运行模式的相关常量
		第三：载入框架入口文件（不要误解 这里只是thinkphp的框架
		路径 和php解释引擎一点关系没有）";
echo"学习 thinkphp http://doc.thinkphp.cn/manual/deploy_directory.html
		部署时 部署目录 该如何构建 ；项目目录 该如何构建 真正的自由定义";


echo "项目部署 入口文件准备好后 就要 具体配置 项目
		惯例配置
		项目配置
		调试配置
		分组配置
		扩展配置
		动态配置
		后边的配置会覆盖前面的配置 配置文件加载顺序（覆盖之前的同名配置未生效之前 加载顺序 从右到左）
		";

echo "惯例配置文件的文件目录 
		系统目录(这里是指thinkphp框架的目录)下 Conf\convention.php ";

echo "在项目中支持自己的项目配置文件 LOAD_EXT_CONFIG 指明文件名即可 但是这些文件的存放目录？";
echo "在项目配置文件中定义 LOAD_EXT_FILE 支持thinkphp支持自己想要的php函数文件 但是这些文件的存放目录？";

echo "直接访问documentroot 项目目录thinkphp-3
		下的应用目录（application）下的index.php
		应用目录下自动生成目录 Common Home Runtime 文件夹";


echo"使用thinkphp的pathinfo模式（可以和rewrite模式一样做到隐藏访问的入口文件的目的）http://服务器名/项目目录名/
		在不考虑路由的情况下 第一个参数被解析成模块名称 第二个参数被解析成操作 后面的参数是显示传递
		这个分析工作由URL调度器（Dispatcher）来实现，并且都分析成下面的规范：
		http://域名/项目名/分组名/模块名/操作名/其他参数
		Dispatcher会根据URL地址来获取当前需要执行的项目、分组（如果有定义的话）模块、
		操作以及其他参数，在某些情况下，项目名可能不会出现在URL地址中
		（通常情况下入口文件则代表了某个项目，而且入口文件可以被隐藏）";

echo "<p>  开发流程:
系统设计、创建数据库和数据表；（可选）
项目命名并创建项目入口文件，开启调试模式；
完成项目配置；
创建项目函数库；（可选）
开发项目需要的扩展（模式、驱动、标签库等）；（可选）
创建控制器类；
创建模型类；（可选）
创建模板文件；
运行和调试、分析日志；
开发和设置缓存功能；（可选）
添加路由支持；（可选）
安全检查；（可选 ）
部署到生产环境。
		</p>";

echo " 典型的URL访问规则 
http://serverName/index.php（或者其他应用入口文件）/模块/控制器/操作/[参数名/参数值...]
在我们启用项目分组之前，由于使用的两个项目，所以URL地址分别是：
http://serverName/index.php/Index/index Home项目地址
http://serverName/Admin/index.php/Index/index Admin项目地址
采用了分组模式后，URL地址变成：
http://serverName/index.php/Home/Index/indexHome分组地址
如果Home是默认分组的话 还可以变成 http://serverName/index.php/Index/index
http://serverName/index.php/Admin/Index/indexAdmin分组地址";


echo "在第一次访问应用入口文件的时候，会显示如图所示的默认的欢迎页面，
		并自动生成了一个默认的应用模块Home。";

echo "3.2发布版本自带了一个应用目录结构，并且带了一个默认的应用入口文件，方便部署和测试，
		默认的应用目录是Application（实际部署过程中可以随意设置）";

echo "thinkphp模块化设计";
