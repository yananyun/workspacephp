<?php

/**
 * 微信h5首页
 * @author Gason Wong <gaoshang_s@163.com>
 */
class homeAction extends Action {
	public function __construct() {
		parent::__construct ( 1 );
	}
	public function index() {
		$this->display ();
	}
}