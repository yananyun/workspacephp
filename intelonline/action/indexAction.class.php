<?php
class indexAction extends Action {
	public function __construct() {
		parent::__construct ( 2 );
	}
	public function index() {
		echo 'test test indexAction<br/>';
		// redirect('/index.php/statistical/index');
	}
}