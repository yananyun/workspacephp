<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class checkOpenid extends Module {
	public function checkOpenid($openid) {
		$this->db->tableName = 'sys_member';
		return $this->first ( "openid='{$openid}'" );
	}
}