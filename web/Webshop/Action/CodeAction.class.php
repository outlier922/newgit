<?php

/**
 * 用户管理控制器
 */

class CodeAction extends BaseAction
{
    /**
     * 用户管理首页
     */
    public function code_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        $export=_GET('_export');
        if ($export) $_REQUEST=unserialize(base64_decode($_GET['_request']));
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $account = _REQUEST('account');
        $validflag = _REQUEST('validflag');
        $flag = _REQUEST('flag');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        $shop_id = $_SESSION['shop']['auth']['uid'];
        //基本查询
        $field_list = "mt.*,c.id as client_id,c.account,c.nickname ";
        $sql_suffix = "from sys_out_no mt ";
        $sql_suffix .= "inner join sys_o2order o on mt.o2order_id = o.id ";
        $sql_suffix .= "left join sys_good g on o.good_id = g.id ";
        $sql_suffix .= "left join sys_client c on o.client_id = c.id ";
        $sql_suffix .= "where mt.id>0 and g.shop_id=$shop_id and mt.is_do=2 ";
        $orderby_str = "mt.id desc";

        //筛选数据
        if($account) $sql_suffix .= "and (c.nickname like '%$account%' or c.account like '%$account%' or c.id like '%$account%' or mt.out_no like '%$account%') ";
        //查询数据
		$list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
        	'is_do'=>array('1'=>'已审核','2'=>'待审核','3'=>'已退款'),
        ));
        
        //UI部分
        $breadcrumb_data = '首页,用户管理,用户管理';
        $search_items = array(
        	array('name'=>'account','placeholder'=>'请输入用户ID、用户账号、用户昵称、核销码','cls'=>'w350','_parser'=>'form_item/search/input'),        
        );
        $buttons = array(   
            array('text'=>'核销','title'=>'核销','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_confirm','url'=>U('Code/code_remove'),'url_param' => array('id'=>'2_id')
            ),    
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'ID'),
            array('name'=>'client_id','cls'=>'w60','title'=>'用户ID'),
            array('name'=>'o2order_id','cls'=>'w60','title'=>'订单ID'),
            array('name'=>'account','cls'=>'w100','title'=>'用户账号'),
            array('name'=>'nickname','cls'=>'w100','title'=>'用户昵称'),
            array('name'=>'out_no','cls'=>'w150','title'=>'核销码'),
            array('name'=>'is_do_text','cls'=>'w100','title'=>'核销码状态'),
            array('name'=>'regdate','cls'=>'w150','title'=>'核销时间'),
        );

        $component_data = array(
            '_parser' => 'container/default',
            '_children' => array(
                array('_parser' => 'breadcrumb/default', 'data' => $breadcrumb_data),
                array('_parser' => 'container/content',
                    '_children' => array(
                        array('_parser' => 'form/search', 'cls' => 'text-c', 'action' => U(MODULE_NAME . '/' . ACTION_NAME),
                            '_children' => $search_items
                        ),
                        array('_parser' => 'button/top_button', '_children' => $buttons),
                        array('_parser' => 'table/datatables/thin',
                            'head' => &$table_headers,
                            'data' => &$list_items,
                        ),
                        array('_parser' => 'pagination/laypage',
                            'total_count' => $GLOBALS['totalcount'],
                            'page_count' => $GLOBALS['page_count'],
                        ),
                    )
                )
            )
        );
        //解析组件
        _display($component_data);
        
    }


    public function code_remove(){
        $id = _REQUEST('id');
        $regdate = sys_get_time();   
        $sql_arr = NULL;   
        $sql_arr[] = "update sys_out_no set is_do=1,regdate='$regdate' where id in ($id)";
        $id_arr = explode(',',$id);
        foreach($id_arr as $v){
	        $client_list = $this->get_list_bysql("select o2.client_id,o2.id,o.out_no from sys_out_no o left join sys_o2order o2 on o.o2order_id=o2.id where o.id=$v");
	        $client_id = $client_list[0]['client_id'];
	        $out_no = $client_list[0]['out_no'];
	        $orderid = $client_list[0]['id'];
	        $content = '消费码'.$out_no.'已核销';
	        $sql_arr[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$regdate',looktype=0,keytype=2,type=1,keyid=$orderid";
	        send_mess(2,$content,$client_id,1);
        }
        $result = $this->do_transaction($sql_arr);
        if($result !== false){
	        $orderid_arr = $this->get_list_bysql("select distinct o2order_id from sys_out_no where id in ($id)");
	        $client_arr = array();
	        $sql_array = NULL;
	        foreach($orderid_arr as $v){
		        $o2order_id = $v['o2order_id'];
		        $is_verification = $this->get_one_bysql("select is_verification from sys_o2order_record where order_id=$o2order_id");
		        if($is_verification == 2){
			        $sql_array[] = "update sys_o2order_record set is_verification=1 where order_id=$o2order_id";
		        }
		        $is_verification = $this->get_one_bysql("select is_verification from sys_o2order where id=$o2order_id");
		        if($is_verification == 2){
			        $sql_array[] = "update sys_o2order set is_verification=1 where id=$o2order_id";
		        }
		        $code_num = $this->get_one_bysql("select count(*) from sys_out_no where o2order_id=$o2order_id and is_do=2");
		        if($code_num == 0){
			        $sql_array[] = "update sys_o2order set payflag=3,assess_time='$regdate' where id=$o2order_id";
			        $shop_list = $this->get_list_bysql("select s.id as shop_id,s.name as shop_name,s.address,s.arrival_rate,s.wealth_rate,s.service_type,s.service_rate,s.service_fee,o.totalfee,o.client_id,o.out_trade_no from sys_o2order o left join sys_o2order_record re on o.id = re.order_id left join sys_shop s on re.shop_id = s.id where o.id=$o2order_id");
					$arrival_rate = $shop_list[0]['arrival_rate'];
					$wealth_rate = $shop_list[0]['wealth_rate'];
					$service_type = $shop_list[0]['service_type'];
					$totalfee = $shop_list[0]['totalfee'];
					$client_id = $shop_list[0]['client_id'];
					$shop_id = $shop_list[0]['shop_id'];
					$shop_name = $shop_list[0]['shop_name'];
					$out_trade_no = $shop_list[0]['out_trade_no'];
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
						$sql_array[] = "update sys_shop set feeaccount=feeaccount+$arrival_fee,wealth_redbag=wealth_redbag+$wealth_fee,redbag=redbag+$wealth_fee where id=$shop_id";				
						$sql_array[] = "update sys_client set wealth=wealth+$wealth_fee where id=$client_id";
						$sql_array[] = "insert into sys_wealth_redbag set type=1,fee=$wealth_fee,regdate='$regdate',client_id=$client_id,shop_id=$shop_id,o2order_id=$o2order_id";
						$sql_array[] = "insert into sys_income set totalfee=$totalfee,type=1,client_id=$client_id,shop_id=$shop_id,shop_name='$shop_name',arrival_fee=$arrival_fee,wealth_fee=$wealth_fee,service_fee=$service_fee,address='$address',regdate='$regdate',o2order_id=$o2order_id";
						$content = '订单'.$out_trade_no.'已核销完毕，恭喜您获得'.$wealth_fee.'成长值';
	        			$sql_array[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$regdate',looktype=0,keytype=2";
					}else{
						if($totalfee >= 1){
							$sql_array[] = "update sys_client set wealth=100,feeaccount=returnfee,islive=1 where id=$client_id";
							$client_arr[] = $client_id;
						}
						$sql_array[] = "update sys_shop set feeaccount=feeaccount+$arrival_fee where id=$shop_id";						
						$wealth_fee = 0;
						$sql_array[] = "insert into sys_income set totalfee=$totalfee,type=1,client_id=$client_id,shop_id=$shop_id,shop_name='$shop_name',arrival_fee=$arrival_fee,wealth_fee=$wealth_fee,service_fee=$service_fee,address='$address',regdate='$regdate',o2order_id=$o2order_id";
					}					
		        }
	        }
        	$result = $this->do_transaction($sql_array);
        	if($result && $client_arr){
	        	$client_arr = array_unique($client_arr);
	        	$client_id = implode(',',$client_arr);
	        	$content = "恭喜您已激活，请点击领取激活红包";
	        	ext_add_notice(1,0,$content,$client_id);
        	}
        }
		sys_out_result($result);
    }

    public function card_code_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        $export=_GET('_export');
        if ($export) $_REQUEST=unserialize(base64_decode($_GET['_request']));
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $account = _REQUEST('account');
        $validflag = _REQUEST('validflag');
        $flag = _REQUEST('flag');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        $shop_id = $_SESSION['shop']['auth']['uid'];
        //基本查询
        $field_list = "mt.*,c.id as client_id,c.account,c.nickname,o.card_id,ca.name,ca.score,ca.price ";
        $sql_suffix = "from sys_card_no mt ";
        $sql_suffix .= "inner join sys_cardorder o on mt.cardorder_id = o.id ";
        $sql_suffix .= "left join sys_card ca on o.card_id = ca.id ";
        $sql_suffix .= "left join sys_client c on o.client_id = c.id ";
        $sql_suffix .= "where mt.id>0 and mt.shop_id=$shop_id and mt.is_do=2 ";
        $orderby_str = "mt.id desc";

        //筛选数据
        if($account) $sql_suffix .= "and (c.nickname like '%$account%' or c.account like '%$account%' or c.id like '%$account%' or mt.out_no like '%$account%') ";
        //查询数据
		$list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
        	'is_do'=>array('1'=>'已审核','2'=>'待审核','3'=>'已过期'),
        ));
        
        //UI部分
        $breadcrumb_data = '首页,用户管理,用户管理';
        $search_items = array(
        	array('name'=>'account','placeholder'=>'请输入用户ID、用户账号、用户昵称、核销码','cls'=>'w350','_parser'=>'form_item/search/input'),        
        );
        $buttons = array(   
            array('text'=>'核销','title'=>'核销','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_confirm','url'=>U('Code/card_code_save'),'url_param' => array('id'=>'2_id')
            ),    
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'ID'),
            array('name'=>'client_id','cls'=>'w60','title'=>'用户ID'),
            array('name'=>'card_id','cls'=>'w60','title'=>'优惠券ID'),
            array('name'=>'name','cls'=>'w100','title'=>'优惠券标题'),
            array('name'=>'score','cls'=>'w60','title'=>'积分数量'),
            array('name'=>'price','cls'=>'w60','title'=>'优惠券金额'),
            array('name'=>'cardorder_id','cls'=>'w80','title'=>'优惠券订单ID'),
            array('name'=>'account','cls'=>'w100','title'=>'用户账号'),
            array('name'=>'nickname','cls'=>'w100','title'=>'用户昵称'),
            array('name'=>'out_no','cls'=>'w100','title'=>'核销码'),
            array('name'=>'is_do_text','cls'=>'w100','title'=>'核销码状态'),
            array('name'=>'regdate','cls'=>'w100','title'=>'核销时间'),
        );

        $component_data = array(
            '_parser' => 'container/default',
            '_children' => array(
                array('_parser' => 'breadcrumb/default', 'data' => $breadcrumb_data),
                array('_parser' => 'container/content',
                    '_children' => array(
                        array('_parser' => 'form/search', 'cls' => 'text-c', 'action' => U(MODULE_NAME . '/' . ACTION_NAME),
                            '_children' => $search_items
                        ),
                        array('_parser' => 'button/top_button', '_children' => $buttons),
                        array('_parser' => 'table/datatables/thin',
                            'head' => &$table_headers,
                            'data' => &$list_items,
                        ),
                        array('_parser' => 'pagination/laypage',
                            'total_count' => $GLOBALS['totalcount'],
                            'page_count' => $GLOBALS['page_count'],
                        ),
                    )
                )
            )
        );
        //解析组件
        _display($component_data);
        
    }   

     //上下架操作
	public function card_code_save(){
		$id = _REQUEST('id');
        $regdate = sys_get_time();   
        $sql_arr = NULL;   
        $sql_arr[] = "update sys_card_no set is_do=1,regdate='$regdate' where id in ($id) and is_do=2";
        $id_arr = explode(',',$id);
        foreach($id_arr as $v){
	        $client_list = $this->get_list_bysql("select o.client_id,o.id,c.out_no from sys_card_no c left join sys_cardorder o on c.cardorder_id=o.id where c.id=$v");
	        $client_id = $client_list[0]['client_id'];
	        $out_no = $client_list[0]['out_no'];
	        $orderid = $client_list[0]['id'];
	        $content = '券码'.$out_no.'已核销';
	        $sql_arr[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$regdate',looktype=0,keytype=2,type=3,keyid=$orderid";
	        send_mess(2,$content,$client_id,3);
        }
        $result = $this->do_transaction($sql_arr);
        sys_out_result($result);
	}
        
}