<?php
namespace  Home\Controller;
use Think\Controller;

//商品控制器
class GoodsController extends Controller
{
	//展示产品列表
	function showList()
	{
		//控制器 调用数据模型model model将数据交给 controller 
		//controller将数据 交给模版
		$this->display("showlist");
	}
	//查看产品信息
	function showGoods()
	{
		
	}
}