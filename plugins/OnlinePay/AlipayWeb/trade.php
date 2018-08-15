<?php 

require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");

//whbmemo:引入第三方支付核心业务处理文件
require_once "../sys_pay.core.php";
//ext_pay_success($out_trade_no,'ceshiyixia',$total_fee,3);
//组装待签名数据
$parameter = array(
	'service' => 'create_direct_pay_by_user',
	'partner' =>trim($alipay_config['partner']),					//合作商户号
	'return_url' =>$alipay_config['return_url'],				//同步返回
	'notify_url' =>$alipay_config['notify_url'],				//异步返回
	'_input_charset' => $alipay_config['input_charset'],	//字符集，默认为GBK
	'subject' => $alipay_config['subject'],	//商品名称，必填
	'body' => $alipay_config['body'],      //商品描述，必填

	"out_trade_no"   => $out_trade_no,     //商品外部交易号，必填（保证唯一性）
	"total_fee"          => $total_fee,           //商品单价，必填（价格不能为0）
	"payment_type"   => "1",              //默认为1,不需要修改

	"show_url"       => $alipay_config['show_url'],        //商品相关网站
	"seller_email"   => $alipay_config['email'],     //卖家邮箱，必填
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo $html_text;
 ?>