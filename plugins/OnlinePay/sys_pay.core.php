<?php
//特别提示：插入支付表sys_pay的逻辑,所有第三方支付插件必须完全相同__________begin
sys_check_token();//检查是否登录
$post_array=array('paytype','orderid','ordertype');
sys_check_post($post_array);//检查post必选参数完整性
unset($post_array);	

$sql_helper=new Mysql();
$client_id=sys_get_cid();
$paytype = _POST("paytype");	//支付类型
$orderid=_POST('orderid');
$ordertype=_POST('ordertype');
if($ordertype == 1){
	$order_list = $sql_helper->get_list_bysql("select out_trade_no,totalfee,payflag,good_id from sys_o2order where id=$orderid and client_id=$client_id");
	if(!$order_list){
		sys_out_fail('该订单不存在');
	}
	$good_id = $order_list[0]['good_id'];
	$good_list = $sql_helper->get_list_bysql("select * from sys_good where id=$good_id");
	if(!$good_list || $good_list[0]['flag'] == 2){
		sys_out_fail('该商品已删除或已下架');
	}
	$payflag = $order_list[0]['payflag'];
	if($payflag != 1){
		sys_out_fail('该订单已支付，请勿重复支付');
	}
}else if($ordertype == 2){
	$order_list = $sql_helper->get_list_bysql("select out_trade_no,totalfee,payflag from sys_sysorder where id=$orderid and client_id=$client_id");
	if(!$order_list){
		sys_out_fail('该订单不存在');
	}
	$payflag = $order_list[0]['payflag'];
	if($payflag == 1){
		sys_out_fail('该订单已支付，请勿重复支付');
	}
}else{
	sys_out_fail('参数传递错误');
}


$out_trade_no = $order_list[0]['out_trade_no'];
$total_fee = $order_list[0]['totalfee'];

sys_close_db($sql_helper);		
//特别提示：插入支付表sys_pay的逻辑,所有第三方支付插件必须完全相同_________end
?>