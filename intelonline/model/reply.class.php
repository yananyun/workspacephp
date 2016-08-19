<?php
/**
 * reply.class.php
 *		自动回复控制类
 * 
 * @author Harry
 * @since 20140711
 * @link http://haoshengzhide.com/
 */
class reply extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->tableName = 'keyword';
	}
	public function getKeywordList($where) {
		$result = array ();
		$p = $where ['p'];
		$pagesize = $where ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		$order = ' ORDER BY id DESC';
		
		$whereStr = ' WHERE 1=1 AND status = 1';
		if ($where ['type']) {
			$whereStr .= ' AND type = ' . $where ['type'];
		}
		$sql = 'SELECT * FROM `' . gtn ( 'keyword' ) . '`' . $whereStr . $order . $limit;
		$result ['list'] = $this->query ( $sql );
		$result ['total'] = $this->count ( $whereStr );
		
		return $result;
	}
	public function updateInfo($row, $where) {
		$this->tableName = 'keyword';
		return $this->update ( $row, $where );
	}
	public function add($row) {
		$this->tableName = 'keyword';
		return $this->insert ( $row );
	}
	public function getInfo($where, $single = FALSE) {
		$this->tableName = 'keyword';
		if (! $single) {
			$return = $this->select ( NULL, $where );
		} else {
			$return = $this->first ( $where );
		}
		
		return $return;
	}
}