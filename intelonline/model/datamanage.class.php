<?php

/*
 * 数据管理模型
 */
class datamanage extends Module {
	public function getList($tableName, $fields = null, $where = null, $groupby = null, $orderby = null, $limit = null) {
		$this->db->tableName = $tableName;
		return $this->select ( $fields, $where, $groupby, $orderby, $limit );
	}
	public function getInfo($tableName, $where, $fields = FALSE) {
		$this->db->tableName = $tableName;
		if ($fields == false) {
			return $this->first ( $where );
		} else {
			return $this->select ( $fields, $where, null, null, 1 );
		}
	}
	public function bysql($tableName, $sql) {
		$this->db->tableName = $tableName;
		$return = $this->query ( $sql );
		return $return;
	}
	public function getTagByOpenid($openid) {
		$this->db->tableName = 'sys_mem_tag_relation';
		$return = $this->select ( null, "mopenid='{$openid}'" );
		if (count ( $return ) > 0) {
			$str = '';
			foreach ( $return as $k => $v ) {
				$str .= $v ['tagid'] . ",";
			}
			$str = rtrim ( $str, ',' );
			return $str;
		} else {
			return false;
		}
	}
}
