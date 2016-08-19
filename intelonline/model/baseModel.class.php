<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class baseModel extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
	}
	public function addData($tableName, $info) {
		$this->db->tableName = $tableName;
		$return = $this->insert ( $info );
		return $return;
	}
	public function upData($tableName, $where, $info) {
		$this->db->tableName = $tableName;
		$return = $this->update ( $info, $where );
		return $return;
	}
	public function getData($tableName, $where, $issingle = FALSE) {
		$this->db->tableName = $tableName;
		if ($issingle) {
			$return = $this->first ( $where );
		} else {
			$return = $this->select ( NULL, $where );
		}
		return $return;
	}
	public function getCount($tableName, $where) {
		$this->db->tableName = $tableName;
		$return = $this->count ( $where );
		return $return;
	}
}
