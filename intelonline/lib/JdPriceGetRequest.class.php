<?php
/**
 * User: zhangfeng
 * Date: 12-6-13
 * Time: 上午11:46
 */
class JdPriceGetRequest {
	private $skuId;
	public function setSkuId($skuId) {
		$this->skuId = $skuId;
	}
	
	/**
	 * 首先需要对业务参数进行安装首字母排序，然后将业务参数转换json字符串
	 * 
	 * @return string
	 */
	public function getAppJsonParams() {
		$apiParams ['sku_id'] = $this->skuId;
		ksort ( $apiParams );
		return json_encode ( $apiParams );
	}
	
	/**
	 *
	 * 获取方法名称
	 * 
	 * @return string
	 */
	public function getApiMethod() {
		return "jingdong.ware.price.get";
	}
}