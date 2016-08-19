<?php

/**
 * gaming扫描人
 * 
 * @author wangying
 * @copyright zevertech
 * 2014/04/03
 */
class scanPrize extends Module {
	public function __construct($platform = 'default') {
		// 设定平台调用的数据库配置项
		$this->platform = $platform;
		// 初始化基类构造函数
		parent::__construct ( __CLASS__ );
		// 连接数据库
		$this->connect ();
	}
	// 添加签到人员
	public function add($tablename, $value) {
		$this->db->tableName = $tablename;
		$id = $this->db->insert ( $value );
		$randomStr = $this->randomStr ();
		$up_checknum = "update gaming_scan_prize set checknum = '{$randomStr}' where id = {$id} limit 1";
		$result = $this->db->query ( $up_checknum );
		return $result ? $result : false;
	}
	public function listall($param = null) {
		$keyword = $param ['keyword'];
		$p = $param ['p'];
		$pagesize = $param ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		$where = '';
		if ($keyword) {
			$where = ' and nickname like "%' . $keyword . '%"';
		}
		$sql = "select * from `gaming_scan_prize`  WHERE 1 " . $where . $limit;
		$data = $this->query ( $sql );
		$sql_count = "select count(*) as num from `gaming_scan_prize`  WHERE 1 " . $where;
		$tmp = $this->db->query ( $sql_count );
		$total = $tmp [0] ['num'];
		$result ['data'] = $data;
		$result ['total'] = $total;
		return $result;
	}
	// 修改读取数据
	public function msg($id) {
		$sql = 'select * from `gaming_scan_prize` where id = ' . $id;
		$result = $this->db->query ( $sql );
		return $result ? $result [0] : array ();
	}
	public function del_scanPrize($id) {
		$sql = 'delete from `gaming_scan_prize` where id = ' . $id;
		$result = $this->query ( $sql );
		return $result;
	}
	// 更新、修改信息（只能修改验证码）
	public function updatemsg($tabname, $param) {
		$this->db->tableName = $tabname;
		$id = $param ['id'];
		$info ['checknum'] = $param ['checknum'];
		$result = $this->update ( $info, 'id = ' . $id );
		return $result ? apiData ( $result, 1000 ) : apiData ( $result, 3004 );
	}
	// 随机字符串
	function randomStr($length = 6) {
		$chars = 'ACDEFGHJKLMNPQRSTUVWXYZ2345679'; // qwertyupkjhgfdsazxcvnm
		$password = '';
		for($i = 0; $i < $length; $i ++) {
			$password .= $chars [mt_rand ( 0, strlen ( $chars ) - 1 )];
		}
		return $password;
	}
	// 检查是否是扫描人员验证码
	public function check_num($checknum) {
		$find_scan = "select id,nickname from gaming_scan_prize where checknum = '{$checknum}' limit 1";
		$scanPrize = $this->db->query ( $find_scan );
		$scanPrize = $scanPrize [0];
		return $scanPrize;
	}
	// 通过验证码找到人员昵称进行对比，准许/禁止其成为扫描人员
	public function insert_scanPrize($scan, $openid) {
		if (! empty ( $scan ['id'] )) {
			$find_scan = "select count(*) as count from gaming_scan_prize where openid='{$openid}' limit 1";
			$scan_res = $this->db->query ( $find_scan );
			if ($scan_res [0] ['count'] > 0) {
				return 'isscanPrize';
			}
			$find_member = "select count(*) as count from sys_member where nickname='{$scan['nickname']}' and openid='{$openid}' limit 1";
			$member = $this->db->query ( $find_member );
			if ($member [0] ['count']) {
				$up_scan = "update gaming_scan_prize set openid = '{$openid}' where id = {$scan['id']} limit 1";
				$result = $this->db->query ( $up_scan );
				return $result;
			}
			return false;
		}
		return false;
	}
	public function check_management($openid) {
		$sql = "select count(*) as count from gaming_scan_prize where openid = '{$openid}' limit 1";
		$result = $this->db->query ( $sql );
		$result = $result [0] ['count'];
		return $result;
	}
	// 扫描领奖二维码，标记领奖时间
	public function scan_get_prize($scene_id) {
		$lottery_type_tmp = substr ( strval ( $scene_id ), 0, 3 );
		switch ($lottery_type_tmp) {
			case '201' :
				$lottery_type = 'ante_participant';
				break;
			case '202' :
				$lottery_type = 'click';
				break;
			case '203' :
				$lottery_type = 'shake';
				break;
			default :
		}
		$table = 'gaming_lottery_' . $lottery_type;
		$sel_prize_state = "select prize_state from {$table} where qr_id={$scene_id} limit 1";
		$res_state = $this->db->query ( $sel_prize_state );
		if ($res_state [0] ['prize_state'] == 0) {
			$up_prize_state = "update {$table} set prize_state=1 where qr_id={$scene_id} limit 1";
			$res_prize_state = $this->db->query ( $up_prize_state );
			if ($res_prize_state) {
				return true;
			} else {
				return false;
			}
		} else {
			return 3;
		}
	}
	
	// 添加腾讯用户验证码
	/*
	 * public function tencent_randStr()
	 * {
	 * for ($i = 0 ; $i < 50 ; $i ++)
	 * {
	 * $str = $this->randomStr();
	 * $qr_id = 210000000 + $i + 1;
	 * $sql = "insert into gaming_tencent values(null,'','',{$qr_id},'{$str}','0000-00-00 00:00:00')";
	 * $this->db->query($sql);
	 * }
	 * return 123;
	 *
	 *
	 * $sql = "select checknum from gaming_tencent";
	 * $result = $this->db->query($sql);
	 * return $result;
	 *
	 * }
	 */
}