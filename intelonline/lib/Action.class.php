<?php

//
// _oo0oo_
// o8888888o
// 88" . "88
// (| -_- |)
// 0\ = /0
// ___/`---'\___
// .' \\| |// '.
// / \\||| : |||// \
// / _||||| -:- |||||- \
// | | \\\ - /// | |
// | \_| ''\---/'' |_/ |
// \ .-\__ '-' ___/-. /
// ___'. .' /--.--\ `. .'___
// ."" '< `.___\_<|>_/___.' >' "".
// | | : `- \`.;`\ _ /`;.`/ - ` : | |
// \ \ `_. \_ __\ /__ _/ .-` / /
// =====`-.____`.___ \_____/___.-`___.-'=====
// `=---='
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// 佛祖保佑 永无BUG 复工大吉
//
//
require_once 'CurlItems.class.php';
class Action {
	public $smarty;
	public $curl; // curl实例
	public $log; // 操作日志
	public $platform; // 平台id
	public $platformname = 'default'; // 平台英文名称
	public $baseServerUrl = '';
	public $userinfo = '';
	public $userid = 0;
	public $memberId;
	public $version_type; // 产品的版本 array('','1年','3年');
	public $pay_status; // 支付状态 array('','未支付','已支付','已取消');
	public $wx_mopenid; // 微信用户mopenid;
	public $wx_mid; // 微信用户id;
	public $system_config;
	
	/**
	 * 主要用于构造smarty相关配置
	 */
	function __construct($userLoginCtrl = FALSE) {
		$this->userinfo = Session::get ( 'userinfo' );
		$this->userid = $this->userinfo ['id'];
		$this->smarty = new Smarty (); // 建立smarty对象
		$this->smarty->caching = false; // 是否使用缓存,项目调试期不建议使用
		$this->smarty->template_dir = ROOT_PATH . "template"; // 设置模板目录
		$this->smarty->compile_dir = ROOT_PATH . "template_c"; // 设置编译目录
		$this->smarty->cache_dir = ROOT_PATH . "cache"; // 设置缓存文件夹
		$this->smarty->left_delimiter = "<{";
		$this->smarty->right_delimiter = "}>";
		$this->version_type = array (
				'',
				'1年',
				'3年' 
		);
		$this->curl = new CurlItems ();
		$this->chkLogin ( $userLoginCtrl );
		$this->assign ( 'module', System::$module );
		$this->assign ( 'action', System::$action );
	}
	public function chkLogin($val) {
		if (isset ( $_GET ['pc'] )) {
			$MemberModel = new member ();
			if ($_GET ['pc'] == 1) {
				$_SESSION ['userInfo'] ['userInfo'] = $MemberModel->getInfoByOpenid ( 'oil0zt8T8ktyeufuSDKtGZs9XChU' );
			}
		}
		
		if ($val == 1) { // 进行授权 wgs修改 2014.10.27
		                 // $_SESSION['memberInfo'] = null;
			
			if (! $_SESSION ['memberInfo']) {
				if ($_GET ['pc']) {
					$M = new member ();
					$sql = "select * from sys_member limit 1";
					$data = $M->query ( $sql );
					if ($data) {
						$_SESSION ['memberInfo'] = $data [0];
					}
				} else {
					
					$this->getInfoByOAuth (); // 微信端使用测试
				}
			}
			
			$this->memberInfo = $_SESSION ['memberInfo'];
			$this->memberId = $_SESSION ['memberInfo'] ['openid'];
			$this->assign ( 'memberInfo', $this->memberInfo );
		}
		if ($val === 2) {
			! $_SESSION ['userinfo'] ? redirect ( '/index.php/login' ) : $this->assign ( 'userinfo', $_SESSION ['userinfo'] );
			// 权限判断
			if ($_SESSION ['userinfo'] ['id'] != 1 && (System::$module) != 'login' && (System::$module) != 'norank' && (System::$module) != 'me') {
				if (! in_array ( (System::$module), $_SESSION ['userinfo'] ['rankArr'] )) {
					redirect ( '/index.php/norank' );
				}
			}
		}
	}
	
	/**
	 * 测试 使用get传递aopenid与mopenid来获取信息，写入$_SESSION['memberInfo'] 与 $_SESSION['accountInfo'];
	 * 通过传递公众账号及不同账号下的用户就可以进行多帐号不同用户的模拟了。
	 */
	private function getInfoByTest() {
		// 测试信息
		$mopenid = MOPENID;
		// 获取粉丝信息
		$wechat = new wechat ();
		$memberInfo = $wechat->getMemberInfo ( $mopenid );
		$_SESSION ['memberInfo'] = $memberInfo; // 将粉丝信息放入session中。
	}
	
	// 通过oatuh2.0获取信息
	private function getInfoByOAuth($getMore = false) {
		if (! $_SESSION ['memberInfo']) { // 不存在session的时候
			if ($_GET ['state'] == 'xuyaoshouquan' && $_GET ['code']) { // 已经跳转回来的时候
				$WechatAction = new wechatAction ();
				
				$wechat = new wechat ();
				$accountInfo = $wechat->getConf ( array (
						'openid' => AOPENID 
				) );
				$_SESSION ['accountInfo'] = $accountInfo;
				
				/* * **微信OAuth授权 开始 ********* */
				$tmp_member_info = $WechatAction->getUserInfoByCode ( $_GET ['code'], $getMore );
				$mopenid = $getMore ? $tmp_member_info ['openid'] : $tmp_member_info;
				
				// 获取粉丝信息
				$wechat = new wechat ();
				$memberInfo = $wechat->getMemberInfo ( $mopenid );
				
				// 自动添加用户
				if (! $memberInfo && $tmp_member_info) {
					if ($getMore) { // 通过
						$member = new member ();
						array_pop ( $tmp_member_info );
						$tmp_member_info ['nickname'] = filterImage ( $tmp_member_info ['nickname'] );
						$res_id = $member->addMemberInfo ( $tmp_member_info );
						$memberInfo = $member->getInfoById ( $res_id );
					} else {
						$memberInfo = $WechatAction->getUserInfoByOpenid ( $mopenid );
					}
				}
				/* * ***微信OAuth授权 结束 ********* */
				
				$_SESSION ['memberInfo'] = $memberInfo; // 将粉丝信息放入session中。
			} else { // 不存在session，则去授权，跳转到微信获取openid
				$wechat = new wechat ();
				$accountInfo = $wechat->getConf ( array (
						'openid' => AOPENID 
				) );
				$_SESSION ['accountInfo'] = $accountInfo;
				$callback = APP_PATH . ltrim ( $_SERVER ['REQUEST_URI'], '/' );
				$scope = $getMore ? "snsapi_userinfo" : "snsapi_base"; // 获取openid
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APPID . "&redirect_uri=" . urlencode ( $callback ) . "&response_type=code&scope=" . $scope . "&state=xuyaoshouquan#wechat_redirect";
				header ( 'Location: ' . $url );
				exit ();
			}
		}
	}
	
	/**
	 * 调用模板
	 *
	 * @param string&null $tpl        	
	 */
	function display($tpl = null) {
		if ($tpl == null) {
			$tpl = System::$module . '/' . System::$action . '.html';
		}
		$this->smarty->display ( $tpl );
	}
	
	/**
	 * 调用管理平台模板
	 *
	 * @param string&null $tpl        	
	 */
	function adminDisplay($tpl = null) {
		if ($tpl == null) {
			$tpl = 'admin/' . System::$module . '/' . System::$action . '.html';
		}
		$this->smarty->display ( $tpl );
	}
	
	/**
	 * 给前台模板赋值
	 *
	 * @param string&array $key        	
	 * @param string $val        	
	 */
	function assign($key, $val = null) {
		if (is_array ( $key )) {
			$this->smarty->assign ( $key );
		} else {
			$this->smarty->assign ( $key, $val );
		}
	}
	
	/**
	 * 二次封装分页类 GX
	 *
	 * @param int $num
	 *        	总条目数
	 * @param int $p
	 *        	得到当前是第几页
	 * @param int $page_size
	 *        	每页显示条数 默认为20
	 * @param int $sub_pages
	 *        	每次显示的页数 默认为10
	 * @return str
	 */
	public function pages($num, $p, $page_size = 20, $sub_pages = 10, $block = '0') {
		$Url = $_SERVER ['REQUEST_URI'];
		$prefix = substr ( time (), 0, 2 );
		$suburl = strpos ( $Url, "?_=$prefix" ) ? strpos ( $Url, "?_=$prefix" ) : strpos ( $Url, "&_=$prefix" );
		if ($suburl) {
			$Url = substr ( $Url, 0, $suburl );
		}
		$UrlCount = strpos ( $Url, '?' );
		$Url = preg_replace ( '/(\/|&)*p(=|\/)[\d]\d*(\&|)/', '', $Url );
		// $Link = substr($Url, -1, 1);
		$Link = strpos ( $Url, '&' );
		
		if ($UrlCount) {
			$Url = $Link ? $Url . '&p=' : $Url . 'p=';
		} else {
			$Url = $Url . '?p=';
		}
		
		// $subPages = new SubPages($page_size, $num, $p > 100 ? 100 : $p, $sub_pages, $Url, 2);
		$subPages = new SubPages ( $page_size, $num, $p, $sub_pages, $Url, 2 );
		
		$pages = $subPages->ajaxSubPage ( $block );
		return $pages;
	}
	
	/**
	 * 二次封装分页类 王高尚
	 *
	 * @param int $num
	 *        	总条目数
	 * @param int $p
	 *        	得到当前是第几页
	 * @param int $page_size
	 *        	每页显示条数 默认为20
	 * @param int $sub_pages
	 *        	每次显示的页数 默认为10
	 * @param string $type
	 *        	1 刷新分页 2 ajax分页
	 * @param string $block
	 *        	返回信息的控制器的id值
	 * @return str
	 */
	function newPages($num, $p, $page_size = 10, $type = 1, $block = '0', $sub_pages = 10) {
		$Url = $_SERVER ['REQUEST_URI'];
		$suburl = strpos ( $Url, "?_=14" ) ? strpos ( $Url, "?_=14" ) : strpos ( $Url, "&_=14" );
		if ($suburl) {
			$Url = substr ( $Url, 0, $suburl );
		}
		$UrlCount = strpos ( $Url, '?' );
		$Url = preg_replace ( '/(\/|&)*p(=|\/)[\d]\d*(\&|)/', '', $Url );
		// $Link = substr($Url, -1, 1);
		$Link = strpos ( $Url, '&' );
		
		if ($UrlCount) {
			$Url = $Link ? $Url . '&p=' : $Url . 'p=';
		} else {
			$Url = $Url . '?p=';
		}
		
		// $subPages = new SubPages($page_size, $num, $p > 100 ? 100 : $p, $sub_pages, $Url, 2);
		$subPages = new SubPages ( $page_size, $num, $p, $sub_pages, $Url, 2 );
		if ($type == 2) {
			$pages = $subPages->ajaxSubPage ( $block );
		} elseif ($type == 1) {
			$pages = $subPages->subPageCss2 ();
		}
		return $pages;
	}
	
	/**
	 * 二次封装分页类 GX
	 *
	 * @param int $num
	 *        	总条目数
	 * @param int $p
	 *        	得到当前是第几页
	 * @param int $page_size
	 *        	每页显示条数 默认为20
	 * @param int $sub_pages
	 *        	每次显示的页数 默认为10
	 * @return str
	 */
	public function searchPages($num, $p, $page_size = 20, $sub_pages = 10, $block = '0', $stor_id = 0) {
		$Url = $_SERVER ['REQUEST_URI'];
		$suburl = strpos ( $Url, "?_=13" ) ? strpos ( $Url, "?_=13" ) : strpos ( $Url, "&_=13" );
		if ($suburl) {
			$Url = substr ( $Url, 0, $suburl );
		}
		$UrlCount = strpos ( $Url, '?' );
		$Url = preg_replace ( '/(\/|&)*p(=|\/)[\d]\d*(\&|)/', '', $Url );
		// $Link = substr($Url, -1, 1);
		$Link = strpos ( $Url, '&' );
		
		if ($UrlCount) {
			$Url = $Link ? $Url . '&p=' : $Url . 'p=';
		} else {
			$Url = $Url . '?p=';
		}
		if ($stor_id) {
			$Url .= "&stor_id={$stor_id}";
		}
		
		// $subPages = new SubPages($page_size, $num, $p > 100 ? 100 : $p, $sub_pages, $Url, 2);
		$subPages = new SubPages ( $page_size, $num, $p, $sub_pages, $Url, 2 );
		
		$pages = $subPages->ajaxSubPage ( $block );
		return $pages;
	}
	
	/**
	 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
	 * showmessage('登录成功', array('默认跳转地址'=>'http://www.phpcms.cn'));
	 * 
	 * @param string $msg
	 *        	提示信息
	 * @param int $imgurl
	 *        	笑脸图片 1 sad 2 happy
	 * @param mixed(string/array) $url_forward
	 *        	跳转地址
	 * @param int $ms
	 *        	跳转等待时间 (秒)
	 */
	public function showmessage($msg, $url_forward, $imgurl = 1, $ms = 3) {
		$url_forward = $url_forward ? $url_forward : 'goback';
		$img_url = $imgurl === 1 ? '/img/ku.png' : '/img/xiao.png';
		$this->assign ( array (
				'msg' => $msg,
				'url_forward' => $url_forward,
				'ms' => $ms,
				'img_url' => $img_url 
		) );
		$this->display ( SHOWMSG_PATH );
		exit ();
	}
	public function errorPage($msg) {
		exit ( $msg );
	}
}
