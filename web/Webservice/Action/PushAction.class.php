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
		//24小时后重新邀请
		$sqlstr="delete from sys_phone where timestampdiff(minute,regdate,NOW())>1440 and iszhuce=2";
		$this->do_execute($sqlstr);
		//过期未核销，自动退款
		$sqlstr="select id,client_id,out_trade_no from sys_o2order where timestampdiff(minute,concat(endtime,' 23:59:59'),NOW())>0 and payflag=2 and is_verification=2";
		$push_array=$this->get_list_bysql($sqlstr);
		if($push_array){
			foreach($push_array as $vo){
				$orderid = $vo['id'];
				$out_no_count = $this -> get_one_bysql("select count(*) from sys_out_no where o2order_id=$orderid and is_do=1");
				if($out_no_count == 0){
					$content='订单'.$vo['out_trade_no'].'已退款成功';
					$this->pub_add_systemnotice(1,1,$vo['id'],$content,$vo['client_id'],1);
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
				$shop_id = $this->get_one_bysql("select s.id from sys_o2order o left join sys_good g on o.good_id=g.id left join sys_shop s on g.shop_id=s.id where o.id=$orderid");
				$sqlstr_array[] = "insert into sys_comment set client_id=$client_id,star=5,o2order_id=$orderid,regdate='$regdate',anonymous=2,shop_id=$shop_id,good_id=$good_id";
				$sqlstr_array[] = "update sys_o2order set payflag=4 where id=$orderid and payflag=3 and client_id=$client_id";
				$shop_list = $this->get_list_bysql("select all_orders,all_star from sys_shop where id=$shop_id");
				$all_orders = $shop_list[0]['all_orders'];
				$all_star = $shop_list[0]['all_star'];
				$all_orders = $all_orders+1;
				$all_star = $all_star+5;
				$star_aver = round($all_star/$all_orders);
				$sqlstr_array[] = "update sys_shop set all_orders=$all_orders,all_star=$all_star,star=$star_aver where id=$shop_id";
				$this->do_transaction($sqlstr_array);
			}									
		}
		sys_log("push_task执行一次");//正式部署时请屏蔽，防止LOG日志文件过大	
    }
}