<?php
class doll extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
	}
	public function checkLotteryCount($rand) {
		$this->db->tableName = 'doll_lottery_conf';
		$return = $this->first ( "gtype={$rand}" );
		return $return;
	}
	public function bycount($tableName, $where) {
		$this->db->tableName = $tableName;
		$return = $this->count ( $where );
		return $return;
	}
	public function addUser($info) {
		$this->db->tableName = 'si_user';
		$return = $this->insert ( $info );
		return $return;
	}
	public function addSighup($info) {
		$this->db->tableName = 'si_signup';
		$return = $this->insert ( $info );
		return $return;
	}
	public function upSighup($info, $where) {
		$this->db->tableName = 'si_signup';
		$return = $this->update ( $info, $where );
		return $return;
	}
	public function becomeSi($openid) {
		$sql = "UPDATE si_user SET isSi = 1 WHERE openid = '$openid';";
		// $sql = "UPDATE si_user SET isSi = 1 WHERE openid = '$openid';"."UPDATE si_user SET lastlotterytimes = lastlotterytimes + 1 WHERE openid = '$openid';";
		$this->query ( $sql );
	}
	public function getInfo($where, $issingle = FALSE) {
		$this->db->tableName = 'si_user';
		if ($issingle) {
			$return = $this->first ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
	public function getLastGiftTotal() {
		$this->db->tableName = 'doll_gift';
		$count = $this->count ( array (
				'status' => 1 
		) );
		return $count;
	}
	public function getGiftInfo($where, $issingle = FALSE) {
		$this->db->tableName = 'doll_gift';
		if ($issingle) {
			$return = $this->first ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
	public function updateGiftInfo($info, $where) {
		$this->db->tableName = 'doll_gift';
		$return = $this->update ( $info, $where );
		return $return;
	}
	public function plusLotteryTimes($whereStr) {
		$sql = "UPDATE si_user SET lastlotterytimes = lastlotterytimes + 1 WHERE {$whereStr} ;";
		// $sql = "UPDATE si_user SET lastlotterytimes = 1 WHERE '{$whereStr}' ;";
		$this->query ( $sql );
	}
	public function cutLotteryTimes($openid) {
		$sql = "UPDATE si_user SET lastlotterytimes = lastlotterytimes - 1 WHERE openid = '{$openid}';";
		$this->query ( $sql );
	}
	public function chkLog($md) {
		$this->db->tableName = 'si_repost_log';
		$count = $this->count ( array (
				'md5' => $md 
		) );
		if ($count > 0) {
			return TRUE;
		}
		return FALSE;
	}
	public function addLog($info) {
		$this->db->tableName = 'si_repost_log';
		$return = $this->insert ( $info );
		return $return;
	}
	public function upUserInfo($row, $where) {
		$this->db->tableName = 'si_user';
		$return = $this->update ( $row, $where );
		return $return;
	}
	public function getSignUp($openid) {
		$this->db->tableName = 'si_signup';
		$return = $this->first ( array (
				'openid' => $openid 
		) );
		return $return;
	}
	public function addData($tableName, $info) {
		$this->db->tableName = $tableName;
		$return = $this->insert ( $info );
		return $return;
	}
	public function upData($tableName, $where, $info) {
		$this->db->tableName = $tableName;
		$return = $this->update ( $info, $where );
		return $return;
	}
	public function getData($tableName, $where, $issingle = FALSE) {
		$this->db->tableName = $tableName;
		if ($issingle) {
			$return = $this->first ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
	public function getCount($tableName, $where) {
		$this->db->tableName = $tableName;
		$return = $this->count ( $where );
		return $return;
	}
	public function getUserInfo($openid) {
		$WECHAT = new wechat ();
		$userinfo = $WECHAT->getUserInfoByOpenId ( $openid );
		return $userinfo;
	}
	public function getOrderList() {
		$this->db->tableName = 'si_queue';
		$sql = "select * from si_queue where status='2'";
		return $this->query ( $sql );
	}
	public function orderWait($opneid) {
		$this->db->tableName = 'si_queue';
		$maxOrderSql = "select max(order) order as order from si_queue";
		$maxOrder = $this->query ( $maxOrderSql );
		$maxOrder = $maxOrder ["0"] ['order'] + 1;
		// $user = $this->first("openid = '{$openid}'");
		$time = time ();
		$sql = "update si_queue set status = '2' waittime={$time}  where openid='{$opneid}'";
		return $this->query ( $sql );
	}
	public function orderStatusByOpenid($openid) {
		$this->db->tableName = 'si_queue';
		return $this->first ( "openid='{$openid}'" );
	}
	public function orderStatusById($id) {
		$this->db->tableName = 'si_queue';
		return $this->first ( "id='{$id}'" );
	}
	// 开始游戏
	public function siGame($openid) {
		$this->db->tableName = 'si_queue';
		$time = time ();
		$sql = "update si_queue set status='4' starttime={$time} where openid='{$openid}'";
		return $this->query ( $sql );
	}
	public function bysql($tableName, $sql) {
		$this->db->tableName = $tableName;
		return $this->query ( $sql );
	}
	public function getFirstOrder() {
		$this->db->tableName = 'si_queue';
		$time = time () - 15;
		$sql = "select * from si_queue where time > {$time} order by time asc limit 1";
		$return = $this->query ( $sql );
		return $return [0] ['openid'];
	}
	public function getConfVal($type) {
		$this->db->tableName = 'edison_conf';
		$return = $this->first ( "type='{$type}'" );
		return $return ['val'];
	}
	
	// 查询抓娃娃是否中奖
	public function duijianginfo($openid) {
		$this->db->tableName = 'doll_gift';
		$return = $this->select ( null, "type != 0 and openid ='{$openid}'" );
		return $return;
	}
}