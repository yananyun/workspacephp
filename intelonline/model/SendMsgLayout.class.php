<?php

/**
 * sendMsg.class.php
 * 
 * @author:ww
 * @since:2014.05.20
 */
class SendMsgLayout extends Module {
	public function __construct() {
		$this->__construct ();
	}
	
	/**
	 * 图文处理
	 * 
	 * @param $message array('FromUserName'
	 *        	=> '','ToUserName' => '','Content' => '') $data array('content' => '','description' => '','mediaurl' => '','url' => '')
	 */
	public function processNews($message = '', $data = '') {
		$fromUsername = $message ['FromUserName'];
		$toUsername = $message ['ToUserName'];
		$keyword = trim ( $message ['Content'] );
		$time = time ();
		$newsTpl = '<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%u</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>1</ArticleCount>
						<Articles>
							<item>
							<Title><![CDATA[%s]]></Title> 
							<Description><![CDATA[%s]]></Description>
							<PicUrl><![CDATA[%s]]></PicUrl>
							<Url><![CDATA[%s]]></Url>
							</item>
						</Articles>
					</xml>';
		$msgType = "news";
		$title = $data ['content'];
		$desc = $data ['description'];
		$picurl = $data ['mediaurl'];
		$url = $data ['url'];
		$resultStr = sprintf ( $newsTpl, $fromUsername, $toUsername, $time, $msgType, $title, $desc, $picurl, $url );
		return $resultStr;
	}
	
	/**
	 * 图片和语音处理
	 * 
	 * @param $message array('FromUserName'
	 *        	=> '','ToUserName' => '','Content' => '') $data array('mediaurl' => '')
	 *        	
	 */
	public function processImageVoice($message = '', $data = '') {
		$media = $this->uplode_media ( $data ['mediaurl'], 'image' );
		$fromUsername = $message ['FromUserName'];
		$toUsername = $message ['ToUserName'];
		$keyword = trim ( $message ['Content'] );
		$time = time ();
		$newsTpl = '<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%u</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Image>
						<MediaId><![CDATA[%s]]></MediaId>
						</Image>
					</xml>';
		$resultStr = sprintf ( $newsTpl, $fromUsername, $toUsername, $time, 'image', $media ['media_id'] );
		return $resultStr;
	}
	
	/**
	 * 视频处理
	 * 
	 * @param $message array('FromUserName'
	 *        	=> '','ToUserName' => '','Content' => '') $data array('content' => '','description' => '','mediaurl' => '')
	 *        	
	 */
	public function processVideo($message = '', $data = '') {
		$msgType = 'video';
		$media = $this->uplode_media ( $data ['mediaurl'], $msgType );
		$fromUsername = $message ['FromUserName'];
		$toUsername = $message ['ToUserName'];
		$keyword = trim ( $message ['Content'] );
		$time = time ();
		$videoTpl = '<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%u</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Video>
							<MediaId><![CDATA[%s]]></MediaId>
							<Title><![CDATA[%s]]></Title>
							<Description><![CDATA[%s]]></Description>
						</Video> 
					</xml>';
		$msgType = 'video';
		$resultStr = sprintf ( $videoTpl, $fromUsername, $toUsername, $time, $msgType, $media ['media_id'], $data ['content'], $data ['description'] );
		return $resultStr;
	}
	
	/**
	 * 音乐处理
	 * 
	 * @param $message array('FromUserName'
	 *        	=> '','ToUserName' => '','Content' => '') $data array('content' => '','description' => '','mediaurl' => '','hqmediaurl' => '')
	 *        	
	 */
	public function processMusic($message = '', $data = '') {
		$msgType = 'music';
		$media = $this->uplode_media ( $data ['thubmurl'], $msgType );
		$fromUsername = $message ['FromUserName'];
		$toUsername = $message ['ToUserName'];
		$keyword = trim ( $message ['Content'] );
		$time = time ();
		$videoTpl = '<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%u</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Music>
						<Title><![CDATA[%s]]></Title>
						<Description><![CDATA[%s]]></Description>
						<MusicUrl><![CDATA[%s]]></MusicUrl>
							<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
							<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
						</Music>
					</xml>';
		$msgType = 'music';
		$resultStr = sprintf ( $videoTpl, $fromUsername, $toUsername, $time, $msgType, $data ['content'], $data ['description'], $data ['mediaurl'], $data ['hqmediaurl'], $media ['media_id'] );
		return $resultStr;
	}
	
	/**
	 * 文本处理
	 */
	public function processText($message = '', $data = '') {
		$msgType = 'text';
		$fromUsername = $message ['FromUserName'];
		$toUsername = $message ['ToUserName'];
		$keyword = trim ( $message ['Content'] );
		$time = time ();
		$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
					  </xml>";
		$msgType = "text";
		$contentSt = $data ['content'];
		return $resultStr = sprintf ( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentSt );
	}
	
	/**
	 * 上传多媒体
	 * 
	 * @param
	 *        	$msgType
	 * @param $url return
	 *        	array()
	 */
	function uplode_media($url, $msgType = 'image') {
		$accesstoken = $this->getAccessToken ();
		$media = array (
				'media' => '@' . ROOT_PATH . $url 
		);
		$data = curl_file_get_contents ( "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=$accesstoken&type=$msgType", $media );
		return json_decode ( $data, true );
	}
}