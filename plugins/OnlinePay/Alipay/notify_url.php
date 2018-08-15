<?php
/*
| --------------------------------------------------------
| 	文件功能：支付宝付款结果异步通知接受文件
|	程序作者：王海滨（技术部）
|	时间版本：2014-09-15
|	特别提示：本文件请勿改动，业务逻辑统一在ext_pay_success定义
| --------------------------------------------------------
*/
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功 
	
	
	//获取支付宝的通知返回参数(特别注意，不能放在验签成功的外面)
	$out_trade_no = $_POST['out_trade_no'];	//我方订单号
	$total_fee = $_POST['total_fee'];				//交易金额
	$trade_no = $_POST['trade_no'];				//支付宝流水号	
	$trade_status = $_POST['trade_status'];	//交易状态

	sys_log("trade_status=".$trade_status);
	
	//whbmemo:TRADE_SUCCESS状态表示交易成功并且可退款 TRADE_FINISHED表示交易成功不可退款
	if($trade_status == 'TRADE_FINISHED'|| $trade_status == 'TRADE_SUCCESS'){		
		//调用支付成功业务处理函数（在include\extend.inc.php中定义）
		ext_pay_success($out_trade_no,$trade_no,$total_fee,1);//1表示是支付宝手机端
    }      
	echo "success";
}
else {
	sys_log("verify_result=".$verify_result);
    //验证失败    
	if(SYS_DEBUG_MODE)
	{
		//调试模式记录到交易日志
		logResult("验证失败!");
	}
   	echo "fail";
}
?>