<?php
/**
 * 	配置账号信息
 */
class WxPayConf_pub {
	// =======【基本信息设置】=====================================
	// 微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = 'wx3830fdbaeeab7e85';
	// 受理商ID，身份标识
	const MCHID = '10022243';
	// 商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = '1485b0943d9669d132b04ce44d504bdd';
	// JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = '9ab6d5081a5c55d431ccdf9ee2f2e513';
	
	// =======【JSAPI路径设置】===================================
	// 获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	const JS_API_CALL_URL = "http://kaspersky.buzzopt.com/index.php/demo/oauthLoad"; // 用来获取公众平台用户相信信息。
	                                                                                 
	// =======【证书路径设置】=====================================
	                                                                                 // 证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = '/weixin/testpay/cacert/apiclient_cert.pem';
	const SSLKEY_PATH = '/weixin/testpay/cacert/apiclient_key.pem';
	
	// =======【异步通知url设置】===================================
	// 异步通知url，商户根据实际开发过程设定
	const NOTIFY_URL = "http://kaspersky.buzzopt.com/index.php/productCenter/notifyUrl"; // 这里url不用在授权目录下，而且不能使用&符链接的url，微信会转义。
	                                                                                     
	// =======【curl超时设置】===================================
	                                                                                     // 本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
?>