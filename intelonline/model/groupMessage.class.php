<?php
/**
 * groupMessage.class.php
 *		消息管理类
 * 
 * @author:Harry
 * @since:2014.7.8
 * @link:http://haoshengzhide.com/ 
 */
class groupMessage extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->tableName = 'groupmsg_log';
	}
	
	// 获取列表
	public function getList($where) {
		$sql = 'SELECT * FROM `' . gtn ( 'groupmsg_log' ) . '` ';
		
		$p = $where ['p'];
		$pagesize = $where ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		
		$whereStr = ' WHERE 1 = 1 AND status = "1"';
		$sql .= $whereStr . ' ORDER BY ctime DESC ' . $limit;
		
		$result = array ();
		$result ['list'] = $this->query ( $sql );
		$result ['total'] = $this->count ( $whereStr );
		
		return $result;
	}
	
	// ADD
	public function addLog($row) {
		$this->db->tableName = gtn ( 'groupmsg_log' );
		$return = $this->insert ( $row );
		return $return;
	}
	
	// UPDATE
	public function updateLogInfo($row, $where) {
		$this->tableName = 'groupmsg_log';
		$return = $this->update ( $row, $where );
		return $return;
	}
	
	// SELECT
	public function selectLog($where, $fields = NULL) {
		$this->tableName = 'groupmsg_log';
		return $this->select ( $fields, $where );
	}
}