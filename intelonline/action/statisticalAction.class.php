<?php

/**
 * statisticalAction.class.php
 * 		运营概况，统计分析
 * 
 * @author Harry
 * @link http://haoshengzhide.com 
 * @since 2014.7.23
 */
class statisticalAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 2 );
	}
	public function index() {
		$this->adminDisplay ();
	}
}
