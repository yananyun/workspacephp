<?php

/**
 * wechatAction.class.php
 * 		微信API总控
 * 
 * @author:Harry
 * @link:http://haoshengzhide.com/
 * @since:2014.5.19 
 */
class wechatAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( FALSE );
		$this->M = new wechat ();
	}
	
	/**
	 * 对外接口
	 */
	public function api() {
		// if($_GET['test']=='test'){
		// dump(111111);exit;
		// }
		// 根据公众平台提交数据的类型判断处理程序
		if (isset ( $_GET ['echostr'] )) {
			// 授权验证
			$this->chk_token ( $_GET ['id'] );
		} elseif (isset ( $GLOBALS ['HTTP_RAW_POST_DATA'] )) {
			// 信息处理中心
			$this->processCenter ( $GLOBALS ['HTTP_RAW_POST_DATA'], $_GET ['id'] );
		}
	}
	
	/**
	 * 公众账号接入验证程序
	 */
	public function chk_token($id) {
		$echoStr = $_GET ["echostr"];
		if ($this->checkSignature ( $id )) {
			echo $echoStr;
			exit ();
		} else {
			echo 'false';
			exit ();
		}
	}
	
	/**
	 * 验证的
	 */
	public function checkSignature($id) {
		$signature = $_GET ["signature"];
		$timestamp = $_GET ["timestamp"];
		$nonce = $_GET ["nonce"];
		
		$configArr = $this->M->getConf ( "id={$id}" ); // --------------------------------------<><><><>><><><>
		file_put_contents ( './upload/aaaaab.html', serialize ( $configArr ) );
		$this->M->setTest ( json_encode ( $configArr ) );
		// $tmpArr = array(TOKEN, $timestamp, $nonce);
		$tmpArr = array (
				$configArr ['token'],
				$timestamp,
				$nonce 
		);
		sort ( $tmpArr, SORT_STRING );
		$tmpStr = implode ( $tmpArr );
		$tmpStr = sha1 ( $tmpStr );
		
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}
	
	// 发布菜单
	public function releaseMenu($menu) {
		$access_token = $this->getAccessTokenReturn ();
		// 设定创建自定义菜单API地址
		// 有菜单 数据则创建，否则删除
		if ($menu) {
			$api_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
		} else {
			$api_url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$access_token}";
		}
		
		$created_menu_status = $this->M->curlpost ( $api_url, $menu );
		return json_decode ( $created_menu_status, true );
	}
	
	/**
	 * 信息处理中心
	 * 
	 * @param varchar $string
	 *        	收到的信息
	 * @param int $id
	 *        	账号ID
	 */
	public function processCenter($string, $id) {
		if ($this->checkSignature ( $id )) {
			// 验证消息真实性
			$accountInfo = $this->M->getConf ( array (
					'id' => $id 
			) ); // 获取账号信息
			$message = ( array ) simplexml_load_string ( $string, 'SimpleXMLElement', LIBXML_NOCDATA );
			$wechat = new WechatApi ( $accountInfo ['access_token'] ); // 实例化微信接口
			
			$MEM = new member ();
			$memberInfo = $MEM->getInfoByOpenid ( $message ['FromUserName'] );
			if (! $memberInfo) {
				$member_info = $wechat->getUserInfoByOpenid ( $message ['FromUserName'] ); // 获取用户基本信息
				$member_info ['nickname'] = filterImage ( $member_info ['nickname'] ); // 过滤掉昵称中的图片
				$mem_group = new memberGroup ();
				$mem_group->subscribe ( $member_info, $message ['ToUserName'] ); // 处理关注用户
				$memberInfo = $MEM->getInfoByOpenid ( $message ['FromUserName'] );
			} else {
				$model = new baseModel ();
				$model->upData ( 'sys_member', "openid='{$message['FromUserName']}'", array (
						'lastintracttime' => time () 
				) );
			}
			// 消息入库
			$getmsg = new getmsg ();
			
			$getmsg->addMsg ( $message );
			
			switch ($message ['MsgType']) {
				case 'text' : // 处理文本消息
				             // 调用多客服
					$Text = new wechat ();
					$Text->processText ( $message ['FromUserName'], $message ['Content'] );
					
					if ($message ['Content'] == 444444) {
						$news = array (
								'articles' => array (
										array (
												'title' => '清除session',
												'description' => '清除session',
												'url' => APP_PATH . 'index.php/wechat/sessionDestroy',
												'picurl' => 'http://eugene-kaspersky.wpengine.netdna-cdn.com/files/2014/10/DSC03262-600x400.jpg' 
										) 
								) 
						);
						$wechat->putMsg ( $message ['FromUserName'], 'news', $news ); // 获取信息响应的信息
					}
					if ($message ['Content'] == '急急如律令') {
						$news = array (
								'articles' => array (
										array (
												'title' => '测试入口',
												'description' => '测试入口',
												'url' => APP_PATH . 'index.php/home/index',
												'picurl' => 'http://ww2.sinaimg.cn/bmiddle/61e89b74jw1en222pzmhmj208x064jre.jpg' 
										) 
								) 
						);
						$wechat->putMsg ( $message ['FromUserName'], 'news', $news ); // 获取信息响应的信息
					}
					if ($message ['Content'] == '电影券兑换') {
						$si = new edison ();
						$info = $si->duijiang ( $message ['FromUserName'] );
						if (! empty ( $info )) {
							$si = new si ();
							$si_info ['openid'] = $message ['FromUserName'];
							$si_info ['content'] = $message ['Content'];
							$si_info ['json'] = json_encode ( $info );
							$si_info ['ctime'] = time ();
							$si->addMsgLog ( $si_info );
							
							$content = "恭喜您！你已获得时光网电影票一张，电子兑换码为{$info['number']}密码为{$info['code']}。";
							$wechat->putMsg ( $message ['FromUserName'], 'text', array (
									'content' => $content 
							) ); // 获取信息响应的信息
						}
						// else{
						// $content = "暂时没有您的中奖信息哦。";
						// $wechat->putMsg($message['FromUserName'], 'text', array('content' => $content)); //获取信息响应的信息
						// }
					}
					// si大转盘中奖信息，
					if ($message ['Content'] == '中奖查询') {
						
						$si = new si ();
						$info = $si->duijiang ( $message ['FromUserName'] );
						if (! empty ( $info )) {
							$si_info ['openid'] = $message ['FromUserName'];
							$si_info ['content'] = $message ['Content'];
							$si_info ['json'] = json_encode ( $info );
							$si_info ['ctime'] = time ();
							$si->addMsgLog ( $si_info );
							if ($info ['number']) {
								$content = "恭喜您！你已获得时光网电影票一张，电子兑换码为{$info['number']}密码为{$info['code']}。";
							} else {
								$content = "恭喜您！你已获得物美超市电子券一张，电子兑换码为{$info['code']}。";
							}
							$wechat->putMsg ( $message ['FromUserName'], 'text', array (
									'content' => $content 
							) ); // 获取信息响应的信息
						}
						// else{
						// $content = "暂时没有您的中奖信息哦。";
						// $wechat->putMsg($message['FromUserName'], 'text', array('content' => $content)); //获取信息响应的信息
						// }
					}
					
					// 抓娃娃中奖信息
					if ($message ['Content'] == '娃娃机中奖信息') {
						$doll = new newdoll ();
						$info = $doll->duijianginfo ( $message ['FromUserName'] );
						if (count ( $info ) > 0) {
							$si = new si ();
							$si_info ['openid'] = $message ['FromUserName'];
							$si_info ['content'] = $message ['Content'];
							$si_info ['json'] = json_encode ( $info );
							$si_info ['ctime'] = time ();
							$si->addMsgLog ( $si_info );
							$str = '';
							$jiang = array (
									'1' => '50元携程券一张',
									'2' => '100元携程券一张',
									'3' => '时光网电影券一张',
									'4' => '物美超市电影券一张' 
							);
							foreach ( $info as $k => $v ) {
								$str .= $jiang [$v ['type']] . "兑换码为{$v['number']},密码为{$v['code']};";
							}
							$content = "恭喜您，您在掌上娃娃机体验环节中获得{$str}。感谢您对英特尔中国在线的支持。";
							$wechat->putMsg ( $message ['FromUserName'], 'text', array (
									'content' => $content 
							) ); // 获取信息响应的信息
						} else {
							$content = "很遗憾，您未能在掌上娃娃机体验环节获奖。感谢您对英特尔中国在线的支持。请关注我们的微信号，获取更多活动资讯。";
							$wechat->putMsg ( $message ['FromUserName'], 'text', array (
									'content' => $content 
							) ); // 获取信息响应的信息
						}
					}
					// 娃娃机旅游券兑换
					if ($message ['Content'] == '娃娃机旅游券兑换') {
						$doll = new newdoll ();
						$info = $doll->duijianginfo ( $message ['FromUserName'] );
						if (count ( $info ) > 0) {
							$si = new si ();
							$si_info ['openid'] = $message ['FromUserName'];
							$si_info ['content'] = $message ['Content'];
							$si_info ['json'] = json_encode ( $info );
							$si_info ['ctime'] = time ();
							$si->addMsgLog ( $si_info );
							$str = '';
							$jiang = array (
									'1' => '50元携程券一张',
									'2' => '100元携程券一张',
									'3' => '时光网电影券一张',
									'4' => '物美超市电影券一张' 
							);
							foreach ( $info as $k => $v ) {
								$str .= $jiang [$v ['type']] . "兑换码为{$v['number']},密码为{$v['code']};";
							}
							$content = "恭喜您，您在掌上娃娃机体验环节中获得{$str}。感谢您对英特尔中国在线的支持。";
							$wechat->putMsg ( $message ['FromUserName'], 'text', array (
									'content' => $content 
							) ); // 获取信息响应的信息
						} else {
							$content = "很遗憾，您未能在掌上娃娃机体验环节获奖。感谢您对英特尔中国在线的支持。请关注我们的微信号，获取更多活动资讯。";
							$wechat->putMsg ( $message ['FromUserName'], 'text', array (
									'content' => $content 
							) ); // 获取信息响应的信息
						}
						// $time = strtotime("2015-6-10");
						// $doll = new doll();
						// $info = $doll->duijianginfo($message['FromUserName']);
						// if (count($info) > 0) {
						// $si = new si();
						// $si_info['openid'] = $message['FromUserName'];
						// $si_info['content'] = $message['Content'];
						// $si_info['json'] = json_encode($info);
						// $si_info['ctime'] = time();
						// $si->addMsgLog($si_info);
						// if (time() > $time) {
						// $num = count($info);
						// $str = '';
						// $type = array('1' => '50元携程网旅游券', '2' => '100元携程网旅游券');
						// foreach ($info as $k => $v) {
						// $str .= $type[$v['type']] . "兑换码为{$v['number']},密码为{$v['code']};";
						// }
						// $str = rtrim($str, ';');
						// //尊敬的用户，您好！
						// //您已获得50元携程网旅游券，兑换码为……，密码为……；100元携程网旅游签，兑换码为……，密码为……。
						// //感谢您对我们活动的支持和关注。请你继续关注“英特尔中国在线”微信公众号，获取更多“英特尔中国智造基地”后续活动资讯。
						// $content = "尊敬的用户，您好！
						// 您已获得{$str}。感谢您对我们活动的支持和关注。请你继续关注“英特尔中国在线”微信公众号，获取更多“英特尔中国智造基地”后续活动资讯。";
						// $wechat->putMsg($message['FromUserName'], 'text', array('content' => $content)); //获取信息响应的信息
						// }
						// }
						// // else{
						// // $content = "很遗憾，您未能在掌上娃娃机体验环节获奖。感谢您对英特尔中国在线的支持。请关注我们的微信号，获取更多活动资讯。";
						// // $wechat->putMsg($message['FromUserName'], 'text', array('content' => $content)); //获取信息响应的信息
						// // }
					}
					
					$this->M->KeyWordProcessing ( $message ['FromUserName'], $message ['Content'], $wechat );
					// $this->M->processTextMessage($message, $accountInfo, $memberInfo, $wechat);
					
					break;
				
				case 'image' : // 处理图片消息
					break;
				
				case 'voice' : // 处理语音消息
					break;
				
				case 'video' : // 处理视频消息
					break;
				
				case 'location' : // 处理用户发送的地理位置消息
					$member = new memberGroup ();
					$member->updateLocation ( $message ); // 修改用户location
					break;
				
				case 'link' : // 处理链接消息
					break;
				
				case 'event' : // 事件推送
					switch ($message ['Event']) {
						case 'subscribe' : // 用户订阅事件（包含扫描关注）
							/* 如果是标签就给用户加上标签 */
							$tag = new tag ();
							$eventkey = str_replace ( 'qrscene_', '', $message ['EventKey'] );
							$tag_name = $tag->getTagByWhereSql ( " where qrcode like '%--{$eventkey}--%' " );
							if (! empty ( $tag_name )) {
								foreach ( $tag_name as $v ) {
									$tag->addUserTagRelation ( $v ['id'], array (
											'openid' => $message ['FromUserName'] 
									) );
								}
							}
							/* 用户关注时会员处理 代码 开始 zhaowei */
							$member_info = $wechat->getUserInfoByOpenid ( $message ['FromUserName'] ); // 获取用户基本信息
							$mem_group = new memberGroup ();
							$mem_group->subscribe ( $member_info, $message ['ToUserName'] ); // 处理关注用户
							
							$this->M->processSubscribeMsg ( $message, $accountInfo, $memberInfo, $wechat );
							/* 用户关注时会员处理 代码 结束 */
							
							/* Edison入驻京东 晒订单去旅行 begin */
							if ($eventkey == 100) {
								$this->M->push_material ( 14, $message ['FromUserName'], $type = 2, $wechat );
							}
							/* Edison入驻京东 晒订单去旅行 end */
							
							/* 英特尔®中国智造基地 助你跨过一步之遥 begin */
							if ($eventkey == 101) {
								$this->M->push_material ( 15, $message ['FromUserName'], $type = 2, $wechat );
							}
							/* 英特尔®中国智造基地 助你跨过一步之遥 end */
							break;
						
						case 'unsubscribe' : // 用户取消订阅事件
							$mem_group = new memberGroup ();
							$mem_group->unsubscribe ( $message ['FromUserName'] );
							break;
						
						case 'LOCATION' : // 获取用户地理位置
							$member = new memberGroup ();
							$member->updateLocation ( $message ); // 修改用户location
							break;
						
						case 'CLICK' : // 用户点击事件
							$this->M->processClick ( $message, $accountInfo, $memberInfo, $wechat ); // 处理用户点击事件
							break;
						
						case 'VIEW' : // 点击菜单跳转链接时的事件推送
							break;
						
						case 'SCAN' : // 扫描带场景值二维码事件（已关注）
							/* 如果是标签就给用户加上标签 */
							$tag = new tag ();
							$tag_name = $tag->getTagByWhereSql ( " where qrcode like '%--{$message['EventKey']}--%' " );
							if (! empty ( $tag_name )) {
								foreach ( $tag_name as $v ) {
									$tag->addUserTagRelation ( $v ['id'], array (
											'openid' => $message ['FromUserName'] 
									) );
								}
							}
							$this->qrcodeResponse ( $message, 2 );
							
							/* Edison入驻京东 晒订单去旅行 begin */
							if ($message ['EventKey'] == 100) {
								$this->M->push_material ( 14, $message ['FromUserName'], $type = 2, $wechat );
							}
							/* Edison入驻京东 晒订单去旅行 end */
							
							/* 英特尔®中国智造基地 助你跨过一步之遥 begin */
							if ($message ['EventKey'] == 101) {
								$this->M->push_material ( 15, $message ['FromUserName'], $type = 2, $wechat );
							}
							/* 英特尔®中国智造基地 助你跨过一步之遥 end */
							
							// $qrcode = new qrcodeAction();
							// $content = $qrcode->qrScan($message); //获取相应的图文
							// $wechat->putMsg($message['FromUserName'], 'news', array('articles' => $content));
							break;
						
						case 'MASSSENDJOBFINISH' : // 事件推送群发结果
							break;
						
						default :
							break;
					}
			}
		}
	}
	
	/**
	 * 获取AccessToken接口
	 */
	public function getAccessToken() {
		$wechat = new wechat ();
		$result = $wechat->getAccessToken ();
		$info ['accesstoken'] = $result;
		$str = json_encode ( $info );
		$jsonp = trim ( $_GET ['jsonpcallback'] );
		if ($jsonp) {
			$str = $jsonp . "(" . $str . ")";
		}
		echo $str;
	}
	
	/**
	 * 获取accesstoken
	 */
	public function getAccessTokenReturn() {
		return $this->getAccessTokenByOpenid ();
	}
	
	/**
	 * 根据openid获取accesstoken
	 *
	 * @param string $openid
	 *        	公众号的OPENID
	 * @return string
	 */
	public function getAccessTokenByOpenid($openid = '') {
		if ($openid == '') {
			$accountInfo = $this->M->getConf ( array (
					'id' => 1 
			) );
		} else {
			$accountInfo = $this->M->getConf ( array (
					'openid' => $openid 
			) );
		}
		return $accountInfo ['access_token'];
	}
	public function demo() {
		$filename = ROOT_PATH . 'demo.txt';
		$content = '最后更新时间为' . date ( 'Y-m-d H:i:s', time () );
		
		file_put_contents ( $filename, $content );
	}
	
	/**
	 * 通过$_GET['code']获取此用户的详细信息
	 *
	 * @param type $code        	
	 * @param bool $getMore
	 *        	是否获取用户详情，默认否
	 * @return type
	 */
	public function getUserInfoByCode($code, $getMore = false) {
		if (empty ( $code )) {
			$str = '没有code呢';
			$this->errorPage ( $str, 'before' );
		} else {
			$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . APPID . '&secret=' . APPSECRET . '&code=' . $code . '&grant_type=authorization_code';
			$tokenInfo = json_decode ( curl_file_get_contents ( $url ), true );
			if ($tokenInfo ['errcode']) {
				$str = '授权错误：' . $tokenInfo ['errcode'];
				$this->errorPage ( $str, 'before' );
			} else {
				$mopenid = $tokenInfo ['openid'];
				if ($getMore) {
					$member_info = $this->getUserInfoByToken ( array (
							'access_token' => $tokenInfo ['access_token'],
							'openid' => $mopenid 
					) );
					return $member_info;
				}
				return $mopenid;
			}
		}
	}
	
	/**
	 * 通过$_GET['code']获取此用户的详细信息
	 *
	 * @param array $param
	 *        	= array('access_token'=>通过code获取的$access_token,'openid'=>用户唯一标识)
	 * @return type
	 */
	public function getUserInfoByToken($param) {
		$access_token = $param ['access_token'];
		$openid = $param ['openid'];
		if (empty ( $access_token )) {
			$str = '没有access_token呢';
			$this->errorPage ( $str, 'before' );
		} else {
			$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
			$member_info = json_decode ( curl_file_get_contents ( $url ), true );
			if ($member_info ['errcode']) {
				$str = '授权错误：' . $member_info ['errcode'];
				$this->errorPage ( $str, 'before' );
			} else {
				return $member_info;
			}
		}
	}
	
	/**
	 * 清除session
	 */
	public function sessionDestroy() {
		session_unset ();
		session_destroy ();
		if (! $_SESSION) {
			echo '<h1>session清除成功</h1>';
		} else {
			echo '<h1>请重试</h1>';
		}
	}
	
	/**
	 * 更新access_token
	 *
	 * @Linux crontab执行
	 */
	public function updateAccessToken() {
		$this->M->updateAccessToken ();
	}
	public function getUserInfoByOpenid($mopenid) {
		$accountInfo = $this->M->getConf ( array (
				'id' => '1' 
		) ); // 获取账号信息
		$wechat = new WechatApi ( $accountInfo ['access_token'] ); // 实例化微信接口
		$MEM = new member ();
		$memberInfo = $MEM->getInfoByOpenid ( $mopenid );
		if (! $memberInfo) {
			$member_info = $wechat->getUserInfoByOpenid ( $mopenid ); // 获取用户基本信息
			$member_info ['nickname'] = filterImage ( $member_info ['nickname'] );
			$mem_group = new memberGroup ();
			$mem_group->subscribe ( $member_info, $mopenid ); // 处理关注用户
			$memberInfo = $MEM->getInfoByOpenid ( $mopenid );
			return $memberInfo;
		}
	}
	
	// 不可修改，外部接口使用
	public function sessionDestory() {
		unset ( $_SESSION ['userinfo'] );
		
		session_unset ();
		session_destroy ();
		echo '<h1>session清除成功</h1>';
	}
	
	/**
	 * 二维码扫描下行
	 * 
	 * @param unknown $message        	
	 * @param string $type
	 *        	1 关注 2 扫描
	 */
	public function qrcodeResponse($message, $type = 1) {
		// 8亿以上临时二维码则推送给合作方去处理后续东西
		$eventkey = $message ['EventKey'];
		$eventkey = str_replace ( 'qrscene_', '', $eventkey );
		// file_put_contents('./uploads/a.html', $eventkey);
		if (intval ( $eventkey ) > 800000000 && $type == 2) {
			// $createtime = $message['CreateTime'];
			$openid = $message ['FromUserName'];
			$mid = $eventkey - 800000000;
			// file_put_contents('./uploads/a.html', $mid);
			$wechat = new wechat ();
			$activepush = new activepush ();
			$articles = $activepush->getMaterialInfo ( $mid, '48' );
			$wechat->putMsg ( $openid, 'news', $articles );
		}
	}
}
