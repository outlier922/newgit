<?php

class CardAction extends BaseAction
{
    public function card_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $name = _REQUEST('name');
        $auditflag = _REQUEST('auditflag');
        $validflag = _REQUEST('validflag');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
		$shop_id = $_SESSION['shop']['auth']['uid'];
        //基本查询
        $field_list = "mt.* ";
        $sql_suffix = "from sys_card mt ";
        $sql_suffix .= "where shop_id=$shop_id ";
        $orderby_str = "";

        //筛选数据
        if($name) $sql_suffix .= "and mt.name like '%$name%' ";
        if($auditflag){
	        $auditflag = $auditflag-1;
			$sql_suffix .= "and mt.auditflag='$auditflag' ";
        }
        if($validflag){
	        $validflag = $validflag-1;
			$sql_suffix .= "and mt.validflag='$validflag' ";
	    }

        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);

        int_to_string($list_items,array(
            'auditflag'=>array('0'=>'未审核','1'=>'审核通过','2'=>'审核未通过'),
            'validflag'=>array('0'=>'不可用','1'=>'可用'),
        ));

        //UI部分
        $breadcrumb_data = '首页,商家管理,优惠券管理';
        $search_items = array(
            array('name'=>'name','placeholder'=>'请输入优惠券名称','cls'=>'w250','_parser'=>'form_item/search/input'),
            array('name'=>'auditflag','_parser'=>'form_item/search/select','data'=>array(''=>'审核状态','1'=>'未审核','2'=>'审核通过','3'=>'审核未通过'),'style'=>'width:120px;'),
            array('name'=>'validflag','_parser'=>'form_item/search/select','data'=>array(''=>'可用状态','1'=>'不可用','2'=>'可用'),'style'=>'width:120px;'),
        );
        $buttons = array(
            array('text'=>'新增商品','title'=>'新增商品','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('Card/card_save'),
            ),
            array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Card/cards_save'),'url_param'=>array('id'=>'2_id')
            ),
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'优惠券ID'),
            array('name'=>'name','cls'=>'w100','title'=>'优惠券名称'),
            array('name'=>'imgurl','title'=>'优惠券图片','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_imgurl','imgurlbig'=>'3_imgurlbig'
            )),            
            array('name'=>'score','cls'=>'w80','title'=>'兑换积分'),
            array('name'=>'price','cls'=>'w80','title'=>'兑换金额'),
            array('name'=>'stock','cls'=>'w80','title'=>'库存'),
            array('name'=>'end_regdate','cls'=>'w100','title'=>'截止使用日期'),
            array('name'=>'','title'=>'兑换说明','cls'=>'w50','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'查看','title'=>'1_查看','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Card/indexshop_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'convertnum','cls'=>'w80','title'=>'兑换数量'),
            array('name'=>'handlenum','cls'=>'w80','title'=>'核销数量'),
            array('name'=>'auditflag_text','cls'=>'w80','title'=>'审核状态'),
            array('name'=>'validflag_text','cls'=>'w80','title'=>'可用状态'),
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

    public function indexshop_get(){
        $id = _REQUEST('id');
        if(!$id) sys_out_fail("参数传递不正确");

        $sqlstr = "select * from sys_card where id =$id";
        $result_r = $this -> get_list_bysql($sqlstr);
        int_to_string($result_r,array(

        ));
        $temp_array = $result_r[0];

        $fields = array(
            array('title'=>'兑换说明','value'=>$temp_array['content']),
        );
        $component_data = array('_parser'=>'table/detail','title'=>'',
            'fields'=>$fields
        );
        _display($component_data);
    }

    public function card_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $shop_id = $_SESSION['shop']['auth']['uid'];
            //获取字段
            $save_fields = array('name','score','price','stock','end_regdate','validflag');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            $end_regdate = $_POST['end_regdate'];
            $end_time = strtotime($end_regdate.' 23:59:59');
            if($end_time <= time()){
	            sys_out_fail("截止使用日期已过期");
            }
            $content = $_POST['content'];
            $content=str_replace("'","\\'",$content);
            $fields_str .= ",shop_id=$shop_id,content='$content'";

			//新增
        	if (!empty($_FILES['temp_file']['name'])) {
                $upload_array = sys_upload_file(1,600,240);
                $fields_str .= ",imgurl='$upload_array[1]',imgurlbig='$upload_array[0]'";
            }else{
	            sys_out_fail("请上传优惠券图片");
            }
            $regdate = sys_get_time();
            $fields_str .= ",regdate='$regdate'";
            $sqlstr = "insert into sys_card set $fields_str";
            $result = $this -> do_execute($sqlstr);
            
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
                array('name'=>'id','_parser'=>'form_item/form/hidden'),
                array('_parser'=>'tab/default','_children'=>array(
                    array('label'=>'基本信息','_parser'=>'container/default','_children'=>array(
                        array('name'=>'name','label'=>'优惠券名称','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                        ),
                        array('name'=>'imgurl','label'=>'优惠券图片',
		                    '_parser'=>'form_item/form/image','required'=>0,
		                ),
                        array('name'=>'score','label'=>'兑换积分','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
                        array('name'=>'price','label'=>'兑换金额','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
                        array('name'=>'stock','label'=>'库存','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
						array('name'=>'end_regdate','label'=>'截止使用日期','placeholder'=>'',
		                    '_parser'=>'form_item/form/date','type'=>'text','required'=>1,
		                ),
		                ['name'=>'validflag','label'=>'可用状态','_parser'=>'form_item/form/select','required'=>1,
                        	'data'=>['0'=>'不可用','1'=>'可用'],'default'=>'1'
                        ],
                    )),
                    array('label'=>'兑换说明','_parser'=>'container/default','_children'=>array(
                        array('name'=>'content','label'=>'兑换说明',
                            '_parser'=>'form_item/form/rich','required'=>1,
                        ),
                    ))
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_card mt ";
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
    public function cards_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            //获取字段
            $save_fields = array('stock','validflag');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            $content = $_POST['content'];
            $content=str_replace("'","\\'",$content);
            $fields_str .= ",content='$content'";

            if($id){//修改
                $sqlstr = "update sys_card set $fields_str where id=$id";
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
                        array('name'=>'stock','label'=>'库存','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
						array('name'=>'validflag','label'=>'可用状态',
							'_parser'=>'form_item/collect/radio',
							'data'=>array('1'=>'正常','0'=>'冻结')
						),
                    )),
                    array('label'=>'兑换说明','_parser'=>'container/default','_children'=>array(
                        array('name'=>'content','label'=>'兑换说明',
                            '_parser'=>'form_item/form/rich','required'=>1,
                        ),
                    ))
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_card mt ";
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