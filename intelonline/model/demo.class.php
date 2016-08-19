<?php
/**
 * 产品数据库操作
 */
class demo extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
	}
	
	/**
	 * 测试方法，把数据写入数据库
	 */
	public function addTestData($data, $tableName = 'wx_test') {
		$this->tableName = $tableName;
		return $this->insert ( $data );
	}
	public function getOpenid() {
		$sql = "select DISTINCT fromusername from sys_weixin_getmsg_new limit 10";
		$this->db->tableName = 'sys_weixin_getmsg_new';
		return $this->query ( $sql );
	}
	public function bySql($table, $where) {
		$this->db->tableName = $table;
		return $this->getOne ( 'openid,sid', $where );
	}
}