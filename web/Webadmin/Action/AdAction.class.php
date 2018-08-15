<?php
//管理员相关
class AdAction extends BaseAction
{
    public function ad_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $adname = $_POST['adname'];
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*,c.name as one_classify_name,cl.name as two_classify_name ";
        $sql_suffix = "from sys_ad mt ";
        $sql_suffix .= "left join sys_good g on mt.goods_id=g.id ";
        $sql_suffix .= "left join sys_shop s on g.shop_id=s.id ";
        $sql_suffix .= "left join sys_classify c on s.one_classify_id=c.id ";
        $sql_suffix .= "left join sys_classify cl on s.two_classify_id=cl.id ";
        $sql_suffix .= "where 1=1 ";
        $orderby_str = "mt.id desc";
        //筛选数据
        if($adname){
            $sql_suffix .= "and mt.adname like '%$adname%' ";
        }
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        foreach($list_items as $k=>&$v){
	        if($v['adlevel'] == 1){
		        $v['two_classify_name'] = '';
	        }
        }
        unset($v);
        int_to_string($list_items,array(
            'isshow'=>array('1'=>'是','2'=>'否'),
            'jumptype'=>array('1'=>'商品详情','2'=>'图文内容'),
            'adlevel'=>array('1'=>'一级','2'=>'二级'),
        ));
        //UI部分
        $breadcrumb_data = '首页,广告管理,广告管理';
        $search_items = array(
            array('name'=>'adname','placeholder'=>'输入名称','cls'=>'w250','_parser'=>'form_item/search/input'),
        );
        $buttons = array(
            array('text'=>'添加','title'=>'添加管理员','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('Ad/ad_save'),
            ),
            array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Ad/ad_save'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'删除','title'=>'删除','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Ad/ad_remove'),'url_param' => array('id'=>'2_id')
            ),
            array('text'=>'批量删除','title'=>'批量删除','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_confirm','url'=>U('Ad/ad_remove'),'url_param' => array('id'=>'2_id')
            ),
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w100','title'=>'ID'),
            array('name'=>'adname','cls'=>'w150','title'=>'名称'),
            array('name'=>'adlevel_text','cls'=>'w100','title'=>'广告级别'),
            array('name'=>'one_classify_name','cls'=>'w100','title'=>'一级分类'),
            array('name'=>'two_classify_name','cls'=>'w100','title'=>'二级分类'),
            array('name'=>'jumptype_text','cls'=>'w100','title'=>'跳转类型'),
            array('name'=>'goods_id','cls'=>'w100','title'=>'对应ID'),
            array('name'=>'','title'=>'图文内容','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'图文内容','title'=>'1_图文内容','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Ad/indexad_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'adimg','title'=>'图片','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_adimg','imgurlbig'=>'3_adbigimg'
            )),
            array('name'=>'isshow','cls'=>'w60','title'=>'显示'),
            array('name'=>'orderby','cls'=>'w60','title'=>'排序'),
            array('name'=>'regdate','cls'=>'w150','title'=>'发布时间')
        );
        $component_data = array(
            '_parser'=>'container/default',
            '_children'=>array(
                array('_parser'=>'breadcrumb/default','data'=>$breadcrumb_data),
                array('_parser'=>'container/content',
                    '_children'=>array(
                        array('_parser'=>'form/search','cls'=>'text-c','action'=>U(MODULE_NAME.'/'.ACTION_NAME),
                            '_children'=>$search_items
                        ),
                        array('_parser'=>'button/top_button','_children'=>$buttons),
                        array('_parser'=>'table/datatables/thin',
                            'head'=>&$table_headers,
                            'data'=>&$list_items,
                            'row_button'=>array('_parser'=>'button/row_dropdown','_children'=>$buttons)
                        ),
                        array('_parser'=>'pagination/laypage',
                            'total_count'=>$GLOBALS['totalcount'],
                            'page_count'=>$GLOBALS['page_count'],
                        ),
                    )
                )
            )
        );
        //解析组件
        _display($component_data);
    }


    public function ad_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $content = $_POST['content'];
            $goods_id = $_POST['goods_id'];
            $adlevel = $_POST['adlevel'];
            $one_classify_id = $_POST['one_classify_id'];
            if(!$one_classify_id){
	    		sys_out_fail("请选择分类");
    		}
            if($adlevel == 1){
	            $good_list = $this->get_list_bysql("select * from sys_good g left join sys_shop s on g.shop_id=s.id where s.one_classify_id=$one_classify_id and g.id=$goods_id");          
            }else{
	            $two_classify_id = $_POST['two_classify_id'];
	            if(!$two_classify_id){
		    		sys_out_fail("请选择二级分类");
	    		}
	            $good_list = $this->get_list_bysql("select * from sys_good g left join sys_shop s on g.shop_id=s.id where s.one_classify_id=$one_classify_id and s.two_classify_id=$two_classify_id and g.id=$goods_id");
            }
            if(!$good_list){
	            sys_out_fail("填写的商品id不属于该类别");
            }
            $content=str_replace("'","\\'",$content);
            //获取字段
            $save_fields = array('adname','jumptype','goods_id','isshow','adlevel','orderby');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            $fields_str .= ",content='$content'";

            //封面
            if (!empty($_FILES['temp_file']['name'])) {
                $upload_array = sys_upload_file(1,600,240);
                $fields_str .= ",adimg='$upload_array[1]',adbigimg='$upload_array[0]'";
            }

            if($id){//修改
                $sqlstr = "update sys_ad set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }
            else{//新增
                $regdate = sys_get_time();
                $fields_str .= ",regdate='$regdate'";
                $sqlstr = "insert into sys_ad set $fields_str";
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
                        array('name'=>'adname','label'=>'名称','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                        ),
                        array('name'=>'jumptype','label'=>'所属分类',
                            '_parser'=>'form_item/form/select_bind','type'=>'text','placeholder'=>'必填字段',
                            'init_url'=>U('Ad/ad_select_list'),
                        ),
                        array('name'=>'goods_id','label'=>'对应ID','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
                        array('name'=>'adimg','label'=>'图片',
                            '_parser'=>'form_item/form/image','required'=>0,
                        ),
                        array('name'=>'isshow','label'=>'显示：','_parser'=>'form_item/collect/radio','data'=>array('1'=>'是','2'=>'否'),),
                        array('name'=>'adlevel','label'=>'轮播图级别：','_parser'=>'form_item/collect/radio','data'=>array('1'=>'一级轮播图','2'=>'二级轮播图'),),
                        ['name'=>'cascade_1','label'=>'选择分类',
                            '_parser'=>'form_item/form/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,
                            'related'=>[
                                ['name'=>'one_classify_id','label'=>'一级分类'],
                                ['name'=>'two_classify_id','label'=>'二级分类'],
                            ],
                            'init_url'=>U(MODULE_NAME.'/classify_list'),
                        ],
                        array('name'=>'orderby','label'=>'轮播图排序','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
                    )),
                    array('label'=>'图文详情','_parser'=>'container/default','_children'=>array(
                        array('name'=>'content','label'=>'图文详情',
                            '_parser'=>'form_item/form/rich','required'=>1,
                        ),
                    ))
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_ad mt ";
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


    // 分级获取课程分类
    public function classify_list(){
        $parentid = _REQUEST('parentid');
        $parentid = $parentid ? $parentid : 0;
        $sqlstr = "select id,name from sys_classify where parentid=$parentid and flag=1";
        $temp_array = $this -> get_list_bysql($sqlstr);
        sys_out_success(0,$temp_array);
    }


    public function ad_select_list(){
        $temp_array = array(
            array('id'=>'1','name'=>'商品详情'),
            array('id'=>'2','name'=>'图文内容')
        );
        sys_out_success(0,$temp_array);
    }


    public function indexad_get(){
        $id = _REQUEST('id');
        if(!$id) sys_out_fail("参数传递不正确");

        $sqlstr = "select * from sys_ad where id =$id";
        $result_r = $this -> get_list_bysql($sqlstr);
        int_to_string($result_r,array(

        ));
        $temp_array = $result_r[0];

        $fields = array(
            array('title'=>'图文内容','value'=>$temp_array['content']),
        );
        $component_data = array('_parser'=>'table/detail','title'=>'',
            'fields'=>$fields
        );
        _display($component_data);
    }


    public function ad_remove(){
        $id = _REQUEST('id');
        $sqlstr = "delete from sys_ad where id in ($id)";
        $result = $this -> do_execute($sqlstr);
        sys_out_result($result);
    }



}