<?php
/**
 * qrcodeAction.class.php
 * 二维码管理类
 * @author wgs
 * @since 2014.5.20
 */
class qrcodeAction extends Action {
	public function __construct() {
		parent::__construct ( FALSE );
		$this->openid = 'gh_ac1de3a89dc7';
		$this->userid = 2;
		$wechat = new wechatAction ();
		$accessToken = $wechat->getAccessTokenByOpenid ( $this->openid );
		$this->M = new qrcode ();
		$this->Api = new WechatApi ( $accessToken );
	}
	
	/**
	 * 二维码扫描响应事件
	 * 
	 * @param array $message
	 *        	扫描二维码响应的数据
	 *        	参数实例：
	 *        	$message = array(
	 *        	'ToUserName'=>'gh_ac1de3a89dc7',
	 *        	'FromUserName'=>'oAhz7t3f8kgvMR1V7AYRkFgFh38M',
	 *        	'CreateTime'=>time(),
	 *        	'MsgType'=>'event',
	 *        	'Event'=>'subscribe',
	 *        	'EventKey'=>4,
	 *        	'Ticket'=>'124321342'
	 *        	);
	 */
	public function qrScan($message) {
		$event = $message ['Event'];
		$aopenid = $message ['ToUserName'];
		$openid = $message ['FromUserName'];
		$sceneid = strpos ( $message ['EventKey'], 'qrscene_' ) === 0 ? substr ( $message ['EventKey'], 8 ) : $message ['EventKey'];
		$where = array (
				'aopenid' => $aopenid,
				'type' => '5',
				'sceneid' => $sceneid,
				'event' => $event 
		); // type = 5为默认二维码扫描回复图文信息
		$mat_arr = $this->M->getMaterial ( $where ); // 包括了关注响应的事件
		                                          // 写入二维码扫描列表
		$this->M->addLogInfo ( array (
				'aopenid' => $aopenid,
				'openid' => $openid,
				'sceneid' => $sceneid 
		) );
		// p($mat_arr);
		return $mat_arr;
	}
	
	/**
	 * 二维码列表
	 * 
	 * @return array $list_arr 二维码的基本信息列表（其中包括素材列表）
	 *         其中描述素材列表的key值为：mat_arr
	 */
	public function qrList() {
		$type = '5'; // 默认回复图文
		$p = empty ( $_GET ['p'] ) ? 1 : $_GET ['p']; // 分页
		$num = 2; // 每页显示的数量
		$where = array (
				'aopenid' => $this->openid,
				'uid' => $this->userid,
				'type' => $type,
				'num' => $num 
		);
		$list_arr = $this->M->qrList ( $where );
		$page = $this->pages ( $list_arr ['total'], $p, $num, 10, 'rep_list' );
	}
	
	/**
	 * 创建二维码
	 * 
	 * @param int $qrid;
	 *        	编辑或删除的时二维码信息唯一id
	 * @param string $opt
	 *        	操作类型 'c':创建 'u':编辑 ‘d’:删除
	 * @param string $type
	 *        	二维码类别 1为永久 2为临时
	 * @param int $expire_second
	 *        	临时二维码有效时长
	 * @param string $desc
	 *        	功能描述
	 * @param
	 *        	string materialid 0 为不响应，其余为素材id，多素材id用逗号分隔
	 * @param string $status
	 *        	'1' 正常 '2' 禁止
	 * @return $opt = 'c' 在uploads/openid下生成sceneid.jpg二维码图片 在qrcode表中添加相应数据
	 *         $opt = 'u' 修改uploads/openid/sceneid.jpg的信息(文件名没有修改)，包括qrcode表中相应数据
	 *         $opt = 'd' 修改uploads/openid/sceneid.jpg.del的名称，同时修改表中status = '4';
	 */
	public function createQrImg() {
		/* 参数 start */
		$opt = 'c';
		// $qrid =1;
		// $expire_second = 1200;
		$qrPath = 'uploads/qrcode/' . $this->openid . '/';
		$type = 1;
		$desc = '响应事件的描述信息';
		$materialid = '2,3';
		$status = '1';
		/* 参数 end */
		
		// 删除二维码信息
		if ($opt == 'd' && $qrid) {
			$qrOne = $this->M->first ( array (
					'id' => $qrid 
			) );
			$qrOne ['status'] == '4' ? exit ( '已经删除' ) : '';
			$del_file = $qrOne ['imgurl'];
			if (file_exists ( $del_file )) {
				rename ( $del_file, $del_file . '.del' );
			}
			$del_res = $this->M->update ( array (
					'status' => 4,
					'imgurl' => $del_file . '.del' 
			), array (
					'id' => $qrid 
			) );
			echo $del_res ? '删除成功' : '操作失败';
		}
		// 生成或修改信息
		if ($opt == 'c' || $opt == 'u') {
			$qrid = $qrid ? $qrid : 0;
			$expire_second = $expire_second ? $expire_second : 0;
			$typeName = $type == 1 ? 'QR_LIMIT_SCENE' : 'QR_SCENE';
			
			if ($opt == 'c') {
				$getMaxSceneid = "SELECT MAX(sceneid) as sceneid FROM `" . TABLE_PREFIX . "qrcode` WHERE `aopenid` = '" . $this->openid . "'"; // 获取最大的sceneid
				$maxSceneArr = $this->M->query ( $getMaxSceneid );
				$maxSceneid = ($maxSceneArr [0] ['sceneid'] ? $maxSceneArr [0] ['sceneid'] : 0) + 1;
			}
			if ($opt == 'u') {
				$qrOne = $qrid ? $this->M->first ( array (
						'id' => $qrid 
				) ) : '';
				$maxSceneid = $qrOne ['sceneid'];
			}
			$qrTicket = $this->Api->makeQrTicket ( $maxSceneid, $typeName, $expire_second );
			$res = $this->Api->getImageByTicket ( $qrTicket ['ticket'] );
			$fpath = $qrPath . $maxSceneid . '.jpg';
			// 生成图片
			if (mkpath ( $qrPath )) {
				file_put_contents ( $fpath, $res );
			}
			// 二维码信息
			$data = array (
					'aopenid' => $this->openid,
					'imgurl' => $fpath,
					'sceneid' => $maxSceneid,
					'type' => $type,
					'desc' => $desc,
					'status' => $status,
					'uid' => $this->userid,
					'materialid' => $materialid,
					'expiretime' => $expire_second 
			);
			if ($qrid) {
				$this->M->upQrcode ( $data, array (
						'id' => $qrid 
				) );
				echo '二维码信息修改成功';
			} else {
				$this->M->addQrcode ( $data );
				echo '二维码信息完成生成';
			}
		}
	}
	
	/**
	 * 扫描用户log日志列表
	 */
	public function qrLogList() {
		$res = $this->M->getLogList ( $this->openid );
		p ( $res );
	}
	
	/**
	 * 删除用户log日志
	 * 
	 * @param
	 *        	logid 为日志的id
	 */
	public function delLogInfo() {
		$logid = 1;
		$where = array (
				'aopenid' => $this->openid,
				'id' => $logid 
		);
		$resid = $this->M->delLogInfo ( $where );
		echo $resid ? '删除成功' : '删除失败';
	}
}