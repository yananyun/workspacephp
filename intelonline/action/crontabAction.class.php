<?php
/**
 * crontabAction.class.php
 *		定时脚本
 * 
 * @author Harry
 * @since 2014.5.20
 */
class crontabAction extends Action {
	public function __construct() {
		parent::__construct ( FALSE );
	}
	
	/**
	 * 更新AccessToken
	 * 每小时执行一次
	 */
	public function updateAccessToken() {
		$Wechat = new wechat ();
		$Api = new WechatApi ( 1 );
		
		$accountInfo = $Wechat->getConf ( array (
				'status' => 1 
		), 1 );
		foreach ( $accountInfo as $account ) {
			$tokenInfo = $Api->getAccessTokenByAccountInfo ( $account ['appid'], $account ['appsecret'] );
			if (! isset ( $tokenInfo ['errcode'] )) {
				$info = array ();
				$info ['access_token'] = $tokenInfo ['access_token'];
				$Wechat->updateInfo ( $info, array (
						'id' => $account ['id'] 
				) );
			}
		}
	}
	
	/**
	 * 获取当前公众号的关注数，并入库
	 * 每天凌晨1点执行一次
	 */
	public function getFansTotal() {
		$MEMBER = new member ();
		$MEMBER->getTotal ();
		unset ( $MEMBER );
	}
	
	/**
	 * 获取礼品统计数据，并入库
	 * 每天凌晨1点执行一次
	 */
	public function getGiftOrderStatistics() {
		set_time_limit ( 0 );
		$GIFTORDER = new giftOrder ();
		$GIFTORDER->getGiftOrderPercent ();
	}
	
	/**
	 * 订单的统计
	 * 每天凌晨1点执行一次
	 */
	public function orderStatistics() {
		$statistical = new statistical ();
		$statistical->orderStatistics ();
	}
	
	/**
	 * 关闭24小时内未支付的订单
	 * 每日凌晨1:30执行一次
	 */
	public function truncateNoPayOrder() {
		$PRODUCT = new products ();
		$PRODUCT->closeOrder ();
	}
}