<?php
/**
 * 主动响应控制器
 * @author wangying
 * @since 2014.5.20
 * 
 * */
class responseAction extends Action {
	public function __construct() {
		parent::__construct ();
		$this->response = new response ();
	}
	public function putMsg($openid, $type, $content) {
		return $this->response->putMsg ( $openid, $type, $content );
	}
	public function get_material($msg_type, $ids, $msglimit) {
		return $this->response->get_material ( $msg_type, $ids, $msglimit );
	}
	public function set_response() {
		$info ['aid'] = ( int ) trim ( $_GET ['id'] );
		$info ['uid'] = 1;
		$info ['materialid'] = 2;
		$info ['ctime'] = time ();
		$info ['status'] = 1;
		$info ['starttime'] = time ();
		$info ['endtime'] = time ();
		$info ['type'] = 1;
		$result = $this->response->set_response ( $info );
		return $result ? true : false;
	}
	public function set_relation() {
		$responseid = $_POST ['responseid'];
		$openid = json_decode ( $_POST ['openid'] );
		return $this->response->set_response ( $responseid, $openid );
	}
}