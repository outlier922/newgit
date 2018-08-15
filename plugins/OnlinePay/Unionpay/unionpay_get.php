<?php
//嵌入系统核心文件
require_once 'unionpay_config.php';

//whb:引入第三方支付核心业务处理文件
require_once "../sys_pay.core.php";	

//组装待签名数据
$params = array(
		//固定填写
		'version'=> '5.0.0',//版本号--M
		//默认取值：UTF-8
		'encoding'=> 'UTF-8',//编码方式--M
		//通过MPI插件获取
		'certId'=> getSignCertId (),//证书ID--M
		//01RSA02 MD5 (暂不支持)
		'signMethod'=> '01',//签名方法--M
		//取值：01 
		'txnType'=> '01',//交易类型--M
		//01：自助消费，通过地址的方式区分前台消费和后台消费（含无跳转支付）03：分期付款
		'txnSubType'=> '01',//交易子类--M
		// 
		'bizType'=> '000000',//产品类型--M
		'channelType'=> '07',//渠道类型--M
		//后台返回商户结果时使用，如上送，则发送商户后台交易结果通知
		'backUrl'=> SYS_ROOT."/plugins/OnlinePay/Unionpay/notify_url.php",//后台通知地址--M
		//0：普通商户直连接入2：平台类商户接入
		'accessType'=> '0',//接入类型--M
		//　
		'merId'=> UNIONPAY_MERID,//商户代码--M
		//商户端生成
		'orderId'=> $out_trade_no,//商户订单号--M
		//商户发送交易时间
		'txnTime'=> date('YmdHis'),//订单发送时间--M
		//交易单位为分
		'txnAmt'=> $total_fee*100,//交易金额--M
		//默认为156交易 参考公参
		'currencyCode'=> '156',//交易币种--M
		//商户自定义保留域，交易应答时会原样返回
		'reqReserved'=> $out_trade_no,//请求方保留域--O
		'orderDesc'=> SYS_ZH_NAME,//订单描述--C
);
// 签名
sign ( $params );
// 发送信息到后台
$result = sendHttpRequest ( $params, SDK_App_Request_Url);
//返回结果展示
$validResp = coverStringToArray ( $result );

// 商户的业务逻辑
if ($validResp){	
	$temp_array[0] = $validResp;
	sys_out_success('验签成功',$temp_array);
	if(SYS_DEBUG_MODE)
	{	
		// 初始化日志
		$log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
		$log->LogInfo ( "后台返回结果为>" . $result );
	}	
}else {
	sys_out_fail('验签错误!原因：请检查银联环境！',500);
	//logResult("unionpay_get签名验证失败");
}

?>
