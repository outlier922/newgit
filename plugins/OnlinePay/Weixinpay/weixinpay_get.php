<?php

	require_once("../../../include/system.core.php");
	//引入第三方支付核心业务处理文件
	require_once "../sys_pay.core.php";	
	include_once("WxPayPubHelper/WxPayPubHelper.php");
	
	//=========使用统一支付接口，获取prepay_id============
	//使用统一支付接口
	$unifiedOrder = new UnifiedOrder_pub();
	
	//设置统一支付接口参数
	//设置必填参数
	//appid已填,商户无需重复填写
	//mch_id已填,商户无需重复填写
	//noncestr已填,商户无需重复填写
	//spbill_create_ip已填,商户无需重复填写
	//sign已填,商户无需重复填写
	// $unifiedOrder->setParameter("openid","$openid");//商品描述
	$unifiedOrder->setParameter("body",SYS_ZH_NAME);//商品描述
	//自定义订单号，此处仅作举例
	//$timeStamp = time();
	//$out_trade_no = WxPayConf_pub::APPID."$timeStamp";
	$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
	$unifiedOrder->setParameter("total_fee",$total_fee*100);//总金额
	$unifiedOrder->setParameter("notify_url",SYS_ROOT."plugins/OnlinePay/Weixinpay/notify_url.php");//通知地址 
	$unifiedOrder->setParameter("trade_type","APP");//交易类型
	//非必填参数，商户可根据实际情况选填
	//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
	//$unifiedOrder->setParameter("device_info","XXXX");//设备号 
	//$unifiedOrder->setParameter("attach","XXXX");//附加数据 
	//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
	//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
	//$unifiedOrder->setParameter("openid","XXXX");//用户标识
	//$unifiedOrder->setParameter("product_id","XXXX");//商品ID

	$prepay_id = $unifiedOrder->getPrepayId();
	if ($prepay_id != null) {
		$temp_array = null;
		$temp_array[0]['appid'] = WxPayConf_pub::APPID;//公众账号ID
		$temp_array[0]['noncestr'] = $unifiedOrder->createNoncestr();//随机字符串
		$temp_array[0]['package'] = "Sign=WXPay";//暂填写固定值Sign=WXPay
		$temp_array[0]['partnerid'] = WxPayConf_pub::MCHID;
		$temp_array[0]['prepayid'] = $prepay_id;
		$temp_array[0]['timestamp'] = time();
		$temp_array[0]['sign'] = $unifiedOrder->getSign($temp_array[0]);//签名
		sys_out_success('验签成功',$temp_array);
	}else{
		sys_out_fail('验签错误!原因：请检查微信环境！',500);
	}
?>