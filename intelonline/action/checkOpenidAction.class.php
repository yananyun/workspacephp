<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class checkOpenidAction extends Action {
	public $access_token;
	public function __construct() {
		parent::__construct ();
		$wechat = new wechat ();
		$this->access_token = $wechat->getAccessToken ();
	}
	// insert a_member
	public function check() {
		$openid = trim ( $_POST ['openid'] );
		if (! $openid) {
			ajaxReturn ( 'error', 'openid is empty!', 0 );
			exit ();
		}
		if ($_POST ['key'] != 'intelonline') {
			ajaxReturn ( 'error', 'Key is not correct!', 0 );
			exit ();
		}
		$url = "https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token={$this->access_token}";
		$data ['openid'] = $openid;
		$return = $this->curlpost ( $url, $data, 3 );
		$data = json_decode ( $return, true );
		//
		if ($data ['errcode'] == '0') {
			ajaxReturn ( 'success', 'Query success', 1 );
		} else {
			ajaxReturn ( 'error', $data ['errmsg'], 0 );
		}
	}
	public function demo() {
		$_POST ['openid'] = 'omH22t2YGfh5E7sQoH-FO43P9X-o1';
		$this->check ();
	}
	/**
	 * 发起POST请求
	 *
	 * @param varchar $url
	 *        	URL链接
	 * @param array $data
	 *        	数据
	 * @return array
	 */
	public function curlpost($url, $data, $type = 1) {
		$CURL = new CurlItems ();
		if ($type == 1) {
			$return = $CURL->post ( $url, (json_encode_zh ( $data )) );
		} elseif ($type == 2) {
			$return = $CURL->post ( $url, json_encode ( $data ) );
		} elseif ($type == 3) {
			$return = $CURL->post ( $url, jsonToZh ( json_encode ( $data ) ) );
		}
		
		return $return;
	}
}

