<?php
/*
| --------------------------------------------------------
| 	文件功能：	公司XX项目定时推送任务类
|	程序作者：	王海滨（移动互联部）
|	时间版本：	2014-11-28
|	特别说明：	(1)本类必须继承公司公共框架PublicAction基类					
				 	(2)Linux系统定时器每2分钟执行一次本程序
| --------------------------------------------------------
*/
// http://124.128.23.74:8008/group16/hm_lhkj/index.php?g=Webservice&m=Push&a=push_task
class PushAction extends PublicAction {

	public function push_task(){	
		//1小时未支付取消订单
		$sqlstr="delete from sys_o2order where timestampdiff(minute,regdate,NOW())>60 and payflag=1";
		$this->do_execute($sqlstr);
		$sqlstr="delete from sys_sysorder where timestampdiff(minute,regdate,NOW())>60 and payflag=2";
		$this->do_execute($sqlstr);
		$sqlstr="delete from sys_cardorder where timestampdiff(minute,regdate,NOW())>60 and payflag=2";
		$this->do_execute($sqlstr);
		//3天后重新邀请
		$sqlstr="delete from sys_phone where timestampdiff(minute,regdate,NOW())>4320 and iszhuce=2";
		$this->do_execute($sqlstr);
		//优惠券过期自动不可用
		$sqlstr_array = NULL;
		$sqlstr_array[]="update sys_card set validflag=2 where timestampdiff(minute,concat(end_regdate,' 23:59:59'),NOW())>0 and validflag=1";
		$card_ids = $this->get_list_bysql("select id from sys_card where timestampdiff(minute,concat(end_regdate,' 23:59:59'),NOW())>0 and validflag=1");		
		if($card_ids){
			$card_id_arr = array();
			foreach($card_ids as $v){
				$card_id_arr[] = $v['id'];
			}
			$card_ids = implode(',',$card_id_arr);
			$cardorder_ids = $this->get_list_bysql("select id from sys_cardorder where card_id in ($card_ids)");
			if($cardorder_ids){
				$cardorder_id_arr = array();
				foreach($cardorder_ids as $v){
					$cardorder_id_arr[] = $v['id'];
				}
				$cardorder_ids = implode(',',$cardorder_id_arr);
				$sqlstr_array[]="update sys_card_no set is_do=3,regdate='" . sys_get_time() . "' where cardorder_id in ($cardorder_ids) and is_do=2";
			}			
		}
		$this->do_transaction($sqlstr_array);
		//过期已核销但未使用的，自动过期
		$sqlstr="select id,client_id,out_trade_no from sys_o2order where timestampdiff(minute,concat(endtime,' 23:59:59'),NOW())>0 and payflag=2 and is_verification=1";
		$push_array=$this->get_list_bysql($sqlstr);
		if($push_array){
			foreach($push_array as $vo){
				$client_id = $vo['client_id'];
				$keyid = $vo['id'];
				$out_no_count = $this -> get_one_bysql("select count(*) from sys_out_no where o2order_id=$keyid and is_do=2");
				if($out_no_count > 0){
					$content='订单'.$vo['out_trade_no'].'已过期';
					//首先插入系统通知表
					$sqlstr_array = NULL;
					$sqlstr_array[]="insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='" . sys_get_time() . "',looktype=0,keytype=2,type=1,keyid=$keyid";
					$sqlstr_array[]="update sys_o2order set payflag=3,assess_time='" . sys_get_time() . "' where id=$keyid and payflag=2 and client_id=$client_id";
					$sqlstr_array[]="update sys_out_no set is_do=4,regdate='" . sys_get_time() . "' where o2order_id=$keyid and is_do=2";
					
			        $shop_list = $this->get_list_bysql("select s.id as shop_id,s.name as shop_name,s.address,s.arrival_rate,s.wealth_rate,s.service_type,s.service_rate,s.service_fee,o.totalfee,o.client_id from sys_o2order o left join sys_o2order_record re on o.id = re.order_id left join sys_shop s on re.shop_id = s.id where o.id=$keyid");
					$arrival_rate = $shop_list[0]['arrival_rate'];
					$wealth_rate = $shop_list[0]['wealth_rate'];
					$service_type = $shop_list[0]['service_type'];
					$totalfee = $shop_list[0]['totalfee'];
					$client_id = $shop_list[0]['client_id'];
					$shop_id = $shop_list[0]['shop_id'];
					$shop_name = $shop_list[0]['shop_name'];
					$address = $shop_list[0]['address'];
					if($service_type == 1){
						$arrival_fee = 	round($totalfee*$arrival_rate,2);				
						$wealth_fee = 	round($totalfee*$wealth_rate,2);
						$service_fee = 	$totalfee - $arrival_fee;									
					}else{
						$service_fee = $shop_list[0]['service_fee'];
						$arrival_fee = $totalfee - $service_fee;				
						$wealth_fee = 	round($totalfee*$wealth_rate,2);					
					}
					$islive = $this->get_one_bysql("select islive from sys_client where id=$client_id");
					if($islive == 1){					
						$sqlstr_array[] = "update sys_shop set feeaccount=feeaccount+$arrival_fee,wealth_redbag=wealth_redbag+$wealth_fee,redbag=redbag+$wealth_fee where id=$shop_id";				
						$sqlstr_array[] = "update sys_client set wealth=wealth+$wealth_fee where id=$client_id";
						$sqlstr_array[] = "insert into sys_wealth_redbag set type=1,fee=$wealth_fee,regdate='$regdate',client_id=$client_id,shop_id=$shop_id,o2order_id=$keyid";
						$sqlstr_array[] = "insert into sys_income set totalfee=$totalfee,type=1,client_id=$client_id,shop_id=$shop_id,shop_name='$shop_name',arrival_fee=$arrival_fee,wealth_fee=$wealth_fee,service_fee=$service_fee,address='$address',regdate='$regdate',o2order_id=$keyid";
						$content = '订单'.$vo['out_trade_no'].'已核销完毕，恭喜您获得'.$wealth_fee.'成长值';
	        			$sqlstr_array[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$regdate',looktype=0,keytype=2,type=1,keyid=$keyid";
					}else{
						if($totalfee >= 30){
							$sqlstr_array[] = "update sys_client set wealth=100,feeaccount=returnfee,islive=1 where id=$client_id";
							$client_arr[] = $client_id;
						}
						$sqlstr_array[] = "update sys_shop set feeaccount=feeaccount+$arrival_fee where id=$shop_id";						
						$wealth_fee = 0;
						$sqlstr_array[] = "insert into sys_income set totalfee=$totalfee,type=1,client_id=$client_id,shop_id=$shop_id,shop_name='$shop_name',arrival_fee=$arrival_fee,wealth_fee=$wealth_fee,service_fee=$service_fee,address='$address',regdate='$regdate',o2order_id=$keyid";
					}
					
					$this->do_transaction($sqlstr_array);
					unset($sqlstr_array);
					$this->pub_add_systemnotice(1,1,1,$content,$client_id,1);
				}		
			}									
		}	
		//过期未核销，自动退款
		$sqlstr="select id,client_id,out_trade_no from sys_o2order where timestampdiff(minute,concat(endtime,' 23:59:59'),NOW())>0 and payflag=2 and is_verification=2";
		$push_array=$this->get_list_bysql($sqlstr);
		if($push_array){
			foreach($push_array as $vo){
				$client_id = $vo['client_id'];
				$keyid = $vo['id'];
				$out_no_count = $this -> get_one_bysql("select count(*) from sys_out_no where o2order_id=$keyid and is_do=1");
				if($out_no_count == 0){				
					$content='订单'.$vo['out_trade_no'].'已退款成功';
					//首先插入系统通知表
					$sqlstr_array = NULL;
					$sqlstr_array[]="insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='" . sys_get_time() . "',looktype=0,keytype=2,type=1,keyid=$keyid";
					$sqlstr_array[]="update sys_o2order set payflag=5 where id=$keyid and payflag=2 and client_id=$client_id";
					$sqlstr_array[]="update sys_out_no set is_do=3,regdate='" . sys_get_time() . "' where o2order_id=$keyid and is_do=2";
					$order_list = $this->get_list_bysql("select num,good_id,totalfee from sys_o2order where id=$keyid");
					$num = $order_list[0]['num'];
					$good_id = $order_list[0]['good_id'];
					$totalfee = $order_list[0]['totalfee'];
					$sqlstr_array[]="update sys_good set salenum=salenum-$num where id=$good_id";
					$sqlstr_array[]="update sys_client set feeaccount=feeaccount+$totalfee,returnfee=returnfee+$totalfee where id=$client_id";
					$sqlstr_array[]="insert into sys_cash set client_id=$client_id,regdate='" . sys_get_time() . "',score=$totalfee,cashflag=4,isget=1";;
					$this->do_transaction($sqlstr_array);
					unset($sqlstr_array);
					$this->pub_add_systemnotice(1,1,1,$content,$client_id,1);
				}		
			}									
		}	
		//超过7天自动好评
		$sqlstr="select id,good_id,client_id from sys_o2order where timestampdiff(minute,assess_time,NOW())>10080 and payflag=3";
		$order_list=$this->get_list_bysql($sqlstr);
		if($order_list){
			$sqlstr_array = NULL;
			$regdate = sys_get_time(); 
			foreach($order_list as $vo){
				$orderid = $vo['id'];
				$good_id = $vo['good_id'];
				$client_id = $vo['client_id'];
				$shop_id = $this->get_one_bysql("select shop_id from sys_o2order_record where order_id=$orderid");
				$sqlstr_array[] = "update sys_o2order set payflag=4 where id=$orderid and payflag=3 and client_id=$client_id";
				if($shop_id){
					$sqlstr_array[] = "insert into sys_comment set client_id=$client_id,star=5,o2order_id=$orderid,regdate='$regdate',anonymous=2,shop_id=$shop_id,good_id=$good_id";				
					$shop_list = $this->get_list_bysql("select all_orders,all_star from sys_shop where id=$shop_id");
					$all_orders = $shop_list[0]['all_orders'];
					$all_star = $shop_list[0]['all_star'];
					$all_orders = $all_orders+1;
					$all_star = $all_star+5;
					$star_aver = round($all_star/$all_orders);
					$sqlstr_array[] = "update sys_shop set all_orders=$all_orders,all_star=$all_star,star=$star_aver where id=$shop_id";
				}			
			}
			$this->do_transaction($sqlstr_array);								
		}
		sys_log("push_task执行一次");//正式部署时请屏蔽，防止LOG日志文件过大	
    }
}