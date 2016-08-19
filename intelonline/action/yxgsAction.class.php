<?php
class yxgsAction extends baseAction {
	
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
	}
	public function index() {
		var_dump ( $_SESSION ['userInfo'] );
		die ();
	}
}
