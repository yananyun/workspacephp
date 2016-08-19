<?php
/**
 * Wechat.class.php
 *		微信API
 * 
 * @author:Harry
 * @link http://haoshengzhide.com/
 * @since: 2014.5.19
 */
class WechatApi {
	public $accesstoken;
	public $CURL;
	
	/**
	 * 构造方法
	 *
	 * @param string $access_token
	 *        	基本都需填写公众号的accesstoken，不需要填写的请填写任意字符
	 */
	public function __construct($access_token = NULL) {
		$sql = "select * from sys_account where id = 1";
		$M = new wechat ();
		$data = $M->query ( $sql );
		
		$this->accesstoken = $data [0] ['access_token'];
		$this->CURL = new CurlItems ();
	}
	
	/**
	 * 发送客服消息
	 *
	 * @param string $openid
	 *        	要发送对象的openid
	 * @param string $type
	 *        	消息类型：text image voice video music news
	 * @param array $content
	 *        	消息内容
	 * @link ：http://mp.weixin.qq.com/wiki/index.php?title=%E5%8F%91%E9%80%81%E5%AE%A2%E6%9C%8D%E6%B6%88%E6%81%AF#.E5.8F.91.E9.80.81.E6.96.87.E6.9C.AC.E6.B6.88.E6.81.AF
	 * @example case text:
	 *          array( 'content' => 'xxxxxxxxxxxxxxxxx' );
	 *          case image:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxx' );
	 *          case voice:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxx' );
	 *          case video:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxx',
	 *          'title' => 'xxxxxxxxxxxxxxxx',
	 *          'description'=> 'xxxxxxxxxxxxxxxxx'
	 *          );
	 *          case music:
	 *          array( 'title' => 'xxxxxxxxxxxxxxxx',
	 *          'description'=> 'xxxxxxxxxxxxxxxxx',
	 *          'musicurl' => 'xxxxxxxxxxxxxxxxx',
	 *          'hqmusicurl'=> 'xxxxxxxxxxxxxxxxx',
	 *          'thumb_media_id'=>'xxxxxxxxxxxxxxxxxxxx',
	 *          );
	 *          case news:
	 *          array('articles' => array(
	 *          array(
	 *          'title' => 'xxxxxxxxxxxxxxxx',
	 *          'description'=> 'xxxxxxxxxxxxxxxxx',
	 *          'url' => 'xxxxxxxxxxxxxxxxx',
	 *          'picurl'=> 'xxxxxxxxxxxxxxxxx',
	 *          ),
	 *          array(......),
	 *          ));
	 *         
	 * @return array errcode错误码 errmsg错误信息
	 */
	public function putMsg($openid, $type, $content) {
		$info = array ();
		$info ['touser'] = $openid;
		$info ['msgtype'] = $type;
		$info [$type] = $content;
		
		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $this->accesstoken;
		$return = $this->curlpost ( $url, $info, 3 );
		return json_decode ( $return, true );
	}
	
	/**
	 * 上传图文消息素材
	 *
	 * @param string $openid
	 *        	要发送对象的openid
	 * @param array $content
	 *        	消息内容
	 * @link ：http://mp.weixin.qq.com/wiki/index.php?title=%E9%AB%98%E7%BA%A7%E7%BE%A4%E5%8F%91%E6%8E%A5%E5%8F%A3
	 * @example array(
	 *          "thumb_media_id" => "qI6_Ze_6PtV7svjolgs-rN6stStuHIjs9_DidOHaj0Q-mwvBelOXCFZiq2OsIU-p",
	 *          "author" => "xxx",
	 *          "title" => "HappyDay",
	 *          "content_source_url" => "www.qq.com",
	 *          "content" => "content",
	 *          "digest" => "digest"
	 *          ),
	 *          array(......),
	 *         
	 * @return array type媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb），次数为news，即图文消息 media_id媒体文件/图文消息上传后获取的唯一标识 created_at 媒体文件上传时间
	 */
	public function uploadNewsMaterial($content) {
		$info = array ();
		$info ['articles'] = $content;
		
		$url = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=' . $this->accesstoken;
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
	}
	
	/**
	 * 根据分组进行群发
	 *
	 * @param int $groupid
	 *        	分组ID
	 * @param string $type
	 *        	消息类型：text image voice mpvideo music mpnews
	 * @param array $content
	 *        	消息内容
	 *        	
	 * @return array errcode错误码 errmsg错误信息 msg_id消息ID
	 *        
	 * @link ：http://mp.weixin.qq.com/wiki/index.php?title=%E9%AB%98%E7%BA%A7%E7%BE%A4%E5%8F%91%E6%8E%A5%E5%8F%A3#.E6.A0.B9.E6.8D.AEOpenID.E5.88.97.E8.A1.A8.E7.BE.A4.E5.8F.91
	 * @example case text:
	 *          array( 'content' => 'xxxxxxxxxxxxxxxxx' );
	 *          case image:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxx' );
	 *          case voice:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxx' );
	 *          case mpvideo:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxx' );
	 *          case music:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxxxxx' );
	 *          case mpnews:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxxxxx' );
	 *         
	 */
	public function putGroupMsgByGroupId($groupid, $type, $content) {
		switch ($type) {
			case 'news' :
				$type = 'mpnews';
				break;
			case 'video' :
				$type = 'mpvideo';
				break;
		}
		
		$info = array ();
		$info ['filter'] ['group_id'] = $groupid;
		$info ['msgtype'] = $type;
		$info [$type] = $content;
		
		$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . $this->accesstoken;
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
	}
	
	/**
	 * 根据OpenID列表群发
	 *
	 * @param array $openidArr
	 *        	如：array( [0] => OPENID1,[1] => OPENID2)
	 * @param string $type
	 *        	消息类型：text image voice mpvideo music mpnews
	 * @param array $content
	 *        	消息内容
	 *        	
	 * @return array errcode错误码 errmsg错误信息 msg_id消息ID
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E9%AB%98%E7%BA%A7%E7%BE%A4%E5%8F%91%E6%8E%A5%E5%8F%A3#.E6.A0.B9.E6.8D.AEOpenID.E5.88.97.E8.A1.A8.E7.BE.A4.E5.8F.91
	 * @example case text:
	 *          array( 'content' => 'xxxxxxxxxxxxxxxxx' );
	 *          case image:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxx' );
	 *          case voice:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxx' );
	 *          case mpvideo:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxx' );
	 *          case music:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxxxxx' );
	 *          case mpnews:
	 *          array( 'media_id' => 'xxxxxxxxxxxxxxxxxxxx' );
	 *         
	 */
	public function putGroupMsgByOpenIds($openidArr, $type, $content) {
		switch ($type) {
			case 'news' :
				$type = 'mpnews';
				break;
			case 'video' :
				$type = 'mpvideo';
				break;
		}
		
		$info = array ();
		$info ['touser'] = $openidArr;
		$info ['msgtype'] = $type;
		$info [$type] = $content;
		
		$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=' . $this->accesstoken;
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
	}
	
	/**
	 * 上传素材获取media id
	 *
	 * @param string $type
	 *        	素材类型
	 * @param string $content
	 *        	内容
	 * @return array
	 */
	public function getMediaIdByUpload($type, $content) {
		switch ($type) {
			case 'image' :
				$pushContent = array (
						"media" => "@" . trim ( $content, '/' ) 
				);
				break;
		}
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$this->accesstoken}&type=$type";
		$return = $this->curlget ( $url, $pushContent );
		return json_decode ( $return, true );
	}
	public function getMediaIdByNews($content) {
		$url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token={$this->accesstoken}";
		$return = $this->curlget ( $url, $content );
		return json_decode ( $return, TRUE );
	}
	
	/**
	 * 删除群发
	 *
	 * @param int $msg_id
	 *        	信息ID
	 * @return array errcode 错误码 errmsg 错误信息
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E9%AB%98%E7%BA%A7%E7%BE%A4%E5%8F%91%E6%8E%A5%E5%8F%A3#.E6.A0.B9.E6.8D.AEOpenID.E5.88.97.E8.A1.A8.E7.BE.A4.E5.8F.91
	 */
	public function deleteGroupMsg($msg_id) {
		$info = array ();
		$info ['msgid'] = $msg_id;
		
		$url = 'https://api.weixin.qq.com//cgi-bin/message/mass/delete?access_token=' . $this->accesstoken;
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
	}
	
	/**
	 * 创建二维码ticket
	 *
	 * @param int $scene_id
	 *        	场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
	 * @param string $type
	 *        	二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久
	 * @param int $expire_second
	 *        	该二维码有效时间，以秒为单位。 最大不超过1800
	 * @return array ticket 获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。 expire_seconds 二维码的有效时间，以秒为单位。最大不超过1800。
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E7%94%9F%E6%88%90%E5%B8%A6%E5%8F%82%E6%95%B0%E7%9A%84%E4%BA%8C%E7%BB%B4%E7%A0%81
	 */
	public function makeQrTicket($scene_id, $type, $expire_second = 1800) {
		$info = array ();
		$info ['action_name'] = $type;
		$info ['action_info'] ['scene'] ['scene_id'] = $scene_id;
		if ($type == 'QR_SCENE') {
			$info ['expire_seconds'] = $expire_second;
		}
		
		$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $this->accesstoken;
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
	}
	
	/**
	 * 通过ticket换取二维码
	 *
	 * @param string $ticket
	 *        	获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。
	 * @return string 图片的内容，直接file_get_contents保存图片
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E7%94%9F%E6%88%90%E5%B8%A6%E5%8F%82%E6%95%B0%E7%9A%84%E4%BA%8C%E7%BB%B4%E7%A0%81
	 */
	public function getImageByTicket($ticket) {
		$url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode ( $ticket );
		$return = $this->curlget ( $url );
		return $return;
	}
	
	/**
	 * 获取用户基本信息
	 * 
	 * @param string $openid
	 *        	普通用户的标识，对当前公众号唯一
	 * @param string $lang
	 *        	返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语。默认简体
	 * @return array 正确时：subscribe用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。 openid用户的标识，对当前公众号唯一 nickname用户的昵称 sex用户的性别，值为1时是男性，值为2时是女性，值为0时是未知 city用户所在城市 country用户所在国家 province用户所在省份 language用户的语言，简体中文为zh_CN headimgurl用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空 subscribe_time用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
	 *         错误时：错误时微信会返回错误码等信息，JSON数据包示例如下（该示例为AppID无效错误）: {"errcode":40013,"errmsg":"invalid appid"}
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E8%8E%B7%E5%8F%96%E7%94%A8%E6%88%B7%E5%9F%BA%E6%9C%AC%E4%BF%A1%E6%81%AF
	 */
	public function getUserInfoByOpenid($openid, $lang = 'zh_CN') {
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$this->accesstoken}&openid=$openid&lang=$lang";
		$return = $this->curlget ( $url );
		return json_decode ( $return, true );
	}
	
	/**
	 * 获取关注者列表
	 *
	 * @param string $next_openid
	 *        	第一个拉取的OPENID，不填默认从头开始拉取
	 * @return array 正确时：total关注该公众账号的总用户数 count拉取的OPENID个数，最大值为10000 data列表数据，OPENID的列表 next_openid拉取列表的后一个用户的OPENID
	 *         错误时：{"errcode":40013,"errmsg":"invalid appid"}
	 *         P.S.当公众号关注者数量超过10000时，可通过填写next_openid的值，从而多次拉取列表的方式来满足需求。具体而言，就是在调用接口时，将上一次调用得到的返回中的next_openid值，作为下一次调用中的next_openid值。
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E8%8E%B7%E5%8F%96%E5%85%B3%E6%B3%A8%E8%80%85%E5%88%97%E8%A1%A8
	 */
	public function getUsers($next_openid = NULL) {
		if ($next_openid === NULL) {
			$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$this->accesstoken}";
		} else {
			$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$this->accesstoken}&next_openid=$next_openid";
		}
		
		$return = $this->curlget ( $url );
		return json_decode ( $return, true );
	}
	
	/**
	 * 创建分组
	 *
	 * @param string $name
	 *        	分组名字（30个字符以内）
	 * @return array 正常时：id分组id，由微信分配 name分组名字，UTF8编码
	 *         错误时：{"errcode":40013,"errmsg":"invalid appid"}
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E5%88%86%E7%BB%84%E7%AE%A1%E7%90%86%E6%8E%A5%E5%8F%A3
	 */
	public function createGroup($name) {
		$info = array ();
		$info ['group'] ['name'] = $name;
		
		$url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token={$this->accesstoken}";
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
	}
	
	/**
	 * 查询所有分组
	 *
	 * @return array 正常时：groups公众平台分组信息列表 id分组id，由微信分配 name分组名字，UTF8编码 count分组内用户数量
	 *         错误时：{"errcode":40013,"errmsg":"invalid appid"}
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E5%88%86%E7%BB%84%E7%AE%A1%E7%90%86%E6%8E%A5%E5%8F%A3
	 */
	public function getGroup() {
		$url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token={$this->accesstoken}";
		$return = $this->curlget ( $url );
		return json_decode ( $return, true );
	}
	
	/**
	 * 查询用户所在分组
	 *
	 * @param string $openid
	 *        	用户的OpenID
	 * @return array 正常时：groupid 用户所属的groupid
	 *         错误时：{"errcode":40013,"errmsg":"invalid appid"}
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E5%88%86%E7%BB%84%E7%AE%A1%E7%90%86%E6%8E%A5%E5%8F%A3#.E6.9F.A5.E8.AF.A2.E7.94.A8.E6.88.B7.E6.89.80.E5.9C.A8.E5.88.86.E7.BB.84
	 */
	public function getGroupByOpenid($openid) {
		$info = array ();
		$info ['openid'] = $openid;
		
		$url = 'https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=' . $this->accesstoken;
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
	}
	
	/**
	 * 修改分组名
	 *
	 * @param int $id
	 *        	分组id，由微信分配
	 * @param string $name分组名字（30个字符以内）        	
	 * @return array {"errcode": 0, "errmsg": "ok"}
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E5%88%86%E7%BB%84%E7%AE%A1%E7%90%86%E6%8E%A5%E5%8F%A3#.E4.BF.AE.E6.94.B9.E5.88.86.E7.BB.84.E5.90.8D
	 */
	public function updateGroupById($id, $name) {
		$info = array ();
		$info ['group'] ['id'] = $id;
		$info ['group'] ['name'] = $name;
		
		$url = 'https://api.weixin.qq.com/cgi-bin/groups/update?access_token=' . $this->accesstoken;
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
	}
	
	/**
	 * 移动用户分组
	 *
	 * @param string $openid
	 *        	用户唯一标识符
	 * @param string $to_groupid
	 *        	分组id
	 * @return array {"errcode": 0, "errmsg": "ok"}
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E5%88%86%E7%BB%84%E7%AE%A1%E7%90%86%E6%8E%A5%E5%8F%A3#.E7.A7.BB.E5.8A.A8.E7.94.A8.E6.88.B7.E5.88.86.E7.BB.84
	 */
	public function updateGroupOfMember($openid, $to_groupid) {
		$info = array ();
		$info ['openid'] = $openid;
		$info ['to_groupid'] = $to_groupid;
		
		$url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=' . $this->accesstoken;
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
	}
	
	/**
	 * 获取access token
	 *
	 * @param int $appid
	 *        	第三方用户唯一凭证
	 * @param string $appsecret
	 *        	第三方用户唯一凭证密钥，即appsecret
	 * @param string $grant_type
	 *        	获取access_token填写client_credential
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E8%8E%B7%E5%8F%96access_token
	 *      
	 * @return array
	 */
	public function getAccessTokenByAccountInfo($appid, $appsecret, $grant_type = 'client_credential') {
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=' . $grant_type . '&appid=' . $appid . '&secret=' . $appsecret;
		$return = $this->curlget ( $url, $info );
		return json_decode ( $return, true );
	}
	
	/**
	 * 下载多媒体文件
	 *
	 * @param string $media_id
	 *        	媒体文件ID
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E4%B8%8A%E4%BC%A0%E4%B8%8B%E8%BD%BD%E5%A4%9A%E5%AA%92%E4%BD%93%E6%96%87%E4%BB%B6
	 *      
	 * @return string 返回内容请直接file_put_contents
	 */
	public function downloadMedia($media_id) {
		$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $this->accesstoken . '&media_id=' . $media_id;
		$return = $this->curlget ( $url );
		return $return;
	}
	
	/**
	 * 创建自定义菜单
	 *
	 * @param array $content
	 *        	已经组合好的自定义菜单
	 * @return array
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E8%87%AA%E5%AE%9A%E4%B9%89%E8%8F%9C%E5%8D%95%E5%88%9B%E5%BB%BA%E6%8E%A5%E5%8F%A3
	 */
	public function createMenu($content) {
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $this->accesstoken;
		$return = $this->curlpost ( $url, $content );
		return json_decode ( $return, true );
	}
	
	/**
	 * 自定义菜单查询接口
	 *
	 * @return array
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E8%87%AA%E5%AE%9A%E4%B9%89%E8%8F%9C%E5%8D%95%E6%9F%A5%E8%AF%A2%E6%8E%A5%E5%8F%A3
	 */
	public function getMenu() {
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=' . $this->accesstoken;
		$return = $this->curlget ( $url );
		return json_decode ( $return, true );
	}
	
	/**
	 * 自定义菜单删除接口
	 *
	 * @return array
	 * @link http://mp.weixin.qq.com/wiki/index.php?title=%E8%87%AA%E5%AE%9A%E4%B9%89%E8%8F%9C%E5%8D%95%E5%88%A0%E9%99%A4%E6%8E%A5%E5%8F%A3
	 */
	public function deleteMenu() {
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $this->accesstoken;
		$return = $this->curlget ( $url );
		return json_decode ( $return, true );
	}
	
	/**
	 * CURL POST METHOD
	 *
	 * @param string $url
	 *        	要提交的URL
	 * @param array $data
	 *        	数据内容
	 * @param int $type
	 *        	json打包类型
	 *        	
	 * @return array
	 */
	public function curlpost($url, $data, $type = 1) {
		if ($type == 1) {
			$return = $this->CURL->post ( $url, (json_encode_zh ( $data )) );
		} elseif ($type == 2) {
			$return = $this->CURL->post ( $url, json_encode ( $data ) );
		} elseif ($type == 3) {
			$return = $this->CURL->post ( $url, jsonToZh ( json_encode ( $data ) ) );
		}
		
		return $return;
	}
	
	/**
	 * CURL GET METHOD
	 *
	 * @param string $url
	 *        	要提交的URL
	 * @return array
	 */
	public function curlget($url, $postFields = null) {
		set_time_limit ( 0 );
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 100 );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:26.0) Gecko/20100101 Firefox/26.0' );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		if ($postFields) {
			curl_setopt ( $ch, CURLOPT_POST, true );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
		}
		$r = curl_exec ( $ch );
		curl_close ( $ch );
		return $r;
	}
}