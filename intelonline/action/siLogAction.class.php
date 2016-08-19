<?php

/**
 * 英特尔中国在线——doll活动
 * 
 * @author Endy
 * @since 15-5-26 下午5:36
 */
// class edisonAction extends Action
class siLogAction extends Action {
	public $M;
	public $openid;
	public function __construct() {
		parent::__construct ( false );
		$this->M = new newdoll ();
		$this->openid = $_GET ['openid'];
	}
	
	// 游戏结束，转到娃娃的给出随机的奖品
	// http://............?openid=test&result=0(1)
	public function siGameOver() {
		$this->M->addData ( 'demo', array (
				'content' => json_encode ( $_GET ) 
		) );
		$openid = $_GET ['openid'];
		$result = $_GET ['result'];
		// 1携程券50元，2携程券100元，3时光网电影券，4物美超市电影券
		$r = rand ( 1, 10 );
		if ($r >= 1 && $r <= 5) {
			$rand = 1;
		} elseif ($r >= 6 && $r <= 8) {
			$rand = 2;
		} elseif ($r >= 9 && $r <= 10) {
			$rand = rand ( 3, 4 );
		}
		if ($result == 1) {
			$total = $this->M->checkLotteryCount ( $rand );
			$_total = $this->M->bycount ( 'newdoll_lottery_conf', "type={$rand}" );
			if ($_total >= $total ['total']) {
				$result = 0;
			}
		}
		
		// 给用户减一次抓娃娃的机会
		$this->game ( $openid );
		
		if ($result == 1) {
			// 抓中，给出相应的奖项
			$this->addLotteryLog ( $this->openid, $rand );
			
			// 取出奖品的信息，并且改变他的状态
			$giftInfo = $this->M->getGiftInfo ( array (
					'type' => $rand,
					'status' => 1 
			), TRUE );
			file_put_contents ( './aaaaaa.html', json_encode ( $giftInfo ) );
			$this->M->updateGiftInfo ( array (
					'status' => 2,
					'openid' => $this->openid,
					'ctime' => time () 
			), array (
					'id' => $giftInfo ['id'] 
			) );
			// $content 为中奖后给用户下行48小时交互的内容文案
			// $centent = "恭喜您！你已获得{$this->getGiftByType($rand)}一张，电子兑换码为{$giftInfo['number']}密码为{$giftInfo['code']}。";
			$str = '';
			if ($giftInfo ['number']) {
				$str = "卡号是：" . $giftInfo ['number'];
			}
			if ($giftInfo ['code']) {
				$str .= "密码是：" . $giftInfo ['code'];
			}
			$jiang = array (
					'1' => '50元携程券',
					'2' => '100元携程券',
					'3' => '时光网电影券',
					'4' => '物美超市电影券' 
			);
			$centent = "恭喜您，您在掌上娃娃机体验环节中获得{$jiang[$rand]}一张。
                        {$str}。感谢您对英特尔中国在线的支持。";
			$this->sendWeixinMsg ( $this->openid, $centent );
		} else {
			// 增加一条没有中奖的记录
			$this->addLotteryLog ( $this->openid, 0 );
		}
	}
	public function game($openid) {
		$sql = "update newdoll_user set lastlotterytimes = lastlotterytimes - 1 where openid='{$openid}'";
		// $info['is_play'] = '2';
		$this->M->bysql ( 'newdoll_user', $sql );
	}
	
	// 写入中奖/抽奖日志
	private function addLotteryLog($openid, $type) {
		$info = array ();
		$info ['openid'] = $openid;
		$info ['type'] = $type;
		$info ['ctime'] = time ();
		
		return $this->M->addData ( 'newdoll_lottery_log', $info );
	}
	private function sendWeixinMsg($openid, $content) {
		$WECHAT = new wechat ();
		$WECHAT->putMsg ( $openid, 'text', array (
				'content' => $content 
		) );
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
		$todayCount = $this->M->getCount ( 'doll_lottery_log', ' type = ' . $type . ' AND ctime > ' . strtotime ( 'today' ) );
		$timeinfo = getdate ();
		$mday = $timeinfo ['mday'];
		// 获取当前礼品的当日配额
		$conf = $this->M->getData ( 'doll_lottery_conf', array (
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
		$temp = $this->M->getData ( 'doll_lottery_log', ' openid = "' . $this->mopenid . '" AND type !=0 ' );
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

?>