<?php
/**
 * 显式授权
 *		弹框授权，用于取得非粉丝的用户属性
 * 
 * @author Harry
 * @since 2014.12.15
 */
class explicitAuthAction extends Action {
	public $wechatAction;
	public $wechat;
	public function __construct() {
		parent::__construct ( FALSE );
		$this->chkAuth ();
	}
	
	// 判断当前用户是否存在授权，如果不存在就去授权
	public function chkAuth() {
		// PC端模拟测试
		if ($_GET ['pc'] == 3) {
			$WECHAT = new wechat ();
			$temp = $WECHAT->getMember ( array (
					'openid' => 'okR04uHi50b9m2iXEnwBp9ygO0w8' 
			) );
			$_SESSION ['userInfo'] ['userInfo'] = $temp [0];
		}
		// 检测授权
		if (! isset ( $_SESSION ['auth'] )) {
			$this->chkParam ();
		}
	}
	
	// 显式授权&隐式授权
	public function chkParam() {
		if ($_GET ['code']) // 两种情况：1隐式授权返回 2显式授权方返回
{
			if ($_GET ['state'] == 'yinshi') // 隐式授权返回
{
				// 获取token信息
				$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . APPID . "&secret=" . APPSECRET . "&code=" . trim ( $_GET ['code'] ) . "&grant_type=authorization_code";
				$tokeninfo = json_decode ( curl_file_get_contents ( $url ), TRUE );
				if (! $tokeninfo ['errcode']) {
					$memInfo = $this->getUserInfo ( $tokeninfo ['openid'] ); // 从DB内查询是否存在过此用户
					if ($memInfo) // 判断系统内是否存在过此用户，若存在，则直接取用户信息做session，
{
						$_SESSION ['auth'] = TRUE;
						$_SESSION ['userinfo'] = $memInfo;
						$_SESSION ['mopenid'] = $tokeninfo ['openid'];
						$_SESSION ['tokenInfo'] = $tokeninfo;
					} else { // 若不存在，则显式授权
						$nowurl = APP_PATH . ltrim ( $_SERVER ['REQUEST_URI'], '/' );
						$callbackUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APPID . "&redirect_uri=$nowurl&response_type=code&scope=snsapi_userinfo&state=xianshi#wechat_redirect";
						header ( "Location:$callbackUrl" );
					}
				}
			} elseif ($_GET ['state'] == 'xianshi') // 显式授权返回
{
				// 获取token信息
				$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . APPID . "&secret=" . APPSECRET . "&code=" . trim ( $_GET ['code'] ) . "&grant_type=authorization_code";
				$tokeninfo = json_decode ( curl_file_get_contents ( $url ), TRUE );
				// 获取用户信息
				$userurl = "https://api.weixin.qq.com/sns/userinfo?access_token={$tokeninfo['access_token']}&openid={$tokeninfo['openid']}&lang=zh_CN";
				$userInfo = json_decode ( curl_file_get_contents ( $userurl ), TRUE );
				
				if (! $userInfo ['errcode']) {
					$_SESSION ['auth'] = TRUE;
					$_SESSION ['userinfo'] = $userInfo;
					$_SESSION ['mopenid'] = $tokeninfo ['openid'];
					$_SESSION ['tokenInfo'] = $tokeninfo;
				} else {
					$_SESSION ['userinfo'] = $_SESSION ['mopenid'] = $_SESSION ['tokenInfo'] = NULL;
				}
			}
		} else { // 啥都没有，全新用户，走隐形授权
			$nowurl = APP_PATH . ltrim ( $_SERVER ['REQUEST_URI'], '/' );
			$callbackUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APPID . "&redirect_uri=$nowurl&response_type=code&scope=snsapi_base&state=yinshi#wechat_redirect";
			header ( "Location:$callbackUrl" );
		}
	}
	//
	// //显式授权操作
	// public function auth()
	// {
	// //授权返回
	// if($_GET['state'] == 'redirectsuccess')
	// {
	// //用户同意授权
	// if($_GET['code'])
	// {
	// $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".appid."&secret=".appsecret."&code=".trim($_GET['code'])."&grant_type=authorization_code";
	// $tokeninfo = json_decode(curl_file_get_contents($url), TRUE);
	// $userurl = "https://api.weixin.qq.com/sns/userinfo?access_token={$tokeninfo['access_token']}&openid={$tokeninfo['openid']}&lang=zh_CN";
	// $userInfo = json_decode(curl_file_get_contents($userurl),TRUE);
	//
	// if(!$userInfo['errcode'])
	// {
	// $_SESSION['auth'] = TRUE;
	// $_SESSION['userinfo'] = $userInfo;
	// $_SESSION['mopenid'] = $tokeninfo['openid'];
	// $_SESSION['tokenInfo'] = $tokeninfo;
	// }else{
	// $_SESSION['userinfo'] = $_SESSION['mopenid'] = $_SESSION['tokenInfo'] = NULL;
	// }
	// }
	// else
	// //用户不同意授权
	// {
	// $_SESSION['auth'] = FALSE;
	// }
	// }
	// else
	// //跳转至授权窗口
	// {
	// $nowurl = APP_PATH.ltrim($_SERVER['REQUEST_URI'], '/');
	// $callbackUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".appid."&redirect_uri=$nowurl&response_type=code&scope=snsapi_userinfo&state=redirectsuccess#wechat_redirect";
	// header("Location:$callbackUrl");
	// }
	// }
	
	// public function openidAuth()
	// {
	// //授权返回
	// if($_GET['state'] == 'redirectsuccess')
	// {
	// //用户同意授权
	// if($_GET['code'])
	// {
	// $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".appid."&secret=".appsecret."&code=".trim($_GET['code'])."&grant_type=authorization_code";
	// $tokeninfo = json_decode(curl_file_get_contents($url), TRUE);
	// if(!$tokeninfo['errcode'])
	// {
	// return $tokeninfo['openid'];
	// }else{
	// return FALSE;
	// }
	// }
	// }else{//跳转至授权窗口
	// $nowurl = APP_PATH.ltrim($_SERVER['REQUEST_URI'], '/');
	// $callbackUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".appid."&redirect_uri=$nowurl&response_type=code&scope=snsapi_base&state=redirectsuccess#wechat_redirect";
	// header("Location:$callbackUrl");
	// }
	// }
	public function getUserInfo($openid) {
		$MEM = new member ();
		$meminfo = $MEM->getMemInfo ( array (
				'openid' => $openid 
		), TRUE );
		return $meminfo;
	}
}