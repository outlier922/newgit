<?php
//whb:客户端插件调用此接SubmitService接口
include_once ("Config.php");
include_once ("Utils.php");
class SubmitEntity
{
    static function getXml($merchantOrderId, $merchantOrderTime,$merchantOrderAmt,$merchantOrderDesc,$transTimeout,$backEndUrl)
    {
        
        $merchantPublicCert = SecretUtils::getPublicKeyBase64(MY_public_key);
        $merchantId = MY_id;
        $merchantName=MY_name;
        $strForSign = "merchantName=" . $merchantName . 
            "&merchantId=" . $merchantId .
            "&merchantOrderId=" . $merchantOrderId . 
            "&merchantOrderTime=" . $merchantOrderTime.
            "&merchantOrderAmt=" . $merchantOrderAmt . 
            "&merchantOrderDesc=" . $merchantOrderDesc.
            "&transTimeout=" .$transTimeout;
        
        $sign = SecretUtils::sign($strForSign, MY_private_key, MY_prikey_password);
		
        $attrArray = array("application" => "SubmitOrder.Req", "version" => "1.0.0");
        $nodeArray = array("merchantName" => $merchantName,
            "merchantId" => $merchantId,
            "merchantOrderId" => $merchantOrderId, 
            "merchantOrderTime" => $merchantOrderTime,
            "merchantOrderAmt"=>$merchantOrderAmt,
            "merchantOrderDesc"=>$merchantOrderDesc,
            "transTimeout"=>$transTimeout,
            "backEndUrl"=>$backEndUrl,
            "sign" => $sign, 
            "merchantPublicCert" => $merchantPublicCert);
        
        $result = XmlUtils::writeXml($attrArray, $nodeArray);       
        
        return $result;
    }
}

$backEndUrl=Bank_End_Url;

require_once "../sys_pay.core.php";

$merchantOrderId=$out_trade_no; // 商户订单号
$merchantOrderTime=date('YmdHis');//由服务器端自动生成，形如：20130830115547
$merchantOrderAmt=$total_fee*100;//单位：分,由客户端post传入
$merchantOrderDesc=SYS_ZH_NAME;
$transTimeout="";//超时时间，默认为空，表示24小时内有效

//提交到支付平台backEndUrl
$getReturnXml = SubmitEntity::getXml($merchantOrderId, $merchantOrderTime,$merchantOrderAmt,$merchantOrderDesc,$transTimeout,$backEndUrl);

$postDeal = new PostUtils();
$recv = $postDeal->submitByPost(SUBMIT_url, $getReturnXml);

$xmlDeal = new XmlUtils();
$parse= $xmlDeal->readXml($recv);

if ($parse) {
    $nodeArray = $xmlDeal->getNodeArray();
    $respCode = $nodeArray['respCode'];
	
    if($respCode=='0000')//如果成功解析
    {
		//得到三要素
    	$merchantId = $nodeArray['merchantId'];
    	$merchantOrderId = $nodeArray['merchantOrderId'];
    	$merchantOrderTime = $nodeArray['merchantOrderTime'];
    
    	$strForSign = "merchantId=" . $merchantId .
            "&merchantOrderId=" . $merchantOrderId . 
            "&merchantOrderTime=" . $merchantOrderTime;
			
		//将三要素再次签名，通过echo  $result 发送给客户端
    	$mySign = SecretUtils::sign($strForSign, MY_private_key, MY_prikey_password);
    	// 兼容webservice接口
    	$infor = array();
    	$infor[0] = array(
			'merchantId' => $merchantId,
			'merchantOrderId' => $merchantOrderId,
    		'merchantOrderTime' => $merchantOrderTime,
    		'merchantOrderAmt' => $merchantOrderAmt,
    		'sign' => $mySign
			);
		$body = array(
			'status' =>'1',
			'infor' => $infor
		);
		$result = json_encode($body);
    }else {
    	$body = array(
			'status' =>'0'
		);	
		$result = json_encode($body);
    }
} 
else 
{
    $body = array(
		'status' =>'0'
	);	
	$result = json_encode($body);
}
//将处理结果通过echo发送给客户端
echo  $result;
?>