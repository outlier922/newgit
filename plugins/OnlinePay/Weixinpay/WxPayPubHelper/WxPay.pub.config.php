<?php
/**
* 	配置账号信息
*/

class WxPayConf_pub
{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = 'wx344819d86939291b';
	//受理商ID，身份标识
	const MCHID = '1485875332';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = '2226287a402c84f0571977793d7c36ae';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = '2226287a402c84f0571977793d7c36ae';
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	//const NOTIFY_URL = SYS_ROOT."/plugins/OnlinePay/Weixinpay/notify_url.php";
	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
	
?>