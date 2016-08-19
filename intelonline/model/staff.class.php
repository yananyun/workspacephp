<?php
/**
 * 员工模型
 */
class staff extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->tableName = 'staff';
	}
	
	// 添加操作
	public function add($data) {
		$this->tableName = 'staff';
		return $this->insert ( $data );
	}
	
	// 根据openid查询
	public function getStaff($openid) {
		$sql = "select * from sys_staff where openid= '$openid' ";
		$result = $this->query ( $sql );
		return result ? $result [0] : array ();
	}
	
	// 根据openid修改数据
	public function updateStaff($row, $where) {
		$this->tableName = 'staff';
		return $this->update ( $row, $where );
	}
	public function getfiles($name, $code, $type) {
		$order = " order by year desc,month desc";
		$sql = "select * from sys_file where id in(select fid from sys_file_info where code='$code' and name='$name') and type='$type' and isdel ='0' " . $order;
		return $this->query ( $sql );
	}
	public function getfile_info($name, $code, $fid) {
		$sql = "select f.title,i.data,i.header,i.code,i.name from sys_file_info as i left join sys_file as f on f.id=i.fid where fid = $fid and name = '$name' and code = '$code' ";
		$result = $this->query ( $sql );
		return $result ? $result [0] : array ();
	}
}