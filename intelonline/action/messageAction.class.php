<?php
/**
 * messageAction.class.php
 *		消息管理
 * 
 * @author:Harry
 * @since:2014.7.1
 * @link:http://haoshengzhide.com/ 
 */
class messageAction extends Action {
	public function __construct() {
		parent::__construct ( 2 );
		$this->M = new message ();
	}
	public function list_manage() {
		$this->display ( 'admin/message/list_manage.html' );
	}
	
	// 消息列表
	public function msglist() {
		$where = array ();
		$where ['timeType'] = $_GET ['timeType'] ? $_GET ['timeType'] : 0;
		$where ['keyword'] = $_GET ['keyword'] ? $_GET ['keyword'] : 0;
		$where ['p'] = $_GET ['p'] ? $_GET ['p'] : 1;
		$where ['pagesize'] = 10;
		
		$result = $this->M->getMsgList ( $where );
		$pages = $this->newPages ( $result ['total'], $where ['p'], $where ['pagesize'], 2, 'intelListWrap' );
		
		$this->assign ( 'now', time () );
		$this->assign ( 'list', $result ['list'] );
		$this->assign ( 'pages', $pages );
		$this->display ( 'admin/message/msglist.html' );
	}
	
	// 回复消息
	public function reply() {
		$return = $this->M->replyTextMsg ( $_SESSION ['accountinfo'] ['openid'], filter_input ( INPUT_POST, 'openid' ), filter_input ( INPUT_POST, 'content' ), $_POST ['id'] );
		if ($return ['errcode'] == 0) {
			echo 1;
		} else {
			echo 4;
		}
	}
	
	// 星标消息
	public function makeStar() {
		$id = ( int ) $_GET ['id'] ? ( int ) $_GET ['id'] : exit ( '缺少参数' );
		$where = array (
				'id' => $id 
		); // msgid
		                            // $info = array('isstar' => $_GET['star']?(int)$_GET['star']:1);//是否星标：1是 2否
		$msg_info = $this->M->getMsgInfo ( $id, 'isstar' );
		$info = $msg_info ['isstar'] == '1' ? '2' : '1';
		$return = $this->M->updateMsg ( $where, array (
				'isstar' => $info 
		) );
		echo 1;
	}
}