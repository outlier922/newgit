<?php
/*
| --------------------------------------------------------
| 	文件功能：支付宝插件核心配置文件
|	程序作者：王海滨（技术部）
|	时间版本：2014-09-15
|	特别提示：请根据项目需要，更换业务处理逻辑
| --------------------------------------------------------
*/

/*
| --------------------------------------------------------
定义支付宝配置信息
| --------------------------------------------------------
*/
define("ALIPAY_PARTNER","2088721402546513");//定义商户PID(必须是2088开头)// 2088521497102951
define("ALIPAY_SELLER_ID","15066330577@163.com");//定义收款邮箱
define("ALIPAY_KEY","h8vy9oc6p9cqdo84s1i7u53minmtzwk4");//定义安全检验码(网页版产品专用)//h8vy9oc6p9cqdo84s1i7u53minmtzwk4

 
//嵌入系统核心配置文件config.inc.php
require_once("../../../include/system.core.php");

//↓↓↓↓↓↓↓↓↓↓whbmemo:以下配置请勿改动↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
$alipay_config['partner']	 =ALIPAY_PARTNER;
$alipay_config['seller_id']  =	ALIPAY_SELLER_ID;
$alipay_config['subject']		=	SYS_ZH_NAME;
$alipay_config['notify_url'] 	=	SYS_ROOT."plugins/OnlinePay/Alipay/notify_url.php";
//商户的私钥（后缀是.pem）文件相对路径
$alipay_config['private_key_path']	= 'key/rsa_private_key.pem';
//支付宝公钥（后缀是.pen）文件相对路径
$alipay_config['ali_public_key_path']= 'key/alipay_public_key.pem';
//签名方式 不需修改
$alipay_config['sign_type']    = strtoupper('RSA');
//字符编码格式 目前支持 gbk 或 utf-8
$alipay_config['input_charset']= strtolower('utf-8');
//ca证书用于curl中ssl校验，必须使用本地硬盘绝对路径地址，请保证cacert.pem文件在当前文件夹目录中
$alipay_config['cacert']    = getcwd().'\\cacert.pem';
//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$alipay_config['transport']    = 'http';
?>