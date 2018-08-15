<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */
if(!$_POST) exit('fail');

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功
	//获取支付宝的通知返回参数(特别注意，不能放在验签成功的外面)
	$out_trade_no = $_POST['out_trade_no']; // 商户订单号
	$total_fee = $_POST['total_fee'];				//交易金额
	$trade_no = $_POST['trade_no'];				//支付宝流水号	
	$trade_status = $_POST['trade_status'];	//交易状态

    if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
		//调用支付成功业务处理函数（在include\extend.inc.php中定义）
		ext_pay_success($out_trade_no,$trade_no,$total_fee,3);//1表示是支付宝网站
    }
	echo "success";	
}
else {
    //验证失败    
	if(SYS_DEBUG_MODE)
	{
		//调试模式记录到交易日志
		logResult("验证失败!");
	}
   	echo "fail";
}
?>