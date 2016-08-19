<?php
/**
 * 自动应答Action
 * 
 * @author wangying
 * @copyright zevertech
 * 2014/06/12
 */
class autoresponseAction extends Action {
	private $auto;
	private $group;
	private $MG;
	private $manage;
	function __construct() {
		parent::__construct ();
		$this->auto = new autoresponse ();
		include '/action/memberGroupAction.class.php';
		$this->group = new memberGroupAction ();
		$this->MG = new membergroup ();
		$this->manage = APP_PATH . 'index.php/autoresponse/autoresponse_manage';
	}
	public function manualresponse_details() {
		$this->display ( 'admin/weixin/manualresponse_details.html' );
	}
	public function manualresponse_manage() {
		$this->display ( 'admin/weixin/manualresponse_manage.html' );
	}
	public function autoresponse_manage() {
		$this->display ( 'admin/weixin/autoresponse_manage.html' );
	}
	public function add_conventionalAttention() {
		$this->display ( 'admin/weixin/add_conventionalAttention.html' );
	}
	public function add_unkeyWord() {
		$this->display ( 'admin/weixin/add_unkeyWord.html' );
	}
	/**
	 * 添加自动应答
	 */
	public function add_autoresponse() {
		$groupmsg = $this->groupmsg ();
		$this->assign ( array (
				'group' => $groupmsg 
		) );
		$this->display ( 'admin/weixin/add_autoresponse.html' );
	}
	public function add_autoresponse_do() {
		$info = $this->post_filter ( $_POST );
		$info ['response_ctime'] = time ();
		$info ['response_userid'] = $_SESSION ['userid'] ? $_SESSION ['userid'] : 0;
		$info ['response_genre'] = 'keyword';
		$result = $this->auto->add_autoresponse ( $info );
		header ( 'location: ' . $this->manage );
	}
	/**
	 * 修改自动应答
	 */
	function edit_response() {
		$id = isset ( $_GET ['id'] ) && ! empty ( $_GET ['id'] ) ? ( int ) trim ( safe_replace ( $_GET ['id'] ) ) : 0;
		$genre = isset ( $_GET ['response_genre'] ) && ! empty ( $_GET ['response_genre'] ) ? addslashes ( $_GET ['response_genre'] ) : '';
		$msg = $this->auto->edit_response ( $id );
		if ($genre == 'keyword') {
			$groupmsg = $this->groupmsg ();
			$this->assign ( array (
					'sid' => $sid,
					'group' => $groupmsg 
			) );
		}
		$this->assign ( array (
				'msg' => $msg 
		) );
		$genre == 'keyword' && $this->display ( 'admin/weixin/edit_autoresponse.html' );
		($genre == 'text' || $genre == 'subscribe') && $this->display ( 'admin/weixin/edit_otherresponse.html' );
	}
	public function edit_response_do() {
		$id = ( int ) $_POST ['response_id'];
		$content = formatUEditor ( $_POST ['response_content'] );
		$_POST ['response_content'] = $content;
		$info = $this->post_filter ( $_POST );
		$this->auto->edit_response_do ( $id, $info );
		header ( 'location: ' . $this->manage );
	}
	/**
	 * 添加关注应答
	 */
	public function add_subscriberesponse() {
		$info ['response_mid'] = ! empty ( $_POST ['response_mid'] ) ? ( int ) $_POST ['response_mid'] : 0;
		$info ['response_content'] = ! empty ( $_POST ['response_content'] ) ? formatUEditor ( $_POST ['response_content'] ) : '';
		$info ['response_genre'] = 'subscribe';
		$info ['response_mode'] = ($info ['response_mid'] != 0 && $info ['response_content'] == '') ? 2 : 1;
		$info ['response_ctime'] = time ();
		$info ['response_userid'] = $_SESSION ['userid'] ? $_SESSION ['userid'] : 0;
		$id = $this->auto->add_subscriberesponse ( $info );
		header ( 'location: ' . $this->manage );
	}
	/**
	 * 添加非关键词应答
	 */
	public function add_textresponse() {
		$info ['response_mid'] = ! empty ( $_POST ['response_mid'] ) ? ( int ) $_POST ['response_mid'] : 0;
		$info ['response_content'] = ! empty ( $_POST ['response_content'] ) ? formatUEditor ( $_POST ['response_content'] ) : '';
		$info ['response_genre'] = 'text';
		$info ['response_mode'] = ($info ['response_mid'] != 0 && $info ['response_content'] == '') ? 2 : 1;
		$info ['response_ctime'] = time ();
		$info ['response_userid'] = $_SESSION ['userid'] ? $_SESSION ['userid'] : 0;
		$id = $this->auto->add_subscriberesponse ( $info );
		header ( 'location: ' . $this->manage );
	}
	/**
	 * 删除自动应答
	 */
	public function del_response() {
		$id = addslashes ( $_POST ['id'] );
		$data = $this->auto->del_response ( $id );
		if ($data) {
			ajaxReturn ( '', '操作成功', 1 );
		} else {
			ajaxReturn ( '', '操作失败', 0 );
		}
	}
	
	/**
	 * 添加自动应答POST数据过滤
	 * 
	 * @param
	 *        	response_keyword 关键字
	 * @param
	 *        	response_keytype 关键字判断类型：1等同，2包含
	 * @param
	 *        	response_groupid 自动应答用户组id（有用户组不能再有别的条件）
	 * @param
	 *        	response_condition 应答人群，filtrate筛选，group使用分组
	 * @param
	 *        	response_sex 性别
	 * @param
	 *        	response_source 来源（二维码）
	 * @param
	 *        	response_type 用户角色，1普通，2RSP，3至尊地带会员
	 * @param
	 *        	response_city 城市
	 * @param
	 *        	response_province 省份
	 * @param
	 *        	response_cross 用于判断交互量，是固定条件还是自定义区间
	 * @param
	 *        	response_cross_min 交互量最小值
	 * @param
	 *        	response_cross_max 交互量最大值
	 * @param
	 *        	response_restime 用于判断应答时间，是不限还是自定义区间
	 * @param
	 *        	response_restime_start 应答开始时间
	 * @param
	 *        	response_restime_end 应答结束时间
	 * @param
	 *        	response_mid 应答素材id
	 * @param
	 *        	response_content 应答文本
	 */
	public function post_filter($POST) {
		$info ['response_mode'] = 1;
		$info ['response_keyword'] = htmlspecialchars ( addslashes ( $POST ['response_keyword'] ) );
		$info ['response_keytype'] = ( int ) $POST ['response_keytype'];
		$info ['response_groupid'] = ! empty ( $POST ['response_groupid'] ) ? $POST ['response_groupid'] : 0;
		$info ['response_condition'] = ! empty ( $info ['response_groupid'] ) ? 'group' : 'filtrate';
		
		$info ['response_sex'] = ! empty ( $POST ['response_sex'] ) ? $POST ['response_sex'] : 0;
		$info ['response_source'] = ! empty ( $POST ['response_source'] [0] ) ? implode ( ',', $POST ['response_source'] ) : '';
		$info ['response_type'] = ! empty ( $POST ['response_type'] ) ? implode ( ',', $POST ['response_type'] ) : '';
		$info ['response_city'] = ! empty ( $POST ['response_city'] ) ? $this->MG->find_territory ( $POST ['response_city'] ) : '';
		$info ['response_province'] = ! empty ( $POST ['response_province'] ) ? $this->MG->find_territory ( $POST ['response_province'] ) : '';
		// $info['condition_share'] = !empty($POST['condition_share']) ? $POST['condition_share'] : 0;
		// $info['condition_concern'] = !empty($POST['condition_concern']) ? $POST['condition_concern'] : 0;
		// $info['condition_browse'] = !empty($POST['condition_browse']) ? $POST['condition_browse'] : 0;
		/**
		 * 应答交互量要求 *
		 */
		if ($POST ['response_cross'] != 1) {
			$info ['response_cross_min'] = ! empty ( $POST ['response_cross'] ) ? $POST ['response_cross'] : 0;
		} else {
			$info ['response_cross_min'] = ! empty ( $POST ['response_cross_min'] ) ? $POST ['response_cross_min'] : 0;
		}
		$info ['response_cross_max'] = ($POST ['response_cross'] == 1) && ! empty ( $POST ['response_cross_max'] ) ? $POST ['response_cross_max'] : 0;
		/**
		 * 应答时限 *
		 */
		if ($POST ['response_restime'] != 1) {
			$info ['response_restime_start'] = '0000-00-00 00:00:00';
		} else {
			$info ['response_restime_start'] = ! empty ( $POST ['response_restime_start'] ) ? $POST ['response_restime_start'] . ' 00:00:00' : '0000-00-00 00:00:00';
		}
		$info ['response_restime_end'] = ($POST ['response_restime'] == 1) && ! empty ( $POST ['response_restime_end'] ) ? $POST ['response_restime_end'] . ' 00:00:00' : '0000-00-00 00:00:00';
		$info ['response_mid'] = ! empty ( $POST ['response_mid'] ) ? ( int ) $POST ['response_mid'] : 0;
		($info ['response_mid'] != 0) && $info ['response_mode'] = 2;
		$info ['response_content'] = ! empty ( $POST ['response_content'] ) ? htmlspecialchars ( addslashes ( $POST ['response_content'] ) ) : '';
		($info ['response_content'] != '') && $info ['response_mode'] = 1;
		return $info;
	}
	/**
	 * 应答列表
	 */
	public function autoresponse_list() {
		$p = trim ( safe_replace ( $_GET ['p'] ) );
		$p = $p ? $p : 1;
		$pagesize = 10;
		$param ['p'] = $p;
		$param ['pagesize'] = $pagesize;
		// 序号基准起点
		$baseNum = $baseNum = ($p - 1) * $pagesize + 1;
		// 总页数
		$total = 0;
		$userlist = array ();
		$list = $this->auto->autoresponse_list ( $param );
		$total = $list ['total'];
		$pages = $this->pages ( $total, $p, $pagesize, 10, 'intelListWrap' );
		$this->assign ( array (
				'list' => $list ['data'],
				'pages' => $pages,
				'baseNum' => $baseNum 
		) );
		$this->smarty->display ( 'admin/weixin/autoresponse_list.html' );
	}
	/**
	 * 查找用户分组
	 * 
	 * @return Ambigous <multitype:, multitype:2 , 查询结果, boolean, NULL, string, number>
	 */
	public function groupmsg() {
		$result = $this->auto->groupmsg ();
		return ($result && ! empty ( $result )) ? $result : array ();
	}
	/**
	 * 查找素材详情
	 */
	public function sel_material() {
		$mid = ( int ) $_POST ['mid'];
		$material = $this->auto->sel_material ( $mid );
		echo json_encode_zh ( $material );
	}
	
	/**
	 * 验证关键字是否存在
	 */
	public function checkKeyword() {
		$keyword = trim ( $_POST ['keyword'] );
		$return = $this->auto->findKeyword ( $keyword );
		if ($return) {
			echo 1;
		}
	}
}