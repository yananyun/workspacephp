<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class edison526Action extends baseAction {
	public $openid;
	public $M;
	public function __construct() {
		parent::__construct ();
		$this->openid = $_SESSION ['userinfo'] ['openid'] ? $_SESSION ['userinfo'] ['openid'] : $_SESSION ['userInfo'] ['openid'];
		$this->nickname = $_SESSION ['userinfo'] ['nickname'] ? $_SESSION ['userinfo'] ['nickname'] : $_SESSION ['userInfo'] ['nickname'];
		$this->M = new baseModel ();
	}
	public function index() {
		$this->assign ( 'openid', $this->openid );
		$this->assign ( 'shareUrl', APP_PATH . "index.php/edison526/index" );
		
		$this->display ( 'intelEdison526/index.html' );
	}
	public function add() {
		$info ['openid'] = $_POST ['openid'];
		$info ['nickname'] = $this->nickname;
		$info ['score'] = $_POST ['isOk'];
		$info ['ctime'] = time ();
		$this->M->addData ( 'sianswer', $info );
	}
}
