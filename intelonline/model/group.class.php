<?php
/**
 * group.class.php
 *		用户组管理类
 *
 * @author:Harry
 * @since:2014.7.4
 * @link:http://haoshengzhide.com/
 */
class group extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->tableName = 'membergroup';
	}
	public function getList($where) {
		$sql = 'SELECT a.*, b.username, (SELECT count(*) FROM `sys_mem_group_relation` where groupid=a.id) as memberNum FROM `' . gtn ( 'membergroup' ) . '` as a LEFT JOIN `' . gtn ( 'user' ) . '` as b ON b.id=a.uid ';
		
		$p = $where ['p'];
		$pagesize = $where ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		
		$whereStr = ' WHERE 1 = 1 AND a.status = 1 ';
		
		$sql = $sql . $whereStr . $limit;
		$result = array ();
		$result ['list'] = $this->query ( $sql );
		$whereStr = ' WHERE 1 = 1 AND status = 1 ';
		$result ['total'] = $this->count ( $whereStr );
		return $result;
	}
	
	// 根据where条件获取group信息
	public function getGroupByWhere($where) {
		$this->db->tableName = gtn ( 'membergroup' );
		return $this->select ( NULL, $where );
	}
	
	// 获取粉丝所在的分组
	public function getMemGroupRelationByOpenid($where) {
		$this->db->tableName = gtn ( 'mem_group_relation' );
		return $this->select ( NULL, $where );
	}
	
	// 获取某个用户组的信息
	public function getGroupInfo($where) {
		return $this->select ( NULL, $where );
	}
	
	// 通过分组ID获取该分组下的会员ID
	public function getOpenIdByGid($id) {
		if ($id == '-1') {
			$this->tableName = 'member';
			return $this->select ( 'openid', array (
					'subscribe' => 1 
			) );
		} else {
			$this->db->tableName = gtn ( 'mem_group_relation' );
			return $this->select ( 'mopenid', array (
					'groupid' => ( int ) $id 
			) );
		}
	}
	
	// 通过分组ID获取该分组下的会员列表 if $type == 1 普通 if $type == 2 48小时内交互
	public function getMemberListByGid($id, $type = 1) {
		if ($type == 1) {
			$sql = "SELECT m.* FROM `" . gtn ( 'mem_group_relation' ) . "` AS r,`" . gtn ( 'member' ) . "` AS m WHERE r.mopenid = m.openid AND r.groupid = $id;";
			if ($id == - 1) {
				// $sql = "SELECT m.* FROM `".gtn('mem_group_relation')."` AS r,`".gtn('member')."` AS m WHERE r.mopenid = m.openid";
				$sql = "SELECT * FROM `" . gtn ( 'member' ) . "`;";
			}
		} else {
			$time = time () - 86400 * 2;
			$sql = "SELECT m.* FROM `" . gtn ( 'mem_group_relation' ) . "` AS r,`" . gtn ( 'member' ) . "` AS m WHERE r.mopenid = m.openid AND r.groupid = $id AND m.lastintracttime > $time;";
			if ($id == - 1) {
				// $sql = "SELECT m.* FROM `".gtn('mem_group_relation')."` AS r,`".gtn('member')."` AS m WHERE r.mopenid = m.openid AND m.lastintracttime > $time;";
				$sql = "SELECT * FROM `" . gtn ( 'member' ) . "` WHERE lastintracttime > $time;";
			}
		}
		return $this->query ( $sql );
	}
	
	// 新增一个用户组
	public function addUserGroup($row) {
		$this->tableName = 'membergroup';
		return $this->insert ( $row );
	}
	
	// 添加用户到组里面
	public function addUserGroupRelation($gid, $where) {
		$this->db->tableName = gtn ( 'member' );
		$memberList = $this->select ( 'openid', $where );
		$this->db->tableName = gtn ( 'mem_group_relation' );
		foreach ( $memberList as $mem ) {
			$info = array ();
			$info ['groupid'] = $gid;
			$info ['mopenid'] = $mem ['openid'];
			
			$this->insert ( $info );
		}
	}
	public function updateUserGroup($info, $where) {
		$this->db->tableName = gtn ( 'membergroup' );
		
		return $this->update ( $info, $where );
	}
	public function deleteMemGroupRelation($id) {
		$this->db->tableName = gtn ( 'mem_group_relation' );
		
		$this->delete ( array (
				'groupid' => $id 
		) );
	}
	
	// 通过openid获取分组名
	public function getGroupByOpenid($openid = '') {
		$sql = "SELECT g.name FROM `" . gtn ( 'mem_group_relation' ) . "` AS r LEFT JOIN `" . gtn ( 'membergroup' ) . "` AS g ON r.groupid = g.id WHERE r.mopenid = '$openid' and g.status='1'";
		$rs = $this->query ( $sql );
		return isset ( $rs [0] ['name'] ) ? $rs [0] ['name'] : '';
	}
	
	// 通过分组ID获取用户列表
	public function getMemberByGroupid($param = NULL) {
		$p = $param ['p'];
		$nickname = $param ['nickname'];
		$pagesize = $param ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		$where = '';
		if ($nickname) {
			$where .= " and m.nickname like '%" . $nickname . "%' ";
		}
		$sql = "SELECT m.* FROM `" . gtn ( 'mem_group_relation' ) . "` AS r LEFT JOIN `" . gtn ( 'member' ) . "` AS m ON r.mopenid = m.openid WHERE r.groupid = " . $param ['groupId'] . $where . $limit;
		$data = $this->query ( $sql );
		
		// total
		$sql = "SELECT count(*) as num FROM `" . gtn ( 'mem_group_relation' ) . "` AS r LEFT JOIN `" . gtn ( 'member' ) . "` AS m ON r.mopenid = m.openid WHERE r.groupid = " . $param ['groupId'] . $where;
		$tmp = $this->query ( $sql );
		$total = $tmp [0] ['num'];
		
		$result ['data'] = $data;
		$result ['total'] = $total;
		return $result;
	}
}