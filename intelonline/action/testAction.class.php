<?php
/**
 * crontabAction.class.php
 *		定时脚本
 * 
 * @author Harry
 * @since 2014.5.20
 */
class testAction extends Action {
	public function __construct() {
		parent::__construct ( FALSE );
	}
	public function index() {
		$this->display ( 'test/index.html' );
	}
	public function upload() {
		$upload = new UploadFile ();
		$upload->upload ();
	}
}