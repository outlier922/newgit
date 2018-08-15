<?php

class GoodAction extends BaseAction
{
    public function good_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $name = _REQUEST('name');
        $commentstar = _REQUEST('commentstar');
        $salenum = _REQUEST('salenum');
        $commentnum = _REQUEST('commentnum');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
		$shop_id = $_SESSION['shop']['auth']['uid'];
        //基本查询
        $field_list = "mt.*,c.name as classfiyname ";
        $sql_suffix = "from sys_good mt ";
        $sql_suffix .= "left join sys_shop s on mt.shop_id = s.id ";
        $sql_suffix .= "left join sys_classify c on s.one_classify_id = c.id ";
        $sql_suffix .= "where mt.id>0 and mt.shop_id=$shop_id ";
        $orderby_str = "";

        //筛选数据
        if($name) $sql_suffix .= "and mt.name like '%$name%' ";
        if($commentstar) $sql_suffix .= "and mt.commentstar='$commentstar' ";
        if($salenum == 1){
			$orderby_str .= "mt.salenum desc,";
	    }else{
		    $orderby_str .= "mt.salenum asc,";
	    }
        if($commentnum == 1){
			$orderby_str .= "mt.commentnum desc,";
	    }else{
		    $orderby_str .= "mt.commentnum asc,";
	    }
	    if(!$orderby_str){
		    $orderby_str = "mt.id desc";
	    }else{
		    $orderby_str = substr($orderby_str,0,-1);
	    }

        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        foreach($list_items as $k=>&$v){
	        $v['price'] = $v['present_price'].'/'.$v['original_price'];
        }
        unset($v);

        int_to_string($list_items,array(
            'doflag'=>array('1'=>'待审核','2'=>'已通过','3'=>'已拒绝'),
            'flag'=>array('1'=>'上架中','2'=>'已下架'),
            'isrecommend'=>array('1'=>'推荐','2'=>'不推荐'),
        ));

        //UI部分
        $breadcrumb_data = '首页,商家管理,商品管理';
        $search_items = array(
            array('name'=>'name','placeholder'=>'请输入商品名称','cls'=>'w250','_parser'=>'form_item/search/input'),
            array('name'=>'commentstar','_parser'=>'form_item/search/select','data'=>array(''=>'评价等级','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'),'style'=>'width:120px;'),
            array('name'=>'salenum','_parser'=>'form_item/search/select','data'=>array(''=>'销量','1'=>'销量从高到低','2'=>'销量从低到高'),'style'=>'width:120px;'),
            array('name'=>'commentnum','_parser'=>'form_item/search/select','data'=>array(''=>'评论数量','1'=>'评论数量从高到低','2'=>'评论数量从低到高'),'style'=>'width:150px;'),
        );
        $buttons = array(
            array('text'=>'新增商品','title'=>'新增商品','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('Good/good_save'),
            ),
            array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Good/good_save'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'上架/下架','title'=>'上架/下架','icon'=>'edit2','checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Good/good_flag'),'url_param' => array('id'=>'2_id')
            ),
            array('text'=>'删除','title'=>'删除','icon'=>'del','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Good/good_remove'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'批量删除','title'=>'批量删除','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_confirm','url'=>U('Good/good_remove'),'url_param' => array('id'=>'2_id')
            ),

        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'商品ID'),
            array('name'=>'img','title'=>'商品封面','cls'=>'w50','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_img','imgurlbig'=>'3_bigimg'
            )),
            array('name'=>'name','cls'=>'w100','title'=>'商品名称'),
            array('name'=>'isrecommend_text','cls'=>'w100','title'=>'是否推荐'),
            array('name'=>'orderby','cls'=>'w100','title'=>'推荐排序'),
            ['name'=>'avatar','title'=>'图片管理','cls'=>'w100','_after_parser'=>[
                '_parser'=>'button_item/td_a_get','text'=>'1_图片管理','title'=>'1_图片管理','full'=>0,
                'target'=>'inner_frame',
                'url'=>MODULE_NAME.'/image_list','url_param'=>['keytype'=>'1_1','keyid'=>'3_id']
            ]],
            array('name'=>'doflag_text','cls'=>'w100','title'=>'审核状态'),
            array('name'=>'flag_text','cls'=>'w100','title'=>'商品状态'),
            array('name'=>'classfiyname','cls'=>'w100','title'=>'商品类别'),
            array('name'=>'','title'=>'商品描述','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'查看','title'=>'1_查看','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Good/indexgood_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'price','cls'=>'w180','title'=>'商品价格（现价/原价）'),
            array('name'=>'salenum','cls'=>'w100','title'=>'销量'),
            array('name'=>'commentnum','cls'=>'w100','title'=>'评论数量'),
            array('name'=>'commentstar','cls'=>'w100','title'=>'评价等级'),
            array('name'=>'','title'=>'评价内容','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'查看','title'=>'1_查看','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Good/comment_get','url_param'=>array('id'=>'3_id')
            )),
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


    public function indexgood_get(){
        $id = _REQUEST('id');
        if(!$id) sys_out_fail("参数传递不正确");

        $sqlstr = "select * from sys_good where id =$id";
        $result_r = $this -> get_list_bysql($sqlstr);
        int_to_string($result_r,array(

        ));
        $temp_array = $result_r[0];

        $fields = array(
            array('title'=>'商家详情','value'=>$temp_array['content']),
        );
        $component_data = array('_parser'=>'table/detail','title'=>'',
            'fields'=>$fields
        );
        _display($component_data);
    }


    public function comment_get($_action_access=0){
	    //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
	    //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        $id = _REQUEST('id');
        if(!$id) sys_out_fail("参数传递不正确");

        //基本查询
        $field_list = "mt.*";
        $sql_suffix = "from sys_comment mt ";
        $sql_suffix .= "where mt.good_id=$id ";
        $orderby_str = "mt.id desc";
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
            
        ));
        
		$breadcrumb_data = '首页,商品管理,评价列表';
        $table_headers = array(
            array('name'=>'client_id','cls'=>'w60','title'=>'用户ID'),
            array('name'=>'content','cls'=>'w60','title'=>'评价内容'),
            array('name'=>'regdate','cls'=>'w60','title'=>'评价时间'),
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


    public function good_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $shop_id = $_SESSION['shop']['auth']['uid'];
            //获取字段
            $isrecommend = $_POST['isrecommend'];
            $orderby = $_POST['orderby'];
            if($isrecommend == 1){
	            if(!$orderby){
		            sys_out_fail("请填写排序");
	            }
	            $orderonly = $this->get_one_bysql("select orderby from sys_good where shop_id=$shop_id and orderby=$orderby and id!=$id");
	            if($orderonly){
		            sys_out_fail("改商家下的商品已设置该排序次数");
	            }
            }
            $save_fields = array('name','present_price','original_price','isrecommend','orderby');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);           
            $content = $_POST['content'];
            $fields_str .= ",content='$content',shop_id=$shop_id";

            if($id){//修改
            	//商家封面
				$img = $this->get_one_bysql("select img from sys_good where id=$id");
	            if(!$img){
					if (empty($_FILES['temp_file']['name'])) {
						sys_out_fail("请上传商品封面");
					}
				}
	            if (!empty($_FILES['temp_file']['name'])) {
	                $upload_array = sys_upload_file(1,600,240);
	                $fields_str .= ",img='$upload_array[1]',bigimg='$upload_array[0]'";
	            }
                $sqlstr = "update sys_good set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }
            else{//新增
            	if (!empty($_FILES['temp_file']['name'])) {
	                $upload_array = sys_upload_file(1,600,240);
	                $fields_str .= ",img='$upload_array[1]',bigimg='$upload_array[0]'";
	            }else{
		            sys_out_fail("请上传商品封面");
	            }
                $regdate = sys_get_time();
                $fields_str .= ",regdate='$regdate'";
                $sqlstr = "insert into sys_good set $fields_str";
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
                        array('name'=>'name','label'=>'商品名称','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                        ),
                        array('name'=>'img','label'=>'商品封面',
		                    '_parser'=>'form_item/form/image','required'=>0,
		                ),
                        array('name'=>'present_price','label'=>'商品现价','required'=>1,'placeholder'=>'单位（元/件）',
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
                        array('name'=>'original_price','label'=>'商品原价','required'=>1,'placeholder'=>'单位（元/件）',
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
                        array('name'=>'isrecommend','label'=>'是否推荐',
		                    '_parser'=>'form_item/collect/radio',
		                    'data'=>array('1'=>'推荐','2'=>'不推荐')
		                ),
		                array('name'=>'orderby','label'=>'推荐排序','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                    )),
                    array('label'=>'商品图文详情','_parser'=>'container/default','_children'=>array(
                        array('name'=>'content','label'=>'商品图文详情',
                            '_parser'=>'form_item/form/rich','required'=>1,
                        ),
                    ))
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_good mt ";
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


    public function good_remove(){
        $id = _REQUEST('id');
        $sql_array = NULL;
        $sql_array[] = "delete from sys_good where id in ($id)";
        $sql_array[] = "delete from sys_img where keyid in ($id) and keytype=1";
        $result = $this->do_transaction($sql_array);
        sys_out_result($result);
    }

    //上下架操作
	public function good_flag(){
		$id = _POST('id');
		$flag = $this->get_one_bysql("select flag from sys_good where id=$id");
		if($flag == 1){
			$sql = "update sys_good set flag=2 where id=$id and flag=1";
		}else{
			$sql = "update sys_good set flag=1 where id=$id and flag=2";
		}
		$result = $this -> do_execute($sql);
		sys_out_result($result);
	}

}