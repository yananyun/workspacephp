<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class auditAction extends Action {
	public $M;
	public $audit;
	public function __construct() {
		parent::__construct ();
		$this->M = new audit ();
		if (System::$action != 'api' && System::$action != 'screen') {
			if (System::$action != 'login' && $_SESSION ['is_login'] == false) {
				redirect ( '/index.php/audit/login' );
			}
		}
	}
	public function index() {
		$type = $_GET ['type'] ? $_GET ['type'] : '1';
		$this->assign ( 'type', $type );
		if ($type == 1) {
			$title = '未审核列表';
		} else if ($type == 2) {
			$title = '审核通过列表';
		} else if ($type == 3) {
			$title = '审核未通过列表';
		}
		$this->assign ( 'title', $title );
		$where = " type='{$type}' ";
		$count = $this->M->getcount ( $where );
		include './lib/AjaxPage.class.php';
		$Page = new AjaxPage ( $count, 10 );
		// $where = " where s.type='{$type}'";
		$sql = "select s.*,a.score,a.ctime,a.nickname from si_signup s left join sianswer a on s.openid=a.openid where s.type='{$type}'  order by id desc " . $Page->limit; // 取得记录的sql语句
		
		$list = $this->M->bysql ( 'si_signup', $sql );
		
		$page_list = $Page->fpage ( array (
				0,
				2,
				3,
				4,
				5,
				6,
				7,
				8 
		) ); // 获取分页信息
		$this->assign ( 'list', $list );
		$this->assign ( 'type', $type );
		$this->assign ( 'page_list', $page_list );
		
		$this->display ();
	}
	public function kaoshi() {
		$count = $this->M->getcountbysql ();
		include './lib/AjaxPage.class.php';
		$Page = new AjaxPage ( $count, 10 );
		// $where = " where " . $where;
		$sql = "select s.*,a.score,a.ctime,a.nickname,a.openid from sianswer a left join si_signup s on s.openid=a.openid group by a.openid order by id desc " . $Page->limit; // 取得记录的sql语句
		$list = $this->M->bysql ( 'si_signup', $sql );
		$page_list = $Page->fpage ( array (
				0,
				2,
				3,
				4,
				5,
				6,
				7,
				8 
		) ); // 获取分页信息
		$this->assign ( 'list', $list );
		$this->assign ( 'page_list', $page_list );
		$this->display ();
	}
	public function showkaoshi() {
		$openid = $_GET ['openid'];
		$list = $this->M->getList ( 'sianswer', "openid='{$openid}'" );
		foreach ( $list as $k => $v ) {
			$list [$k] ['ctime'] = date ( "Y-m-d H:i:s", $list [$k] ['ctime'] );
		}
		$this->assign ( 'list', $list );
		$this->display ();
	}
	public function audit() {
		$id = ( int ) $_GET ['id'];
		$type = trim ( $_GET ['type'] );
		$sql = "update si_signup set type='{$type}' where id={$id}";
		$status = $this->M->bysql ( 'si_signup', $sql );
		if ($status) {
			ajaxReturn ( $status, '操作成功', 1 );
		} else {
			ajaxReturn ( $status, '操作失败', 0 );
		}
	}
	public function show() {
		$id = ( int ) $_GET ['id'];
		$sql = "SELECT * FROM si_signup WHERE id ={$id} LIMIT 1;";
		$info = $this->M->bysql ( 'si_signup', $sql );
		$this->assign ( 'info', $info ['0'] );
		$this->display ();
	}
	public function login() {
		if ($_POST) {
			$password = $_POST ['password'];
			if ($password == 'intel123456') {
				$_SESSION ['is_login'] = TRUE;
				ajaxReturn ( $password, '登录成功', 1 );
			} else {
				ajaxReturn ( $password, '用户名或者密码错误', 0 );
			}
		} else {
			$this->display ();
		}
	}
	public function doAudit() {
		if ($_POST ['id'] && $_POST ['type']) {
			$id = ( int ) $_POST ['id'];
			$info = array ();
			$info ['type'] = ( int ) $_POST ['type'];
			$info ['reason'] = $_POST ['reason'];
			$info ['utime'] = time ();
			$SI = new si ();
			$SI->upData ( 'si_signup', array (
					'id' => $id 
			), $info );
			if ($info ['type'] == 2) {
				// 如果通过就给两次抓娃娃的机会
				$newdoll = new newdoll ();
				$data = $newdoll->getData ( 'si_signup', "id={$id}", true );
				$sql = "update newdoll_user set lastlotterytimes = lastlotterytimes + 6 where openid='{$data['openid']}'";
				$newdoll->bysql ( 'newdoll_user', $sql );
			}
			redirect ( '/index.php/audit/index' );
		} else {
			echo '参数错误';
		}
	}
	public function export() {
		set_time_limit ( 0 );
		error_reporting ( 0 );
		$type = intval ( $_GET ['type'] );
		$fprefix = '审核通过';
		if ($type == 3) {
			$fprefix = '未审核通过';
		}
		header ( "Content-type:text/html;charset=gbk" );
		header ( 'Content-Type: application/vnd.ms-excel' );
		$filename = $fprefix . "数据导出" . date ( 'Y-m-d_H_i_s' ) . " .csv";
		header ( 'Content-Disposition: attachment;filename="' . $filename . '"' );
		header ( 'Cache-Control: max-age=0' );
		
		if ($type < 4) {
			$sql = "select s.name,s.mobile,s.title,s.email,s.companyname,s.type,a.nickname,a.id as aid,a.score,a.ctime from si_signup s left join sianswer a on s.openid=a.openid where s.type='{$type}'  order by s.id desc";
		} else {
			$sql = 'select s.name,s.mobile,s.title,s.email,s.companyname,s.type,a.nickname,a.id as aid,a.score,a.ctime from sianswer a left join si_signup s on s.openid=a.openid group by a.openid order by a.id desc ';
		}
		$M = new user ();
		$res = $M->query ( $sql );
		
		$fp = fopen ( 'php://output', 'a' );
		
		$head = array (
				'姓名',
				'手机',
				'职位',
				'邮箱',
				'公司名称',
				'审核状态',
				'昵称',
				'考试状态',
				'考试分数',
				'创建时间' 
		);
		
		foreach ( $head as $i => $v ) {
			$head [$i] = iconv ( 'utf-8', 'gbk', $v );
		}
		
		fputcsv ( $fp, $head );
		
		$limit = 10; // 每10条数据刷新一下缓冲区
		$cnt = 0;
		
		foreach ( $res as $row ) {
			$cnt ++;
			if ($cnt == $limit) {
				ob_flush ();
				flush ();
				$cnt = 0;
			}
			$row ['ctime'] = ' ' . date ( "Y-m-d H:i:s", $row ['ctime'] );
			if ($type < 4) {
				$row ['type'] = $row ['type'] == 2 ? '审核通过' : '审核未通过';
			} else {
				$row ['type'] = $row ['type'] == 2 ? '审核通过' : '';
			}
			
			$row ['aid'] = $row ['aid'] > 0 ? '参与' : '未参与';
			foreach ( $row as $i => $v ) {
				$row [$i] = iconv ( 'utf-8', 'gbk', $v );
			}
			fputcsv ( $fp, $row );
		}
	}
}
