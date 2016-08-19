<?php
/**
 * 对外接口
 * 
 * @author Harry
 * @since 2015.3.20
 */
class apiAction extends Action {
	public $W;
	public function __construct() {
		parent::__construct ();
		$this->W = new wechatAction ();
	}
	
	/**
	 * 通过openid获取用户的基本信息
	 */
	public function getUserInfoByOpenid() {
		if (! $_GET ['openid']) {
			exit ( json_encode ( "param error" ) );
		}
		$openid = ( string ) $_GET ['openid'];
		$accesstoken = $this->W->getAccessToken ();
		$userinfo = $this->W->getUserInfoByToken ( $accesstoken, $openid );
		exit ( json_encode ( $userinfo ) );
	}
}