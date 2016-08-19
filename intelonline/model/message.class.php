<?php
/**
 * message.class.php
 *		消息处理
 * 
 * @author:Harry
 * @since:2014.7.2
 * @link:http://haoshengzhide.com/
 */
class message extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->tableName = 'get_msg';
	}
	
	// 拉取消息列表
	public function getMsgList($where) {
		$result = array ();
		$p = $where ['p'];
		$pagesize = $where ['pagesize'];
		$limit = ' LIMIT ' . ($p - 1) * $pagesize . ',' . $pagesize;
		$order = ' ORDER BY msg.id DESC ';
		
		// $whereStr = ' WHERE 1=1 AND msgtype != '.'"event"';
		$whereStr = ' WHERE 1=1  AND `msgtype` = "text" AND `issys` = 2 ';
		$today = strtotime ( 'today' );
		switch ($where ['timeType']) {
			case 0 : // 全部
				break;
			case 1 : // 今天
				$whereStr .= 'AND msg.createtime > ' . $today;
				break;
			case 2 : // 昨天
				$whereStr .= 'AND msg.createtime < ' . $today . ' AND msg.createtime > ' . ($today - 86400);
				break;
			case 3 : // 前天
				$whereStr .= 'AND msg.createtime < ' . ($today - 86400) . ' AND msg.createtime > ' . ($today - 86400 - 86400);
				break;
			case 4 : // 更早
				$whereStr .= 'AND msg.createtime < ' . ($today - 86400 - 86400);
				break;
		}
		
		if ($where ['keyword'] != '0') {
			$whereStr .= ' AND msg.content LIKE "%' . $where ['keyword'] . '%"';
		}
		$sql = "select msg.*,mem.openid,mem.nickname,mem.sex,mem.headimgurl,mem.isapprove from `" . gtn ( 'get_msg' ) . "` as msg Left join `" . gtn ( 'member' ) . "` as mem ON msg.fromusername = mem.openid " . $whereStr . $order . $limit;
		$result ['list'] = $this->query ( $sql );
		
		$countSql = "select COUNT(*) AS total from `" . gtn ( 'get_msg' ) . "` as msg Left join `" . gtn ( 'member' ) . "` as mem ON msg.fromusername = mem.openid " . $whereStr;
		$return = $this->db->query ( $countSql );
		$result ['total'] = $return [0] ['total'];
		
		return $result;
	}
	
	// 回复一条文本信息
	public function replyTextMsg($mopenid, $openid, $text, $id) {
		$Wechat = new wechatAction ();
		$accesstoken = $Wechat->getAccessToken ();
		
		$API = new WechatApi ( $accesstoken );
		$content = array (
				'content' => $text 
		);
		$result = $API->putMsg ( $openid, 'text', $content );
		
		if ($result ['errcode'] == 0) // send success
{
			$info = array ();
			$info ['fromusername'] = $mopenid;
			$info ['tousername'] = $openid;
			$info ['createtime'] = time ();
			$info ['msgtype'] = 'text';
			$info ['content'] = $text;
			$info ['issys'] = '1';
			$info ['ctime'] = time ();
			$this->addReplyMsgLog ( $info );
			
			$msginfo = array ();
			$msginfo ['isreply'] = 1;
			$this->updateMsg ( array (
					'id' => $id 
			), $msginfo );
		}
		return $result;
	}
	
	// 添加回复消息的日志
	public function addReplyMsgLog($info) {
		$this->tableName = 'get_msg';
		$this->insert ( $info );
	}
	
	// 更新一条消息
	public function updateMsg($where, $row) {
		$this->tableName = 'get_msg';
		$return = $this->update ( $row, $where );
		return $return;
	}
	
	// 根据id获取消息详情
	public function getMsgInfo($id, $fields = null) {
		$this->tableName = 'get_msg';
		$msg_info = $this->getOne ( $fields, array (
				'id' => $id 
		) );
		return $msg_info;
	}
}