<?php

/**
 * 用户管理控制器
 */

class ClientAction extends BaseAction
{
    /**
     * 用户管理首页
     */
    public function client_list($_action_access=0){
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
        //基本查询
        $field_list = "mt.*";
        $sql_suffix = "from sys_client mt ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "mt.id desc";
        //筛选数据
        if($account) $sql_suffix .= "and (mt.nickname like '%$account%' or mt.account like '%$account%') ";
        if($validflag) $sql_suffix .= "and mt.validflag='$validflag' ";
        if($flag) $sql_suffix .= "and mt.flag='$flag' ";
        //查询数据
        if ($export) $list_items=$this->get_list_bysql("select $field_list $sql_suffix order by $orderby_str");
        else {
            $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        }
        int_to_string($list_items,array(
            'validflag'=>array('1'=>'正常','2'=>'冻结'),
            'islive'=>array('1'=>'已激活','2'=>'未激活'),
        ));

        foreach($list_items as $k=>&$v){
	        if($v['islive'] == 2){
		        $v['wealth'] = '';
	        }
	        $client_id = $v['id'];
	        $v['total_redbagfee'] = $this->get_one_bysql("select sum(score) from sys_cash where client_id=$client_id and cashflag=3");
	        if(!$v['total_redbagfee']){
		        $v['total_redbagfee'] = 0;
	        }
	        $v['total_redbagscore'] = $this->get_one_bysql("select sum(score) from sys_scoredetail where client_id=$client_id and scoretype=1 or scoretype=2");
	        if(!$v['total_redbagscore']){
		        $v['total_redbagscore'] = 0;
	        }
	        $v['total_shopscore'] = $this->get_one_bysql("select sum(score) from sys_scoredetail where client_id=$client_id and scoretype=5");
	        if(!$v['total_shopscore']){
		        $v['total_shopscore'] = 0;
	        }
	        $v['total_cashscore']  = $this->get_one_bysql("select sum(score) from sys_cash where client_id=$client_id and cashflag=2");
	        if(!$v['total_cashscore']){
		        $v['total_cashscore'] = 0;
	        }
        }
        unset($v);
        
        //UI部分
        $breadcrumb_data = '首页,用户管理,用户管理';
        $search_items = array(
        	array('name'=>'account','placeholder'=>'请输入用户账号，用户名称','cls'=>'w250','_parser'=>'form_item/search/input'),
            array('name'=>'validflag','_parser'=>'form_item/search/select','data'=>array(''=>'用户状态','1'=>'正常','2'=>'冻结'),'style'=>'width:120px;'),
            array('name'=>'flag','_parser'=>'form_item/search/select','data'=>array(''=>'用户身份','1'=>'普通','2'=>'代理商'),'style'=>'width:120px;'),          
        );
        $buttons = array(
            array('text'=>'发送消息','title'=>'发送消息','icon'=>'edit2','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_frame','url'=>U('Client/mess_add'),'url_param' => array('id'=>'2_id','push_method'=>'1_1')
            ),
            array('text'=>'修改财气值','title'=>'修改财气值','icon'=>'edit2','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_frame','url'=>U('Client/wealth_save'),'url_param' => array('id'=>'2_id','push_method'=>'1_1')
            ),
            array('position'=>1,'_after_parser'=>array('_parser'=>'button_item/position/blank','text'=>'导出Excel','title'=>'导出Excel','icon'=>'edit2',
                'target'=>'blank','full'=>0,'url'=>U(MODULE_NAME.'/'.ACTION_NAME.'?_export=1&_request='.base64_encode(serialize($_REQUEST)))),
            ),
            array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Client/client_save'),'url_param'=>array('id'=>'2_id')
            ),           
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'用户ID'),
            array('name'=>'account','cls'=>'w100','title'=>'用户账号'),
            array('name'=>'nickname','cls'=>'w100','title'=>'用户昵称'),
            array('name'=>'avatar','title'=>'头像','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_avatar','imgurlbig'=>'3_avatarbig'
            )),
            array('name'=>'','title'=>'用户积分','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'3_score','title'=>'1_积分明细','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Client/score_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'','title'=>'账户余额','cls'=>'w80','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'3_feeaccount','title'=>'1_余额明细','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Client/balance_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'total_redbagfee','cls'=>'w100','title'=>'红包现金'),
            array('name'=>'total_redbagscore','cls'=>'w100','title'=>'红包积分'),
            array('name'=>'total_shopscore','cls'=>'w100','title'=>'商城兑换积分'),
            array('name'=>'total_cashscore','cls'=>'w100','title'=>'兑换现金'),
            array('name'=>'islive_text','cls'=>'w100','title'=>'是否激活'),
            array('name'=>'','title'=>'当前财气值','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'3_wealth','title'=>'1_当前财气值','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Client/wealth_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'validflag_text','cls'=>'w100','title'=>'用户状态'),
            array('name'=>'remarks','cls'=>'w150','title'=>'备注'),
        );
        if ($export) {
            $export_headers= array(
                array('name'=>'id','cls'=>'w60','title'=>'用户ID'),
	            array('name'=>'account','cls'=>'w100','title'=>'用户账号'),
	            array('name'=>'nickname','cls'=>'w100','title'=>'用户昵称'),
	            array('name'=>'score','title'=>'用户积分'),
	            array('name'=>'feeaccount','title'=>'账户余额'),
	            array('name'=>'validflag_text','cls'=>'w50','title'=>'用户状态'),
	            array('name'=>'remarks','title'=>'备注'),
            );
            ext_export("用户列表",$export_headers,$list_items);
        }
        else {
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
            //解析组件
            _display($component_data);
        }
    }

    public function mess_add(){
        if(IS_POST){
        	$client_id = _POST('client_id');
        	$push_method = 1;//推送方法：1:推送到指定用户,2:推送到整个APP,可以扩展推送给某一类人
        	$push_content = _POST('content');
            $from_id = $_SESSION['admin']['auth']['uid'];
            ext_add_notice(1,0,$push_content,$client_id,$from_id,$push_method);
            sys_out_success();
        }
        else{
        	$client_id = _REQUEST('id');
        	$form_items = array(
        		array('name'=>'client_id','_parser'=>'form_item/form/hidden','value'=>$client_id),
                array('name'=>'content','label'=>'通知内容','placeholder'=>'最长140个字符','required'=>1,
                    '_parser'=>'form_item/form/textarea',
                    '_validation'=>array(
                        'maxlength'=>array(140,"最长140个字符")
                    ),
                ),
        	);
        	form_validation_create($form_items,$rules,$messages);//获取验证规则
        	$component_data = array('_parser'=>'form/default',
        		'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
        		'rules' => $rules,'messages' => $messages,
        	);
        	_display($component_data);
        }
    }

    public function wealth_save($_action_access=0){
	    if(IS_POST){
        	$wealth = _POST('wealth');
        	if($wealth < 100){
	        	sys_out_fail("财气值不能小于100");
        	}
        	$client_id = _POST('client_id');
        	$id_arr = explode(',',$client_id);
        	$sql_array = null;
        	foreach($id_arr as $v){
	        	$sql_array[] = "update sys_client set wealth=$wealth where id=$v and islive=1";
        	}
        	$result = $this->do_transaction($sql_array);
            sys_out_result($result);
        }
        else{
        	$client_id = _REQUEST('id');
        	$form_items = array(
        		array('name'=>'client_id','_parser'=>'form_item/form/hidden','value'=>$client_id),
                array('name'=>'wealth','label'=>'将指定用户财气值修改为','required'=>1,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    'placeholder'=>'输入用户财气值',
                    '_validation'=>array(
                        'isNumber'=>array(true,"必须是数字"),
                        //'min'=>[100, '最小值100'],
                    ),
                ),
        	);
        	form_validation_create($form_items,$rules,$messages);//获取验证规则
        	$component_data = array('_parser'=>'form/default',
        		'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
        		'rules' => $rules,'messages' => $messages,
        	);
        	_display($component_data);
        }
    }

    public function score_get($_action_access=0){
	    //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
	    //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        $id = _REQUEST('id');
        if(!$id) layer_out_fail("参数传递不正确");

        //基本查询
        $field_list = "mt.*";
        $sql_suffix = "from sys_scoredetail mt ";
        $sql_suffix .= "where mt.client_id=$id ";
        $orderby_str = "mt.id desc";
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
            'scoretype'=>array('1'=>'抽奖积分','2'=>'抽奖积分','3'=>'推广积分','4'=>'积分转余额','5'=>'积分购买'),
        ));
        
		$breadcrumb_data = '首页,用户管理,积分明细';
        $table_headers = array(
            array('name'=>'scoretype_text','cls'=>'w60','title'=>'变动原因'),
            array('name'=>'score','cls'=>'w60','title'=>'额度'),
            array('name'=>'regdate','cls'=>'w60','title'=>'时间'),
        );
        $component_data = array(
            '_parser' => 'container/default',
            '_children' => array(
                array('_parser' => 'breadcrumb/default', 'data' => $breadcrumb_data),
                array('_parser' => 'container/content',
                    '_children' => array(
                        array('_parser' => 'table/datatables/thin',
                            'head' => &$table_headers,
                            'data' => &$list_items
                        ),
                        array('_parser' => 'pagination/laypage',
                            'total_count' => $GLOBALS['totalcount'],
                            'page_count' => $GLOBALS['page_count'],
                        ),
                    )
                )
            )
        );
        _display($component_data);
    }

    public function balance_get($_action_access=0){
	    //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
	    //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        $id = _REQUEST('id');
        if(!$id) layer_out_fail("参数传递不正确");

        //基本查询
        $field_list = "mt.*";
        $sql_suffix = "from sys_cash mt ";
        $sql_suffix .= "where mt.client_id=$id ";
        $orderby_str = "mt.id desc";
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
            'cashflag'=>array('1'=>'余额提现','2'=>'积分转化','3'=>'红包抽奖','4'=>'激活红包'),
        ));
        
		$breadcrumb_data = '首页,用户管理,余额明细';
        $table_headers = array(
            array('name'=>'cashflag_text','cls'=>'w60','title'=>'变动原因'),
            array('name'=>'score','cls'=>'w60','title'=>'额度'),
            array('name'=>'regdate','cls'=>'w60','title'=>'时间'),
        );
        $component_data = array(
            '_parser' => 'container/default',
            '_children' => array(
                array('_parser' => 'breadcrumb/default', 'data' => $breadcrumb_data),
                array('_parser' => 'container/content',
                    '_children' => array(
                        array('_parser' => 'table/datatables/thin',
                            'head' => &$table_headers,
                            'data' => &$list_items
                        ),
                        array('_parser' => 'pagination/laypage',
                            'total_count' => $GLOBALS['totalcount'],
                            'page_count' => $GLOBALS['page_count'],
                        ),
                    )
                )
            )
        );
        _display($component_data);
    }


    
    public function wealth_get($_action_access=0){
	    //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
	    //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        $id = _REQUEST('id');
        if(!$id) layer_out_fail("参数传递不正确");

        //基本查询
        $field_list = "mt.*";
        $sql_suffix = "from sys_wealth mt ";
        $sql_suffix .= "where mt.client_id=$id ";
        $orderby_str = "mt.id desc";
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
            'cashflag'=>array('1'=>'线下消费','2'=>'红包抽奖'),
        ));
        
		$breadcrumb_data = '首页,用户管理,财气值明细';
        $table_headers = array(
            array('name'=>'type','cls'=>'w60','title'=>'变动原因'),
            array('name'=>'score','cls'=>'w60','title'=>'额度'),
            array('name'=>'regdate','cls'=>'w60','title'=>'时间'),
        );
        $component_data = array(
            '_parser' => 'container/default',
            '_children' => array(
                array('_parser' => 'breadcrumb/default', 'data' => $breadcrumb_data),
                array('_parser' => 'container/content',
                    '_children' => array(
                        array('_parser' => 'table/datatables/thin',
                            'head' => &$table_headers,
                            'data' => &$list_items
                        ),
                        array('_parser' => 'pagination/laypage',
                            'total_count' => $GLOBALS['totalcount'],
                            'page_count' => $GLOBALS['page_count'],
                        ),
                    )
                )
            )
        );
        _display($component_data);
    }

    //保存会员
    public function client_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
			$save_fields = array('remarks','validflag');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            
            if($id){//修改
                $sqlstr = "update sys_client set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
				array('name'=>'id','_parser'=>'form_item/form/hidden'),
				array('name'=>'validflag','label'=>'账号状态',
					'_parser'=>'form_item/collect/radio',
					'data'=>array('1'=>'正常','2'=>'冻结')
				),
				array('name'=>'remarks','label'=>'添加备注','placeholder'=>'最长140个字符','required'=>0,
                    '_parser'=>'form_item/form/textarea',
                    '_validation'=>array(
                        'maxlength'=>array(140,"最长140个字符")
                    ),
                ),
			);
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.id,mt.remarks,mt.validflag";
                $sql_suffix = "from sys_client mt ";
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

    public function advice_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $account = _REQUEST('account');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*,c.nickname,c.account";
        $sql_suffix = "from sys_advice mt left join sys_client c on c.id = mt.client_id ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "mt.id desc";
        //筛选数据
        if($account) $sql_suffix .= "and (c.nickname like '%$account%' or c.account like '%$account%') ";
        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
            'devicetype'=>array('1'=>'苹果','2'=>'安卓'),
        ));
        
        //UI部分
        $breadcrumb_data = '首页,用户管理,意见反馈';
        $search_items = array(
        	array('name'=>'account','placeholder'=>'请输入用户账号，用户名称','cls'=>'w250','_parser'=>'form_item/search/input'),         
        );
        $buttons = array(
            array('text'=>'删除','title'=>'删除','icon'=>'del','full'=>1,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Client/advice_remove'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'批量删除','title'=>'批量删除','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_confirm','url'=>U('Client/advice_remove'),'url_param' => array('id'=>'2_id')
            ),          
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'ID'),           
            array('name'=>'nickname','cls'=>'w100','title'=>'用户昵称'),
            array('name'=>'devicetype_text','cls'=>'w80','title'=>'来源'),
            array('name'=>'lastloginversion','cls'=>'w100','title'=>'版本号'),
            array('name'=>'phonetype','cls'=>'w100','title'=>'手机品牌'),
            array('name'=>'phoneversion','cls'=>'w60','title'=>'系统型号'),
            array('name'=>'advice','cls'=>'w150','title'=>'意见内容'),
            array('name'=>'adv_time','cls'=>'w100','title'=>'反馈时间'),
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
        //解析组件
        _display($component_data);
        
    }

    public function advice_remove(){
        $id = _REQUEST('id');
        $sqlstr = "delete from sys_advice where id in ($id)";
        $result = $this -> do_execute($sqlstr);
        sys_out_result($result);
    }
    
        
}