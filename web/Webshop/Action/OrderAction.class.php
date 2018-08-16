<?php//管理员相关class OrderAction extends BaseAction{    public function order_list($_action_access=0){        //访问控制变量        $GLOBALS['_action_access'] = $_action_access;        $export=_GET('_export');        if ($export) $_REQUEST=unserialize(base64_decode($_GET['_request']));        //声明变量        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;        $keyid = $_POST['keyid'];        $payflag = $_POST['payflag'];        if(_POST('search-flag')){            unset($_POST['page']);            unset($_GET['page']);        }        $shop_id = $_SESSION['shop']['auth']['uid'];        //基本查询        $field_list = "mt.*,c.nickname ";        $sql_suffix = "from sys_o2order mt ";        $sql_suffix .= "left join sys_client c on mt.client_id=c.id ";        $sql_suffix .= "left join sys_good g on mt.good_id=g.id ";        $sql_suffix .= "where g.shop_id=$shop_id ";        $orderby_str = "mt.id desc";        //筛选数据        if($keyid){            $sql_suffix .= "and (g.name like '%$keyid%' or mt.out_trade_no like '%$keyid%') ";        }        //时间段        $time_start=_REQUEST('time_start');        if ($time_start) $sql_suffix.=" and mt.regdate>='$time_start'";        $time_end=_REQUEST('time_end');        if ($time_end) $sql_suffix.=" and mt.regdate<='$time_end'";		if ($payflag) $sql_suffix .= " and mt.payflag=$payflag";        if ($export) $list_items=$this->get_list_bysql("select $field_list $sql_suffix order by $orderby_str");        else {            $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);        }        int_to_string($list_items,array(            'payflag'=>array('1'=>'待付款','2'=>'待使用','3'=>'待评价','4'=>'已完成','5'=>'已退款'),        ));        //UI部分        $breadcrumb_data = '首页,商家管理,订单管理';        $search_items = array(            array('name'=>'keyid','placeholder'=>'请输入商品名称或订单号','cls'=>'w250','_parser'=>'form_item/search/input'),            array('name'=>'time','label'=>'下单时间','_parser'=>'form_item/search/date'),          	array('name'=>'payflag','_parser'=>'form_item/search/select','data'=>array(''=>'订单状态','1'=>'待付款','2'=>'待使用','3'=>'待评价','4'=>'已完成','5'=>'已退款'),'style'=>'width:120px;'),        );        $buttons = array(             array('text'=>'备注','title'=>'备注','full'=>0,'checked'=>1,'position'=>2,                'target'=>'inner_frame','url'=>U('Order/remarks_save'),'url_param'=>array('id'=>'2_id')            ),              array('position'=>1,'_after_parser'=>array('_parser'=>'button_item/position/blank','text'=>'导出Excel','title'=>'导出Excel','icon'=>'edit2',                'target'=>'blank','full'=>0,'url'=>U(MODULE_NAME.'/'.ACTION_NAME.'?_export=1&_request='.base64_encode(serialize($_REQUEST)))),            ),        );        $table_headers = array(            array('name'=>'id','title'=>'ID'),            array('name'=>'out_trade_no','title'=>'编号'),            array('name'=>'payflag_text','title'=>'状态'),            array('name'=>'totalfee','title'=>'金额'),            array('name'=>'good_name','title'=>'订单内容'),            array('name'=>'nickname','title'=>'下单人昵称'),            array('name'=>'regdate','title'=>'下单时间'),            ['name'=>'avatar','title'=>'评价图片集','_after_parser'=>[                '_parser'=>'button_item/td_a_get','text'=>'1_查看','title'=>'1_评价图片集','full'=>0,                'target'=>'inner_frame',                'url'=>MODULE_NAME.'/img_list','url_param'=>['keytype'=>'1_3','keyid'=>'3_id']            ]],            array('name'=>'','title'=>'用户评价','cls'=>'w60','_after_parser'=>array(                '_parser'=>'button_item/td_a_get','text'=>'1_查看','title'=>'1_查看评价','full'=>0,                'target'=>'inner_frame',                'url'=>'Order/order_save','url_param'=>array('id'=>'3_id')            )),            array('name'=>'remarks','title'=>'备注')        );        if ($export) {            $export_headers= array(                array('name'=>'id','title'=>'ID'),	            array('name'=>'out_trade_no','title'=>'编号'),	            array('name'=>'payflag_text','title'=>'状态'),	            array('name'=>'totalfee','title'=>'金额'),	            array('name'=>'good_name','title'=>'订单内容'),	            array('name'=>'nickname','title'=>'下单人昵称'),	            array('name'=>'regdate','title'=>'下单时间'),	            array('name'=>'remarks','title'=>'备注')            );            ext_export("商城订单列表",$export_headers,$list_items);        } else {	        $component_data = array(	            '_parser'=>'container/default',	            '_children'=>array(	                array('_parser'=>'breadcrumb/default','data'=>$breadcrumb_data),	                array('_parser'=>'container/content',	                    '_children'=>array(	                        array('_parser'=>'form/search','cls'=>'text-c','action'=>U(MODULE_NAME.'/'.ACTION_NAME),	                            '_children'=>$search_items	                        ),	                        array('_parser'=>'button/top_button','_children'=>$buttons),	                        array('_parser'=>'table/datatables/thin',	                            'head'=>&$table_headers,	                            'data'=>&$list_items,	                            'row_button'=>array('_parser'=>'button/row_dropdown','_children'=>$buttons)	                        ),	                        array('_parser'=>'pagination/laypage',	                            'total_count'=>$GLOBALS['totalcount'],	                            'page_count'=>$GLOBALS['page_count'],	                        ),	                    )	                )	            )	        );    	}        //解析组件        _display($component_data);    }    public function order_save(){        if(IS_POST){            $id = _POST('id');            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑            //获取字段            $save_fields = array('reply');            $post_keys = array_keys($_POST);            $post_fields = array_intersect($save_fields,$post_keys);//取公共            $fields_str = fields2SqlStrByPost($post_fields);            if($id){//修改                $sqlstr = "update sys_comment set $fields_str where id=$id";                $result = $this -> do_execute($sqlstr);            }            sys_out_result($result);        }        else{            $id = _REQUEST('id');            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑            $form_items = array(                array('name'=>'id','_parser'=>'form_item/form/hidden'),                array('_parser'=>'tab/default','_children'=>array(                    array('label'=>'基本信息','_parser'=>'container/default','_children'=>array(                        array('name'=>'content','label'=>'评价内容','readonly'=>2,                            '_parser'=>'form_item/form/textarea','required'=>0,                        ),                        array('name'=>'reply','label'=>'添加回复','placeholder'=>'最长60个字符',                            '_parser'=>'form_item/form/textarea','required'=>0,                            '_validation'=>array(                                'maxlength'=>array(60,"最长60个字符")                            ),                        ),                    ))                )),            );            form_validation_create($form_items,$rules,$messages);//获取验证规则            if($id) {                //取值                $field_list = "mt.*";                $sql_suffix = "from sys_comment mt ";                $sql_suffix .= "where mt.o2order_id=$id ";                $temp_array = $this->get_list_bysql("select $field_list $sql_suffix");                form_item_add_value($form_items,$temp_array[0]);//赋值            }            $component_data = array('_parser'=>'form/default',                'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,                'rules' => $rules,'messages' => $messages,            );            _display($component_data);        }    }    public function remarks_save(){        if(IS_POST){            $id = _POST('id');            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑            //获取字段            $save_fields = array('remarks');            $post_keys = array_keys($_POST);            $post_fields = array_intersect($save_fields,$post_keys);//取公共            $fields_str = fields2SqlStrByPost($post_fields);            if($id){//修改                $sqlstr = "update sys_o2order set $fields_str where id=$id";                $result = $this -> do_execute($sqlstr);            }            sys_out_result($result);        }        else{            $id = _REQUEST('id');            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑            $form_items = array(                array('name'=>'id','_parser'=>'form_item/form/hidden'),                array('_parser'=>'tab/default','_children'=>array(                    array('label'=>'基本信息','_parser'=>'container/default','_children'=>array(                        array('name'=>'remarks','label'=>'备注','placeholder'=>'最长140个字符',                            '_parser'=>'form_item/form/textarea','required'=>0,                            '_validation'=>array(                                'maxlength'=>array(140,"最长140个字符")                            ),                        ),                    ))                )),            );            form_validation_create($form_items,$rules,$messages);//获取验证规则            if($id) {                //取值                $field_list = "mt.*";                $sql_suffix = "from sys_o2order mt ";                $sql_suffix .= "where mt.id=$id ";                $temp_array = $this->get_list_bysql("select $field_list $sql_suffix");                form_item_add_value($form_items,$temp_array[0]);//赋值            }            $component_data = array('_parser'=>'form/default',                'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,                'rules' => $rules,'messages' => $messages,            );            _display($component_data);        }    }    public function sysorder_list($_action_access=0){        //访问控制变量        $GLOBALS['_action_access'] = $_action_access;        $export=_GET('_export');        if ($export) $_REQUEST=unserialize(base64_decode($_GET['_request']));        //声明变量        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;        $keyid = $_POST['keyid'];        $payflag = $_POST['payflag'];        if(_POST('search-flag')){            unset($_POST['page']);            unset($_GET['page']);        }        $shop_id = $_SESSION['shop']['auth']['uid'];        //基本查询        $field_list = "mt.*,c.nickname ";        $sql_suffix = "from sys_sysorder mt ";        $sql_suffix .= "left join sys_client c on mt.client_id=c.id ";        $sql_suffix .= "where mt.shop_id=$shop_id ";        $orderby_str = "mt.id desc";        //筛选数据        if($keyid){            $sql_suffix .= "and mt.out_trade_no like '%$keyid%' ";        }        //时间段        $time_start=_REQUEST('time_start');        if ($time_start) $sql_suffix.=" and mt.paytime>='$time_start'";        $time_end=_REQUEST('time_end');        if ($time_end) $sql_suffix.=" and mt.paytime<='$time_end'";		if ($payflag) $sql_suffix .= " and mt.payflag=$payflag";        if ($export) $list_items=$this->get_list_bysql("select $field_list $sql_suffix order by $orderby_str");        else {            $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);        }        int_to_string($list_items,array(            'payflag'=>array('1'=>'支付成功','2'=>'未支付'),            'paytype'=>array('1'=>'支付宝','2'=>'银联','3'=>'微信','4'=>'余额支付'),        ));        //UI部分        $breadcrumb_data = '首页,商家管理,订单管理';        $search_items = array(            array('name'=>'keyid','placeholder'=>'请输入订单号','cls'=>'w250','_parser'=>'form_item/search/input'),            array('name'=>'time','label'=>'支付时间','_parser'=>'form_item/search/date'),          	array('name'=>'payflag','_parser'=>'form_item/search/select','data'=>array(''=>'订单状态','1'=>'支付成功','2'=>'未支付'),'style'=>'width:120px;'),        );        $buttons = array(             array('position'=>1,'_after_parser'=>array('_parser'=>'button_item/position/blank','text'=>'导出Excel','title'=>'导出Excel','icon'=>'edit2',                'target'=>'blank','full'=>0,'url'=>U(MODULE_NAME.'/'.ACTION_NAME.'?_export=1&_request='.base64_encode(serialize($_REQUEST)))),            ),        );        $table_headers = array(            array('name'=>'id','title'=>'ID'),            array('name'=>'out_trade_no','title'=>'编号'),            array('name'=>'payflag_text','title'=>'状态'),            array('name'=>'totalfee','title'=>'金额'),            array('name'=>'nickname','title'=>'下单人昵称'),            array('name'=>'paytime','title'=>'支付时间'),            array('name'=>'paytype_text','title'=>'支付方式'),        );        if ($export) {            ext_export("扫码订单列表",$export_headers,$list_items);        } else {	        $component_data = array(	            '_parser'=>'container/default',	            '_children'=>array(	                array('_parser'=>'breadcrumb/default','data'=>$breadcrumb_data),	                array('_parser'=>'container/content',	                    '_children'=>array(	                        array('_parser'=>'form/search','cls'=>'text-c','action'=>U(MODULE_NAME.'/'.ACTION_NAME),	                            '_children'=>$search_items	                        ),	                        array('_parser'=>'button/top_button','_children'=>$buttons),	                        array('_parser'=>'table/datatables/thin',	                            'head'=>&$table_headers,	                            'data'=>&$list_items,	                            'row_button'=>array('_parser'=>'button/row_dropdown','_children'=>$buttons)	                        ),	                        array('_parser'=>'pagination/laypage',	                            'total_count'=>$GLOBALS['totalcount'],	                            'page_count'=>$GLOBALS['page_count'],	                        ),	                    )	                )	            )	        );    	}        //解析组件        _display($component_data);    }}