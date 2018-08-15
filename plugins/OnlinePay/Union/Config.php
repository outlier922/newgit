<?php header("Content-Type:text/html; charset=utf-8");?>
<?php

/**whbdemo:

898000000000001.cer 为前置公钥，仅用于验签交易结果通知
898000000000002.cer 为商户公钥
898000000000002.p12 为商户私钥

**/

//因为路径问题，此处不能直接加入head.inc.php,system.core.php中已经嵌入config配置文件
require_once("../../../include/system.core.php");

define("SUBMIT_url","http://mobilepay.unionpaysecure.com/qzjy/MerOrderAction/deal.action");
define("Bank_End_Url",SYS_ROOT."/plugins/OnlinePay/Union/notify_url.php");
//whbmemo:只能使用硬盘绝对路径，不能使用http网络路径
define("UNIONPAY_MOBILE_ROOT",dirname($_SERVER['SCRIPT_FILENAME']));	
define("UPOMP_public_key",UNIONPAY_MOBILE_ROOT."/qzscgy.cer");// 前置验签公钥
define("NOTIFY_public_key",UNIONPAY_MOBILE_ROOT."/qzscgy.cer");// 前置验签公钥
define("MY_id","802110048990739");// 商户ID
define("MY_name",SYS_EN_NAME);
define("MY_public_key",UNIONPAY_MOBILE_ROOT."/yinlianzhengshu.cer");// 商户公钥证书
//define("NOTIFY_URL",UNIONPAY_MOBILE_ROOT."/NotifyDeal.php");
define("MY_private_key",UNIONPAY_MOBILE_ROOT."/yinliansiyao.p12");// 商户私钥证书
define("MY_prikey_password","123456aA");// 商户私钥证书密码
date_default_timezone_set('PRC'); //设置时区
?>