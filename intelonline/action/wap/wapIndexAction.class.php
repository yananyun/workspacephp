<?php
class wapIndexAction extends Action {
	public $M;
	public function __construct() {
		parent::__construct ( 1 );
		$this->M = new staff ();
	}
	public function index() {
		// var_dump($this->memberId);
		$this->display ( 'wap/index.html' );
	}
	public function apply() {
		$this->display ( 'wap/staff/apply.html' );
	}
	
	/**
	 * 获取当前用户openid
	 */
	public function getOpenid() {
		echo $_SESSION ['memberInfo'] ['openid'] ? $_SESSION ['memberInfo'] ['openid'] : 0;
	}
	
	/**
	 * 如果当前url属于标签就自动给用户绑定标签
	 */
	public function bindTag() {
		if (! $_POST ['url']) {
			return false;
		}
		if (! $this->memberId) {
			return false;
		}
		$url = explode ( '/', $_POST ['url'] );
		$url5 = explode ( '?', $url [5] );
		$tmpUrl = $url5 [0] ? '/' . $url5 [0] : '';
		$newUrl = $url [0] . '//' . $url [2] . '/' . $url [3] . '/' . $url [4] . $tmpUrl;
		
		/* 如果是标签就给用户加上标签 */
		$tag = new tag ();
		$tag_name = $tag->getTagByWhereSql ( " where active_url like '%--{$newUrl}%--%' " );
		if (! empty ( $tag_name )) {
			foreach ( $tag_name as $v ) {
				$tag->addUserTagRelation ( $v ['id'], array (
						'openid' => $this->memberId 
				) );
			}
		}
	}
	
	// 检查员工是否通过验证
	public function check_staff() {
		$data = array ();
		$data ['name'] = isset ( $_POST ['name'] ) && ! empty ( $_POST ['name'] ) ? safe_replace ( trim ( $_POST ['name'] ) ) : '';
		$data ['idtype'] = isset ( $_POST ['idtype'] ) && ! empty ( $_POST ['idtype'] ) ? trim ( $_POST ['idtype'] ) : '';
		$data ['code'] = isset ( $_POST ['code'] ) && ! empty ( $_POST ['code'] ) ? trim ( $_POST ['code'] ) : '';
		$data ['apply_time'] = time ();
		if ($data ['idtype'] == '1' && ! isCreditNo ( $data ['code'] )) {
			ajaxReturn ( null, '身份证验证失败', 0 );
		}
		// $this->memberId = 'ovbQIuE28ZTTEihS-kl0tvkJgn58';
		if (! $this->memberId) {
			ajaxReturn ( null, '参数错误', 0 );
		}
		$staff = $this->M->getStaff ( $this->memberId );
		
		if ($staff) {
			$where = array (
					'openid' => $this->memberId,
					'id' => $staff ['id'] 
			);
			if ($this->M->updateStaff ( $data, $where )) {
				ajaxReturn ( null, '提交成功', 1 );
			} else {
				ajaxReturn ( null, '提交失败', 0 );
			}
		} else {
			$data ['openid'] = $this->memberId;
			$result = $this->M->add ( $data );
			if ($result) {
				ajaxReturn ( null, '提交成功', 1 );
			} else {
				ajaxReturn ( null, '提交失败', 0 );
			}
		}
	}
}