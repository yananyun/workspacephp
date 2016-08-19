<?php
/**
 * 主动响应model
 * @author wangying
 * @since 2014.5.20
 * 
 * */
class response extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->M = new wechat ();
	}
	/**
	 *
	 * @param string $msg_type
	 *        	信息类型 text|image|voice|video|music|news
	 * @param array $ids
	 *        	指定发送的素材id，如果没有指定id,取最新的同类型消息
	 * @param number $msglimit
	 *        	发送条数
	 * @return array material message 返回信息数组，数组下标按微信标准，第一维以0开始
	 */
	public function get_material($msg_type, $ids = array(), $msglimit = 1) {
		$ids = implode ( $ids, ',' );
		$where = ' 1';
		$column = '';
		if ($msglimit != 1 && $msg_type != 'news') {
			return false;
		}
		// 用于拼接sql的数组
		$material = array (
				'text' => array (
						'type' => 1,
						'content' => 'content' 
				),
				'news' => array (
						'type' => 5,
						'title' => 'content',
						'description' => 'description',
						'url' => 'linkurl',
						'picurl' => 'mediaurl' 
				) 
		);
		foreach ( $material [$msg_type] as $key => $val ) {
			if ($key == 'type') {
				$where .= " and type = {$val}";
				continue;
			}
			$column .= " {$val} as {$key},";
		}
		$column = substr ( $column, 0, strlen ( $column ) - 1 );
		// 如果没有指定id,取最新的同类型消息
		if (empty ( $ids )) {
			$where .= " order by id desc limit {$msglimit}"; // " and type = {$material[$msg_type]['type']}";
		} else {
			$where .= " and id in({$ids})";
		}
		$sql = "select {$column} from `zwca_material` where {$where}";
		$result = $this->query ( $sql );
		return $result;
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
	public function get_token() {
		$accountInfo = $this->M->getConf ( array (
				'id' => ( int ) trim ( $_GET ['id'] ) 
		) );
		return $accountInfo ['access_token'];
	}
	public function putMsg($openid, $type, $content) {
		$info = array ();
		$info ['touser'] = $openid;
		$info ['msgtype'] = $type;
		$info [$type] = $content;
		$accesstoken = $this->get_token ();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $accesstoken;
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
	}
	/**
	 *
	 * @param int $responseid
	 *        	应答消息id，对应表zwca_response
	 * @param array $openid
	 *        	发送的openid，数组，多个
	 * @return boolean
	 */
	public function set_relation($responseid, $openid) {
		$info ['responseid'] = $responseid;
		$this->db->tableName = 'zwca_response_relation';
		$res = true;
		foreach ( $openid as $val ) {
			$info ['openid'] = $val;
			$id = $this->db->insert ( $info );
			$res = $id ? true : false;
		}
		return $res;
	}
	/**
	 *
	 * @param array $info
	 *        	插入数组
	 * @return Ambigous <number, boolean>
	 */
	public function set_response($info) {
		$this->db->tableName = 'zwca_response';
		return $this->db->insert ( $info );
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
	public function _putMsg($openid, $type, $content) {
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
		}
		$accesstoken = $this->get_token ();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $accesstoken;
		$return = $this->curlpost ( $url, $info );
		return json_decode ( $return, true );
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
	public function curlpost($url, $data) {
		$CURL = new CurlItems ();
		$return = $CURL->post ( $url, $this->jsonToZh ( json_encode ( $data ) ) );
		return $return;
	}
	
	/**
	 * utf-8内码转中文,将类似\u123a这样的编码转换为中文
	 * 
	 * @param string $str
	 *        	传入包含json_encode处理的字符串,其中包含中文utf8编码字符(格式为\uabcd之类的编码)
	 * @return string 返回经过处理的内容,其中所有的utf8的编码全部变回中文
	 */
	function jsonToZh($str) {
		return preg_replace ( "#\\\u([0-9a-f]{4})#ie", "mb_convert_encoding(pack('H4', '\\1'), 'UTF-8', 'UCS-2')", $str );
	}
}