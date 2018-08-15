<?php

//嵌入系统核心文件
require_once 'unionpay_config.php';


if (isset ( $_REQUEST ['signature'] )) {
	if (verify ( $_REQUEST )){// 服务器签名验证成功
		//获取银联通知返回参数(特别注意，不能放在验签成功的外面)	
		$out_trade_no = $_REQUEST['orderId'];// 我方订单号
		$total_fee = $_REQUEST['txnAmt']/100;// 交易金额，转换为元
		$trade_no = $_REQUEST['queryId'];// 银联流水号
		$transStatus = $_REQUEST['respCode'];// 交易状态		

		if (""!=$transStatus && "00"==$transStatus){
			//调用支付成功业务处理函数（在include\extend.inc.php中定义）
			ext_pay_success($out_trade_no,$trade_no,$total_fee,2);//2表示是银联手机端      
	    }      
		echo '验签成功';
	}else {// 服务器签名验证失败
		echo '验签失败';
	}
}
else {
	echo '签名为空';
}
?>