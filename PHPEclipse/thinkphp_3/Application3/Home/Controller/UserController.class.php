<?php
namespace Home\Controller;
use Think\Controller;

//用户控制器
//Action是普通控制器的父类
class UserController extends Controller
{
	//登录操作
	function login()
	{
		echo "home login suc";
		//控制器 调用数据模型model model将数据交给 controller
		//controller将数据 交给模版
		$this->display("test5");
	}
	//注册操作
	function register()

	{
		//控制器 调用数据模型model model将数据交给 controller
		//controller将数据 交给模版
		$this->display("register");
		 
	}
}