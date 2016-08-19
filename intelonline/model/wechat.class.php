<?php
/**
 * wechat.class.php
 *		微信总控控制类
 * @author:Harry
 * @link:http://haoshengzhide.com/
 * @since:2014.5.19
 */
class wechat extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->db->tableName = 'sys_account';
	}
	public function setTest($str) {
		$this->db->tableName = 'sys_test';
		$this->insert ( array (
				'content' => '$str' 
		) );
	}
	
	/**
	 * 根据ID获取账号配置项
	 *
	 * @param array|string $where
	 *        	condition
	 * @return array
	 */
	public function getConf($where, $all = 0) {
		$this->db->tableName = 'sys_account';
		if ($all == 0) {
			$return = $this->first ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
	
	/**
	 * 更新account信息
	 *
	 * @param array $info
	 *        	要更新的内容
	 * @param array $where
	 *        	条件
	 */
	public function updateInfo($info, $where) {
		$this->tableName = 'account';
		return $this->update ( $info, $where );
	}
	
	/**
	 * 根据aopenid和mopenid获取微信用户信息
	 * 
	 * @param char $aopenid
	 *        	公众账号openid 暂时取消
	 * @param char $mopenid
	 *        	微信用户openid
	 * @return array $memberInfo用户信息
	 */
	public function getMemberInfo($mopenid) {
		$this->tableName = 'member';
		$memberInfo = $this->newFirst ( array (
				'openid' => $mopenid 
		) );
		
		if (! $memberInfo) {
			$access_token = $_SESSION ['accountInfo'] ['access_token'];
			$wechatApi = new WechatApi ();
			$member_info = $wechatApi->getUserInfoByOpenid ( $mopenid );
			$member = new memberGroup ();
			$member->subscribe ( $member_info, $_SESSION ['accountInfo'], '' );
			$memberInfo = $this->first ( array (
					'openid' => $mopenid 
			) );
		}
		return $memberInfo;
	}
	
	/**
	 * 获取access_token
	 */
	public function getAccessToken() {
		$this->db->tableName = 'sys_account';
		$return = $this->select ( 'access_token', 'id = 1' );
		return $return [0] ['access_token'];
	}
	
	/**
	 * 通过openid获取此用户的基本信息
	 *
	 * @param varchar $openid        	
	 */
	public function getUserInfoByOpenId($openid) {
		$token = $this->getAccessToken ();
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid";
		$userinfo = curl_file_get_contents ( $url );
		$return = json_decode ( $userinfo, TRUE );
		// 如果是特殊字符无法正常解Json
		if (! $return) {
			$userinfo = substr ( str_replace ( '\"', '"', json_encode ( $userinfo ) ), 1, - 1 );
			$return = json_decode ( $userinfo, TRUE );
		} else {
			$return = json_decode ( $userinfo, TRUE );
		}
		
		if (isset ( $return ['errcode'] )) {
			// 如果是token失效
			if ($return ['errcode'] == '40001' || $return ['errcode'] == '42001' || $return ['errcode'] == '40029') {
				$this->updateAccessToken ();
			}
			
			$this->addErrorLog ( $userinfo, 'getUserInfoByOpenId' );
			$token = $this->getAccessToken ();
			$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid";
			$userinfo = curl_file_get_contents ( $url );
			$return = json_decode ( $userinfo, TRUE );
			
			// 如果是特殊字符无法正常解Json
			if (! $return) {
				$userinfo = substr ( str_replace ( '\"', '"', json_encode ( $userinfo ) ), 1, - 1 );
				$return = json_decode ( $userinfo, TRUE );
			} else {
				$return = json_decode ( $userinfo, TRUE );
			}
			
			if (isset ( $return ['errcode'] )) {
				$this->addErrorLog ( $userinfo . '-' . $openid, 'getUserInfoByOpenId' );
			}
		}
		// 过滤表情
		$return ['nickname'] = $this->filterImage ( $return ['nickname'] );
		
		return $return;
	}
	
	/**
	 * 更新access_token
	 */
	public function updateAccessToken() {
		$configArr = $this->config ();
		$content = json_decode ( file_get_contents ( 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $configArr ['appid'] . '&secret=' . $configArr ['appsecret'] ), true );
		
		$info = array ();
		$info ['accesstoken'] = $content ['access_token'];
		$info ['utime'] = time ();
		
		$this->db->tableName = 'sys_weixin_accesstoken';
		$this->update ( $info, 'id = 1' );
		
		$token = array ();
		$token ['access_token'] = $content ['access_token'];
		$token ['ctime'] = time ();
		$this->db->tableName = 'sys_account';
		$this->update ( $token, 'id = 1' );
	}
	
	/**
	 * 根据条件获取member信息
	 *
	 * @param varchar|array $where
	 *        	where条件
	 * @return array
	 */
	public function getMember($where = NULL) {
		$this->db->tableName = 'sys_member';
		$return = $this->select ( NULL, $where );
		return $return;
	}
	
	/**
	 * 监测会员是否存在，存在的话返回id，不存在返回false
	 *
	 * @param varchar $openid        	
	 * @return boolean|int
	 */
	public function chkMember($openid) {
		$this->db->tableName = 'sys_member';
		$result = $this->select ( NULL, 'openid = "' . $openid . '"' );
		if (empty ( $result )) {
			return false;
		}
		return $result [0];
	}
	
	/**
	 * 新增member
	 *
	 * @param array $userinfo
	 *        	接口返回的用户信息
	 */
	public function addMember($userinfo) {
		$oldUserInfo = $this->chkMember ( $userinfo ['openid'] );
		$chk = $oldUserInfo ['id'] ? $oldUserInfo ['id'] : FALSE;
		$info = array ();
		$info ['openid'] = $userinfo ['openid'];
		if ($userinfo ['subscribe'] == 1) {
			$info ['nickname'] = $userinfo ['nickname'];
			$info ['sex'] = $userinfo ['sex'];
			$info ['language'] = $userinfo ['language'];
			$info ['city'] = $userinfo ['city'];
			$info ['province'] = $userinfo ['province'];
			$info ['country'] = $userinfo ['country'];
			$info ['subscribe'] = $userinfo ['subscribe'];
			$info ['subscribe_time'] = $userinfo ['subscribe_time'];
			$info ['headimgurl'] = $userinfo ['headimgurl'];
			if ($info ['country'] == '中国') {
				$info ['provincecode'] = $this->locationToCode ( $info ['province'] );
				$info ['citycode'] = $this->locationToCode ( $info ['city'], 2, $info ['provincecode'] );
			} else {
				$info ['provincecode'] = 0;
				$info ['citycode'] = 0;
			}
		} else if ($userinfo ['subscribe'] === 0) {
			$info ['nickname'] = '暂未获取';
			$info ['sex'] = 1;
			$info ['language'] = 'zh_CN';
			$info ['city'] = '朝阳';
			$info ['province'] = '北京';
			$info ['country'] = '中国';
			$info ['subscribe'] = $userinfo ['subscribe'];
			$info ['headimgurl'] = $userinfo ['headimgurl'] ? $userinfo ['headimgurl'] : '/images/man.png';
			$info ['subscribe_time'] = 0;
			$info ['provincecode'] = 0;
			$info ['citycode'] = 0;
		}
		
		$this->db->tableName = 'sys_member';
		if (! $chk) {
			$info ['ctime'] = time ();
			$this->db->tableName = 'sys_member';
			$return = $this->insert ( $info );
			$mid = $return;
		} else {
			$this->db->tableName = 'sys_member';
			if (! empty ( $info ['openid'] ) && $info ['openid'] != '') {
				$return = $this->update ( $info, 'id = ' . $chk );
			}
			$mid = $chk;
		}
		return $mid;
	}
	
	/**
	 * 发送信息
	 *
	 * @param varchar $openid
	 *        	对方openid
	 * @param varchar $type
	 *        	信息类型（text|image|voice|video|music|news）
	 * @param array $content
	 *        	信息详情，根据信息类型各有区分
	 */
	public function putMsg($openid, $type, $content) {
		$info = array ();
		$info ['touser'] = $openid;
		$info ['msgtype'] = $type;
		switch ($type) {
			case 'text' :
				$info ['text'] = $content;
				break;
			case 'news' :
				$info ['news'] = $content;
				break;
			case 'image' :
				$data = $this->media_upload ( $content, 'image' );
				$mediaid = $data ['media_id'];
				$info ['image'] = array (
						'media_id' => $mediaid 
				);
				break;
			case 'music' :
				$data = $this->media_upload ( $content ['thumb'], 'thumb' );
				$media_id = $data ['media_id'];
				$content ['thumb_media_id'] = $media_id;
				unset ( $content ['thumb'] );
				$info ['music'] = $content;
				break;
		}
		$accesstoken = $this->getAccessToken ();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $accesstoken;
		$return = $this->curlpost ( $url, $info, 3 );
		$data = json_decode ( $return, true );
		
		// 如果发送失败，添加到错误日志表 2014-09-23
		if ($data ['errcode'] == '40001' || $data ['errcode'] == '42001' || $data ['errcode'] == '40029') {
			$this->addErrorLog ( $return . '-' . json_encode_zh ( $info ), 'pubMsg' );
		}
		return json_decode ( $return, true );
	}
	
	/**
	 * 处理用户发过来的文本消息
	 *
	 * @param varchar $openid
	 *        	用户的openid
	 * @param varchar $text
	 *        	文本内容
	 */
	public function processText($openid, $text) {
		$this->redirectCustomer (); // 调用多客服
		
		if ($text == "审核状态") {
			$si = new si ();
			$info = $si->getData ( 'si_signup', "openid='{$openid}'", true );
			$type = array (
					'1' => '未审核',
					'2' => '审核通过',
					'3' => '审核未通过' 
			);
			if ($info) {
				$this->putMsg ( $openid, "text", array (
						'content' => $type [$info ['type']] 
				) );
			}
		}
	}
	
	/**
	 * 处理用户被动推送的地理位置信息
	 *
	 * @param varchar $openid
	 *        	用户的OPENDI
	 * @param float $Latitude
	 *        	地理位置纬度
	 * @param float $Longitude
	 *        	地理位置经度
	 * @param float $Precision
	 *        	地理位置精度
	 */
	public function processLocation($openid, $Latitude, $Longitude, $Precision) {
		$data ['openid'] = $openid;
		$data ['latitude'] = $Latitude;
		$data ['longitude'] = $Longitude;
		$data ['precision'] = $Precision;
		$url = "http://osgmobile.intel.com/index.php/api/users/track";
		// $header [] = "content-type: application/x-www-form-urlencoded; charset=UTF-8";
		$b = http_post ( $url, $data );
		// $this->db->table = "sys_demo";
		// $this->insert(array('content'=> serialize($b)));
		
		// $urls = "http://shanguang.buzzopt.com/index.php/index/alert";
		// // $header [] = "content-type: application/x-www-form-urlencoded; charset=UTF-8";
		// $a = http_post($urls, $data);
		// http_post($urls, array('abc'=>$b));
	}
	
	/**
	 * 处理用户扫描事件
	 *
	 * @param varchar $openid
	 *        	用户的OPENID
	 * @param int $eventkey
	 *        	EventKey
	 */
	public function processScan($openid, $eventkey) {
		$this->putMsg ( $openid, "text", array (
				'content' => '欢迎登录到 Intel CCE！ <a href=\"https://ccechina.intel.com/WXloginbind.aspx?UserInfo=$id&key=$key&RealName=$nickname\">点击登录</a>' 
		) );
	}
	
	/**
	 * 公众账号接入验证程序
	 */
	public function chk_token() {
		$echoStr = $_GET ["echostr"];
		if ($this->checkSignature ()) {
			echo $echoStr;
			exit ();
		} else {
			echo 'false';
			exit ();
		}
	}
	
	/**
	 * 配置项
	 *
	 * @return array
	 */
	public function config() {
		return array (
				'TOKEN' => TOKEN,
				'appid' => APPID,
				'appsecret' => APPSECRET 
		);
	}
	
	/**
	 * 验证的
	 * 
	 * @return boolean 返回true or false
	 */
	public function checkSignature() {
		$signature = $_GET ["signature"];
		$timestamp = $_GET ["timestamp"];
		$nonce = $_GET ["nonce"];
		
		$configArr = $this->config ();
		$tmpArr = array (
				$configArr ['TOKEN'],
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
	
	/**
	 * 信息处理中心
	 *
	 * @param varchar $string
	 *        	收到的信息
	 */
	public function processCenter($string) {
		$message = ( array ) simplexml_load_string ( $string, 'SimpleXMLElement', LIBXML_NOCDATA );
		// 最新的消息日志，分类型记录
		$getmsg = new getmsg ();
		$getmsg->addMsg ( $message );
		switch ($message ['MsgType']) {
			case 'text' :
				file_put_contents ( ROOT_PATH . 'tmpTest.txt', 1234567 );
				// $this->KeyWordProcessing($message['FromUserName'], $message['Content']);//处理关键词信息
				$this->processText ( $message ['FromUserName'], $message ['Content'] ); // 处理文本消息
				break;
			case 'voice' :
				break;
			case 'image' :
				$this->processImage ( $message ['FromUserName'], $message ['Content'] ); // 处理图片消息
				break;
			case 'location' :
				$this->processPutLocation ( $message ['FromUserName'], $message ['Location_X'], $message ['Location_Y'], $message ['xxxxxxx'] );
				break;
			case 'link' :
				break;
			// 事件推送
			case 'event' :
				switch ($message ['Event']) {
					case 'CLICK' : // 用户点击事件
						$this->processClick ( $message ['FromUserName'], $message ['EventKey'] ); // 处理用户点击事件
						break;
					
					case 'LOCATION' : // 获取用户地理位置
						$this->processLocation ( $message ['FromUserName'], $message ['Latitude'], $message ['Longitude'], $message ['Precision'] ); // 处理用户地理位置信息
						break;
					
					case 'subscribe' : // 用户订阅事件（包含扫描关注）
						$this->processSubscribe ( $message ['FromUserName'], $message ['EventKey'] ); // 处理关注事件(old)
						break;
					case 'unsubscribe' : // 用户取消订阅时间
						$this->unsubscribe ( $message ['FromUserName'], $message ['CreateTime'] );
						break;
					
					case 'SCAN' : // 扫描带场景值二维码事件
						$this->processScan ( $message ['FromUserName'], $message ['EventKey'] );
						break;
					case 'MASSSENDJOBFINISH' :
						$this->advancedApi_log ( $message );
						break;
					default :
						break;
				}
		}
	}
	
	/**
	 * 发送模板消息
	 */
	function send_template_msg($openid = '', $info = null) {
		$access_token = $this->getAccessToken ();
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
		$info ['touser'] = $openid;
		$return = $this->curlpost ( $url, $info, 3 );
		$data = json_decode ( $return, true );
		
		// 如果发送失败，添加到错误日志表 2014-09-23
		if ($data ['errcode'] == '40001' || $data ['errcode'] == '42001' || $data ['errcode'] == '40029') {
			$this->addErrorLog ( $return . '-' . json_encode_zh ( $info ), 'pubMsg' );
		}
		return json_decode ( $return, true );
	}
	public function KeyWordProcessing($openid, $word, $wechat) {
		/* 如果是标签就给用户加上标签 */
		$tag = new tag ();
		$tag_name = $tag->getTagByWhereSql ( " where keywords like '%--{$word}--%' " );
		
		if (! empty ( $tag_name )) {
			foreach ( $tag_name as $v ) {
				$tag->addUserTagRelation ( $v ['id'], array (
						'openid' => $openid 
				) );
			}
		}
		
		$word = strtolower ( $word );
		$send = FALSE;
		$KEY = new keyword ();
		$find_keyword_sql = "select * from sys_autoresponse where isdel='0' and response_genre = 'keyword'";
		
		$find_keyword_res = $this->db->query ( $find_keyword_sql );
		
		$conform_response = $find_keyword_res;
		$keywordList = $conform_response;
		foreach ( $keywordList as $keyword ) {
			/**
			 * 检查是否包含或等同关键字
			 */
			$state = $KEY->checkKeyword ( $keyword ['response_keyword'], $word, $keyword ['response_keytype'] );
			
			// file_put_contents(ROOT_PATH.'tmpTest.txt', '4444'.serialize($state));
			if ($state) {
				/**
				 * 检查该用户是否符合使用关键字的条件
				 */
				$send_state = $this->KeywordSendCondition ( $keyword, $openid );
				
				// file_put_contents('tmpTest.txt', '1234'.$send_state.json_encode($send_state));
				if ($send_state) {
					/**
					 * 使用文本发送
					 */
					if ($keyword ['response_mode'] == 1) {
						$str = htmlspecialchars ( $keyword ['response_content'] );
						$str = str_replace ( 'APP_PATH', APP_PATH, $str );
						$str = str_replace ( '<div>', '', $str );
						$str = str_replace ( '</div>', "\r\n", $str );
						$str = str_replace ( '<br>', "\r\n", $str );
						$content = array (
								'content' => $str 
						);
						$this->putMsg ( $openid, 'text', $content );
						$send = TRUE;
					/**
					 * 使用素材发送
					 */
					} else {
						
						$this->push_material ( $keyword ['response_mid'], $openid, $type = 2, $wechat );
						$send = TRUE;
					}
				}
				$KEY->mkKwLog ( $openid, $keyword ['response_keyword'], $word );
			}
		}
		/**
		 * 检查Z3000KeyWord
		 */
		// if(($this->Z3000_spectator($openid,$word) === false) || ($this->aboutZ3000($openid,$word) === false)){
		// return false;
		// }
		/**
		 * 检查其他关键字（旧关键字）
		 */
		$old_send = $this->processWord ( $openid, $word );
		/**
		 * 非关键字应答
		 */
		if (($old_send !== false) && ! $send) {
			$find_unkey = "select * from sys_autoresponse where isdel='0' and response_genre = 'text'";
			$res_unkey = $this->db->query ( $find_unkey );
			foreach ( $res_unkey as $unkey ) {
				/**
				 * 使用文本
				 */
				if ($unkey ['response_mode'] == 1) {
					$content = array (
							'content' => str_replace ( "APP_PATH", APP_PATH, urldecode ( $unkey ['response_content'] ) ) 
					);
					$this->putMsg ( $openid, 'text', $content );
					$send = TRUE;
				/**
				 * 使用素材发送
				 */
				} else {
					$this->push_material ( $unkey ['response_mid'], $openid, $type = 2 );
					$send = TRUE;
				}
			}
		}
		$info = array ();
		$info ['openid'] = $openid;
		$info ['content'] = $word;
		$info ['ctime'] = time ();
		$this->db->tableName = 'sys_msg_fankui';
		$this->insert ( $info );
	}
	/**
	 * 推送素材消息
	 * 
	 * @param number $materid
	 *        	素材id
	 * @param string $openid
	 *        	接收人的id,
	 * @param number $type
	 *        	响应方式 1 主动响应 2 被动响应
	 */
	function push_material($materialid = 0, $openid = null, $type = 1, $wechat) {
		$material = new material ();
		$temp = $material->getMaterialInfo ( $materialid );
		$mtype = $temp ['type'];
		$data = null;
		
		switch ($mtype) {
			case 'news' :
				$data = $temp ['content'] ['articles'];
				$result = $this->putMsg ( $openid, 'news', array (
						'articles' => $data 
				) );
				break;
			case 'image' :
				if ($type == 1) {
					$filepath = rtrim ( ROOT_PATH, '/' ) . $temp ['filepath'];
					$result = $this->putMsg ( $openid, 'image', $filepath );
				} else {
					$data = $this->media_upload ( rtrim ( ROOT_PATH, '/' ) . $temp ['filepath'], 'image' );
					$mediaid = $data ['media_id'];
					$this->message_image_send ( $mediaid, $wechat );
				}
				break;
		}
	}
	
	/**
	 * 发送被动响应图片消息
	 * 
	 * @param number $media_id        	
	 */
	function message_image_send($media_id = 0) {
		$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
		$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
		$fromUsername = $postObj->FromUserName;
		$toUsername = $postObj->ToUserName;
		$time = time ();
		$textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[image]]></MsgType>
					<Image>
					<MediaId><![CDATA[%s]]></MediaId>
					</Image>
					</xml>";
		$msgType = "text";
		$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $media_id );
		echo $resultStr;
	}
	
	/**
	 * 发送被动响应图文消息
	 * 
	 * @param string $contentStr        	
	 */
	function message_news_send($news = array(), $wechat) {
		$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
		$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
		$fromUsername = $postObj->FromUserName;
		$toUsername = $postObj->ToUserName;
		$time = time ();
		$data [] = $news;
		if (! isset ( $news ['title'] )) {
			$data = $news;
		}
		$resultStr = "<xml>
		<ToUserName><![CDATA[$fromUsername]]></ToUserName>
		<FromUserName><![CDATA[$toUsername]]></FromUserName>
		<CreateTime>$time</CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<ArticleCount>" . count ( $data ) . "</ArticleCount>
					<Articles>";
		
		foreach ( $data as $new ) {
			$resultStr .= "<item>
			<Title><![CDATA[" . $new ['title'] . "]]></Title>
			<Description><![CDATA[" . $new ['description'] . "]]></Description>
			<PicUrl><![CDATA[" . $new ['picurl'] . "]]></PicUrl>
					<Url><![CDATA[" . $new ['url'] . "]]></Url>
							</item>";
		}
		
		$resultStr .= "</Articles>
		</xml>";
		
		// $wechat->putMsg($message['FromUserName'],$type,$content);
		file_put_contents ( ROOT_PATH . 'tmpTest.txt', $resultStr );
		echo $resultStr;
	}
	/**
	 * 素材上传
	 * 
	 * @param string $filepath        	
	 * @param string $type        	
	 * @return mixed
	 */
	function media_upload($filepath = 'd:/a.jpg', $type = 'image') {
		$effectivetime = time () - 71 * 3600;
		
		$sql = "select * from sys_media where filepath = '$filepath'";
		$tmp = $this->db->query ( $sql );
		if ($tmp && $tmp [0] ['ctime'] > $effectivetime) {
			$data = $tmp [0];
		} else {
			$sql = "delete from sys_media where filepath = '$filepath'";
			$this->db->query ( $sql );
			
			$data = array (
					"media" => "@" . $filepath 
			);
			$access_token = $this->getAccessToken ();
			$url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?type=' . $type . '&access_token=' . $access_token;
			$data = curl_file_get_contents ( $url, $data );
			$data = json_decode ( $data, TRUE );
			if ($data) {
				$info = array ();
				$info ['filepath'] = $filepath;
				$info ['media_id'] = $data ['media_id'];
				if ($type == 'thumb') {
					$info ['media_id'] = $data ['thumb_media_id'];
					$data ['media_id'] = $data ['thumb_media_id'];
				}
				
				$info ['type'] = $type;
				$info ['ctime'] = time ();
				$this->db->tableName = 'sys_media';
				$this->insert ( $info );
			}
		}
		return $data;
	}
	
	/**
	 * 上传图文素材
	 * 
	 * @param string $filepath        	
	 * @param string $type        	
	 * @return Ambigous <mixed, 2>
	 */
	function media_uploads($articles = array()) {
		$data = array ();
		
		if ($articles && isset ( $articles ['articles'] )) {
			
			foreach ( $articles ['articles'] as $k => $v ) {
				$thumb = $v ['thumb'];
				$tmp = $this->media_upload ( $thumb, 'image' );
				$articles ['articles'] [$k] ['thumb_media_id'] = $tmp ['media_id'];
				unset ( $articles ['articles'] [$k] ['thumb'] );
			}
			$access_token = $this->getAccessToken ();
			$url = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=' . $access_token;
			$data = json_encode_zh ( $articles );
			$data = json_decode ( curl_file_get_contents ( $url, $data ), true );
		} else {
			return $data;
		}
		return $data;
	}
	/**
	 * 判断用户是否符合关键字发送条件筛选
	 */
	public function KeywordSendCondition($response, $openid) {
		// file_put_contents(ROOT_PATH.'tmpTest.txt',json_encode($response));
		if ($response ['response_condition'] == 'group' && $response ['response_groupid'] != 0) {
			// $membergroup = new membergroup();
			// $intel_fans = $membergroup->conform($response['response_groupid']);
			$sql = "select c.openid from sys_membergroup as a left join sys_mem_group_relation as b on a.id=b.groupid left join sys_member as c on b.mopenid=c.openid where a.id = {$response['response_groupid']}";
			$intel_fans = $this->db->query ( $sql );
		} else {
			$find_where = '1';
			/**
			 * 根据autoresponse表条件拼凑查找member表的where
			 */
			! empty ( $response ['response_sex'] ) && $find_where .= " and sex = {$response['response_sex']}";
			! empty ( $response ['response_source'] ) && $find_where .= " and sid IN ({$response['response_source']})";
			! empty ( $response ['response_type'] ) && $find_where .= " and type IN ({$response['response_type']})";
			! empty ( $response ['response_city'] ) && $find_where .= " and city = '{$response['response_city']}'";
			! empty ( $response ['response_province'] ) && $find_where .= " and province = '{$response['response_province']}'";
			// !empty($response['response_cross_min']) && $find_where .= " and interactive >= {$response['response_cross_min']}";
			// !empty($response['response_cross_max']) && $find_where .= " and interactive <= {$response['response_cross_max']}";
			
			$tmp_where = '';
			if (! empty ( $response ['response_cross_min'] )) {
				$tmp_where .= " num >= {$response['response_cross_min']} ";
			}
			if (! empty ( $response ['response_cross_max'] )) {
				if ($tmp_where) {
					$tmp_where .= " AND num <= {$response['response_cross_max']} ";
				} else {
					$tmp_where .= " num <= {$response['response_cross_max']} ";
				}
			}
			$tmp_openid = '';
			if ($tmp_where) {
				$sql = "select fromusername, count(*) as num from sys_get_msg GROUP BY fromusername HAVING $tmp_where ";
				$jh = $this->query ( $sql );
				if ($jh) {
					foreach ( $jh as $v ) {
						$tmp_arr [] = "'{$v['fromusername']}'";
					}
					$tmp_openid = implode ( ',', $tmp_arr );
				}
			}
			if ($tmp_openid) {
				$find_where .= " and openid IN ({$tmp_openid})";
			}
			
			if ($find_where == '1') {
				return true;
			}
			$find_fans = "select openid from sys_member where {$find_where}";
			$intel_fans = $this->db->query ( $find_fans );
			file_put_contents ( ROOT_PATH . 'tmpTest.txt', $find_fans . json_encode ( $intel_fans ) );
		}
		
		$thistime = date ( 'Y-m-d H:i:s', time () );
		
		$fans_openid_str = '';
		/**
		 * 判断是否在用户筛选条件中
		 */
		foreach ( $intel_fans as $fans ) {
			$fans_openid_str .= $fans ['openid'] . ',';
		}
		$fans_openid_str = substr ( $fans_openid_str, 0, strlen ( $fans_openid_str ) - 1 );
		
		$send_state = strpos ( $fans_openid_str, $openid );
		return $send_state === false ? false : true;
	}
	/**
	 * 关键词响应
	 *
	 * @param array $message
	 *        	消息详情
	 * @param array $accountInfo
	 *        	公众号详情
	 * @param array $memberInfo
	 *        	用户详情
	 * @param object $wechat
	 *        	实例化后的微信接口类
	 */
	public function processTextMessage($message, $accountInfo, $memberInfo, $wechat) {
		/* 如果是标签就给用户加上标签 */
		$tag = new tag ();
		$tag_name = $tag->getTagByWhereSql ( " where keywords like '%--{$message['Content']}--%' " );
		
		if (! empty ( $tag_name )) {
			foreach ( $tag_name as $v ) {
				$tag->addUserTagRelation ( $v ['id'], array (
						'openid' => $message ['FromUserName'] 
				) );
			}
		}
		
		$REPLY = new reply ();
		$return = $REPLY->getInfo ( array (
				'type' => '1',
				'keyword' => $message ['Content'],
				'status' => '1' 
		), TRUE );
		if ($return) {
			if ($return ['materialid'] == '0') // 文本
{
				$content = array (
						'content' => $return ['content'] 
				);
				$type = 'text';
			} else { // 素材
				$temp = $this->mkNews ( $return ['materialid'] );
				// $content = array('articles'=>$temp['content']);
				$content = $temp ['content'];
				$type = $temp ['type'];
			}
			$wechat->putMsg ( $message ['FromUserName'], $type, $content );
		} else {
			$return = $REPLY->getInfo ( array (
					'type' => '3',
					'status' => '1' 
			), TRUE );
			if ($return) {
				if ($return ['materialid'] == '0') // 文本
{
					$content = array (
							'content' => $return ['content'] 
					);
					$type = 'text';
				} else { // 素材
					$temp = $this->mkNews ( $return ['materialid'] );
					// $content = array('articles'=>$temp['content']);
					$content = $temp ['content'];
					$type = $temp ['type'];
				}
				$wechat->putMsg ( $message ['FromUserName'], $type, $content );
			}
		}
	}
	
	/**
	 * 关注回复 & 自动回复
	 *
	 * @param array $message
	 *        	消息详情
	 * @param array $accountInfo
	 *        	公众号详情
	 * @param array $memberInfo
	 *        	用户详情
	 * @param object $wechat
	 *        	实例化后的微信接口类
	 */
	public function processSubscribeMsg($message, $accountInfo, $memberInfo, $wechat) {
		$autoresponse = new autoresponse ();
		$return = $autoresponse->query ( "select * from sys_autoresponse where isdel='0' and response_genre='subscribe'" );
		if ($return) {
			foreach ( $return as $r ) {
				if ($r ['response_mode'] == '1') // 文本
{
					$content = array (
							'content' => $r ['response_content'] 
					);
					$type = 'text';
				} else { // 素材
					$temp = $this->mkNews ( $r ['response_mid'] );
					// $content = array('articles'=>$temp['content']);
					// file_put_contents('./uploads/a.html',$r['response_mid']);
					$content = $temp ['content'];
					$type = $temp ['type'];
				}
				$wechat->putMsg ( $message ['FromUserName'], $type, $content );
			}
		}
	}
	
	/**
	 * 调起多客服功能
	 */
	public function redirectCustomer() {
		$postStr = $GLOBALS ["HTTP_RAW_POST_DATA"];
		$postObj = simplexml_load_string ( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
		$fromUsername = $postObj->FromUserName;
		$toUsername = $postObj->ToUserName;
		$time = time ();
		$textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[transfer_customer_service]]></MsgType>
			</xml>";
		$resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time );
		echo $resultStr;
		// return false;
	}
	public function processClick($message, $accountInfo, $memberInfo, $obj) {
		$openid = $message ['FromUserName'];
		$eventKey = $message ['EventKey'];
		
		if ($eventKey == '123456') {
			$this->putMsg ( $openid, 'text', array (
					'content' => '感谢您对英特尔中国智造基地项目的关注与支持。有关项目申请、项目审核咨询及其他与项目相关的问题，均可直接通过微信号与英特尔客服联系。' 
			) );
			exit ();
		}
		$MENU = new menu ();
		$info = $MENU->getMenu ( '`key` = "' . $eventKey . '"' );
		
		switch ($info [0] ['type']) {
			case '2' : // 文本
				$result = $obj->putMsg ( $openid, 'text', array (
						'content' => $info [0] ['content'] 
				) );
				break;
			case '3' : // 素材
				$temp = $this->mkNews ( $info [0] ['content'] );
				$result = $obj->putMsg ( $openid, $temp ['type'], $temp ['content'] );
				break;
		}
	}
	public function mkNews($id) {
		$MATER = new material ();
		$return = $MATER->getMaterialInfo ( $id );
		return $return;
	}
	public function filterImage($str) {
		$arr = array ();
		$pattern = '/([a-zA-Z0-9\x{4e00}-\x{9fa5}])+/u';
		preg_match_all ( $pattern, $str, $arr );
		$res = implode ( '', $arr [0] );
		return $res;
	}
	
	/**
	 * 获取所有自定义菜单内容
	 *
	 * @return array
	 */
	public function getAllMenu() {
		$menu = new menu ();
		$return = $menu->getAllMenu ();
		return $return;
	}
	public function wDemo($content) {
		$this->tableName = 'demo';
		$info = array ();
		$info ['content'] = json_encode ( $content );
		
		$this->insert ( $info );
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
	
	/**
	 * 新增错误日志
	 *
	 * @param varchar $content        	
	 * @param varchar $source        	
	 */
	public function addErrorLog($content, $source) {
		$info = array ();
		$info ['content'] = $content;
		$info ['source'] = $source;
		$info ['ctime'] = time ();
		
		$this->db->tableName = 'sys_weixin_errlog';
		$this->insert ( $info );
	}
	
	/**
	 * 通过地区名字获取地区编码
	 *
	 * @param varchar $name
	 *        	地区名字
	 * @param int $type
	 *        	类型：1省份 2城市
	 */
	public function locationToCode($name, $type = 1, $fid = 0) {
		$L = new location ();
		
		if ($type == 1) {
			$provinceList = $L->provinceList ();
			$code = $provinceList [$name];
		}
		
		if ($type == 2) {
			$this->db->tableName = 'sys_location';
			$info = $this->select ( 'id', 'fid = ' . $fid . ' AND name ="' . $name . '"' );
			$code = $info [0] ['id'];
		}
		return $code;
	}
}