<?php
/*
| --------------------------------------------------------
| 	文件功能：支付宝服务器端签名文件
|	程序作者：王海滨（技术部）
|	时间版本：2014-09-15
|	特别提示：本核心函数文件作为FrameWork的底层承载部分，
|				  由王海滨专职维护，请勿更改
| --------------------------------------------------------
*/


require_once "alipay.config.php";
require_once "lib/alipay_rsa.function.php";

//whbmemo:引入第三方支付核心业务处理文件
require_once "../sys_pay.core.php";
ext_pay_success($out_trade_no,'ceshi',$total_fee,1);//1表示是支付宝手机端
//组装待签名数据
//whbmemo：（1）不允许将增加自定义参数（2）不允许转换双引号为单引号
$signData = "partner="."\"".$alipay_config['partner']."\"";
$signData .= "&";
$signData .= "seller_id="."\"".$alipay_config['seller_id']."\"";
$signData .= "&";
$signData .= "out_trade_no=" . "\"" . $out_trade_no ."\"";
$signData .= "&";
$signData .= "subject=" . "\"" . $alipay_config['subject'] ."\"";
$signData .= "&";
$signData .= "body=" . "\"" .SYS_ZH_NAME ."\"";
$signData .= "&";
$signData .= "total_fee=" . "\"" . $total_fee ."\"";
$signData .= "&";
$signData .= "notify_url=" . "\"" . urlencode($alipay_config['notify_url']) ."\"";
$signData .= "&";
$signData .= "_input_charset=" . "\"" . $alipay_config['input_charset'] ."\"";
$signData .= "&";
$signData .= "service=" . "\"" . "mobile.securitypay.pay" ."\"";
$signData .= "&";
$signData .= "payment_type=" . "\"" . "1" ."\"";

//服务器生成签名
$mySign = rsaSign($signData,"key/rsa_private_key.pem");//rsaSign函数内部进行了base-64编码
$mySign = urlencode($mySign);//单独对sign进行编码，非常重要

$returnStr = $signData;
$returnStr .= "&";
$returnStr .= "sign_type=" . "\"" . "RSA" ."\"";
$returnStr .= "&";
$returnStr .= "sign=" . "\"" . $mySign ."\"";

$temp_array[0]['alipaysign'] = $returnStr;
sys_out_success('验签成功',$temp_array);
?>