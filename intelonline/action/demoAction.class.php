<?php
/**
 * 产品管理
 * @author wgs  2014.6.12
 */
class demoAction extends Action {
	public $demo;
	public function __construct() {
		parent::__construct ( 1 );
		$this->demo = new demo ();
	}
	public function upload() {
		$this->display ();
	}
	public function func() {
		$a = "\"'他是个“好孩子”呢，‘你’说是吧？'\"<br/>";
		echo $a;
		$b = htmlspecialchars ( $a );
		echo $b;
		$c = htmlspecialchars ( $a, ENT_QUOTES );
		echo $c;
		$d = htmlspecialchars_decode ( $c );
		echo $d;
		$e = htmlspecialchars_decode ( $c, ENT_QUOTES );
		echo $e;
	}
	public function test() {
		dump ( $_SESSION );
		dump ( 'abc' );
	}
	public function error() {
		$this->showmessage ( '错误啦' );
		exit ();
	}
	public function orderList() {
		$productCenter = new productCenter ();
		$order_list = $productCenter->getOrderListByMid ( array (
				'mid' => 16 
		) );
		// order_type 1：普通订单 2：预定订单
		p ( $order_list );
	}
	public function test_add() {
		$demo = new demo ();
		$id = $demo->addTestData ( array (
				'content' => 'adfasdfasdfs',
				'type' => '4' 
		) );
		$id ? exit ( '写入成功,id为：' . $id ) : exit ( '写入失败' );
	}
	public function oauthLoad() {
		require_once ROOT_PATH . 'lib/WxPayPubHelper/WxPayPubHelper.php';
		
		// 使用jsapi接口
		$jsApi = new JsApi_pub ();
		
		// =========步骤1：网页授权获取用户openid============
		// 通过code获得openid
		if (! isset ( $_GET ['code'] )) {
			// 触发微信返回code码
			$url = $jsApi->createOauthUrlForCode ( WxPayConf_pub::JS_API_CALL_URL );
			Header ( "Location: $url" );
		} else {
			// 获取code码，以获取openid
			$code = $_GET ['code'];
			$jsApi->setCode ( $code );
			$jsApi->getOpenId ();
			$memberInfo = $jsApi->getMemberInfo ();
			$this->demo->addTestData ( array (
					'content' => json_encode ( $memberInfo ),
					'type' => '4' 
			) );
		}
	}
	// insert a_member
	public function add_member() {
		echo 1;
		$openid = $this->model->getOpenid ();
		dump ( $openid );
		exit ();
		foreach ( $openid as $k => $v ) {
			dump ( $v ['fromusername'] );
			$member = $this->model->bySql ( 'sys_member', "openid='{$v['fromusername']}'" );
			dump ( $member );
			exit ();
		}
	}
}