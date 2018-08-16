<?php

/**
 * 用户管理控制器
 */

class IncomeAction extends BaseAction
{
	public function cash_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        $export=_GET('_export');
        if ($export) $_REQUEST=unserialize(base64_decode($_GET['_request']));
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $account = _REQUEST('account');
        $flag = _REQUEST('flag');
        $keytype = _REQUEST('keytype');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*,s.username,c.account,c.nickname,s.name ";
        $sql_suffix = "from sys_allcash mt ";
        $sql_suffix .= "left join sys_client c on mt.client_id=c.id ";
        $sql_suffix .= "left join sys_shop s on mt.shop_id=s.id ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "mt.id desc";
        
        //时间段
        $time_start=_REQUEST('time_start');
        if ($time_start) $sql_suffix.=" and mt.regdate>='$time_start'";
        $time_end=_REQUEST('time_end');
        if ($time_end){
			$time_end = date("Y-m-d", strtotime("+1 day" , strtotime($time_end)));
			$sql_suffix.=" and mt.regdate<='$time_end'";
	    }
        $dotime_start=_REQUEST('dotime_start');
        if ($dotime_start) $sql_suffix.=" and mt.dotime>='$dotime_start'";
        $dotime_end=_REQUEST('dotime_end');
        if ($dotime_end){
			$dotime_end = date("Y-m-d", strtotime("+1 day" , strtotime($dotime_end)));
			$sql_suffix.=" and mt.dotime<='$dotime_end'";
	    }
        if($account) $sql_suffix .= "and (c.nickname like '%$account%' or c.account like '%$account%' or mt.client_id like '%$account%' or mt.shop_id like '%$account%' or s.username like '%$account%' or s.name like '%$account%') ";
		if($flag) $sql_suffix .= " and mt.flag=$flag";
		if($keytype) $sql_suffix .= " and mt.keytype=$keytype";
        //查询数据
        if ($export) $list_items=$this->get_list_bysql("select $field_list $sql_suffix order by $orderby_str");
        else {
            $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        }
		foreach($list_items as $k=>&$v){
			if($v['keytype'] == 1){
				$about_id = $v['client_id'];
				$account = $this->get_one_bysql("select account from sys_client where id=$about_id");
			}else{
				$about_id = $v['shop_id'];
				$account = $this->get_one_bysql("select username from sys_shop where id=$about_id");
			}
			$v['account'] = $account;
		}
		unset($v);
        int_to_string($list_items,array(
        	'type'=>array('1'=>'支付宝','2'=>'银行卡'),
        	'flag'=>array('1'=>'待处理','2'=>'已处理','3'=>'已退回'),
        ));
        
        //UI部分
        $breadcrumb_data = '首页,财务管理,提现管理';
        $search_items = array(
        	array('name'=>'account','placeholder'=>'请输入用户或商家ID、账号、昵称','cls'=>'w250','_parser'=>'form_item/search/input'),      
        	array('name'=>'time','label'=>'申请时间','_parser'=>'form_item/search/date'),  
        	array('name'=>'dotime','label'=>'处理时间','_parser'=>'form_item/search/date'),  
        	array('name'=>'flag','_parser'=>'form_item/search/select','data'=>array(''=>'处理状态','1'=>'待处理','2'=>'已处理','3'=>'已退回'),'style'=>'width:120px;'),
        	array('name'=>'keytype','_parser'=>'form_item/search/select','data'=>array(''=>'提现身份','1'=>'用户提现','2'=>'商家提现'),'style'=>'width:120px;'),
        );
        $buttons = array(   
            array('text'=>'处理','title'=>'处理','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Income/cash_save'),'url_param'=>array('id'=>'2_id')
            ),  
            array('position'=>1,'_after_parser'=>array('_parser'=>'button_item/position/blank','text'=>'导出Excel','title'=>'导出Excel','icon'=>'edit2',
                'target'=>'blank','full'=>0,'url'=>U(MODULE_NAME.'/'.ACTION_NAME.'?_export=1&_request='.base64_encode(serialize($_REQUEST)))),
            ),
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'ID'),
            array('name'=>'client_id','cls'=>'w60','title'=>'用户ID'),
            array('name'=>'shop_id','cls'=>'w60','title'=>'商家ID'),
            array('name'=>'account','cls'=>'w100','title'=>'用户账号'),
            array('name'=>'type_text','cls'=>'w100','title'=>'提现方式'),
            array('name'=>'alipay_account','cls'=>'w120','title'=>'支付宝账号'),
            array('name'=>'bankcard_name','cls'=>'w60','title'=>'户名'),
            array('name'=>'bank','cls'=>'w60','title'=>'银行名称'),
            array('name'=>'bankcard','cls'=>'w150','title'=>'卡号'),
            array('name'=>'score','cls'=>'w60','title'=>'提现金额'),
            array('name'=>'flag_text','cls'=>'w60','title'=>'状态'),
            array('name'=>'regdate','cls'=>'w160','title'=>'申请时间'),
            array('name'=>'dotime','cls'=>'w160','title'=>'处理时间'),
            array('name'=>'remarks','cls'=>'w150','title'=>'备注'),
        );

        if ($export) {
            $export_headers= array(
                array('name'=>'id','cls'=>'w60','title'=>'ID'),
	            array('name'=>'client_id','cls'=>'w60','title'=>'用户ID'),
	            array('name'=>'shop_id','cls'=>'w60','title'=>'商家ID'),
	            array('name'=>'account','cls'=>'w100','title'=>'用户账号'),
	            array('name'=>'type_text','cls'=>'w100','title'=>'提现方式'),
	            array('name'=>'alipay_account','cls'=>'w120','title'=>'支付宝账号'),
	            array('name'=>'bankcard_name','cls'=>'w60','title'=>'户名'),
	            array('name'=>'bank','cls'=>'w60','title'=>'银行名称'),
	            array('name'=>'bankcard','cls'=>'w150','title'=>'卡号'),
	            array('name'=>'score','cls'=>'w60','title'=>'提现金额'),
	            array('name'=>'flag_text','cls'=>'w60','title'=>'状态'),
	            array('name'=>'regdate','cls'=>'w160','title'=>'申请时间'),
	            array('name'=>'dotime','cls'=>'w160','title'=>'处理时间'),
	            array('name'=>'remarks','cls'=>'w150','title'=>'备注'),
            );
            ext_export("提现列表",$export_headers,$list_items);
        } else {
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
	                            'row_button' => array('_parser' => 'button/row_dropdown', '_children' => $buttons)
	                        ),
	                        array('_parser' => 'pagination/laypage',
	                            'total_count' => $GLOBALS['totalcount'],
	                            'page_count' => $GLOBALS['page_count'],
	                        ),
	                    )
	                )
	            )
	        );
    	}
        //解析组件
        _display($component_data);
        
    }


    public function cash_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            //获取字段
            $save_fields = array('flag','remarks');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            $dotime = sys_get_time();
			$fields_str .= ",dotime='$dotime'";
			$flag = $_POST['flag'];
			$keytype = $_POST['keytype'];
			$sqlstr_array = NULL;
			$cash_list = $this->get_list_bysql("select cash_id,score,client_id,shop_id from sys_allcash where id=$id");
			$client_id = $cash_list[0]['client_id'];
			$shop_id = $cash_list[0]['shop_id'];
			$score = $cash_list[0]['score'];	
			if($flag == 3){	
				if($keytype == 1){
					$cash_id = $cash_list[0]['cash_id'];		
					$sqlstr_array[] = "delete from sys_cash where id=$cash_id";
					$sqlstr_array[] = "update sys_client set feeaccount=feeaccount+$score where id='$client_id'";
					//发送退款成功通知
					$content = '提现失败，请联系管理员';
					send_mess(1,$content,$client_id);
					//写入通知列表
					$sqlstr_array[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$dotime',looktype=0,keytype=1";
				}else{
					$sqlstr_array[] = "update sys_shop set feeaccount=feeaccount+$score where id='$shop_id'";
				}
				
			}else if($flag == 2){
				if($keytype == 1){
					//发送退款成功通知
					$content = '恭喜您，提现成功';
					send_mess(1,$content,$client_id);
					//写入通知列表
					$sqlstr_array[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$dotime',looktype=0,keytype=1";
					$sqlstr_array[] = "insert into sys_income set totalfee=$score,type=2,client_id=$client_id,regdate='$dotime'";
				}else{
					$shop_list = $this->get_list_bysql("select * from sys_shop where id=$shop_id");
					$shop_name = $shop_list[0]['name'];
					$address = $shop_list[0]['address'];
					$sqlstr_array[] = "insert into sys_income set totalfee=$score,type=3,shop_id=$shop_id,shop_name='$shop_name',address='$address',regdate='$dotime'";
				}
				
			}
			
            if($id){//修改
                $sqlstr_array[] = "update sys_allcash set $fields_str where id=$id";
                $result = $this->do_transaction($sqlstr_array);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
                array('name'=>'id','_parser'=>'form_item/form/hidden'),
                array('name'=>'keytype','_parser'=>'form_item/form/hidden'),
                array('_parser'=>'tab/default','_children'=>array(
                    array('label'=>'基本信息','_parser'=>'container/default','_children'=>array(
                        array('name'=>'flag','label'=>'状态：','_parser'=>'form_item/collect/radio','data'=>array('1'=>'待处理','2'=>'已处理','3'=>'已退回'),'default'=>'2'),
                        array('name'=>'remarks','label'=>'备注','placeholder'=>'最长100个字符',
                            '_parser'=>'form_item/form/textarea','required'=>0,
                            '_validation'=>array(
                                'maxlength'=>array(100,"最长100个字符")
                            ),
                        ),
                    ))
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_allcash mt ";
                $sql_suffix .= "where mt.id=$id ";
                $temp_array = $this->get_list_bysql("select $field_list $sql_suffix");
                form_item_add_value($form_items,$temp_array[0]);//赋值
            }
            $component_data = array('_parser'=>'form/default',
                'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
                'rules' => $rules,'messages' => $messages,
            );
            _display($component_data);
        }
    }

    public function income_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        $export=_GET('_export');
        if ($export) $_REQUEST=unserialize(base64_decode($_GET['_request']));
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $account = _REQUEST('account');
        $type = _REQUEST('type');
        $district_1_id = _REQUEST('district_1_id');
        $district_2_id = _REQUEST('district_2_id');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*,s.redbag ";
        $sql_suffix = "from sys_income mt ";
        $sql_suffix .= "left join sys_shop s on mt.shop_id=s.id ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "mt.id desc";
        
        //时间段
        $time_start=_REQUEST('time_start');
        if ($time_start) $sql_suffix.=" and mt.regdate>='$time_start'";
        $time_end=_REQUEST('time_end');
        if ($time_end) $sql_suffix.=" and mt.regdate<='$time_end'";
        if($account) $sql_suffix .= "and (mt.id like '%$account%' or mt.client_id like '%$account%' or mt.shop_id like '%$account%' or mt.shop_name like '%$account%') ";
		if($type) $sql_suffix .= " and mt.type=$type";
		if($district_1_id != 0 ){
			$sql_suffix .= " and s.district_1_id=$district_1_id";
		}
		if($district_2_id != 0 ){
			$sql_suffix .= " and s.district_2_id=$district_2_id";
		}
        //查询数据
        if ($export) $list_items=$this->get_list_bysql("select $field_list $sql_suffix order by $orderby_str");
        else {
            $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
            $list_item=$this->get_list_bysql("select $field_list $sql_suffix order by $orderby_str");
        }
        $alltotalfee = 0;
        $allarrival_fee = 0;
        $allwealth_fee = 0;
        $allservice_fee = 0;
        $allredbag = 0;
        $total_redbagfee = $this->get_one_bysql("select sum(score) from sys_cash where cashflag=3");
        $total_redbagscore = $this->get_one_bysql("select sum(score) from sys_scoredetail where scoretype=1 or scoretype=2");
        $total_shopscore = $this->get_one_bysql("select sum(score) from sys_scoredetail where scoretype=5");
        $total_cashscore  = $this->get_one_bysql("select sum(score) from sys_cash where cashflag=2");
		foreach($list_item as $v){
			$totalfee = $v['totalfee'];
			$arrival_fee = $v['arrival_fee'];
			$wealth_fee = $v['wealth_fee'];
			$service_fee = $v['service_fee'];
			$redbag = $v['redbag'];
			$type = $v['type'];
			if($type == 1 || $type == 4){
				$alltotalfee = $alltotalfee + $totalfee;
			}//else{
			//	$alltotalfee = $alltotalfee - $totalfee;
			//}
			if($arrival_fee){
				$allarrival_fee = $allarrival_fee + $arrival_fee;
			}
			if($wealth_fee){
				$allwealth_fee = $allwealth_fee + $wealth_fee;
			}
			if($service_fee){
				$allservice_fee = $allservice_fee + $service_fee;
			}
			if($redbag){
				$allredbag = $allredbag + $redbag;
			}
		}
		
        int_to_string($list_items,array(
        	'type'=>array('1'=>'用户消费','2'=>'用户提现','3'=>'商家提现','4'=>'扫码支付'),
        ));
        
        //UI部分
        $breadcrumb_data = '首页,财务管理,财务管理';
        $search_items = array(
        	array('name'=>'account','placeholder'=>'请输入财务记录ID、用户ID、商家ID、商家名称','cls'=>'w300','_parser'=>'form_item/search/input'),      
        	array('name'=>'time','label'=>'记录时间','_parser'=>'form_item/search/date'),  
        	array('name'=>'type','_parser'=>'form_item/search/select','data'=>array(''=>'记录类型','1'=>'用户消费','2'=>'用户提现','3'=>'商家提现','4'=>'扫码支付'),'style'=>'width:120px;'),
        	['name'=>'cascade_1','label'=>'选择分类',
                '_parser'=>'form_item/search/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,
                'related'=>[
                    ['name'=>'district_1_id','label'=>'省'],
                    ['name'=>'district_2_id','label'=>'市'],
                ],
                'init_url'=>U(MODULE_NAME.'/district_list'),
            ],
        );
        $buttons = array(   
            array('text'=>'备注','title'=>'备注','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Income/income_save'),'url_param'=>array('id'=>'2_id')
            ),  
            array('position'=>1,'_after_parser'=>array('_parser'=>'button_item/position/blank','text'=>'导出Excel','title'=>'导出Excel','icon'=>'edit2',
                'target'=>'blank','full'=>0,'url'=>U(MODULE_NAME.'/'.ACTION_NAME.'?_export=1&_request='.base64_encode(serialize($_REQUEST)))),
            ),
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'ID'),
            array('name'=>'totalfee','cls'=>'w60','title'=>'金额'),
            array('name'=>'type_text','cls'=>'w60','title'=>'类型'),
            array('name'=>'client_id','cls'=>'w60','title'=>'用户ID'),
            array('name'=>'shop_id','cls'=>'w60','title'=>'商家ID'),
            array('name'=>'shop_name','cls'=>'w70','title'=>'商家名称'),
            array('name'=>'wealth_fee','cls'=>'w80','title'=>'用户财气值'),
            array('name'=>'service_fee','cls'=>'w80','title'=>'平台入账金额'),
            array('name'=>'arrival_fee','cls'=>'w110','title'=>'商家入账金额'),
            array('name'=>'address','cls'=>'w180','title'=>'商家地址'),
            array('name'=>'regdate','cls'=>'w150','title'=>'记录时间'),
            array('name'=>'remarks','cls'=>'w120','title'=>'备注'),
        );

        if ($export) {
            $export_headers= array(
                array('name'=>'id','cls'=>'w60','title'=>'ID'),
	            array('name'=>'totalfee','cls'=>'w60','title'=>'金额'),
	            array('name'=>'type_text','cls'=>'w60','title'=>'类型'),
	            array('name'=>'client_id','cls'=>'w60','title'=>'用户ID'),
	            array('name'=>'shop_id','cls'=>'w60','title'=>'商家ID'),
	            array('name'=>'shop_name','cls'=>'w100','title'=>'商家名称'),
	            array('name'=>'wealth_fee','cls'=>'w100','title'=>'用户财气值'),
	            array('name'=>'service_fee','cls'=>'w120','title'=>'平台入账金额'),
	            array('name'=>'arrival_fee','cls'=>'w120','title'=>'商家红包池入账金额'),
	            array('name'=>'address','cls'=>'w60','title'=>'商家地址'),
	            array('name'=>'regdate','cls'=>'w160','title'=>'记录时间'),
	            array('name'=>'remarks','cls'=>'w150','title'=>'备注'),
            );
            ext_export("财务列表",$export_headers,$list_items);
        } else {
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
	                            'row_button' => array('_parser' => 'button/row_dropdown', '_children' => $buttons)
	                        ),
	                        array('_parser' => 'pagination/laypage',
	                            'total_count' => $GLOBALS['totalcount'],
	                            'page_count' => $GLOBALS['page_count'],
	                            'alltotalfee' => $alltotalfee,
	                            'allarrival_fee' => $allarrival_fee,
	                            'allwealth_fee' => $allwealth_fee,
	                            'allservice_fee' => $allservice_fee,
	                            'allredbag' => $allredbag,
	                            'total_redbagfee' => $total_redbagfee,
	                            'total_redbagscore' => $total_redbagscore,
	                            'total_shopscore' => $total_shopscore,
	                            'total_cashscore' => $total_cashscore,
	                        ),
	                    )
	                )
	            )
	        );
    	}
        //解析组件
        _display($component_data);
        
    }


    // 分级获取课程分类
    public function district_list(){
        $parentid = _REQUEST('parentid');
        $parentid = $parentid ? $parentid : 0;
        $sqlstr = "select id,name from sys_cascade_district where parentid=$parentid";
        $temp_array = $this -> get_list_bysql($sqlstr);
        sys_out_success(0,$temp_array);
    }


    public function income_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            //获取字段
            $save_fields = array('remarks');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            if($id){//修改
                $sqlstr = "update sys_income set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
                array('name'=>'id','_parser'=>'form_item/form/hidden'),
                array('_parser'=>'tab/default','_children'=>array(
                    array('label'=>'基本信息','_parser'=>'container/default','_children'=>array(
                        array('name'=>'remarks','label'=>'备注','placeholder'=>'最长140个字符',
                            '_parser'=>'form_item/form/textarea','required'=>0,
                            '_validation'=>array(
                                'maxlength'=>array(140,"最长140个字符")
                            ),
                        ),
                    ))
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_income mt ";
                $sql_suffix .= "where mt.id=$id ";
                $temp_array = $this->get_list_bysql("select $field_list $sql_suffix");
                form_item_add_value($form_items,$temp_array[0]);//赋值
            }
            $component_data = array('_parser'=>'form/default',
                'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
                'rules' => $rules,'messages' => $messages,
            );
            _display($component_data);
        }
    }

}