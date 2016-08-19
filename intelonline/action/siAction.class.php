<?php

/**
 * 英特尔中国在线——SI活动
 * 
 * @author Harry
 * @since 2015.3.13
 */
class siAction extends siBaseAction {
	public $mopenid;
	public $nickname;
	public $headimgurl;
	public $M;
	public function __construct() {
		parent::__construct ();
		$this->M = new si ();
		$this->mopenid = $_SESSION ['mopenid'];
		$this->nickname = $_SESSION ['userinfo'] ['nickname'];
		$this->headimgurl = $_SESSION ['userinfo'] ['headimgurl'];
		$this->assign ( 'openid', $this->mopenid );
		$this->assign ( 'title', '英特尔硬享公社活动来袭' );
		$this->assign ( 'url', 'http://intelonline.buzzopt.com/index.php/si/index/sourceopenid/' . $this->mopenid );
	}
	
	// 主页
	public function index() {
		// 若自朋友圈跳转而来的，则给源用户增加一次repost机会
		if ($_GET ['sourceopenid']) {
			$sourceopenid = $_GET ['sourceopenid']; // 通过某个用户分享而来的
			$nowopenid = $this->mopenid; // 当前用户的OPENID
			$this->addRepostTimes ( $sourceopenid, $nowopenid );
		}
		
		// $info = array(
		// 'openid' => $this->mopenid,
		// 'nickname' => $_SESSION['userinfo']['nickname']? filterImage($_SESSION['userinfo']['nickname']): filterImage($_SESSION['memberinfo']['nickname']),
		// 'headimgurl'=> $_SESSION['userinfo']['headimgurl']?$_SESSION['userinfo']['headimgurl']:$_SESSION['memberinfo']['headimgurl'],
		// 'ctime' => time(),
		// );
		//
		// $this->M->addUser($info);
		
		$this->display ( 'wawaji/Guide.html' );
	}
	
	/**
	 * 点击开始游戏
	 * 判断用户是否在排队表里面，没有的就入库处理，生成orderid
	 */
	public function enterTheGame() {
		
		// 判断库里有没有该用户
		$user = $this->upOrderTime ();
		$orderList = $this->M->getOrderList ();
		foreach ( $orderList as $k => $v ) {
			if ($this->mopenid == $v ['openid']) {
				$order = $k + 2;
			}
		}
		$this->assign ( array (
				'uopenid' => $this->mopenid,
				'order' => $order 
		) );
		$this->display ( 'doll/show.html' );
	}
	public function upOrderTime() {
		$ordersql = "select max(queuing) queuing from si_queue";
		$ordernum = $this->M->bysql ( 'si_queue', $ordersql );
		$ordernum = $ordernum ['0'] ['queuing'] + 1;
		$user = $this->M->orderStatusByOpenid ( $this->mopenid );
		if (! $user) {
			// 没有该用户就入库操作
			$info ['openid'] = $this->mopenid;
			$info ['nickname'] = $this->nickname;
			$info ['headimgurl'] = $this->headimgurl;
			$info ['status'] = '2';
			$info ['queuing'] = $ordernum;
			$info ['time'] = time (); // 用户在线时间依据
			$id = $this->M->addData ( 'si_queue', $info );
			$user = $this->M->orderStatusById ( $id );
		} else {
			$info ['openid'] = $this->mopenid;
			$info ['nickname'] = $this->nickname;
			$info ['headimgurl'] = $this->headimgurl;
			$info ['status'] = '2';
			if ($user ['time'] < time () - 20) {
				$info ['queuing'] = $ordernum;
			}
			$info ['time'] = time (); // 用户在线时间依据
			$id = $this->M->upData ( 'si_queue', "openid='{$this->mopenid}'", $info );
			$user = $this->M->orderStatusByOpenid ( $this->mopenid );
		}
		return $user;
	}
	
	// 判断是否到自己玩游戏了
	public function play() {
		$openid = trim ( $_POST ['openid'] );
		$first_openid = $this->M->getFirstOrder ();
		$status = $this->game_is_empty ();
		
		$orderList = $this->M->getOrderList ();
		foreach ( $orderList as $k => $v ) {
			if ($this->mopenid == $v ['openid']) {
				$order = $k + 2;
			}
		}
		
		if ($status) {
			if ($openid == $first_openid) {
				switch ($status) {
					case 0 :
						$this->upOrderTime ();
						ajaxReturn ( $order, '机器繁忙！', 0 );
						break;
					case 1003 :
						$this->gogame ();
						// redirect("http://vm.tbswx.com/pay/mobile/doll/index.php?openid={$openid}&deviceid=1002");
						ajaxReturn ( "http://vm.tbswx.com/pay/mobile/doll/index.php?openid={$openid}&deviceid=1003", 'success', 1 );
						break;
					case 1004 :
						$this->gogame ();
						// redirect("http://vm.tbswx.com/pay/mobile/doll/index.php?openid={$openid}&deviceid=1003");
						ajaxReturn ( "http://vm.tbswx.com/pay/mobile/doll/index.php?openid={$openid}&deviceid=1004", 'success', 1 );
						break;
					default :
						$this->upOrderTime ();
						ajaxReturn ( $order, '机器繁忙！', 0 );
						break;
				}
			} else {
				$this->upOrderTime ();
				ajaxReturn ( $order, '还没有到你哦！', 0 );
			}
		} else {
			$this->upOrderTime ();
			ajaxReturn ( $order, '机器繁忙！', 0 );
		}
	}
	public function gogame() {
		$info ['is_play'] = '2';
		$this->M->upData ( 'si_signup', "openid='{$this->mopenid}'", $info );
	}
	
	// 判断娃娃机是否有空闲
	public function game_is_empty() {
		$url = "http://vm.tbswx.com/pay/mobile/controllers/isBusiness.php";
		$result = curl_file_get_contents ( $url );
		if ($result == 0) {
			return 0;
		} else {
			return $result;
		}
	}
	
	// 抓娃娃开始游戏
	// public function is_play($openid) {
	//
	// $status = $this->game_is_empty();
	// switch ($status) {
	// case 0:
	// ajaxReturn('', '机器繁忙！', 0);
	// break;
	// case 1002:
	// ajaxReturn("http://vm.tbswx.com/pay/mobile/doll/index.php?openid={$this->openid}&deviceid=1002", 'success', 1);
	// break;
	// case 1003:
	// ajaxReturn("http://vm.tbswx.com/pay/mobile/doll/index.php?openid={$this->openid}&deviceid=1003", 'success', 1);
	// break;
	// default:
	// ajaxReturn('', '机器繁忙！', 0);
	// break;
	// }
	// $this->M->siGame($this->mopenid);
	// //跳转到玩游戏的页面
	// }
	// 活动介绍
	public function huodong() {
		$this->display ();
	}
	
	// 项目介绍
	public function rules() {
		$this->display ();
	}
	
	// 活动细则
	public function rules2() {
		$this->display ();
	}
	
	// 填写信息
	public function form() {
		$info = $this->M->getSignUp ( $this->mopenid );
		if ($info) {
			redirect ( '/index.php/si/info/' );
		}
		$this->assign ( 'up', 4 );
		$this->display ();
	}
	public function doAdd() {
		$up = ( int ) $_POST ['up']; // 若为1，则update 若为4，则insert
		$website = $_POST ['website'] ? 'http://' . str_replace ( array (
				'http://',
				'https://' 
		), '', ( string ) $_POST ['website'] ) : '';
		if (! pregEmail ( $_POST ['email'] )) {
			ajaxReturn ( '', "请检查Email格式是否正确", FALSE );
		}
		if (! $this->pregMobile ( $_POST ['mobile'] )) {
			ajaxReturn ( '', "请检查手机号是否正确", FALSE );
		}
		$info = array (
				'openid' => $this->mopenid,
				'identity' => $_POST ['identity'],
				'companyname' => ( string ) $_POST ['companyname'],
				'website' => $website,
				'companysize' => ( string ) $_POST ['companysize'],
				'sale' => ( string ) $_POST ['sale'],
				'industry' => ( string ) $_POST ['industry'],
				'rdnum' => ( string ) $_POST ['rdnum'],
				'salesnum' => ( string ) $_POST ['salesnum'],
				'workernum' => ( string ) $_POST ['workernum'],
				'productdescription' => ( string ) $_POST ['productdescription'],
				'productimg' => ( string ) $_POST ['productimg'],
				'expectedsales' => ( string ) $_POST ['expectedsales'],
				'name' => ( string ) $_POST ['name'],
				'mobile' => ( string ) $_POST ['mobile'],
				'title' => ( string ) $_POST ['title'],
				'email' => ( string ) $_POST ['email'],
				'address' => ( string ) $_POST ['address'],
				'ctime' => time () 
		);
		if ($up == 1) {
			$info ['type'] = 1; // 切换成待审核状态
			unset ( $info ['openid'] );
			$this->M->upSighup ( $info, "openid='{$this->mopenid}'" );
			ajaxReturn ( 'up', "", TRUE );
		} else {
			$return = $this->M->addSighup ( $info ); // 录入报名信息
			$WECHAT = new wechat ();
			$WECHAT->putMsg ( $this->mopenid, "text", array (
					'content' => '恭喜，您已完成项目填报，我们将在2个工作日内完成审核。您可回复【审核状态】了解项目审核进度。' 
			) );
			unset ( $WECHAT );
			if ($return) {
				$this->M->becomeSi ( $this->mopenid ); // 数据库修改为SI用户，增加1次抽奖机会。
				ajaxReturn ( 'add', "", TRUE );
			} else {
				ajaxReturn ( 'add', "", FALSE );
			}
		}
	}
	public function success() {
		$this->assign ( 'source', $_GET ['from'] );
		$this->display ();
	}
	
	// 抽奖页面
	public function lottery() {
		// 已废弃，所有分享都进index
		// 若自朋友圈跳转而来的，则给源用户增加一次repost机会
		// if($_GET['sourceopenid'])
		// {
		// $sourceopenid = (string)$_GET['sourceopenid'];//通过某个用户分享而来的
		// $nowopenid = $this->mopenid;//当前用户的OPENID
		// $this->addRepostTimes($sourceopenid,$nowopenid);
		// }
		$userinfo = $this->M->getInfo ( array (
				'openid' => $this->mopenid 
		), TRUE );
		
		$lastRepostTimes = 10 - $userinfo ['reposttimes'] % 10;
		$lastGiftTotal = $this->M->getLastGiftTotal ();
		
		$this->assign ( 'userinfo', $userinfo ); // 用户基本信息
		$this->assign ( 'lastRepostTimes', $lastRepostTimes ); // 距离下次抽奖还要多少个朋友点击
		$this->assign ( 'lastGiftTotal', $lastGiftTotal ); // 剩余可用奖品
		$this->display ();
	}
	
	/**
	 * 增加转发次数
	 *
	 * @param type $sourceopenid
	 *        	//通过某个用户分享而来的
	 * @param type $nowopenid
	 *        	//当前用户的OPENID
	 */
	private function addRepostTimes($sourceopenid, $nowopenid) {
		if (! $sourceopenid || ! $nowopenid) {
			return FALSE;
		}
		$md = md5 ( $sourceopenid . $nowopenid );
		$chk = $this->M->chkLog ( $md );
		if (! $chk) {
			$info = array (
					'sourceopenid' => $sourceopenid,
					'vistopenid' => $nowopenid,
					'md5' => $md,
					'ctime' => time () 
			);
			$this->M->addLog ( $info ); // 增加转发日志
			$userinfo = $this->M->getInfo ( array (
					'openid' => $sourceopenid 
			), TRUE ); // 获取源用户的用户信息
			
			$upInfo = array ();
			$upInfo ['reposttimes'] = $userinfo ['reposttimes'] + 1;
			if ($upInfo ['reposttimes'] >= 10) {
				$upInfo ['reposttimes'] = $upInfo ['reposttimes'] - 10;
				$upInfo ['lastlotterytimes'] = $userinfo ['lastlotterytimes'] + 1;
			}
			// 更新用户信息
			$this->M->upUserInfo ( $upInfo, array (
					'id' => $userinfo ['id'] 
			) );
		}
	}
	
	// 抽奖操作
	public function getLottery() {
		set_time_limit ( 20 );
		$userinfo = $this->M->getInfo ( array (
				'openid' => $_SESSION ['mopenid'] 
		), TRUE );
		
		// 若存在抽奖次数
		if ($userinfo ['lastlotterytimes'] > 0) {
			// 减少用户的抽奖次数
			$this->M->cutLotteryTimes ( $this->mopenid );
			$status = rand ( 1, 10 );
			if ($status <= 3) {
				$rand = rand ( 1, 5 ); // 0未中奖 1时光网电影票 2麦当劳代金券 3物美电子券 4星巴克咖啡券 5好利来蛋糕券
			} else {
				$rand = 0;
			}
			// 控制用户只能中奖一次
			$temp = $this->M->getData ( 'si_lottery_log', "openid='{$this->mopenid}' and type != 0" );
			if ($temp) {
				$rand = 0;
			}
			
			// 未关注用户不能中奖
			$olinfo = $this->M->getUserInfo ( $this->mopenid );
			if ($olinfo ['subscribe'] != 1) {
				$rand = 0;
			}
			
			// 判断当日该礼品是否已到上限
			$rand = $this->chkGift ( $rand );
			// 未中奖
			$rand = 0; // 把中奖概率设置为0
			if ($rand == 0) {
				// 写入抽奖日志，0为未中奖
				$this->addLotteryLog ( $this->mopenid, 0 );
				ajaxReturn ( '4', "您没有中奖", TRUE ); // 4是“谢谢参与”时，指针所在的位置
			} else {
				// 时光网电影票，礼品未到
				// if($rand == 1)
				// {
				// //写入抽奖日志，0为未中奖
				// $this->addLotteryLog($this->mopenid, 0);
				// ajaxReturn('4', "您没有中奖", TRUE);//4是“谢谢参与”时，指针所在的位置
				// }
				
				if (in_array ( $rand, array (
						1,
						2,
						3,
						4,
						5 
				) )) {
					$giftInfo = $this->M->getGiftInfo ( array (
							'type' => $rand,
							'status' => 1 
					), TRUE );
					if ($giftInfo) {
						// 写入中奖日志，0为未中奖
						$gid = $this->addLotteryLog ( $this->mopenid, $rand );
						// 修改奖品状态
						$this->M->updateGiftInfo ( array (
								'status' => 2,
								'openid' => $this->mopenid,
								'ctime' => time () 
						), array (
								'id' => $giftInfo ['id'] 
						) );
						
						// 电子券
						if ($rand == 1 || $rand == 3) {
							// 微信H5下发中奖消息
							// if($rand == 1){
							// $centent = "恭喜您！你已获得{$this->getGiftByType($rand)}一张，电子兑换号为{$giftInfo['number']},密码为{$giftInfo['code']}。您可通过微信菜单“火热招募”—“我的主页”查看中奖情况。";
							// }else{
							// $centent = "恭喜您！你已获得{$this->getGiftByType($rand)}一张，电子兑换码为{$giftInfo['code']}。您可通过微信菜单“火热招募”—“我的主页”查看中奖情况。";
							// }
							// $this->sendWeixinMsg($this->mopenid, $centent);
							// $this->sendWeixinMsg($this->mopenid, '恭喜您，你已获得'.$this->getGiftByType($rand).'一张，电子兑换码'.$giftInfo['code'].'。您可通过微信菜单“SI Program”—“我的SI”查看中奖情况及相应电子码。');
							// 返回中奖信息
							ajaxReturn ( $this->getHTMLCode ( $rand ), $giftInfo ['code'], TRUE );
						} else { // 实体券，需填写收货信息
						         // 微信H5下发中奖消息
						         // $centent = "恭喜您！你已获得{$this->getGiftByType($rand)}一张。为保证奖品发放顺利，请点击“火热招募”—“我的主页”及时查看中奖情况及地址提交、礼品发放等信息。如显示未成功提交，请直接在微信号以“姓名+通讯地址+手机号”格式回复消息。
						         // 注：如未能在活动截止日期前（4月21日）成功提交邮寄信息，将被视为自动放弃。";
						         // $this->sendWeixinMsg($this->mopenid, $centent);
						         // $this->sendWeixinMsg($this->mopenid,'恭喜您！你已获得'.$this->getGiftByType($rand).'一张。为保证奖品发放顺利，请点击“SI Program”—“我的SI”及时查看中奖情况及地址提交、礼品发放等信息。如显示未成功提交，请直接在微信号以“姓名+通讯地址+手机号”格式回复消息。');
						         // 返回中奖信息
							ajaxReturn ( $this->getHTMLCode ( $rand ), $gid, TRUE );
						}
					} else {
						// 写入抽奖日志，0为未中奖
						$this->addLotteryLog ( $this->mopenid, 0 );
						ajaxReturn ( '4', "您没有中奖", TRUE ); // 4是“谢谢参与”时，指针所在的位置
					}
				}
			}
		} else {
			ajaxReturn ( '4', "SORRY 您当前没有可用的抽奖机会", FALSE ); // 4是“谢谢参与”时，指针所在的位置
		}
	}
	
	/**
	 * 实体券中奖后，填写收货信息
	 */
	public function upAddress() {
		$info = array (
				'name' => ( string ) $_POST ['name'],
				'address' => ( string ) $_POST ['address'],
				'mobile' => ( string ) $_POST ['mobile'] 
		);
		$this->M->upData ( 'si_lottery_log', array (
				'openid' => $this->mopenid 
		), $info );
	}
	
	// 感谢参与
	public function thanks() {
		$this->assign ( 'url', 'http://intelonline.buzzopt.com/index.php/si/lottery/sourceopenid/' . $this->mopenid );
		$this->display ();
	}
	
	// 写入中奖/抽奖日志
	private function addLotteryLog($openid, $type) {
		$info = array ();
		$info ['openid'] = $openid;
		$info ['type'] = $type;
		$info ['ctime'] = time ();
		
		return $this->M->addData ( 'si_lottery_log', $info );
	}
	private function sendWeixinMsg($openid, $content) {
		$WECHAT = new wechat ();
		$WECHAT->putMsg ( $openid, 'text', array (
				'content' => $content 
		) );
	}
	
	/**
	 * 根据type类型获取对应奖品文案
	 * 
	 * @param int $type
	 *        	1时光网电影票 2麦当劳代金券 3物美电子券 4星巴克咖啡券 5好利来蛋糕券
	 * @return string
	 */
	private function getGiftByType($type) {
		switch ($type) {
			case 1 :
				$g = '时光网电影票';
				break;
			case 2 :
				$g = '麦当劳代金券';
				break;
			case 3 :
				$g = '物美超市电子券';
				break;
			case 4 :
				$g = '星巴克咖啡券';
				break;
			case 5 :
				$g = '好利来蛋糕券';
				break;
		}
		return $g;
	}
	
	/**
	 * 判断当前礼品是否已满足当日礼品发送上限
	 *
	 * @param int $type
	 *        	礼品类型
	 * @return int
	 */
	private function chkGift($type) {
		if ($type == 0) {
			return 0;
		}
		// 获取当日已领取礼品数量
		$todayCount = $this->M->getCount ( 'si_lottery_log', ' type = ' . $type . ' AND ctime > ' . strtotime ( 'today' ) );
		$timeinfo = getdate ();
		$mday = $timeinfo ['mday'];
		// 获取当前礼品的当日配额
		$conf = $this->M->getData ( 'si_lottery_conf', array (
				'gtype' => $type,
				'mday' => $mday 
		), TRUE );
		
		// 没有设定的话，直接中奖~！！！！！！
		// if (!$conf) {
		// return $type;
		// }
		
		if ($todayCount >= $conf ['total']) {
			return 0;
		}
		
		return $type;
	}
	
	// 通过奖品类型得到大转盘指针位置
	private function getHTMLCode($rand) {
		switch ($rand) {
			case 0 : // 未中奖
				return 4;
			case 1 : // 时光网电影票
				return 5;
			case 2 : // 麦当劳代金券
				return 0;
			case 3 : // 物美电子券
				return 1;
			case 4 : // 星巴克咖啡券
				return 2;
			case 5 : // 好利来蛋糕券
				return 3;
		}
	}
	
	// 图片上传
	public function uploadImage() {
		$upPath = ROOT_PATH . 'upload/image/';
		if (! in_array ( $_FILES ['fileUp'] ['type'], array (
				'image/jpeg',
				'image/gif' 
		) )) {
			echo '41';
			exit (); // wrong type
				      // ajaxReturn('', '4.1', TRUE);
		}
		$upName = md5 ( $_FILES ['fileUp'] ['tmp_name'] . time () . rand ( 0, 999999 ) );
		$pathinfo = pathinfo ( $_FILES ['fileUp'] ['name'] );
		$return = move_uploaded_file ( $_FILES ['fileUp'] ['tmp_name'], $upPath . $upName . '.' . $pathinfo ['extension'] );
		// $return = move_uploaded_file($_FILES['fileUp']['tmp_name'], $upPath . $upName . '.' . $pathinfo['extension']);
		if ($return) {
			$imgUrl = APP_PATH . "upload/image/" . $upName . '.' . $pathinfo ['extension'];
			echo $imgUrl;
			exit ();
			// ajaxReturn($imgUrl, 'success', TRUE);
		} else {
			echo '42';
			exit ();
			// ajaxReturn('', '4.2', FALSE);
		}
	}
	
	// 验证手机
	public function chkMobile() {
		// if($_COOKIE['lastsendtime'] && $_COOKIE['lastsendtime'] < (time() - 60))
		// {
		// ajaxReturn('', '一分钟只能发送一次哦~请稍后再试', FALSE);
		// }
		if (! $_GET ['mobile']) {
			ajaxReturn ( '', '请输入手机号', FALSE );
		}
		$mobile = $_GET ['mobile'] ? ltrim ( $_GET ['mobile'], '0' ) : '15652229779';
		if (! $this->pregMobile ( $mobile )) {
			ajaxReturn ( '', '手机格式不正确', FALSE );
		}
		$randNum = rand ( 100000, 999999 );
		
		$content = "验证码：$randNum 不要告诉任何人哦~！";
		$this->sendSMS ( $mobile, $content );
		setcookie ( 'lastsendtime', time (), time () + 1800 );
		ajaxReturn ( $randNum, "发送成功，请注意查收短信内容", TRUE );
	}
	
	/**
	 * 发送验证短信
	 *
	 * @param type $mobile
	 *        	手机号
	 * @param type $content
	 *        	短信内容
	 * @return boolean
	 */
	private function sendSMS($mobile, $content) {
		// $url = "http://sms.1xinxi.cn/asmx/smsservice.aspx?name=18610475309&pwd=EA122A24589CEE069D2D34210949&content=" . urlencode($content) . "&mobile=$mobile&sign=英特尔中国在线&type=pt&extno=001";
		$url = "http://web.1xinxi.cn/asmx/smsservice.aspx?name=18610475309&pwd=EA122A24589CEE069D2D34210949&content=" . urlencode ( $content ) . "&mobile=$mobile&sign=英特尔中国在线&type=pt&extno=001";
		$return = curl_file_get_contents ( $url );
		if ($return [0] === '0') {
			return TRUE;
		}
		return FALSE;
	}
	private function pregMobile($mobile) {
		if (preg_match ( '/^\d{11}$/', $mobile )) {
			RETURN TRUE;
		} else {
			RETURN FALSE;
		}
	}
	
	// 个人信息展示
	public function info() {
		// SI报名信息
		$info = $this->M->getSignUp ( $this->mopenid );
		// 中奖信息
		$temp = $this->M->getData ( 'si_lottery_log', ' openid = "' . $this->mopenid . '" AND type !=0 ' );
		$lf = array_pop ( $temp );
		if ($lf) { // 已中奖
			$lotteryStatus = 1;
			$this->assign ( 'giftName', $this->getGiftByType ( $lf ['type'] ) );
			if ($lf ['name'] && $lf ['address'] && $lf ['mobile']) {
				$this->assign ( 'adStatus', 1 ); // 发货地址设置状态
			} else {
				$this->assign ( 'adStatus', 4 ); // 发货地址设置状态
			}
		} else { // 未中奖
			$lotteryStatus = 4;
		}
		
		$si_queue = $this->M->getData ( 'si_queue', "openid='{$this->mopenid}'" );
		$this->assign ( 'si_queue', $si_queue );
		$this->assign ( 'title', '我的智造基地' );
		$this->assign ( 'info', $info );
		$this->assign ( 'userinfo', $_SESSION ['userinfo'] );
		$this->assign ( 'lotteryStatus', $lotteryStatus );
		$this->display ();
	}
	public function upInfo() {
		$info = $this->M->getSignUp ( $this->mopenid );
		if (! $info) {
			redirect ( '/index.php/si/form/' );
		}
		
		$this->assign ( 'up', 1 );
		$this->assign ( 'info', $info );
		$this->display ( 'si/form.html' );
	}
	public function demo() {
		$url = "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=1WAjXnt0j0y38bkHbBoJVn7FvL--61SCHciQSKGu-wmLHiFgWVNRH9ZcqtQX5RK6onylmmwQn5ccA1955PrXWBjmfu1h6uX1wncKpdnIiDY";
		$content = curl_file_get_contents ( $url );
		p ( $content );
	}
}
