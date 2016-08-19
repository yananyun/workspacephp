<?php

/**
 * 英特尔中国在线——SI活动
 * 
 * @author Harry
 * @since 2015.3.13
 */
// class edisonAction extends Action
class edisonAction extends edisonBaseAction {
	public $mopenid;
	public $nickname;
	public $headimgurl;
	public $M;
	public function __construct() {
		parent::__construct ( false );
		$this->M = new edison ();
		$this->mopenid = $_SESSION ['mopenid'];
		$this->nickname = $_SESSION ['userinfo'] ['nickname'];
		$this->headimgurl = $_SESSION ['userinfo'] ['headimgurl'];
		
		$userinfo = $this->M->getInfo ( array (
				'openid' => $this->mopenid 
		), TRUE );
		$this->assign ( 'count', $userinfo ['lastlotterytimes'] ); // 用户基本信息
		
		$this->assign ( 'openid', $this->mopenid );
		$this->assign ( 'title', '大转盘' );
		$this->assign ( 'url', APP_PATH . 'index.php/edison/index/sourceopenid/' . $this->mopenid );
	}
	
	// 主页
	public function index() {
		// 若自朋友圈跳转而来的，则给源用户增加一次repost机会
		if ($_GET ['sourceopenid']) {
			$sourceopenid = $_GET ['sourceopenid']; // 通过某个用户分享而来的
			$nowopenid = $this->mopenid; // 当前用户的OPENID
			$this->addRepostTimes ( $sourceopenid, $nowopenid );
		}
		
		$this->display ( 'intel519/index.html' );
	}
	public function dd() {
		echo date ( "Y-m-d", strtotime ( "-7 day" ) );
	}
	
	/**
	 * 点击开始游戏
	 * 判断用户是否在排队表里面，没有的就入库处理，生成orderid
	 */
	public function enterTheGame() {
		
		// 判断库里有没有该用户
		$user = $this->M->orderStatusByOpenid ( $this->mopenid );
		if (! $user) {
			// 没有该用户就入库操作
			$info ['openid'] = $this->mopenid;
			$info ['nickname'] = $this->nickname;
			$info ['headimgurl'] = $this->headimgurl;
			$info ['status'] = '2';
			$id = $this->M->addData ( 'edison_queue', $info );
			$user = $this->M->orderStatusById ( $id );
		}
		$time = time () - 300;
		$ordersql = "select max(queuing) from edison_queue";
		$ordernum = $this->M->bysql ( 'edison_queue', $ordersql );
		$ordernum = $ordernum ['0'] ['queuing'] ? $ordernum ['0'] ['queuing'] : 0;
		$sql = "select count(*) num from edison_queue where (status = '1' or status = '2') and waittime < {$time}";
		$num = $this->M->bysql ( 'edison_queue', $sql );
		$num_pre = $num ['0'] ['num'];
		switch ($num_pre) {
			case 0 :
				$status ['status'] = '1';
				$status ['starttime'] = time ();
				$status ['waittime'] = time ();
				$status ['queuing'] = $ordernum + 1;
				$this->M->upData ( 'edison_queue', "openid='{$user['openid']}'", $status );
				$this->siGame ( 1 );
				exit (); // 开始游戏
				break;
			case 1 :
				$status ['status'] = '1';
				$status ['starttime'] = time ();
				$status ['waittime'] = time ();
				$status ['queuing'] = $ordernum + 1;
				$this->M->upData ( 'edison_queue', "openid='{$user['openid']}'", $status );
				$this->siGame ( 1 );
				exit (); // 开始游戏
				break;
			case 2 :
				$status ['status'] = '2';
				$status ['waittime'] = time ();
				$status ['queuing'] = $ordernum + 1;
				$this->M->upData ( 'edison_queue', "openid='{$user['openid']}'", $status );
				$this->siGame ( 2 );
				exit (); // 开始游戏
				break;
			case 3 :
				$status ['status'] = '2';
				$status ['waittime'] = time ();
				$status ['queuing'] = $ordernum + 1;
				$this->M->upData ( 'edison_queue', "openid='{$user['openid']}'", $status );
				$this->siGame ( 2 );
				exit (); // 开始游戏
				break;
			case 4 :
				// 下行消息
				$status ['status'] = '3';
				// $status['waittime'] = time();
				$status ['queuing'] = $ordernum + 1;
				$this->M->upData ( 'edison_queue', "openid='{$user['openid']}'", $status );
				$this->siGame ( 3 );
				exit (); // 开始游戏
				break;
			
			default :
				$status ['status'] = '3';
				$status ['waittime'] = time ();
				$status ['queuing'] = $ordernum + 1;
				$this->M->upData ( 'edison_queue', "openid='{$user['openid']}'", $status );
				$this->siGame ( 3 );
				exit (); // 开始游戏
				break;
		}
	}
	
	// 判断娃娃机是否有空闲
	public function game_is_empty() {
	}
	
	// 抓娃娃开始游戏
	public function siGame($status) {
		$is_empty = '';
		switch ($status) {
			case 1 :
				
				break;
			
			default :
				break;
		}
		$this->M->siGame ( $this->mopenid );
		// 跳转到玩游戏的页面
	}
	
	// 游戏结束，转到娃娃的给出随机的奖品
	public function siGameOver() {
	}
	public function active() {
		$this->display ( 'intel519/active.html' );
	}
	public function aircraft() {
		$this->display ( 'intel519/aircraft.html' );
	}
	public function atools() {
		$this->display ( 'intel519/atools.html' );
	}
	public function health() {
		$this->display ( 'intel519/health.html' );
	}
	public function robot() {
		$this->display ( 'intel519/robot.html' );
	}
	public function sHome() {
		$this->display ( 'intel519/sHome.html' );
	}
	public function wearable() {
		$this->display ( 'intel519/wearable.html' );
	}
	
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
			$this->M->upSighup ( $info, array (
					'openid' => $this->mopenid 
			) );
			ajaxReturn ( 'up', "", TRUE );
		} else {
			$return = $this->M->addSighup ( $info ); // 录入报名信息
			$WECHAT = new wechat ();
			$WECHAT->putMsg ( $this->mopenid, "text", array (
					'content' => '恭喜，您已完成SI Program项目填报，我们将在X个工作日内完成审核。您可回复【审核状态】了解项目审核进度' 
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
		$this->display ( 'intel519/wheel.html' );
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
			if ($upInfo ['reposttimes'] >= 5) {
				$upInfo ['reposttimes'] = $upInfo ['reposttimes'] - 5;
				$upInfo ['lastlotterytimes'] = $userinfo ['lastlotterytimes'] + 1;
			}
			// 更新用户信息
			$this->M->upUserInfo ( $upInfo, array (
					'id' => $userinfo ['id'] 
			) );
		}
	}
	
	/**
	 * 抽奖操作
	 */
	public function getLottery() {
		set_time_limit ( 20 );
		$userinfo = $this->M->getInfo ( array (
				'openid' => $_SESSION ['mopenid'] 
		), TRUE );
		
		// 若存在抽奖次数
		if ($userinfo ['lastlotterytimes'] > 0) {
			//
			// 减少用户的抽奖次数
			$this->M->cutLotteryTimes ( $this->mopenid );
			$userinfo = $this->M->getInfo ( array (
					'openid' => $this->mopenid 
			), TRUE );
			$count = $userinfo ['lastlotterytimes']; //
			                                        // $rand = rand(0,5);//1星巴克咖啡券 2时光网电影券 3京东券 4携程网旅游券 5智能手环 0谢谢参与
			$rand = rand ( 1, 100 );
			$rand = $this->rand ( $rand );
			// 控制用户只能中奖一次
			$temp = $this->M->getData ( 'edison_lottery_log', "openid='{$this->mopenid}' and type != 0", true );
			// file_put_contents('./aaaaaa.html', serialize($_SESSION));
			// echo $temp;exit;
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
			// $rand = 2;
			if ($rand == 0) {
				// 写入抽奖日志，0为未中奖
				$this->addLotteryLog ( $this->mopenid, 0 );
				ajaxReturn ( array (
						'data' => 0,
						'count' => $count 
				), "很遗憾，大礼与您擦肩而过，感谢您的参与，另外偷偷告诉您：分享越多机会越多哦！", TRUE ); // 4是“谢谢参与”时，指针所在的位置
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
						if ($rand == 2) {
							// 微信H5下发中奖消息
							$centent = "恭喜您！你已获得{$this->getGiftByType($rand)}一张，电子兑换码为{$giftInfo['number']}密码为{$giftInfo['code']}。";
							$this->sendWeixinMsg ( $this->mopenid, $centent );
							// $this->sendWeixinMsg($this->mopenid, '恭喜您，你已获得'.$this->getGiftByType($rand).'一张，电子兑换码'.$giftInfo['code'].'。您可通过微信菜单“SI Program”—“我的SI”查看中奖情况及相应电子码。');
							// 返回中奖信息
							ajaxReturn ( array (
									'data' => $rand,
									'count' => $count 
							), $giftInfo ['code'], TRUE );
						} else { // 实体券，需填写收货信息
						         // 微信H5下发中奖消息
						         // $centent = "恭喜您！你已获得{$this->getGiftByType($rand)}一张。";
						         // $centent = "恭喜您！你已获得{$this->getGiftByType($rand)}一张。为保证奖品发放顺利，请点击“火热招募”—“我的主页”及时查看中奖情况及地址提交、礼品发放等信息。如显示未成功提交，请直接在微信号以“姓名+通讯地址+手机号”格式回复消息。
						         // 注：如未能在活动截止日期前（4月21日）成功提交邮寄信息，将被视为自动放弃。";
						         // $this->sendWeixinMsg($this->mopenid,$centent);
						         // $this->sendWeixinMsg($this->mopenid,'恭喜您！你已获得'.$this->getGiftByType($rand).'一张。为保证奖品发放顺利，请点击“SI Program”—“我的SI”及时查看中奖情况及地址提交、礼品发放等信息。如显示未成功提交，请直接在微信号以“姓名+通讯地址+手机号”格式回复消息。');
						         // 返回中奖信息
							ajaxReturn ( array (
									'data' => $rand,
									'count' => $count 
							), $gid, TRUE );
						}
					} else {
						// 写入抽奖日志，0为未中奖
						$this->addLotteryLog ( $this->mopenid, 0 );
						ajaxReturn ( array (
								'data' => 0,
								'count' => $count 
						), "很遗憾，大礼与您擦肩而过，感谢您的参与，另外偷偷告诉您：分享越多机会越多哦！", TRUE ); // 4是“谢谢参与”时，指针所在的位置
					} // {'data':'sdfsa','info':'fsdfas','status':'true'}
				}
			}
		} else {
			ajaxReturn ( '0', "您的机会已用尽，继续努力哦！", FALSE ); // 4是“谢谢参与”时，指针所在的位置
		}
	}
	public function rand($rand) {
		// 1星巴克咖啡券 2时光网电影票 3京东券 4携程网旅游券 5智能手环 0谢谢参与
		$i = 1;
		$y = 0;
		
		$loottery_1 = $this->M->getConfVal ( "星巴克咖啡券" );
		if ($loottery_1) {
			$y = $loottery_1 + $y;
			if ($i <= $rand and $rand <= $y) {
				return 1;
			}
			$i += $loottery_1;
		}
		$loottery_2 = $this->M->getConfVal ( "时光网电影券" );
		if ($loottery_2) {
			$y = $loottery_2 + $y;
			if ($i <= $rand and $rand <= $y) {
				return 2;
			}
			$i += $loottery_2;
		}
		$loottery_3 = $this->M->getConfVal ( "京东券" );
		if ($loottery_3) {
			$y += $loottery_3;
			if ($i <= $rand and $rand <= $y) {
				return 3;
			}
			$i += $loottery_3;
		}
		$loottery_4 = $this->M->getConfVal ( "携程网旅游券" );
		if ($loottery_4) {
			$y += $loottery_4;
			if ($i <= $rand and $rand <= $y) {
				return 4;
			}
			$i += $loottery_4;
		}
		$loottery_5 = $this->M->getConfVal ( "智能手环" );
		if ($loottery_5) {
			$y += $loottery_5;
			if ($i <= $rand and $rand <= $y) {
				return 5;
			}
			$i += $loottery_5;
		}
		$loottery_6 = $this->M->getConfVal ( "谢谢参与" );
		if ($loottery_6) {
			$y += $loottery_6;
			if ($i <= $rand and $rand <= $y) {
				return 0;
			}
			$i += $loottery_6;
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
		$this->M->upData ( 'edison_lottery_log', array (
				'openid' => $this->mopenid 
		), $info );
	}
	
	// 感谢参与
	public function thanks() {
		$this->assign ( 'url', 'http://intelonline.buzzopt.com/index.php/edison/lottery/sourceopenid/' . $this->mopenid );
		$this->display ();
	}
	
	// 写入中奖/抽奖日志
	private function addLotteryLog($openid, $type) {
		$info = array ();
		$info ['openid'] = $openid;
		$info ['type'] = $type;
		$info ['ctime'] = time ();
		
		return $this->M->addData ( 'edison_lottery_log', $info );
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
	 *        	1星巴克咖啡券 2时光网电影票 3京东券 4携程网旅游券 5智能手环 0谢谢参与
	 * @return string
	 */
	private function getGiftByType($type) {
		switch ($type) {
			case 1 :
				$g = '星巴克咖啡券';
				break;
			case 2 :
				$g = '时光网电影票';
				break;
			case 3 :
				$g = '京东券';
				break;
			case 4 :
				$g = '携程网旅游券';
				break;
			case 5 :
				$g = '智能手环';
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
		$todayCount = $this->M->getCount ( 'edison_lottery_log', ' type = ' . $type . ' AND ctime > ' . strtotime ( 'today' ) );
		$timeinfo = getdate ();
		$mday = $timeinfo ['mday'];
		// 获取当前礼品的当日配额
		$conf = $this->M->getData ( 'edison_lottery_conf', array (
				'gtype' => $type,
				'mday' => $mday 
		), TRUE );
		
		// 没有设定的话，直接中奖~！！！！！！
		// if(!$conf)
		// {
		// return 2;
		// }
		
		if ($conf ['total'] && $todayCount >= $conf ['total']) {
			return 0;
		}
		return $type;
	}
	
	// 通过奖品类型得到大转盘指针位置 1星巴克咖啡券 2时光网电影票 3京东券 4携程网旅游券 5智能手环 0谢谢参与
	private function getHTMLCode($rand) {
		switch ($rand) {
			case 0 : // 未中奖
				return 0;
			case 1 : // 星巴克咖啡券
				return 1;
			case 2 : // 时光网电影票
				return 2;
			case 3 : // 物美电子券
				return 3;
			case 4 : // 星巴克咖啡券
				return 4;
			case 5 : // 好利来蛋糕券
				return 5;
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
		$url = "http://sms.1xinxi.cn/asmx/smsservice.aspx?name=18610475309&pwd=EA122A24589CEE069D2D34210949&content=" . urlencode ( $content ) . "&mobile=$mobile&sign=英特尔中国在线&type=pt&extno=001";
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
		$temp = $this->M->getData ( 'edison_lottery_log', ' openid = "' . $this->mopenid . '" AND type !=0 ' );
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
