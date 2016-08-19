<?php
class JdPromotionGetRequest {
	private $skuId;
	private $webSite = 1;
	private $origin = 1;
	public function setSkuId($skuId) {
		$this->skuId = $skuId;
	}
	public function setWebSite($webSite) {
		$this->webSite = $webSite;
	}
	public function setOrigin($origin) {
		$this->origin = $origin;
	}
	
	/**
	 * 首先需要对业务参数进行安装首字母排序，然后将业务参数转换json字符串
	 * 
	 * @return string
	 */
	public function getAppJsonParams() {
		$apiParams ["skuId"] = $this->skuId;
		$apiParams ["webSite"] = $this->webSite;
		$apiParams ['origin'] = $this->origin;
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
		return "jingdong.ware.promotionInfo.get";
	}
}