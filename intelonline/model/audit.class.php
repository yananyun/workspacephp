<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class audit extends Module {
	public function getList($tableName, $where) {
		$this->db->tableName = $tableName;
		$return = $this->select ( null, $where );
		return $return;
	}
	public function bysql($table, $sql) {
		$this->db->tableName = $table;
		return $this->query ( $sql );
	}
	public function getcount($where) {
		$this->db->tableName = 'si_signup';
		return $this->count ( $where );
	}
	public function getfrist($id) {
		$where = " id =" . $id;
		$this->db->tableName = 'si_signup';
		$this->first ( $where );
		echo $this->getSql ();
	}
	public function getcountbysql() {
		$sql = "select count(*) num from sianswer group by openid";
		$this->db->tableName = 'sianswer';
		$return = $this->query ( $sql );
		return count ( $return );
	}
}
