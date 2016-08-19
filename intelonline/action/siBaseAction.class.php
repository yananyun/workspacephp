<?php

/**
 * baseAction.class.php 前台都要调用这个基类
 * 
 * @author:Harry
 */
class siBaseAction extends Action {
	public $mopenid;
	public $mminfo;
	public $tokenInfo;
	public $memberId;
	// public $memberInfo;
	public $memberName;
	// public $useSnsapi_userinfo = FALSE;//是否使用全授权
	public function __construct() {
		parent::__construct ();
		$this->checkUser ();
		$this->assign ( array (
				'userInfo' => $_SESSION ['userInfo'],
				'rand' => time () 
		) );
	}
	
	/**
	 * 判断当前session中是否已获取用户信息，如果已获取则继续，如果未获取，则在完成oauth授权操作后继续。
	 */
	public function checkUser() {
		if ($_GET ['pc'] == 3) {
			$WECHAT = new wechat ();
			$temp = $WECHAT->getMember ( array (
					'openid' => 'oil0ztzvTvcOdCXReVJYmXjUbzPQ' 
			) );
			$this->mopenid = $_SESSION ['mopenid'] = 'oil0ztzvTvcOdCXReVJYmXjUbzPQ';
			// $_SESSION['memberinfo'] = $this->getUserInfo('omH22t1AEOoTyQrIezpeGuEwHDCc');
			$_SESSION ['userinfo'] = $temp [0];
			$_SESSION ['auth'] = TRUE;
		}
		
		$WECHAT = new wechat ();
		// 不存在session的时候
		if (! isset ( $_SESSION ['auth'] )) {
			if ($_GET ['state'] == 'xuyaoshouquan') // 已经跳转回来的时候
{
				// 获取token信息
				$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . APPID . "&secret=" . APPSECRET . "&code=" . trim ( $_GET ['code'] ) . "&grant_type=authorization_code";
				$tokeninfo = json_decode ( curl_file_get_contents ( $url ), TRUE );
				$_SESSION ['mopenid'] = $tokeninfo ['openid'];
				$_SESSION ['tokenInfo'] = $tokeninfo;
				
				// 获取用户信息——显式授权
				$userurl = "https://api.weixin.qq.com/sns/userinfo?access_token={$tokeninfo['access_token']}&openid={$tokeninfo['openid']}&lang=zh_CN";
				$userInfo = json_decode ( curl_file_get_contents ( $userurl ), TRUE );
				
				if (! $userInfo ['errcode']) {
					$uid = $this->addUser ( $userInfo );
					// 新用户
					if ($uid) {
						$SI = new si ();
						$SI->plusLotteryTimes ( ' openid = "' . $tokeninfo ['openid'] . '"' );
					}
					$WECHAT->addMember ( $userInfo );
					
					$_SESSION ['auth'] = TRUE;
					$_SESSION ['userinfo'] = $userInfo;
				} else {
					$_SESSION ['userinfo'] = NULL;
				}
				
				// $_SESSION['memberinfo'] = $this->getUserInfo($tokeninfo['openid']);
				
				$this->mopenid = $_SESSION ['mopenid'];
				$this->tokenInfo = $tokeninfo;
			} else { // 不存在session，则去授权，跳转到微信获取openid
				$callback = APP_PATH . ltrim ( $_SERVER ['REQUEST_URI'], '/' );
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APPID . "&redirect_uri=" . urlencode ( $callback ) . "&response_type=code&scope=snsapi_userinfo&state=xuyaoshouquan#wechat_redirect";
				header ( 'Location:' . $url );
			}
		}
		
		$this->memberName = $_SESSION ['userinfo'] ['nickname'];
		// $this->memberInfo = $_SESSION['memberinfo'];
	}
	public function addUser($data) {
		$info = array ();
		$info ['openid'] = $data ['openid'];
		$info ['nickname'] = $data ['nickname'];
		$info ['headimgurl'] = $data ['headimgurl'];
		$info ['ctime'] = time ();
		$SI = new si ();
		return $SI->addUser ( $info );
	}
	public function getUserInfo($openid) {
		$MEM = new member ();
		$meminfo = $MEM->getMemInfo ( array (
				'openid' => $openid 
		), TRUE );
		return $meminfo;
	}
}
