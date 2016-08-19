<?php

/**
 * baseAction.class.php 前台都要调用这个基类
 * 
 * @author:Harry
 */
class baseAction extends Action {
	public $mopenid;
	public $mminfo;
	public $tokenInfo;
	public $memberId;
	public $memberInfo;
	public $memberName;
	// public $useSnsapi_userinfo = FALSE;//是否使用全授权
	public function __construct() {
		parent::__construct ();
		$this->checkUser ();
		$this->assign ( array (
				'userInfo' => $_SESSION ['userinfo'],
				'rand' => time () 
		) );
		$this->memberId = $_SESSION ['userinfo'] ['openid'];
	}
	
	/**
	 * 判断当前session中是否已获取用户信息，如果已获取则继续，如果未获取，则在完成oauth授权操作后继续。
	 */
	public function checkUser() {
		if ($_GET ['pc'] == 3) {
			// $WECHAT = new wechat();
			// $temp = $WECHAT->getMember(array('openid' => ''));
			
			$_SESSION ['auth'] = TRUE;
			$_SESSION ['userinfo'] = true;
			// $_SESSION['userinfo'] = $temp[0];
		}
		
		// $WechatAction = new wechatAction();
		if (! isset ( $_SESSION ['userinfo'] ) || empty ( $_SESSION ['userinfo'] )) { // 不存在session的时候
			if ($_GET ['state'] == 'xuyaoshouquan') { // 已经跳转回来的时候
				$tempArr = $this->getUserInfoByCode ( $_GET ['code'] );
				// $this->mopenid = $tempArr['mopenid'];
				// $this->mminfo = $tempArr['tokenInfo'];
				
				$_SESSION ['userinfo'] = $tempArr;
				$_SESSION ['openid'] = $_SESSION ['userinfo'] ['openid'];
			} else { // 不存在session，则去授权，跳转到微信获取openid
				$callback = APP_PATH . ltrim ( $_SERVER ['REQUEST_URI'], '/' );
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APPID . "&redirect_uri=" . urlencode ( $callback ) . "&response_type=code&scope=snsapi_base&state=xuyaoshouquan#wechat_redirect";
				header ( 'Location:' . $url );
			}
		}
	}
	public function getUserInfoByCode($code) {
		$wheach = new wechat ();
		$config = $wheach->config ();
		$return = array ();
		$return ['status'] = true;
		if (empty ( $code )) {
			$return ['status'] = false;
		} else {
			$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $config ['appid'] . '&secret=' . $config ['appsecret'] . '&code=' . $code . '&grant_type=authorization_code';
			
			$tokenInfo = json_decode ( curl_file_get_contents ( $url ), true );
			if (! isset ( $tokenInfo ['errcode'] )) {
				$return ['mopenid'] = $tokenInfo ['openid'];
				$return ['tokenInfo'] = $tokenInfo;
				$userinfo = $wheach->getUserInfoByOpenId ( $tokenInfo ['openid'] );
			}
		}
		if ($userinfo) {
			return $userinfo;
		} else {
			return false;
		}
	}

/**
 * 判断当前session中是否已获取用户信息，如果已获取则继续，如果未获取，则在完成oauth授权操作后继续。
 */
	// public function checkUser()
	// {
	// if($_GET['pc'] == 3)
	// {
	// $WECHAT = new wechat();
	// $temp = $WECHAT->getMember(array('openid'=>'okR04uHi50b9m2iXEnwBp9ygO0w8'));
	// $_SESSION['userInfo']['userInfo'] = $temp[0];
	// }
	// $WechatAction = new wechatAction();
	// if (!isset($_SESSION['userInfo']) || empty($_SESSION['userInfo']['userInfo']))
	// {//不存在session的时候
	// if ($_GET['state'] == 'xuyaoshouquan')//已经跳转回来的时候
	// {
	// $tempArr = $WechatAction->getUserInfoByCode($_GET['code']);
	// $this->mopenid = $tempArr['mopenid'];
	// $this->mminfo = $tempArr['tokenInfo'];
	// $_SESSION['userInfo'] = $tempArr;
	// }else{//不存在session，则去授权，跳转到微信获取openid
	// $callback = APP_PATH . ltrim($_SERVER['REQUEST_URI'], '/');
	// $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APPID . "&redirect_uri=" . urlencode($callback) . "&response_type=code&scope=snsapi_base&state=xuyaoshouquan#wechat_redirect";
	// // if ($this->useSnsapi_userinfo)
	// // {
	// // $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APPID . "&redirect_uri=" . urlencode($callback) . "&response_type=code&scope=snsapi_userinfo&state=xuyaoshouquan#wechat_redirect";
	// // }
	// header('Location:' . $url);
	// }
	// }
	// // dump($_SESSION);
	// // $this->mopenid = $_SESSION['userInfo']['mopenid'] ? $_SESSION['userInfo']['mopenid'] : $_SESSION['userInfo']['userInfo']['openid'];
	// // $this->tokenInfo = $_SESSION['userInfo']['tokenInfo'];
	// $this->memberId = $_SESSION['userInfo'] ? $_SESSION['userInfo'] : $_SESSION['userInfo']['userinfo']['openid'];
	// // $this->memberName = $_SESSION['userInfo']['userInfo']['nickname'];
	// // $this->memberInfo = $_SESSION['userInfo']['userInfo'];
	// }
}
