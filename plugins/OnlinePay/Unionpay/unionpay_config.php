<?php
/*
| --------------------------------------------------------
定义银联支付配置信息
| --------------------------------------------------------
*/
define("UNIONPAY_MERID","898110272950092");//定义商户ID

//签名证书路径 （联系运营获取两码，在CFCA网站下载后配置，自行设置证书密码并配置）
//签名证书路径
const SDK_SIGN_CERT_PATH = 'certs/hm_ymzp.pfx';
// 签名证书密码 
const SDK_SIGN_CERT_PWD = '111111';

//嵌入系统核心文件
require_once '../../../include/system.core.php';
require_once 'func/common.php';
require_once 'func/SDKConfig.php';
require_once 'func/secureUtil.php';
require_once 'func/httpClient.php';
require_once 'func/log.class.php';


?>