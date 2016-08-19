<?php
class norankAction extends Action {
	public function __construct() {
		parent::__construct ( 2 );
	}
	public function index() {
		$this->adminDisplay ();
	}
}