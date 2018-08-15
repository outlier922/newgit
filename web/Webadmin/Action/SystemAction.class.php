<?php
class SystemAction extends BaseAction{
    //系统配置
    public function all_set(){
        if(IS_POST){
            $save_fields = array('aboutus','protocal','rule','disclaimer','function_intr','prize_name','score_rate','max_cost','invite_score','sharing_points','phone');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            $sqlstr = "update sys_config set $fields_str where id=1";
            $result = $this -> do_execute($sqlstr);
            sys_out_result($result);
        }
        else{
            $form_items = array(
                array('name'=>'id','_parser'=>'form_item/form/hidden'),
                array('_parser'=>'tab/default','_children'=>array(
                    array('label'=>'关于我们','_parser'=>'container/default','_children'=>array(
                        array('name'=>'aboutus','label'=>'关于我们',
                            '_parser'=>'form_item/form/rich','required'=>0,
                        ),
                    )),
                    array('label'=>'注册协议','_parser'=>'container/default','_children'=>array(
                        array('name'=>'protocal','label'=>'注册协议',
                            '_parser'=>'form_item/form/rich','required'=>0,
                        ),
                    )),
                    array('label'=>'服务条款','_parser'=>'container/default','_children'=>array(
                        array('name'=>'rule','label'=>'服务条款',
                            '_parser'=>'form_item/form/rich','required'=>0,
                        ),
                    )),
                    array('label'=>'免责声明','_parser'=>'container/default','_children'=>array(
                        array('name'=>'disclaimer','label'=>'免责声明',
                            '_parser'=>'form_item/form/rich','required'=>0,
                        ),
                    )),
                    array('label'=>'功能介绍','_parser'=>'container/default','_children'=>array(
                        array('name'=>'function_intr','label'=>'功能介绍',
                            '_parser'=>'form_item/form/rich','required'=>0,
                        ),
                    )),
                    array('label'=>'奖品名称设置','_parser'=>'container/default','_children'=>array(
                        array('name'=>'prize_name','label'=>'奖品名称设置',
                            '_parser'=>'form_item/form/input','type'=>'text','placeholder'=>'必填字段'
                        ),
                    )),
                    array('name'=>'score_rate','label'=>'积分兑换比例设置','required'=>1,
                        '_parser'=>'form_item/form/input','type'=>'text',
                        '_validation'=>array(
                            'isNumber'=>array(true,"必须是数字")
                        ),
                    ),
                    array('name'=>'max_cost','label'=>'最多可兑换余额','required'=>1,
                        '_parser'=>'form_item/form/input','type'=>'text',
                        '_validation'=>array(
                            'isNumber'=>array(true,"必须是数字")
                        ),
                    ),
                    array('name'=>'invite_score','label'=>'邀请奖励积分','required'=>1,
                        '_parser'=>'form_item/form/input','type'=>'text',
                        '_validation'=>array(
                            'isNumber'=>array(true,"必须是数字")
                        ),
                    ),
                    array('name'=>'sharing_points','label'=>'分享积分','required'=>1,
                        '_parser'=>'form_item/form/input','type'=>'text',
                        '_validation'=>array(
                            'isNumber'=>array(true,"必须是数字")
                        ),
                    ),
                    array('name'=>'phone','label'=>'举报电话','required'=>1,
                        '_parser'=>'form_item/form/input','type'=>'text',
                    ),
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            $field_list = "mt.*";
            $sql_suffix = "from sys_config mt ";
            $sql_suffix .= "where mt.id=1 ";
            $temp_array = $this->get_list_bysql("select $field_list $sql_suffix");
            form_item_add_value($form_items,$temp_array[0]);//赋值
            $component_data = array('_parser'=>'form/default',
                'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
                'rules' => $rules,'messages' => $messages,
            );
            _display($component_data);
        }
    }


	public function opencity_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $name = _REQUEST('name');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.* ";
        $sql_suffix = "from sys_opencity mt ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "";

        //筛选数据
        if($name) $sql_suffix .= "and (mt.province like '%$name%' or mt.city like '%$name%') ";
        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
        ));
        
        //UI部分
        $breadcrumb_data = '首页,商城管理,商家管理';
        $search_items = array(
        	array('name'=>'name','placeholder'=>'请输入城市名称或省份名称','cls'=>'w250','_parser'=>'form_item/search/input'),
        );
        $buttons = array(
            array('text'=>'新增开通城市','title'=>'新增开通城市','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('System/city_save'),
            ),
            array('text'=>'删除','title'=>'删除','icon'=>'del','full'=>1,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('System/city_remove'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'删除开通城市','title'=>'删除开通城市','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_confirm','url'=>U('System/city_remove'),'url_param' => array('id'=>'2_id')
            ), 
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'城市ID'),
            array('name'=>'province','cls'=>'w60','title'=>'所属省份'),
            array('name'=>'city','cls'=>'w60','title'=>'城市名称'),
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


    public function city_save(){
        if(IS_POST){
            $GLOBALS['cur_operate'] = 1;//目前的操作类型，2^0:新增;2^1:编辑
            //获取字段
    		$district_1_id = I('district_1_id');
    		$district_2_id = I('district_2_id');
        	if(!$district_1_id){
	        	sys_out_fail("请选择省市");
        	}
        	$province = $this->get_one_bysql("select name from sys_cascade_district where id=$district_1_id");
			if(strpos($province, '市') === false){
				if(!$district_2_id){
					sys_out_fail("请选择城市");
				}else{
					$city = $this->get_one_bysql("select name from sys_cascade_district where id=$district_2_id");
				}
			}else{
				$city = $province;
			}
			$regdate = sys_get_time();
            $fields_str = "province='$province',city='$city',district_1_id=$district_1_id,district_2_id=$district_2_id,regdate='$regdate'";
            $sqlstr = "insert into sys_opencity set $fields_str";
            $result = $this -> do_execute($sqlstr);
            sys_out_result($result);
        }
        else{
            $GLOBALS['cur_operate'] = 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
                array('label'=>'基本信息','_parser'=>'container/default','_children'=>array(                       
                    ['name'=>'cascade_1','label'=>'所属地区',
	                    '_parser'=>'form_item/form/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,
	                    'related'=>[
	                        ['name'=>'district_1_id','label'=>'选择省份'],
	                        ['name'=>'district_2_id','label'=>'选择城市'],
	                    ],
	                    'init_url'=>U(MODULE_NAME.'/district_select_list'),
	                ],
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            $component_data = array('_parser'=>'form/default',
                'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
                'rules' => $rules,'messages' => $messages,
            );
            _display($component_data);
        }
    }


    public function city_remove(){
        $id = _REQUEST('id');
        $sqlstr = "delete from sys_opencity where id in ($id)";
        $result = $this -> do_execute($sqlstr);
        sys_out_result($result);
    }


    public function label_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $name = _REQUEST('name');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.* ";
        $sql_suffix = "from sys_label mt ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "";

        //筛选数据
        if($name) $sql_suffix .= "and mt.name like '%$name%' ";
        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
        ));
        
        //UI部分
        $breadcrumb_data = '首页,商城管理,商家管理';
        $search_items = array(
        	array('name'=>'name','placeholder'=>'请输入标签名称','cls'=>'w250','_parser'=>'form_item/search/input'),
        );
        $buttons = array(
            array('text'=>'新增','title'=>'新增','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('System/label_save'),
            ),
            array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('System/label_save'),'url_param'=>array('id'=>'2_id')
            ),  
            array('text'=>'删除','title'=>'删除','icon'=>'del','full'=>1,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('System/label_remove'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'删除','title'=>'删除','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_confirm','url'=>U('System/label_remove'),'url_param' => array('id'=>'2_id')
            ), 
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'标签ID'),
            array('name'=>'name','cls'=>'w60','title'=>'标签名称'),
            array('name'=>'regdate','cls'=>'w60','title'=>'添加时间'),
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


    public function label_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
			$save_fields = array('name');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            
            if($id){//修改
                $sqlstr = "update sys_label set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }else{//新增
                $regdate = sys_get_time();
                $fields_str .= ",regdate='$regdate'";
                $sqlstr = "insert into sys_label set $fields_str";
                $result = $this -> do_execute($sqlstr);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
				array('name'=>'id','_parser'=>'form_item/form/hidden'),
				['name'=>'name','label'=>'标签名称','placeholder'=>'','required'=>1,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    '_validation'=>[],
                ],
			);
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.id,mt.name";
                $sql_suffix = "from sys_label mt ";
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


    public function label_remove(){
        $id = _REQUEST('id');
        $sqlstr = "delete from sys_label where id in ($id)";
        $result = $this -> do_execute($sqlstr);
        sys_out_result($result);
    }

    
}