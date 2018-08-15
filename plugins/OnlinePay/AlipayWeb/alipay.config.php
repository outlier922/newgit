<?php
/* *
 * 配置文件
 * 版本：3.3
 * 日期：2012-07-19
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */
 
//嵌入系统核心配置文件config.inc.php
require_once("../../../include/system.core.php");
//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//合作身份者id，以2088开头的16位纯数字
$alipay_config['partner'] = ALIPAY_PARTNER;
//安全检验码，以数字和字母组成的32位字符
$alipay_config['key'] = ALIPAY_KEY;
// 卖家收款邮箱
$alipay_config['email']  =	ALIPAY_SELLER_ID;
$alipay_config['subject']		=	SYS_ZH_NAME;
$alipay_config['body']		=	SYS_ZH_NAME;
//签名方式 不需修改
$alipay_config['sign_type']    = strtoupper('MD5');
//字符编码格式 目前支持 gbk 或 utf-8
$alipay_config['input_charset']= strtolower('utf-8');
//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
// $alipay_config['cacert']    = getcwd().'\\cacert.pem';
//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$alipay_config['transport']    = 'http';
// 同步返回地址
$alipay_config['return_url']    = '';
$alipay_config['notify_url']    = SYS_ROOT."plugins/OnlinePay/AlipayWeb/notify_url.php";
$alipay_config['show_url'] = ''; //你网站商品的展示地址,可以为空
?>