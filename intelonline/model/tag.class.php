<?php
/**
 * tag.class.php
 *		用户组管理类
 * 
 * @author:ww
 * @since:2015.5.8
 */
class tag extends Module {
	public $tableName;
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->tableName = gtn ( 'tag' );
		$this->db->tableName = gtn ( 'tag' );
	}
	public function getList($where) {
		$sql = 'SELECT * FROM `' . $this->tableName . '` ';
		
		$p = $where ['p'];
		$pagesize = $where ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		
		$whereStr = " WHERE 1 = 1 and status = '1' ";
		
		$sql = $sql . $whereStr . $limit;
		$result = array ();
		$result ['list'] = $this->query ( $sql );
		$result ['total'] = $this->count ( $whereStr );
		return $result;
	}
	
	// 根据where条件获取group信息
	public function getTagByWhere($where, $field = NULL) {
		return $this->select ( $field, $where );
	}
	
	// 根据where条件获取group信息
	public function getTagByWhereSql($where, $field = NULL) {
		$sql = "select * from {$this->tableName} $where ";
		return $this->query ( $sql );
	}
	
	// 获取粉丝已打的标签
	public function getMemTagsByOpenid($where) {
		$this->db->tableName = gtn ( 'mem_tag_relation' );
		return $this->select ( NULL, $where );
	}
	
	// 根据where条件获取标签下的用户数
	public function getTagNumByWhere($where) {
		$this->db->tableName = gtn ( 'mem_tag_relation' );
		$rs = $this->select ( 'count(*) as num', $where );
		return isset ( $rs [0] ['num'] ) ? $rs [0] ['num'] : 0;
	}
	
	// 已有标签用户数
	public function tagNum() {
		$this->db->tableName = gtn ( 'mem_tag_relation' );
		$rs = $this->select ( 'count(distinct mopenid) as num' );
		return isset ( $rs [0] ['num'] ) ? $rs [0] ['num'] : 0;
	}
	
	// 获取某个用户组的信息
	public function getTagInfo($where) {
		$this->select ( NULL, $where );
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
			$this->db->tableName = gtn ( 'mem_tag_relation' );
			return $this->select ( 'mopenid', array (
					'tagid' => ( int ) $id 
			) );
		}
	}
	
	// 通过分组ID获取该分组下的会员列表 if $type == 1 普通 if $type == 2 48小时内交互
	public function getMemberListByGid($id, $type = 1) {
		if ($type == 1) {
			$sql = "SELECT m.* FROM `" . gtn ( 'mem_tag_relation' ) . "` AS r,`" . gtn ( 'member' ) . "` AS m WHERE r.mopenid = m.openid AND r.tagid = $id;";
			if ($id == - 1) {
				// $sql = "SELECT m.* FROM `".gtn('mem_tag_relation')."` AS r,`".gtn('member')."` AS m WHERE r.mopenid = m.openid";
				$sql = "SELECT * FROM `" . gtn ( 'member' ) . "`;";
			}
		} else {
			$time = time () - 86400 * 2;
			$sql = "SELECT m.* FROM `" . gtn ( 'mem_tag_relation' ) . "` AS r,`" . gtn ( 'member' ) . "` AS m WHERE r.mopenid = m.openid AND r.tagid = $id AND m.lastintracttime > $time;";
			if ($id == - 1) {
				// $sql = "SELECT m.* FROM `".gtn('mem_tag_relation')."` AS r,`".gtn('member')."` AS m WHERE r.mopenid = m.openid AND m.lastintracttime > $time;";
				$sql = "SELECT * FROM `" . gtn ( 'member' ) . "` WHERE lastintracttime > $time;";
			}
		}
		return $this->query ( $sql );
	}
	
	// 通过openid获取标签名
	public function getTagByOpenid($openid = '') {
		$sql = "SELECT t.name FROM `" . gtn ( 'mem_tag_relation' ) . "` AS r LEFT JOIN `" . gtn ( 'tag' ) . "` AS t ON r.tagid = t.id WHERE r.mopenid = '$openid'";
		$rs = $this->query ( $sql );
		return isset ( $rs [0] ['name'] ) ? $rs [0] ['name'] : '';
	}
	public function getTagByopenids($openid = '') {
		$sql = "SELECT t.name FROM `" . gtn ( 'mem_tag_relation' ) . "` AS r LEFT JOIN `" . gtn ( 'tag' ) . "` AS t ON r.tagid = t.id WHERE r.mopenid = '$openid' and t.status='1'";
		return $this->query ( $sql );
	}
	// 新增一个用户标签
	public function addUserTag($row) {
		$this->db->tableName = 'sys_tag';
		return $this->insert ( $row );
	}
	
	// 添加用户到组里面
	public function addUserTagRelation($tid, $where) {
		$this->db->tableName = gtn ( 'member' );
		$memberList = $this->select ( 'openid', $where );
		$this->db->tableName = gtn ( 'mem_tag_relation' );
		foreach ( $memberList as $mem ) {
			$info = array ();
			$info ['tagid'] = $tid;
			$info ['mopenid'] = $mem ['openid'];
			$info ['ctime'] = time ();
			$this->insert ( $info );
			// var_dump($this->insert($info), $this->getSql());die;
		}
	}
	public function updateUserTag($info, $where) {
		$this->update ( $info, $where );
		return $this->update ( $info, $where );
	}
	public function deleteMemTagRelation($id) {
		$this->db->tableName = gtn ( 'mem_tag_relation' );
		
		$this->delete ( array (
				'groupid' => $id 
		) );
	}
	
	// 通过分组ID获取用户列表
	public function getMemberByTagid($param = NULL) {
		$p = $param ['p'];
		$nickname = $param ['nickname'];
		$pagesize = $param ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		$where = '';
		if ($nickname) {
			$where .= " and m.nickname like '%" . $nickname . "%' ";
		}
		$sql = "SELECT m.*,r.id as rid FROM `" . gtn ( 'mem_tag_relation' ) . "` AS r LEFT JOIN `" . gtn ( 'member' ) . "` AS m ON r.mopenid = m.openid WHERE r.tagid = " . $param ['tagId'] . $where . $limit;
		$data = $this->query ( $sql );
		
		// total
		$sql = "SELECT count(*) as num FROM `" . gtn ( 'mem_tag_relation' ) . "` AS r LEFT JOIN `" . gtn ( 'member' ) . "` AS m ON r.mopenid = m.openid WHERE r.tagid = " . $param ['tagId'] . $where;
		$tmp = $this->query ( $sql );
		$total = $tmp [0] ['num'];
		
		$result ['data'] = $data;
		$result ['total'] = $total;
		return $result;
	}
}