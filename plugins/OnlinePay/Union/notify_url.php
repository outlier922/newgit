<?php
require_once("../../../include/system.core.php");
//whb:支付平台在流程结束后异步通知客户端插件调用此接SubmitService接口
include_once("Config.php");
include_once("Utils.php");

$xmlDeal = new XmlUtils();
// 解析获取到的xml
$parse=$xmlDeal->readXml($xml);

if ($parse) {
	 //获取键值对
	$nodeArray = $xmlDeal->getNodeArray();
	//验签
	$checkIdentifier = "transType=".$nodeArray['transType'].
		"&merchantId=".$nodeArray['merchantId'].
		"&merchantOrderId=".$nodeArray['merchantOrderId'].
		"&merchantOrderAmt=".$nodeArray['merchantOrderAmt'].
		"&settleDate=".$nodeArray['settleDate'].
		"&setlAmt=".$nodeArray['setlAmt'].
		"&setlCurrency=".$nodeArray['setlCurrency'].
		"&converRate=".$nodeArray['converRate'].
		"&cupsQid=".$nodeArray['cupsQid'].
		"&cupsTraceNum=".$nodeArray['cupsTraceNum'].
		"&cupsTraceTime=".$nodeArray['cupsTraceTime'].
		"&cupsRespCode=".$nodeArray['cupsRespCode'].
		"&cupsRespDesc=".$nodeArray['cupsRespDesc'].
		"&respCode=".$nodeArray['respCode'] ;
	$respCode=SecretUtils::checkSign($checkIdentifier,NOTIFY_public_key,$nodeArray['sign']);
	$out_trade_no = $nodeArray['merchantOrderId'];
	$total_fee = $nodeArray['merchantOrderAmt']*0.01;
	$trade_no = $nodeArray['cupsQid'];

	if($respCode=='0000'){
		//验签通过 此处调用结束方法，打印日志
		//因为只有验签通过的才是真实的返回信息
		ext_pay_success($out_trade_no,$trade_no,$total_fee,2);
		whb_log($out_trade_no.'验签通过');
	}else{
		ext_pay_fail($out_trade_no);
		if($respCode=='0001'){
			
			whb_log($out_trade_no.'验签失败：0001');
			//验签失败 
		}
		if($respCode=='9999'){
			
			whb_log($out_trade_no.'验签异常：9999');
		}
	}	
	//生成响应报文给银联支付平台（即如果respCode不是0000则会一直发送给商户服务器的notifyDeal）	
	$attrArray = array("application" =>"TransNotify.Rsp" ,"version"=>"1.0.0");
	$nodeArray = array("transType" =>$nodeArray['transType'] ,
		"merchantId"=>$nodeArray['merchantId'],
		"merchantOrderId"=>$nodeArray['merchantOrderId'],
		"respCode"=>$respCode);
	$result = XmlUtils::writeXml($attrArray,$nodeArray);
	echo $result ;
}else{
   echo "recieve notify message is xml";
}

/**日志消息,把支付平台服务器异步通知的消息打印到日志文件
 * 请注意服务器是否开通fopen配置
 */
function  whb_log($word) {
    $fp = fopen("whb_notify_log.txt","a+");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}
?>