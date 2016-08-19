<?php
class edison extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
	}
	public function addUser($info) {
		$this->db->tableName = 'edison_user';
		$return = $this->insert ( $info );
		return $return;
	}
	public function addSighup($info) {
		$this->db->tableName = 'edison_signup';
		$return = $this->insert ( $info );
		return $return;
	}
	public function upSighup($info, $where) {
		$this->db->tableName = 'edison_signup';
		$return = $this->update ( $info, $where );
		return $return;
	}
	public function becomeSi($openid) {
		$sql = "UPDATE edison_user SET isSi = 1 WHERE openid = '$openid';";
		// $sql = "UPDATE edison_user SET isSi = 1 WHERE openid = '$openid';"."UPDATE edison_user SET lastlotterytimes = lastlotterytimes + 1 WHERE openid = '$openid';";
		$this->query ( $sql );
	}
	public function getInfo($where, $issingle = FALSE) {
		$this->db->tableName = 'edison_user';
		if ($issingle) {
			$return = $this->first ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
	public function getLastGiftTotal() {
		$this->db->tableName = 'edison_gift';
		$count = $this->count ( array (
				'status' => 1 
		) );
		return $count;
	}
	public function getGiftInfo($where, $issingle = FALSE) {
		$this->db->tableName = 'edison_gift';
		if ($issingle) {
			$return = $this->first ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
	public function updateGiftInfo($info, $where) {
		$this->db->tableName = 'edison_gift';
		$return = $this->update ( $info, $where );
		return $return;
	}
	public function plusLotteryTimes($whereStr) {
		$sql = "UPDATE edison_user SET lastlotterytimes = lastlotterytimes + 1 WHERE {$whereStr} ;";
		// $sql = "UPDATE edison_user SET lastlotterytimes = 1 WHERE '{$whereStr}' ;";
		$this->query ( $sql );
	}
	public function cutLotteryTimes($openid) {
		$sql = "UPDATE edison_user SET lastlotterytimes = lastlotterytimes - 1 WHERE openid = '{$openid}';";
		$this->query ( $sql );
	}
	public function chkLog($md) {
		$this->db->tableName = 'edison_repost_log';
		$count = $this->first ( "md5='{$md}'" );
		return $count;
		// $count = $this->count("md5='{$md}'");
		// if($count > 0)
		// {
		// return TRUE;
		// }
		// return FALSE;
	}
	public function addLog($info) {
		$this->db->tableName = 'edison_repost_log';
		$return = $this->insert ( $info );
		return $return;
	}
	public function upUserInfo($row, $where) {
		$this->db->tableName = 'edison_user';
		$return = $this->update ( $row, $where );
		
		return $return;
	}
	public function getSignUp($openid) {
		$this->db->tableName = 'edison_signup';
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
	public function getConfVal($type) {
		$this->db->tableName = 'edison_conf';
		$return = $this->first ( "type='{$type}'" );
		return $return ['val'];
	}
	public function duijiang($openid) {
		$this->db->tableName = 'edison_gift';
		$return = $this->first ( "openid='{$openid}' and type='2'" );
		return $return;
	}
}