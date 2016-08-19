<?php

/**
 * 消息日志
 * @author Administrator
 *
 */
class getmsg extends Module {
	
	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->db->tableName = 'sys_get_msg';
		// $this->db->tableName = 'sys_weixin_getmsg';
	}
	
	/**
	 * 添加消息日志
	 * 
	 * @param string $message        	
	 */
	public function addMsg($message = null) {
		$info = array ();
		$info ['tousername'] = $message ['ToUserName'];
		$info ['fromusername'] = $openid = $info ['fromusername'] = $message ['FromUserName'];
		$info ['createtime'] = $message ['CreateTime'];
		$info ['ctime'] = time ();
		$info ['msgid'] = $message ['MsgId'];
		$info ['msg_json'] = json_encode_zh ( $message );
		$info ['msgtype'] = $message ['MsgType'];
		// 文本消息 内容
		if (isset ( $message ['Content'] )) {
			$info ['content'] = $message ['Content'];
		}
		// 图片消息链接
		if (isset ( $message ['PicUrl'] )) {
			$info ['picurl'] = $message ['PicUrl'];
		}
		// 视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
		if (isset ( $message ['ThumbMediaId'] )) {
			$info ['thumbmediaid'] = $message ['ThumbMediaId'];
		}
		// 媒体下载ID
		if (isset ( $message ['MediaId'] )) {
			$info ['mediaid'] = $message ['MediaId'];
		}
		// 链接消息
		if (isset ( $message ['Url'] )) {
			$info ['url'] = $message ['Url'];
		}
		// 地理位置消息
		if (isset ( $message ['Label'] )) {
			$info ['label'] = $message ['Label'];
		}
		// 事件类型
		if ($message ['Event']) {
			$info ['event'] = $message ['Event'];
		}
		// 事件key值
		if ($message ['EventKey']) {
			$info ['eventkey'] = str_replace ( 'qrscene_', '', $message ['EventKey'] );
		}
		
		// 语音识别结果
		if ($message ['Recognition']) {
			$info ['recognition'] = $message ['Recognition'];
		}
		
		// 语音识别结果
		if ($message ['Format']) {
			$info ['format'] = $message ['Format'];
		}
		$this->insert ( $info );
	}
}
