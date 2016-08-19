<?php
/**
 * qrcode.class.php
 * 二维码数据操作类
 * @author wgs
 * @since 2014.5.20
 */
class qrcode extends Module {
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->tableName = 'qrcode';
	}
	/**
	 * 添加二维码
	 * 
	 * @param array $data        	
	 */
	public function addQrcode($data) {
		$this->tableName = 'qrcode';
		$data ['ctime'] = time ();
		$data ['uptime'] = time ();
		$this->insert ( $data );
	}
	
	/**
	 * 修改二维码信息
	 * 
	 * @param array $data        	
	 */
	public function upQrcode($data, $where) {
		$this->tableName = 'qrcode';
		$data ['uptime'] = time ();
		$this->update ( $data, $where );
	}
	public function getInfoByMid($mid = 0) {
		$sql = "select * from sys_qrcode where materialid = $mid ";
		$result = $this->query ( $sql );
		return $result ? $result [0] : array ();
	}
	
	/**
	 * 获取二维码列表
	 * 
	 * @return array $list_arr 符合条件的二维码列表，其中每个二维码信息中的除了包括二维码创建时的基本信息，还包括该二维码包含的多素材的信息列表
	 */
	public function qrList($where) {
		$this->tableName = 'qrcode';
		$list_arr = array ();
		$type = $where ['type'];
		$aopenid = $where ['aopenid'];
		$uid = $where ['uid'];
		$num = $where ['num'];
		$page = empty ( $_GET ['page'] ) ? 1 : $_GET ['page'];
		$paging = ($page - 1) * $num;
		$qr_sql = "SELECT * FROM `" . TABLE_PREFIX . "qrcode` WHERE `aopenid` = '" . $aopenid . "' AND `status` != 4 AND `uid` = " . $uid . " limit {$paging},{$num}";
		$list_arr = $this->query ( $qr_sql );
		// p($list_arr);
		foreach ( $list_arr as &$qr_v ) {
			$matid_str = trim ( $qr_v ['materialid'], ',' );
			$mat_sql = "SELECT * FROM `" . TABLE_PREFIX . "material` WHERE `aopenid` = '" . $aopenid . "' AND `type` = '" . $type . "' AND `status` = '1' AND `id` IN (" . $matid_str . ")";
			$mat_arr = $this->query ( $mat_sql ); // 素材结果
			$qr_v ['mat_arr'] = $mat_arr;
		}
		$qr_count = "SELECT count(*) as total FROM `" . TABLE_PREFIX . "qrcode` WHERE `aopenid` = '" . $aopenid . "' AND `status` != 4 AND `uid` = " . $uid;
		$count = $this->query ( $qr_count );
		$list_arr ['total'] = $count [0] ['total'];
		return $list_arr;
	}
	
	/**
	 * 获取扫描二维码日志表
	 */
	public function getLogList($aopenid) {
		$this->tableName = 'qrcode_log';
		$where = "aopenid = '" . $aopenid . "' AND `status` != '4'";
		$log_list = $this->select ( '*', $where );
		echo $this->getSql ();
		return $log_list;
	}
	
	/**
	 * 删除指定用户的二维码信息
	 */
	public function delLogInfo($where) {
		$this->tableName = 'qrcode_log';
		$log_info = $this->first ( $where );
		$status = $log_info ['status'] == '4' ? exit ( '已经删除' ) : '4';
		return $this->update ( array (
				'status' => $status 
		), $where );
	}
	
	/**
	 * 添加用户扫描日志列表
	 */
	public function addLogInfo($data) {
		$data ['ctime'] = time ();
		$data ['status'] = '1';
		$this->tableName = 'qrcode_log';
		return $this->insert ( $data );
	}
	
	/**
	 * 只返回扫描事件的素材列表
	 * 
	 * @param array $where
	 *        	条件搜索
	 *        	
	 */
	public function getMaterial($where) {
		$this->tableName = 'qrcode';
		$aopenid = $where ['aopenid'];
		$type = $where ['type'];
		$sceneid = $where ['sceneid'];
		$qr_sql = "SELECT * FROM `" . TABLE_PREFIX . "qrcode` WHERE `aopenid` = '" . $aopenid . "' AND `sceneid`= {$sceneid} AND `status` != '4'";
		$qr_one = $this->query ( $qr_sql );
		$this->getSql ();
		$mat_id = $qr_one [0] ['materialid'];
		if ($mat_id != 0) {
			$mat_sql = "SELECT * FROM `" . TABLE_PREFIX . "material` WHERE `aopenid` = '" . $aopenid . "' AND `type` = '" . $type . "' AND `status` = '1' AND `id` IN (" . $mat_id . ")";
			$mat_arr = $this->query ( $mat_sql );
		}
		// 关注回复
		if (! $mat_arr && $where ['event'] == 'subscribe') {
			$account_sql = "SELECT `materialid` FROM " . TABLE_PREFIX . "account WHERE `openid` = '{$aopenid}'";
			$account = $this->query ( $account_sql );
			$mat_sql = "SELECT * FROM `" . TABLE_PREFIX . "material` WHERE `aopenid` = '" . $aopenid . "' AND `type` = '" . $type . "' AND `status` = '1' AND `id` IN (" . $account [0] ['materialid'] . ")";
			$mat_arr = $this->query ( $mat_sql );
		}
		// 将图文数据转换为微信需要的数据格式
		if ($mat_arr) {
			foreach ( $mat_arr as $v ) {
				$res_mat_arr [] = array (
						'title' => $v ['content'],
						'description' => $v ['description'] ? $v ['description'] : '',
						'url' => $v ['linkurl'],
						'picurl' => $v ['thubmurl'] 
				);
			}
		}
		
		return $res_mat_arr;
	}
}