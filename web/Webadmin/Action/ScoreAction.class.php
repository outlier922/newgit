<?php

class ScoreAction extends BaseAction
{
    public function goods_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $goodsname = _REQUEST('goodsname');
        $saleflag = _REQUEST('saleflag');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*";
        $sql_suffix = "from sys_goods mt ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "mt.id desc";

        //筛选数据
        if($goodsname) $sql_suffix .= "and (mt.goodsname like '%$goodsname%' or mt.id like '%$goodsname%') ";
        if($saleflag) $sql_suffix .= "and mt.saleflag='$saleflag' ";

        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);

        int_to_string($list_items,array(
            'saleflag'=>array('1'=>'上架','2'=>'下架'),
        ));

        //UI部分
        $breadcrumb_data = '首页,积分商城管理,商品管理';
        $search_items = array(
            array('name'=>'goodsname','placeholder'=>'请输入商品ID、商品名称','cls'=>'w250','_parser'=>'form_item/search/input'),
            array('name'=>'saleflag','_parser'=>'form_item/search/select','data'=>array(''=>'商品状态','1'=>'上架','2'=>'下架'),'style'=>'width:120px;'),
        );
        $buttons = array(
            array('text'=>'新建商品','title'=>'添加','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('Score/goods_save'),
            ),
            array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Score/goods_save'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'删除','title'=>'删除','icon'=>'del','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Score/goods_remove'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'删除商品','title'=>'删除商品','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_confirm','url'=>U('Score/goods_remove'),'url_param' => array('id'=>'2_id')
            ),

        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'商品ID'),
            array('name'=>'goodsimg','title'=>'商品图片','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_goodsimg','imgurlbig'=>'3_goodsbigimg'
            )),
            array('name'=>'goodsname','cls'=>'w100','title'=>'商品名称'),
            array('name'=>'score','cls'=>'w100','title'=>'所需积分'),
            array('name'=>'regdate','cls'=>'w150','title'=>'发布时间'),
            array('name'=>'saleflag_text','cls'=>'w100','title'=>'商品状态'),
            array('name'=>'remarks','cls'=>'w150','title'=>'备注'),
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


    public function goods_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            //获取字段
            $save_fields = array('goodsname','score','saleflag');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            $remarks = $_POST['remarks'];
            $fields_str .= ",remarks='$remarks'";

            //封面
            if (!empty($_FILES['temp_file']['name'])) {
                $upload_array = sys_upload_file(1,600,240);
                $fields_str .= ",goodsimg='$upload_array[1]',goodsbigimg='$upload_array[0]'";
            }

            if($id){//修改
                $sqlstr = "update sys_goods set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }
            else{//新增
                $regdate = sys_get_time();
                $fields_str .= ",regdate='$regdate'";
                $sqlstr = "insert into sys_goods set $fields_str";
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
                        array('name'=>'goodsname','label'=>'商品名称','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                        ),
                        array('name'=>'score','label'=>'所需积分','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
                        array('name'=>'goodsimg','label'=>'商品图片',
                            '_parser'=>'form_item/form/image','required'=>0,
                        ),
                        array('name'=>'saleflag','label'=>'商品状态：','_parser'=>'form_item/collect/radio','data'=>array('1'=>'上架','2'=>'下架'),),
                        array('name'=>'remarks','label'=>'备注信息','placeholder'=>'最长140个字符',
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
                $sql_suffix = "from sys_goods mt ";
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


    public function goods_remove(){
        $id = _REQUEST('id');
        $sqlstr = "delete from sys_goods where id in ($id)";
        $result = $this -> do_execute($sqlstr);
        sys_out_result($result);
    }


    public function order_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        $export=_GET('_export');
        if ($export) $_REQUEST=unserialize(base64_decode($_GET['_request']));
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*";
        $sql_suffix = "from sys_order mt ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "mt.id desc";

        //筛选数据
        //时间段
        $time_start=_REQUEST('time_start');
        if ($time_start) $sql_suffix.=" and mt.regdate>='$time_start'";
        $time_end=_REQUEST('time_end');
        if ($time_end) $sql_suffix.=" and mt.regdate<='$time_end'";
        //订单状态
        $orderflag=_REQUEST('orderflag');
        if (!sys_check_empty($orderflag)) {
            $sql_suffix .= " and mt.orderflag=$orderflag";
        }
        //订单编号
        $out_trade_no=_REQUEST('out_trade_no');
        if($out_trade_no) $sql_suffix .= " and (mt.out_trade_no like '%$out_trade_no%' or mt.goods_name like '%$out_trade_no%') ";

        //查询数据
        if ($export) $list_items=$this->get_list_bysql("select $field_list $sql_suffix order by $orderby_str");
        else {
            $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        }

        int_to_string($list_items,array(
            'orderflag'=>array('1'=>'已发货','2'=>'未发货'),
        ));

        //UI部分
        $breadcrumb_data = '首页,积分商城管理,订单管理';
        $search_items = array(
            array('name'=>'time','label'=>'下单时间','_parser'=>'form_item/search/date'),
            array('name'=>'orderflag','_parser'=>'form_item/search/select','data'=>array(''=>'订单状态','1'=>'已发货','2'=>'未发货'),'style'=>'width:120px;'),
            array('name'=>'out_trade_no','placeholder'=>'请输入商品名称关键词、订单编号','cls'=>'w250','_parser'=>'form_item/search/input'),
        );
        $buttons = array(
            array('position'=>1,'_after_parser'=>array('_parser'=>'button_item/position/blank','text'=>'导出Excel','title'=>'导出Excel','icon'=>'edit2',
                'target'=>'blank','full'=>0,'url'=>U(MODULE_NAME.'/'.ACTION_NAME.'?_export=1&_request='.base64_encode(serialize($_REQUEST)))),
            ),
            array('text'=>'发货','title'=>'发货','icon'=>'del','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Score/order_change'),'url_param'=>array('id'=>'2_id')
            ),
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'订单ID'),
            array('name'=>'out_trade_no','cls'=>'w150','title'=>'订单编号'),
            array('name'=>'goods_id','cls'=>'w60','title'=>'商品ID'),
            array('name'=>'goods_name','cls'=>'w60','title'=>'订单商品'),
            array('name'=>'goods_img','title'=>'商品图片','cls'=>'w50','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_goods_img','imgurlbig'=>'3_goods_big_img'
            )),
            array('name'=>'orderflag_text','cls'=>'w50','title'=>'订单状态'),
            array('name'=>'goods_score','cls'=>'w60','title'=>'商品积分'),
            array('name'=>'phone','cls'=>'w100','title'=>'联系电话'),
            array('name'=>'client_id','cls'=>'w100','title'=>'用户ID'),
            array('name'=>'regdate','cls'=>'w150','title'=>'下单时间'),
        );

        if ($export) {
            $export_headers= array(
                array('name'=>'id','cls'=>'w60','title'=>'订单ID'),
                array('name'=>'out_trade_no','cls'=>'w100','title'=>'订单编号'),
                array('name'=>'goods_id','cls'=>'w100','title'=>'商品ID'),
                array('name'=>'goods_name','title'=>'订单商品'),
                array('name'=>'orderflag_text','title'=>'订单状态'),
                array('name'=>'goods_score','cls'=>'w50','title'=>'商品积分'),
                array('name'=>'phone','title'=>'联系电话'),
                array('name'=>'client_id','title'=>'用户ID'),
                array('name'=>'regdate','title'=>'下单时间'),
            );
            ext_export("订单列表",$export_headers,$list_items);
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


    public function order_change(){
        $id = _REQUEST('id');
        $sqlstr = "update sys_order set orderflag = 1 where id in ($id)";
        $result = $this -> do_execute($sqlstr);
        sys_out_result($result);
    }


    public function cash_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        $export=_GET('_export');
        if ($export) $_REQUEST=unserialize(base64_decode($_GET['_request']));
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $id = _REQUEST('id');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*";
        $sql_suffix = "from sys_cash mt ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "mt.id desc";

        //筛选数据
        if($id) $sql_suffix .= "and mt.id = '$id' ";
        //查询数据
        if ($export) $list_items=$this->get_list_bysql("select $field_list $sql_suffix order by $orderby_str");
        else {
            $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        }

        int_to_string($list_items,array(
            'cashflag'=>array('1'=>'用户提现','2'=>'积分转化','3'=>'红包抽奖'),
        ));

        //UI部分
        $breadcrumb_data = '首页,积分商城管理,财务管理';
        $search_items = array(
            array('name'=>'id','placeholder'=>'请输入财务记录ID','cls'=>'w250','_parser'=>'form_item/search/input'),
        );
        $buttons = array(
            array('position'=>1,'_after_parser'=>array('_parser'=>'button_item/position/blank','text'=>'导出Excel','title'=>'导出Excel','icon'=>'edit2',
                'target'=>'blank','full'=>0,'url'=>U(MODULE_NAME.'/'.ACTION_NAME.'?_export=1&_request='.base64_encode(serialize($_REQUEST)))),
            ),
            array('text'=>'备注','title'=>'备注','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Score/cash_save'),'url_param'=>array('id'=>'2_id')
            ),

        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'财务记录ID'),
            array('name'=>'score','cls'=>'w100','title'=>'财务记录金额'),
            array('name'=>'cashflag_text','title'=>'财务记录类型'),
            array('name'=>'regdate','title'=>'财务记录时间'),
            array('name'=>'remarks','title'=>'备注'),
        );

        if ($export) {
            $export_headers= array(
                array('name'=>'id','cls'=>'w60','title'=>'财务记录ID'),
                array('name'=>'score','cls'=>'w100','title'=>'财务记录金额'),
                array('name'=>'cashflag_text','cls'=>'w100','title'=>'财务记录类型'),
                array('name'=>'regdate','title'=>'财务记录时间'),
                array('name'=>'remarks','title'=>'备注'),
            );
            ext_export("财务列表",$export_headers,$list_items);
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


    public function cash_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $remarks = $_POST['remarks'];
            if($id){//修改
                $sqlstr = "update sys_cash set remarks='$remarks' where id=$id";
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
                $sql_suffix = "from sys_cash mt ";
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
        //基本查询
        $field_list = "mt.*,s.name as shopname ";
        $sql_suffix = "from sys_card mt ";
        $sql_suffix .= "left join sys_shop s on mt.shop_id=s.id ";
        $sql_suffix .= "where 1=1 ";
        $orderby_str = "";

        //筛选数据
        if($name) $sql_suffix .= "and (mt.name like '%$name%' or s.name like '%$name%') ";
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
        $breadcrumb_data = '首页,商城管理,优惠券管理';
        $search_items = array(
            array('name'=>'name','placeholder'=>'请输入商家名称、优惠券名称','cls'=>'w250','_parser'=>'form_item/search/input'),
            array('name'=>'auditflag','_parser'=>'form_item/search/select','data'=>array(''=>'审核状态','1'=>'未审核','2'=>'审核通过','3'=>'审核未通过'),'style'=>'width:120px;'),
            array('name'=>'validflag','_parser'=>'form_item/search/select','data'=>array(''=>'可用状态','1'=>'不可用','2'=>'可用'),'style'=>'width:120px;'),
        );
        $buttons = array(
            array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Score/card_save'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'拒绝','title'=>'拒绝','icon'=>'edit2','checked'=>2,'position'=>3,
                'target'=>'inner_confirm','url'=>U(MODULE_NAME.'/card_inaudit'),'url_param' => array('id'=>'2_id')
            ),
            array('text'=>'通过','title'=>'通过','icon'=>'edit2','checked'=>2,'position'=>3,
                'target'=>'inner_confirm','url'=>U(MODULE_NAME.'/card_audit'),'url_param' => array('id'=>'2_id')
            ),
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'优惠券ID'),
            array('name'=>'shopname','cls'=>'w60','title'=>'商家名称'),
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
                'url'=>'Score/indexshop_get','url_param'=>array('id'=>'3_id')
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
    //冻结账号
    public function card_inaudit(){
        $client_ids = _POST('id');
        $result = $this -> do_execute("update sys_card set auditflag=2,validflag=0 where id in ($client_ids) and auditflag !=2");
        sys_out_result($result);
    }
    //解冻账号
    public function card_audit(){
        $client_ids = _POST('id');
        $result = $this -> do_execute("update sys_card set auditflag=1,validflag=1 where id in ($client_ids) and auditflag !=1");
        sys_out_result($result);
    }
	public function card_save(){
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