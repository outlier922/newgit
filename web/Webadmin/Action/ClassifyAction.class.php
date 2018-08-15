<?php
class ClassifyAction extends BaseAction{
    //一级分类列表
    public function one_classify_list($_action_access=0){
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
        $field_list = "mt.*";
        $sql_suffix = "from sys_classify mt ";
        $sql_suffix .= "where mt.id>0 and mt.parentid=0 and flag=1 ";
        $orderby_str = "mt.id desc";

        //筛选数据
        if($name) $sql_suffix .= "and mt.name like '%$name%' ";
        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);     
        int_to_string($list_items,array(
        
        ));
        
        //UI部分
        $breadcrumb_data = '首页,用户管理,用户管理';
        $search_items = array(
        	array('name'=>'name','placeholder'=>'请输入一级分类名称','cls'=>'w250','_parser'=>'form_item/search/input'),            
        );
        $buttons = array(
			array('text'=>'新增一级分类','title'=>'新增一级分类','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('Classify/one_classify_save'),
            ),
            array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Classify/one_classify_save'),'url_param'=>array('id'=>'2_id')
            ),  
            ['text'=>'删除','title'=>'删除','icon'=>'del2','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Classify/one_classify_remove'),'url_param'=>['id'=>'2_id']
            ],         
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'一级分类ID'),
            array('name'=>'img','title'=>'分类图标','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_img','imgurlbig'=>'3_imgbig'
            )),
            array('name'=>'name','cls'=>'w100','title'=>'一级分类名称'),
            array('name'=>'goodsnum','cls'=>'w100','title'=>'一级分类下商品个数'),           
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

	public function one_classify_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
			$save_fields = array('name');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            
            //封面
            if (!empty($_FILES['temp_file']['name'])) {
                $upload_array = sys_upload_file(1,600,240);
                $fields_str .= ",img='$upload_array[1]',imgbig='$upload_array[0]'";
            }
            
            if($id){//修改
                $sqlstr = "update sys_classify set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }else{//新增
                $regdate = sys_get_time();
                $fields_str .= ",regdate='$regdate',parentid=0";
                $sqlstr = "insert into sys_classify set $fields_str";
                $result = $this -> do_execute($sqlstr);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
				array('name'=>'id','_parser'=>'form_item/form/hidden'),
				['name'=>'name','label'=>'分类名称','placeholder'=>'','required'=>1,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    '_validation'=>[],
                ],
                array('name'=>'img','label'=>'分类图标',
                    '_parser'=>'form_item/form/image','required'=>0,
                ),
			);
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.id,mt.name";
                $sql_suffix = "from sys_classify mt ";
                $sql_suffix .= "where mt.id=$id and flag = 1 ";
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

    //删除分类
    public function one_classify_remove(){
        $type_id = _POST('id');
        $sqlstr = "update sys_classify set flag=2 where id=$type_id and parentid=0 and flag=1";
        $result = $this -> do_execute($sqlstr);
        sys_out_result($result);
    }


    //二级分类列表
    public function two_classify_list($_action_access=0){
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
        $field_list = "mt.*";
        $sql_suffix = "from sys_classify mt ";
        $sql_suffix .= "where mt.id>0 and mt.parentid!=0 and flag=1 ";
        $orderby_str = "mt.id desc";

        //筛选数据
        if($name) $sql_suffix .= "and mt.name like '%$name%' ";
        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);     
        int_to_string($list_items,array(
        
        ));
        
        //UI部分
        $breadcrumb_data = '首页,用户管理,用户管理';
        $search_items = array(
        	array('name'=>'name','placeholder'=>'请输入二级分类名称','cls'=>'w250','_parser'=>'form_item/search/input'),            
        );
        $buttons = array(
			array('text'=>'新增二级分类','title'=>'新增二级分类','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('Classify/two_classify_save'),
            ),
            array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Classify/two_classify_save'),'url_param'=>array('id'=>'2_id')
            ),  
            ['text'=>'删除','title'=>'删除','icon'=>'del2','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Classify/two_classify_remove'),'url_param'=>['id'=>'2_id']
            ],         
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'二级分类ID'),
            array('name'=>'img','title'=>'二级分类图标','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_img','imgurlbig'=>'3_imgbig'
            )),
            array('name'=>'name','cls'=>'w100','title'=>'二级分类名称'),
            array('name'=>'parentid','cls'=>'w100','title'=>'上级分类ID'),
            array('name'=>'goodsnum','cls'=>'w100','title'=>'二级分类下商品个数'),           
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

    public function two_classify_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
			$save_fields = array('name');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            $parentid = _POST('parentid');
            $re = $this->get_list_bysql("select * from sys_classify where id=$parentid and flag=1");
            if(!$re){
	            sys_out_fail("该上级分类ID不存在");
            }
            //封面
            if (!empty($_FILES['temp_file']['name'])) {
                $upload_array = sys_upload_file(1,600,240);
                $fields_str .= ",img='$upload_array[1]',imgbig='$upload_array[0]'";
            }
            
            if($id){//修改
       			$fields_str .= ",parentid=$parentid";
                $sqlstr = "update sys_classify set $fields_str where id=$id and parentid!=0 and flag = 1";
                $result = $this -> do_execute($sqlstr);
            }else{//新增
                $regdate = sys_get_time();
                $fields_str .= ",regdate='$regdate',parentid=$parentid";
                $sqlstr = "insert into sys_classify set $fields_str";
                $result = $this -> do_execute($sqlstr);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
				array('name'=>'id','_parser'=>'form_item/form/hidden'),
				['name'=>'name','label'=>'二级分类名称','placeholder'=>'','required'=>1,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    '_validation'=>[],
                ],
                array('name'=>'img','label'=>'二级分类图标',
                    '_parser'=>'form_item/form/image','required'=>0,
                ),
                ['name'=>'parentid','label'=>'上级分类ID','placeholder'=>'','required'=>1,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    '_validation'=>[
                        'digits'=>[true,'必须是数字'],
                        'min'=>[1,'必须大于等于1'],
                    ],
                ],
			);
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.id,mt.name,mt.parentid";
                $sql_suffix = "from sys_classify mt ";
                $sql_suffix .= "where mt.id=$id and parentid!=0 and flag = 1 ";
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

    //删除分类
    public function two_classify_remove(){
        $type_id = _POST('id');
        $sqlstr = "update sys_classify set flag=2 where id=$type_id and parentid!=0 and flag=1";
        $result = $this -> do_execute($sqlstr);
        sys_out_result($result);
    }
    
    
}