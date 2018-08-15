<?php
class HotwordAction extends BaseAction{
    // 热词管理
    public function hot_word_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $keyword = I('keyword');
        $showflag = I('showflag');
        //基本查询
        $field_list = "mt.id,mt.name,mt.orderby,mt.showflag";
        $field_list .= "";
        $sql_suffix = "from sys_hot_word mt  ";
        $sql_suffix .= " ";
        $sql_suffix .= "where 1=1 ";
        $orderby_str = "mt.orderby asc,mt.id desc";
        //筛选数据
        if($keyword){
            $sql_suffix .= "and (mt.name like '%$keyword%') ";
        }
        if($showflag) $sql_suffix .= "and mt.showflag=".($showflag-1).' ';
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,[
            'showflag'=>['0'=>'不显示','1'=>'显示']
        ]);
        //UI部分
        $breadcrumb_data = '首页,系统配置,热词管理';
        $search_items = [
            ['name'=>'showflag','_parser'=>'form_item/search/select','style'=>'width:100px;','data'=>[
                '0'=>'全部','1'=>'不显示','2'=>'显示'
            ]],
            ['name'=>'keyword','placeholder'=>'输入名称','cls'=>'w200','_parser'=>'form_item/search/input'],
        ];
        $buttons = [
            ['text'=>'新增热词','title'=>'新增热词','icon'=>'add2','full'=>0,'checked'=>0,'position'=>1,
            	'target'=>'inner_frame','url'=>U(MODULE_NAME.'/hot_word_save'),'url_param'=>[]
            ],
            ['text'=>'编辑热词','title'=>'编辑热词','icon'=>'edit2','full'=>0,'checked'=>1,'position'=>3,
                'target'=>'inner_frame','url'=>U(MODULE_NAME.'/hot_word_save'),'url_param'=>['id'=>'2_id']
            ],
            ['text'=>'删除热词','title'=>'删除热词','icon'=>'del2','full'=>0,'checked'=>1,'position'=>3,
                'target'=>'inner_confirm','url'=>U(MODULE_NAME.'/hot_word_remove'),'url_param'=>['id'=>'2_id']
            ],
        ];
        $table_headers = [
            ['name'=>'id','title'=>'热词ID','cls'=>'w100'],
            ['name'=>'name','title'=>'热词名称','cls'=>'w100'],
            ['name'=>'orderby','title'=>'手动排序','cls'=>'w100'],
            ['name'=>'showflag_text','title'=>'显示状态','cls'=>'w100'],
        ];
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
    //保存热词
    public function hot_word_save(){
        if(IS_POST){
        	$id = _POST('id');
        	$GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑

        	//根据post参数生成field字符串
        	$save_fields = ['name','showflag','orderby'];
        	$post_keys = array_keys($_POST);
        	$post_fields = array_intersect($save_fields,$post_keys);//取公共
        	$fields_str = fields2SqlStrByPost($post_fields);

        	if($id){//修改
        		$sqlstr = "update sys_hot_word set $fields_str where id=$id";
        		$result = $this -> do_execute($sqlstr);
        	}
        	else{//新增
        		$sqlstr = "insert into sys_hot_word set $fields_str";
        		$result = $this -> do_execute($sqlstr);
        	}

        	sys_out_result($result);
        }
        else{
        	$id = _REQUEST('id');
        	$GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
        	$form_items = [
        		['name'=>'id','_parser'=>'form_item/form/hidden'],
                ['name'=>'name','label'=>'名称','placeholder'=>'','required'=>1,
                	'_parser'=>'form_item/form/input','type'=>'text',
                	'_validation'=>[],
                ],
                ['name'=>'orderby','label'=>'手动排序','placeholder'=>'1~254之间的数字,数字越小越排在前面','required'=>1,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    '_validation'=>[
                        'digits'=>[true,'必须是数字'],
                        'min'=>[1,'必须大于等于1'],
                        'max'=>[254,'必须小于等于254'],
                    ],
                ],
                ['name'=>'showflag','label'=>'显示状态','_parser'=>'form_item/form/select','required'=>1,
                    'data'=>['1'=>'显示','0'=>'不显示'],'default'=>'1'
                ],
        	];
        	form_validation_create($form_items,$rules,$messages);//获取验证规则
        	if($id){
        		//取值
        		$field_list = "mt.*";
        		$sql_suffix = "from sys_hot_word mt ";
        		$sql_suffix .= "where mt.id=$id ";
        		$temp_array = $this -> get_list_bysql("select $field_list $sql_suffix");
        		form_item_add_value($form_items,$temp_array[0]);
        	}
        	$component_data = ['_parser'=>'form/default',
        		'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
        		'rules' => $rules,'messages' => $messages,
        	];
        	_display($component_data);
        }
    }
    //删除热词
    public function hot_word_remove(){
        $type_id = _POST('id');
        $sqlstr = "delete from sys_hot_word where id in ($type_id)";
        $result = $this -> do_execute($sqlstr);
        sys_out_result($result);
    }
}