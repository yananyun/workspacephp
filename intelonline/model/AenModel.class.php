<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class AenModel extends Module {
	public function __construct() {
		parent::__construct ();
	}
	public function byquery($tableName, $sql) {
		$this->db->tableName = $tableName;
		$return = $this->query ( $sql );
		file_put_contents ( './aaaaaa.html', $this->getSql () );
		return $return;
	}
}