<?php
/**
 * information.class.php
 *		最新资讯管理类
 * 
 * @author:ww
 * @since:2015.5.8
 */
class information extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->tableName = 'information';
	}
	public function getList($where) {
		$sql = 'SELECT * FROM sys_information ';
		
		$p = $where ['p'];
		$pagesize = $where ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		
		$whereStr = ' WHERE 1 = 1 and isdel = "0" and status = "1" ';
		if ($where ['title']) {
			$whereStr .= " and title like '%" . $where ['title'] . "%' ";
		}
		$orderBy = " order by weight desc";
		$sql = $sql . $whereStr . $orderBy . $limit;
		$result = array ();
		$result ['list'] = $this->query ( $sql );
		$result ['total'] = $this->count ( $whereStr );
		return $result;
	}
	public function getListAmin($where) {
		$sql = 'SELECT * FROM sys_information ';
		
		$p = $where ['p'];
		$pagesize = $where ['pagesize'];
		$limit = ' limit ' . ($p - 1) * $pagesize . ',' . $pagesize;
		
		$whereStr = ' WHERE 1 = 1 and isdel = "0" ';
		if ($where ['title']) {
			$whereStr .= " and title like '%" . $where ['title'] . "%' ";
		}
		$orderBy = " order by weight desc";
		$sql = $sql . $whereStr . $orderBy . $limit;
		$result = array ();
		$result ['list'] = $this->query ( $sql );
		$result ['total'] = $this->count ( $whereStr );
		return $result;
	}
	/**
	 * 更具id查找一条记录
	 * 
	 * @param unknown $id        	
	 */
	public function findById($id) {
		$result = $this->getOne ( null, "id=$id and isdel='0'" );
		return $result ? $result : array ();
	}
}