<?php
class ReceiveMail {
	private $mbox;
	private $server;
	public $save_path;
	/**
	 * 构造函数
	 * 
	 * @param string $domain        	
	 * @param string $username        	
	 * @param string $password        	
	 * @param int $port        	
	 */
	public function __construct($domain, $username, $password, $type = 'pop', $port = 110, $ssl = false) {
		$server = '{' . $domain . ":" . $port . '}INBOX';
		if ($type == 'pop') {
			$server = '{' . $domain . ":" . $port . '/pop3' . ($ssl ? '/ssl' : '') . '}INBOX';
		}
		$this->mbox = @imap_open ( $server, $username, $password ) or die ( imap_last_error () );
		$this->server = $server;
	}
	
	/**
	 * 获取邮箱列表（层级）
	 * 
	 * @return multitype:
	 */
	public function getMailBoxList() {
		$list = imap_list ( $this->mbox, $this->server, "*" );
		if (is_array ( $list )) {
			foreach ( $list as $val ) {
				$list [] = imap_utf7_decode ( $val );
			}
		} else {
			die ( imap_last_error () );
		}
		return $list;
	}
	/**
	 * 获取标题信息
	 * 
	 * @return multitype:
	 */
	public function getHeaders() {
		return imap_headers ( $this->mbox );
	}
	/**
	 * 下载附件并返回路径信息
	 * 
	 * @param unknown_type $mid        	
	 */
	public function getAttache($mid) {
		if (! is_dir ( $this->save_path )) {
			mkdir ( $this->save_path );
		}
		
		$structure = imap_fetchstructure ( $this->mbox, $mid );
		if (! $structure->parts) {
			return $this->getPart ( $mid, $structure, 0, 'octet-stream' );
		} else {
			foreach ( $structure->parts as $index => $p ) {
				$data = $this->getPart ( $mid, $p, $index + 1, 'octet-stream' );
				if ($data) {
					return $data;
				}
			}
		}
	}
	
	/**
	 * 获取内容
	 * 
	 * @param int $mid        	
	 * @param objetc $p        	
	 * @param int $index        	
	 * @param string $type        	
	 */
	public function getPart($mid, $p, $pno, $type = 'html') {
		$data = ($pno) ? imap_fetchbody ( $this->mbox, $mid, $pno ) : imap_body ( $this->mbox, $mid );
		if ($p->encoding == 4) {
			$data = imap_qprint ( $data );
		} elseif ($p->encoding == 3) {
			$data = imap_base64 ( $data );
		}
		$params = array ();
		
		if ($p->parameters) {
			foreach ( $p->parameters as $x ) {
				$params [strtolower ( $x->attribute )] = $x->value;
			}
		}
		
		if ($p->dparameters) {
			foreach ( $p->dparameters as $x ) {
				$params [strtolower ( $x->attribute )] = $x->value;
			}
		}
		
		if (strtolower ( $p->subtype ) == 'octet-stream' || strtolower ( $p->subtype ) == 'csv') {
			$filename = $this->save_path . '/' . $params ['filename'];
			
			file_put_contents ( $filename, $data );
			
			if (file_exists ( $filename )) {
				return $params ['filename'];
			} else {
				exit ( "文件保存失败，请联系管理员" );
			}
		} elseif (strtolower ( $p->subtype ) == $type) {
			return $data;
		}
	}
}