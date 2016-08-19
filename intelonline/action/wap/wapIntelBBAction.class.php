<?php
class wapIntelBBAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 0 );
		$this->M = new staff ();
	}
	public function index($html = 'index') {
		$this->display ( 'intel_bb/' . $html . '.html' );
	}
}