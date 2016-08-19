<?php
class transitAction extends baseAction {
	
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
	}
	public function index() {
		$openid = $this->memberId;
		if ($openid) {
			$url = isset ( $_GET ['url'] ) && ! empty ( $_GET ['url'] ) ? trim ( $_GET ['url'] ) : '';
			
			if ($url) {
				$url = urldecode ( $url );
				$url = str_replace ( '\\', '', $url );
				if (! strpos ( '_' . $url, 'http' )) {
					$url = 'http://' . $url;
				}
				if (strpos ( $url, '?' )) {
					$url .= '&openid=' . $openid;
				} else {
					$url .= '?openid=' . $openid;
				}
				
				header ( "Location:$url" );
			}
		}
		die ();
	}
}
