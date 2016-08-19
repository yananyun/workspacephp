<?php
class memberAction extends Action {
	public $M;
	public $D;
	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct ( 2 );
		$this->M = new member ();
	}
	public function test1($limit = 0) {
		set_time_limit ( 0 );
		$sql = "select openid from sys_openid limit {$limit},1000";
		$openid = $this->M->query ( $sql );
		// $openid = array(0 => array('openid' => 'omH22tyqSjv49DipgSVdFUvg0rio'));
		// var_dump($openid);
		echo '<pre>';
		echo count ( $openid );
		if ($openid) {
			$wechatApi = new WechatApi ();
			$mem_group = new memberGroup ();
			foreach ( $openid as $v ) {
				$member = $wechatApi->getUserInfoByOpenid ( $v ['openid'] );
				var_dump ( $member );
				if ($member) {
					$tmp_rs = $mem_group->subscribe ( $member, 'gh_ac518c1b2445' ); // 处理关注用户
					echo '<pre>';
					var_dump ( $tmp_rs );
				}
			}
			sleep ( 1 );
			$this->test1 ( $limit + 1000 );
		}
		echo 'end';
	}
	public function test() {
		$str = '';
		$openidArr = preg_split ( '/\s/', $str );
		foreach ( $openidArr as $k => $v ) {
			if (empty ( $v )) {
				unset ( $openidArr [$k] );
			}
		}
		echo "'" . implode ( "','", $openidArr ) . "'";
		exit ();
		var_dump ( $openidArr );
		exit ();
		exit ();
		$sql = "select openid from sys_member order by id desc limit 1";
		$lastOpenid = $this->M->query ( $sql );
		var_dump ( $lastOpenid );
	}
	function getFansList($next_openid = '') {
		set_time_limit ( 0 );
		$wechat = new wechat ();
		$accesstoken = $wechat->getAccessToken ();
		$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $accesstoken;
		if ($next_openid) {
			$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $accesstoken . '&next_openid=' . $next_openid;
		}
		$data = curl_file_get_contents ( $url );
		$data = json_decode ( $data, true );
		
		$openids = $data ['data'] ['openid'];
		$next_openid = $data ['next_openid'];
		$test = new test ();
		if ($openids) {
			foreach ( $openids as $openid ) {
				$sql = "insert into b(`fromusername`) value('$openid')";
				$test->query ( $sql );
			}
			$this->getFansList ( $openid );
		}
	}
	public function getOpenidList($nextOpenid = '') {
		ini_set ( 'max_execution_time', '0' );
		
		$wechat = new wechat ();
		$configArr = $wechat->getConf ( "id=1" );
		$info = file_get_contents ( "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$configArr['access_token']}&next_openid={$nextOpenid}" );
		$info = json_decode ( $info, true );
		var_dump ( $info );
		$openid = "('" . implode ( "'),('", $info ['data'] ['openid'] ) . "')";
		
		$nextOpenid = $info ['next_openid'];
		if ($nextOpenid) {
			sleep ( 5 );
			$openid .= $this->getOpenidList ( $nextOpenid );
		}
		echo $openid;
		// return $openid;
	}
	public function getMemberInfoList($nextOpenid = 'omH22tyMw1a3klV2Iy7oDcf1S8dc') {
		ini_set ( 'max_execution_time', '0' );
		
		$sql = "select openid from sys_member order by id desc limit 1";
		$lastOpenid = $this->M->query ( $sql );
		$nextOpenid = $lastOpenid [0] ['openid'];
		
		$wechat = new wechat ();
		$configArr = $wechat->getConf ( "id=1" );
		// $configArr['openid'] = 'gh_ac518c1b2445';
		// $configArr['token'] = 'ZzTC3eoyfHYhQHJyBlIlMEDuBLzewpSMfcHCahb_kJc3uai2AEmQOQ8stGkvcpfDf-WM9ZgAmRVwkeaICDTo6C-wdBtQakHIGh1FDcbNHEI';
		$info = file_get_contents ( "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$configArr['access_token']}&next_openid={$nextOpenid}" );
		echo '<pre>';
		var_dump ( $configArr );
		$info = json_decode ( $info, true );
		$wechatApi = new WechatApi ();
		$mem_group = new memberGroup ();
		var_dump ( $info );
		foreach ( $info ['data'] ['openid'] as $v ) {
			$member = $wechatApi->getUserInfoByOpenid ( $v );
			if ($member) {
				@$tmp_rs = $mem_group->subscribe ( $member, $configArr ['openid'] ); // 处理关注用户
					                                                                 // echo '<pre>';
					                                                                 // var_dump($member);
			}
		}
		echo $nextOpenid = $info ['next_openid'];
		if ($nextOpenid) {
			sleep ( 5 );
			$this->getMemberInfoList ( $nextOpenid );
		}
		// echo '<pre>';
		// var_dump(json_decode($info, true));exit;
	}
	public function member_manage() {
		$guanzhu = $this->M->getCount ( 1 );
		$weiguanzhu = $this->M->getCount ();
		$this->assign ( array (
				'memberCount' => $this->member_num ( true ),
				'tagNum' => $this->tagNum (),
				'guanzhu' => $guanzhu,
				'weiguanzhu' => $weiguanzhu 
		) );
		$this->display ( 'admin/member/member_manage.html' );
	}
	
	// 会员列表
	public function list_member() {
		$type = isset ( $_GET ['type'] ) && ! empty ( $_GET ['type'] ) ? intval ( $_GET ['type'] ) : 0;
		// 分页
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 12;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		$param ['nickname'] = isset ( $_GET ['nickname'] ) && ! empty ( $_GET ['nickname'] ) ? urldecode ( trim ( safe_replace ( $_GET ['nickname'] ) ) ) : '';
		$param ['provincecode'] = trim ( safe_replace ( $_GET ['provinceId'] ) );
		$param ['citycode'] = trim ( safe_replace ( $_GET ['cityId'] ) );
		$param ['type'] = $type;
		$param ['sex'] = $_GET ['sex'];
		$interactionStime = $_GET ['interactionStime'];
		$stime = explode ( '-', $interactionStime );
		$interactionStime = $stime [2] . '-' . $stime [0] . '-' . $stime [1];
		$param ['interactionStime'] = $interactionStime;
		$interactionDtime = $_GET ['interactionDtime'];
		$dtime = explode ( '-', $interactionDtime );
		$interactionDtime = $dtime [2] . '-' . $dtime [0] . '-' . $dtime [1];
		$param ['interactionDtime'] = $interactionDtime;
		$param ['interactionNum'] = $_GET ['interactionNum'];
		$param ['interactionMin'] = $_GET ['interactionMin'];
		$param ['interactionMax'] = $_GET ['interactionMax'];
		
		$member = new member ();
		$list = $member->getList ( $param );
		
		$tagObject = new tag ();
		$groupObject = new group ();
		foreach ( $list ['data'] as $k => &$v ) {
			$v ['tagName'] = $tagObject->getTagByopenids ( $v ['openid'] );
			$v ['groupName'] = $groupObject->getGroupByOpenid ( $v ['openid'] );
		}
		$total = $list ['total'];
		$pages = $this->newPages ( $total, $p, $pagesize, 2, 'intelListWrap' );
		$this->assign ( array (
				'list' => $list ['data'],
				'pages' => $pages 
		) );
		$this->display ( 'admin/member/list_member.html' );
	}
	
	// 会员详情
	public function member_detail() {
		$MEMBER = new member ();
		$strlen = strlen ( $_GET ['id'] );
		if ($strlen < 15) {
			$info = $MEMBER->getMember ( 'id = ' . $_GET ['id'] );
		} else {
			$info = $MEMBER->getMember ( array (
					'openid' => $_GET ['id'] 
			) );
		}
		$info = $info [0];
		
		if ($info ['isapprove'] == 3) {
			$dealerInfo = $this->D->getInfo ( array (
					'mopenid' => $info ['openid'] 
			) );
			$dealerInfo = $dealerInfo [0];
			
			$GIFTORDER = new giftOrder ();
			$where = array ();
			$p = $where ['p'] = $_GET ['p'] ? $_GET ['p'] : 1;
			$pagesize = $where ['pagesize'] = 6;
			
			$list = $GIFTORDER->getOrderList ( array (
					'did' => $dealerInfo ['id'] 
			) );
			$pages = $this->pages ( $list ['total'], $p, $pagesize, 10, '' );
			
			$this->assign ( 'list', $list ['list'] );
			$this->assign ( 'pages', $pages );
			$this->assign ( 'dealerInfo', $dealerInfo );
		}
		$this->assign ( 'member', $info );
		$this->display ( 'admin/member/member_detail.html' );
	}
	
	/**
	 * 统计会员数量
	 */
	public function member_num($flag = false) {
		$type = isset ( $_GET ['type'] ) && ! empty ( $_GET ['type'] ) ? intval ( $_GET ['type'] ) : 0;
		$param ['type'] = $type;
		$memeber = new member ();
		$count = $memeber->member_num ( $param );
		if ($flag) {
			return $count;
		} else {
			ajaxReturn ( $count, '', 1 );
		}
	}
	
	/**
	 * 已有标签用户数
	 */
	public function tagNum() {
		$tag = new tag ();
		$count = $tag->tagNum ();
		return $count;
	}
	public function memberCount() {
		$m = new member ();
		$member_count = $m->memberCount ();
		if (is_int ( $member_count )) {
			ajaxReturn ( "{$member_count}", '1000', TRUE );
		} else {
			ajaxReturn ( "发生系统错误！", $member_count, FALSE );
		}
	}
}
