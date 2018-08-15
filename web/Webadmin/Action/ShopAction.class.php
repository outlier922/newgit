<?php

class ShopAction extends BaseAction
{
	public function shop_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $name = _REQUEST('name');
        $validflag = _REQUEST('validflag');
        $one_classify_id = _REQUEST('type_1_id');
        $two_classify_id = _REQUEST('type_2_id');
        $salenum = _REQUEST('salenum');
        $star = _REQUEST('star');
        $starflag = _REQUEST('starflag');
        $turnover = _REQUEST('turnover');
        $redbag = _REQUEST('redbag');
        $wealth_redbag = _REQUEST('wealth_redbag');
        $recharge_redbag = _REQUEST('recharge_redbag');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*,c.name as one_classify_name,cl.name as two_classify_name ";
        $sql_suffix = "from sys_shop mt ";
        $sql_suffix .= "left join sys_classify c on mt.one_classify_id = c.id ";
        $sql_suffix .= "left join sys_classify cl on mt.two_classify_id = cl.id ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "";

        //筛选数据
        if($name) $sql_suffix .= "and (mt.name like '%$name%' or mt.id like '%$name%') ";
        if($validflag) $sql_suffix .= "and mt.validflag='$validflag' ";
        if($star) $sql_suffix .= "and mt.star='$star' ";
        if($one_classify_id != 0 && $two_classify_id == 0){
	        $sql_suffix .= "and mt.one_classify_id='$one_classify_id' ";
        }
        if($one_classify_id != 0 && $two_classify_id != 0){
	        $sql_suffix .= "and mt.one_classify_id='$one_classify_id' and mt.two_classify_id='$two_classify_id' ";
        }
        if($salenum == 1){
	        $orderby_str .= "mt.salenum desc,";
        }else if($salenum == 2){
	        $orderby_str .= "mt.salenum asc,";
        }
        if($starflag == 1){
	        $orderby_str .= "mt.star desc,";
        }else if($starflag == 2){
	        $orderby_str .= "mt.star asc,";
        }
        if($turnover == 1){
	        $orderby_str .= "mt.turnover desc,";
        }else if($turnover == 2){
	        $orderby_str .= "mt.turnover asc,";
        }
        if($redbag == 1){
	        $orderby_str .= "mt.redbag desc,";
        }else if($redbag == 2){
	        $orderby_str .= "mt.redbag asc,";
        }
        if($wealth_redbag == 1){
	        $orderby_str .= "mt.wealth_redbag desc,";
        }else if($wealth_redbag == 2){
	        $orderby_str .= "mt.wealth_redbag asc,";
        }
        if($recharge_redbag == 1){
	        $orderby_str .= "mt.recharge_redbag desc,";
        }else if($recharge_redbag == 2){
	        $orderby_str .= "mt.recharge_redbag asc,";
        }
        if(!$orderby_str){
	        $orderby_str = "mt.id desc";
        }else{
		    $orderby_str = substr($orderby_str,0,-1);
	    }
        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
            'validflag'=>array('1'=>'正常','2'=>'冻结'),
            'star'=>array('1'=>'1颗星','2'=>'2颗星','3'=>'3颗星','4'=>'4颗星','5'=>'5颗星'),
            'service_type'=>array('1'=>'比率','2'=>'固定'),
            'isrecommend'=>array('1'=>'推荐','2'=>'不推荐'),
        ));
        
        //UI部分
        $breadcrumb_data = '首页,商城管理,商家管理';
        $search_items = array(
        	array('name'=>'name','placeholder'=>'请输入商家ID、商家名称','cls'=>'w250','_parser'=>'form_item/search/input'),
            array('name'=>'validflag','_parser'=>'form_item/search/select','data'=>array(''=>'商家状态','1'=>'正常','2'=>'冻结','3'=>'下架'),'style'=>'width:120px;'),
            array('name'=>'star','_parser'=>'form_item/search/select','data'=>array(''=>'商家星级','1'=>'一星','2'=>'二星','3'=>'三星','4'=>'四星','5'=>'五星'),'style'=>'width:120px;'),
            ['name'=>'cascade_1','label'=>'选择分类',
                '_parser'=>'form_item/search/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,
                'related'=>[
                    ['name'=>'type_1_id','label'=>'一级分类'],
                    ['name'=>'type_2_id','label'=>'二级分类'],
                ],
                'init_url'=>U(MODULE_NAME.'/classify_list'),
            ],
            array('name'=>'salenum','_parser'=>'form_item/search/select','data'=>array(''=>'成交总量','1'=>'从高到低','2'=>'从低到高'),'style'=>'width:120px;'),
            array('name'=>'starflag','_parser'=>'form_item/search/select','data'=>array(''=>'商家评价','1'=>'从高到低','2'=>'从低到高'),'style'=>'width:120px;'),
            array('name'=>'turnover','_parser'=>'form_item/search/select','data'=>array(''=>'商家营业额','1'=>'从高到低','2'=>'从低到高'),'style'=>'width:120px;'),
            array('name'=>'redbag','_parser'=>'form_item/search/select','data'=>array(''=>'商家红包池','1'=>'从高到低','2'=>'从低到高'),'style'=>'width:120px;'),
            array('name'=>'wealth_redbag','_parser'=>'form_item/search/select','data'=>array(''=>'财气红包池','1'=>'从高到低','2'=>'从低到高'),'style'=>'width:120px;'),
            array('name'=>'recharge_redbag','_parser'=>'form_item/search/select','data'=>array(''=>'充值红包池','1'=>'从高到低','2'=>'从低到高'),'style'=>'width:120px;'),
        );
        $buttons = array(
            array('text'=>'新建商家','title'=>'新建商家','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('Shop/shop_save'),
            ),           
            array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Shop/shop_save'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'指定相关比率','title'=>'指定相关比率','full'=>0,'checked'=>1,'position'=>1,
                'target'=>'inner_frame','url'=>U('Shop/rate_save'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'商家红包池充值','title'=>'商家红包池充值','full'=>0,'checked'=>1,'position'=>1,
                'target'=>'inner_frame','url'=>U('Shop/redbag_save'),'url_param'=>array('id'=>'2_id')
            ),
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'商家ID'),
            array('name'=>'img','title'=>'商家封面','cls'=>'w50','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_img','imgurlbig'=>'3_bigimg'
            )),
            ['name'=>'','title'=>'账号信息','cls'=>'w180','_after_parser'=>[
                '_parser'=>'td/card','_children'=>[
                    ['title'=>'商家名称：','value'=>'3_name'],
                    ['title'=>'商家账号：','value'=>'3_username'],
                    ['title'=>'一级分类：','value'=>'3_one_classify_name'],
                    ['title'=>'二级分类：','value'=>'3_two_classify_name'],
                    ['title'=>'商家联系电话：','value'=>'3_telphone'],
                    ['title'=>'商家联系人：','value'=>'3_linker'],
                    ['title'=>'商家开启时间：','value'=>'3_opentime'],
                    ['title'=>'商家营业额：','value'=>'3_turnover'],
                    ['title'=>'成交总量：','value'=>'3_salenum'],
                    ['title'=>'商家评价：','value'=>'3_star_text'],
                    ['title'=>'商家状态：','value'=>'3_validflag_text'],
                    ['title'=>'商家到账率：','value'=>'3_arrival_rate'],
                    ['title'=>'财气转换率：','value'=>'3_wealth_rate'],
                ]
            ]],
            array('name'=>'isrecommend_text','cls'=>'w60','title'=>'是否推荐'),
            array('name'=>'averfee','cls'=>'w60','title'=>'人均价'),
            array('name'=>'orderby','cls'=>'w60','title'=>'推荐排序'),
            array('name'=>'','title'=>'商家红包池','cls'=>'w60','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'3_redbag','title'=>'1_商家红包池','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Shop/redbags_save','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'','title'=>'财气红包池','cls'=>'w60','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'3_wealth_redbag','title'=>'1_财气红包池','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Shop/wealth_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'','title'=>'充值红包池','cls'=>'w60','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'3_recharge_redbag','title'=>'1_充值红包池','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Shop/recharge_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'','title'=>'商家详情','cls'=>'w50','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'查看','title'=>'1_查看商家详情','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Shop/indexshop_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'address','cls'=>'w100','title'=>'商家地址'),
            //array('name'=>'address','cls'=>'w100','title'=>'地图定位'),
            array('name'=>'service_type_text','cls'=>'w60','title'=>'服务费类型'),
            array('name'=>'service_rate','cls'=>'w60','title'=>'服务费比率'),
            array('name'=>'service_fee','cls'=>'w60','title'=>'每笔交易固定服务费'),
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


    // 分级获取课程分类
    public function classify_list(){
        $parentid = _REQUEST('parentid');
        $parentid = $parentid ? $parentid : 0;
        $sqlstr = "select id,name from sys_classify where parentid=$parentid and flag=1";
        $temp_array = $this -> get_list_bysql($sqlstr);
        sys_out_success(0,$temp_array);
    }


    public function indexshop_get(){
        $id = _REQUEST('id');
        if(!$id) sys_out_fail("参数传递不正确");

        $sqlstr = "select * from sys_shop where id =$id";
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


    public function shop_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $content = $_POST['content'];
            $content=str_replace("'","\\'",$content);
            $remarks = $_POST['remarks'];
            $district_1_id = $_POST['district_1_id'];
        	$district_2_id = $_POST['district_2_id'];
        	$district_3_id = $_POST['district_3_id'];
        	if(!$district_3_id){
	        	sys_out_fail("商家地址请选择到区县");
        	}
            $isrecommend = $_POST['isrecommend'];
            $orderby = $_POST['orderby'];
            if($isrecommend == 1){
	            if(!$orderby){
		            sys_out_fail("请填写排序");
	            }
	            $province = $this->get_one_bysql("select name from sys_cascade_district where id=$district_1_id");
	            if(strpos($province, '市') === false){
		            $city = $this->get_one_bysql("select name from sys_cascade_district where id=$district_2_id");
		            $orderonly = $this->get_one_bysql("select orderby from sys_shop where district_2_id=$district_2_id and orderby=$orderby and id!=$id");
	            }else{
		            $orderonly = $this->get_one_bysql("select orderby from sys_shop where district_1_id=$district_1_id and orderby=$orderby and id!=$id");
	            }
	            if($orderonly){
					sys_out_fail("改市区已设置该排序次数");
		        }	            
            }
            $username = $_POST['username'];
            $shop_id = $this->get_one_bysql("select id from sys_shop where username='$username'");
            if($shop_id){
	            sys_out_fail("该账号已存在");
            }
            //获取字段
            $save_fields = array('name','username','linker','telphone','validflag','lng','lat','isrecommend','averfee','orderby');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            $opentime_start = I('opentime_start');
    		$opentime_end = I('opentime_end');
    		$one_classify_id = I('one_classify_id');
    		$two_classify_id = I('two_classify_id');
    		if(!$one_classify_id || !$two_classify_id){
	    		sys_out_fail("类别请选择到二级分类");
    		}
        	$tag = $_POST['tag'];
        	$address = $_POST['addresses'];
        	if($tag){
	        	$opentime = '24H';
        	}else{
	        	if(!$opentime_start || !$opentime_end){
		        	sys_out_fail("营业时间未填写");
	        	}
	        	$opentime = $opentime_start.'—'.$opentime_end;
        	}       	
        	$address_head = $this->get_one_bysql("select namepath from sys_cascade_district where id=$district_3_id");
        	$address_head_arr = explode(',',$address_head);
        	$add = '';
        	foreach($address_head_arr as $v){
	        	$add .= $v;
        	}
        	$add .= $address;
            $fields_str .= ",content='$content',remarks='$remarks',one_classify_id=$one_classify_id,two_classify_id=$two_classify_id,district_1_id=$district_1_id,district_2_id=$district_2_id,district_3_id=$district_3_id,address='$add',opentime='$opentime'";		
			
            if($id){//修改
            	//商家封面
				$img = $this->get_one_bysql("select img from sys_shop where id=$id");
	            if(!$img){
					if (empty($_FILES['temp_file']['name'])) {
						sys_out_fail("请上传商家封面");
					}
				}
	            if (!empty($_FILES['temp_file']['name'])) {
	                $upload_array = sys_upload_file(1,600,240);
	                $fields_str .= ",img='$upload_array[1]',bigimg='$upload_array[0]'";
	            }
                $sqlstr = "update sys_shop set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }
            else{//新增
            	if (!empty($_FILES['temp_file']['name'])) {
	                $upload_array = sys_upload_file(1,600,240);
	                $fields_str .= ",img='$upload_array[1]',bigimg='$upload_array[0]'";
	            }else{
		            sys_out_fail("请上传商家封面");
	            }
                $regdate = sys_get_time();
                $password = md5('123456');
                $fields_str .= ",regdate='$regdate',password='$password'";
                $sqlstr = "insert into sys_shop set $fields_str";
                $result = $this -> do_execute($sqlstr);
                $shop_id = $this->get_insert_id();
                if ($shop_id) {
					$url = SYS_ROOT.'index.php?id='.$shop_id;
					$qrcode_url = sys_get_qrcode($url);
					$qrcode_big_url = sys_get_big_qrcode($url);
					$sqlstr = " update sys_shop set qrcode='$qrcode_url',qrcode_big='$qrcode_big_url' where id =$shop_id ";
					$this->do_execute($sqlstr);
				} 
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
                        array('name'=>'name','label'=>'商家名称','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                        ),
                        array('name'=>'img','label'=>'商家封面',
		                    '_parser'=>'form_item/form/image','required'=>0,
		                ),
                        array('name'=>'username','label'=>'商家账号','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                        ),
                        ['name'=>'cascade_1','label'=>'选择分类',
                            '_parser'=>'form_item/form/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,
                            'related'=>[
                                ['name'=>'one_classify_id','label'=>'一级分类'],
                                ['name'=>'two_classify_id','label'=>'二级分类'],
                            ],
                            'init_url'=>U(MODULE_NAME.'/classify_list'),
                        ],
                        array('name'=>'validflag','label'=>'商家状态',
		                    '_parser'=>'form_item/collect/radio',
		                    'data'=>array('1'=>'正常','2'=>'冻结')
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
		                array('name'=>'averfee','label'=>'人均价','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                        array('name'=>'linker','label'=>'联系人','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                        ),
                        array('name'=>'telphone','label'=>'联系电话','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                        ),
                        array('name'=>'opentime','label'=>'营业时间','required'=>1,
                            '_parser'=>'form_item/form/time','type'=>'text',
                        ),   
                        ['name'=>'tag','label'=>'','_parser'=>'form_item/form/checkbox_one','required'=>0,
                            'data'=>array('1'=>'24H营业'),
                        ],                                        
                        ['name'=>'cascade_1','label'=>'所属地区',
		                    '_parser'=>'form_item/form/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,
		                    'related'=>[
		                        ['name'=>'district_1_id','label'=>'选择省份'],
		                        ['name'=>'district_2_id','label'=>'选择城市'],
		                        ['name'=>'district_3_id','label'=>'选择区县'],
		                    ],
		                    'init_url'=>U(MODULE_NAME.'/district_select_list'),
		                ],
		                array('name'=>'addresses','label'=>'详细地址','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                        ),
		                array('name'=>'address','label'=>'地图选点','required'=>1,
                            '_parser'=>'form_item/form/amap','type'=>'text',
                        ),
                        array('name'=>'lng','label'=>'经度','required'=>1,
                            '_parser'=>'form_item/form/amap','type'=>'text',
                        ),
                        array('name'=>'lat','label'=>'纬度','required'=>1,
                            '_parser'=>'form_item/form/amap','type'=>'text',
                        ),
                        array('name'=>'remarks','label'=>'备注信息','placeholder'=>'最长140个字符',
                            '_parser'=>'form_item/form/textarea','required'=>0,
                            '_validation'=>array(
                                'maxlength'=>array(140,"最长140个字符")
                            ),
                        ),
                    )),
                    array('label'=>'商家详情','_parser'=>'container/default','_children'=>array(
                        array('name'=>'content','label'=>'商家详情',
                            '_parser'=>'form_item/form/rich','required'=>1,
                        ),
                    ))
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_shop mt ";
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


    public function rate_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
			$save_fields = array('arrival_rate','wealth_rate','service_type');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            $service_type = _POST('service_type');
            $service_fee = _POST('service_fee');
            $arrival_rate = _POST('arrival_rate');
        	$wealth_rate = _POST('wealth_rate');            
            if($service_type == 1){  
            	$service_rate = 1-$arrival_rate-$wealth_rate;
            	if($service_rate <= 0){
	            	sys_out_fail("商家到账率与财气转换率相加不能大于等于1");
            	}
	            $fields_str .= ",service_rate=$service_rate";
            }else{
	            $rate = $arrival_rate+$wealth_rate;
	            if($rate > 1){
		            sys_out_fail("商家到账率与财气转换率相加不能大于1");
	            }
	            if(!$service_fee){
		            sys_out_fail("请填写每笔定额");
	            }
	            $fields_str .= ",service_fee=$service_fee";
            }
            
            if($id){//修改
                $sqlstr = "update sys_shop set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
				array('name'=>'id','_parser'=>'form_item/form/hidden'),
				['name'=>'arrival_rate','label'=>'商家到账率','placeholder'=>'','required'=>1,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    '_validation'=>[
                        'number'=>[true,'必须是数字'],
                        'min'=>[0,'必须大于等于0'],
                        'max'=>[1,'必须小于等于1'],
                    ],
                ],
				['name'=>'wealth_rate','label'=>'财气转换率','placeholder'=>'','required'=>1,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    '_validation'=>[
                        'number'=>[true,'必须是数字'],
                        'min'=>[0,'必须大于等于0'],
                        'max'=>[1,'必须小于等于1'],
                    ],
                ],
                array('name'=>'service_type','label'=>'服务费类型',
                    '_parser'=>'form_item/collect/radio',
                    'data'=>array('1'=>'每笔比率','2'=>'每笔固定')
                ),
                ['name'=>'service_fee','label'=>'每笔定额','placeholder'=>'','required'=>0,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    '_validation'=>[
                        'number'=>[true,'必须是数字'],
                    ],
                ],
			);
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_shop mt ";
                $sql_suffix .= "where mt.id=$id";
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


    
    public function redbag_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $fee = _POST('fee');
            $regdate = sys_get_time();
            if($id){//修改
            	$sql_array = NULL;
            	$add_before = $this->get_one_bysql("select recharge_redbag from sys_shop where id=$id");
            	$add_after = $add_before + $fee;
                $sql_array[] = "update sys_shop set recharge_redbag=recharge_redbag+$fee,redbag=redbag+$fee where id=$id";
                $sql_array[] = "insert into sys_recharge_redbag set type=1,fee='$fee',shop_id=$id,regdate='$regdate',add_before=$add_before,add_after=$add_after";
                $result = $this->do_transaction($sql_array);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
				array('name'=>'id','_parser'=>'form_item/form/hidden'),
                ['name'=>'fee','label'=>'输入充值金额','placeholder'=>'','required'=>0,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    '_validation'=>[
                        'number'=>[true,'必须是数字'],
                        'min'=>[0,'必须大于0'],
                    ],
                ],
			);
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_shop mt ";
                $sql_suffix .= "where mt.id=$id";
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

    
    public function redbags_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $fee = _POST('fee');
            $regdate = sys_get_time();
            if($id){//修改
                $sql_array = NULL;
                $add_before = $this->get_one_bysql("select recharge_redbag from sys_shop where id=$id");
            	$add_after = $add_before + $fee;
                $sql_array[] = "update sys_shop set recharge_redbag=recharge_redbag+$fee,redbag=redbag+$fee where id=$id";
                $sql_array[] = "insert into sys_recharge_redbag set type=1,fee='$fee',shop_id=$id,regdate='$regdate',add_before=$add_before,add_after=$add_after";
                $result = $this->do_transaction($sql_array);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
				array('name'=>'id','_parser'=>'form_item/form/hidden'),
				['name'=>'wealth_redbag','label'=>'财气红包池','placeholder'=>'','readonly'=>2,
                    '_parser'=>'form_item/form/input','type'=>'text',
                ],
				['name'=>'recharge_redbag','label'=>'充值红包池','placeholder'=>'','readonly'=>2,
                    '_parser'=>'form_item/form/input','type'=>'text',
                ],
                ['name'=>'fee','label'=>'输入充值金额','placeholder'=>'','required'=>0,
                    '_parser'=>'form_item/form/input','type'=>'text',
                    '_validation'=>[
                        'number'=>[true,'必须是数字'],
                        'min'=>[0,'必须大于0'],
                    ],
                ],
			);
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_shop mt ";
                $sql_suffix .= "where mt.id=$id";
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
        $sql_suffix = "from sys_wealth_redbag mt ";
        $sql_suffix .= "where mt.shop_id=$id ";
        $orderby_str = "mt.id desc";
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
            'type'=>array('1'=>'用户消费','2'=>'抽奖扣减'),
        ));
        
		$breadcrumb_data = '首页,商家管理,财气红包池明细';
        $table_headers = array(
            array('name'=>'type_text','cls'=>'w60','title'=>'变动原因'),
            array('name'=>'fee','cls'=>'w60','title'=>'额度'),
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


    
    public function recharge_get($_action_access=0){
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
        $sql_suffix = "from sys_recharge_redbag mt ";
        $sql_suffix .= "where mt.shop_id=$id ";
        $orderby_str = "mt.id desc";
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
            'type'=>array('1'=>'商家充值','2'=>'抽奖扣减'),
        ));
        
		$breadcrumb_data = '首页,商家管理,充值红包池明细';
        $table_headers = array(
            array('name'=>'type_text','cls'=>'w60','title'=>'变动原因'),
            array('name'=>'fee','cls'=>'w60','title'=>'额度'),
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


	public function goods_list($_action_access=0){
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
        //基本查询
        $field_list = "mt.*,c.name as classfiyname,s.name as shop_name ";
        $sql_suffix = "from sys_good mt ";
        $sql_suffix .= "left join sys_shop s on mt.shop_id = s.id ";
        $sql_suffix .= "left join sys_classify c on s.one_classify_id = c.id ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "";

        //筛选数据
        if($name) $sql_suffix .= "and (mt.name like '%$name%' or mt.id like '%$name%') ";
        if($commentstar) $sql_suffix .= "and mt.commentstar='$commentstar' ";
        if($salenum == 1){
			$orderby_str .= "mt.salenum desc,";
	    }else if($salenum == 2){
		    $orderby_str .= "mt.salenum asc,";
	    }
        if($commentnum == 1){
			$orderby_str .= "mt.commentnum desc,";
	    }else if($commentnum == 2){
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
            'flag'=>array('1'=>'上架中','2'=>'已下架'),
        ));
        
        //UI部分
        $breadcrumb_data = '首页,商城管理,商家商品管理';
        $search_items = array(
        	array('name'=>'name','placeholder'=>'请输入商品ID、商品名称','cls'=>'w200','_parser'=>'form_item/search/input'),
            array('name'=>'commentstar','_parser'=>'form_item/search/select','data'=>array(''=>'评价等级','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'),'style'=>'width:120px;'),
            array('name'=>'salenum','_parser'=>'form_item/search/select','data'=>array(''=>'销量','1'=>'销量从高到低','2'=>'销量从低到高'),'style'=>'width:120px;'),
            array('name'=>'commentnum','_parser'=>'form_item/search/select','data'=>array(''=>'评论数量','1'=>'评论数量从高到低','2'=>'评论数量从低到高'),'style'=>'width:150px;'),
        );
        $buttons = array(
            array('text'=>'上架/下架','title'=>'上架/下架','icon'=>'edit2','checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Shop/good_flag'),'url_param' => array('id'=>'2_id')
            ),
            array('text'=>'删除','title'=>'删除','icon'=>'del','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Shop/good_remove'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'批量删除','title'=>'批量删除','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_confirm','url'=>U('Shop/good_remove'),'url_param' => array('id'=>'2_id')
            ),
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'商品ID'),
            array('name'=>'name','cls'=>'w60','title'=>'商品名称'),
            array('name'=>'shop_id','cls'=>'w60','title'=>'商家ID'),
            array('name'=>'shop_name','cls'=>'w60','title'=>'商家名称'),
            array('name'=>'flag_text','cls'=>'w60','title'=>'商品状态'),
            array('name'=>'classfiyname','cls'=>'w60','title'=>'商品类别'),
            array('name'=>'','title'=>'商品描述','cls'=>'w50','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'查看','title'=>'1_查看','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Shop/indexgood_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'price','cls'=>'w100','title'=>'商品价格（现价/原价）'),
            array('name'=>'salenum','cls'=>'w100','title'=>'销量'),
            array('name'=>'commentnum','cls'=>'w60','title'=>'评论数量'),
            array('name'=>'commentstar','cls'=>'w60','title'=>'评价等级'),
            array('name'=>'','title'=>'评价内容','cls'=>'w100','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'查看','title'=>'1_查看','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Shop/comment_get','url_param'=>array('id'=>'3_id')
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

	
	public function audit_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $name = _REQUEST('name');
        $doflag = _REQUEST('doflag');
        $one_classify_id = _REQUEST('one_classify_id');
        $tag = _REQUEST('tag');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*,c.name as classfiyname,s.name as shop_name ";
        $sql_suffix = "from sys_good mt ";
        $sql_suffix .= "left join sys_shop s on mt.shop_id = s.id ";
        $sql_suffix .= "left join sys_classify c on s.one_classify_id = c.id ";
        $sql_suffix .= "where mt.id>0 ";

        //筛选数据
        if($name) $sql_suffix .= "and (mt.name like '%$name%' or mt.id like '%$name%' or s.name like '%$name%' or s.id like '%$name%') ";
        if($doflag) $sql_suffix .= "and mt.doflag='$doflag' ";
        if($one_classify_id) $sql_suffix .= "and s.one_classify_id='$one_classify_id' ";
        if($tag == 1){
	        $orderby_str = "mt.doflag asc";
        }else{
	        $orderby_str = "mt.id desc";
        }

        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
            'doflag'=>array('1'=>'待审核','2'=>'已通过','3'=>'已拒绝'),
        ));
        
        //UI部分
        $breadcrumb_data = '首页,商城管理,商家商品管理';
        $search_items = array(
        	array('name'=>'name','placeholder'=>'请输入商品ID、商品名称、商家ID、商家名称','cls'=>'w300','_parser'=>'form_item/search/input'),
            array('name'=>'doflag','_parser'=>'form_item/search/select','data'=>array(''=>'审核状态','1'=>'待审核','2'=>'已通过','3'=>'已拒绝'),'style'=>'width:100px;'),
            ['name'=>'cascade_1','label'=>'商品类别',
                '_parser'=>'form_item/search/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,
                'related'=>[
                    ['name'=>'one_classify_id','label'=>'商品类别'],
                ],
                'init_url'=>U(MODULE_NAME.'/classify_list'),
            ],
            ['name'=>'tag','label'=>'','_parser'=>'form_item/search/checkbox_one','required'=>0,'cls'=>'w300',
                'data'=>array('1'=>'置顶显示待审核商品'),
            ],  
        );
        $buttons = array(
            array('text'=>'处理','title'=>'处理','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Shop/audit_save'),'url_param'=>array('id'=>'2_id')
            ),
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'商品ID'),
            array('name'=>'name','cls'=>'w60','title'=>'商品名称'),
            array('name'=>'shop_id','cls'=>'w60','title'=>'商家ID'),
            array('name'=>'shop_name','cls'=>'w60','title'=>'商家名称'),
            array('name'=>'doflag_text','cls'=>'w60','title'=>'审核状态'),
            array('name'=>'classfiyname','cls'=>'w60','title'=>'商品类别'),
            array('name'=>'','title'=>'商品描述','cls'=>'w50','_after_parser'=>array(
                '_parser'=>'button_item/td_a_get','text'=>'查看','title'=>'1_查看','full'=>0,
                'target'=>'inner_frame',
                'url'=>'Shop/indexgood_get','url_param'=>array('id'=>'3_id')
            )),
            array('name'=>'present_price','cls'=>'w100','title'=>'商品价格'),
            array('name'=>'regdate','cls'=>'w100','title'=>'提交时间'),
            array('name'=>'doregdate','cls'=>'w100','title'=>'审核时间'),
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


    public function audit_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
			$save_fields = array('doflag');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);  
            $doregdate = sys_get_time();
			$fields_str .= ",doregdate='$doregdate'";
            if($id){//修改
                $sqlstr = "update sys_good set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
				array('name'=>'id','_parser'=>'form_item/form/hidden'),
                array('name'=>'doflag','label'=>'审核状态',
                    '_parser'=>'form_item/collect/radio',
                    'data'=>array('2'=>'通过','3'=>'拒绝')
                ),
			);
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            if($id) {
                //取值
                $field_list = "mt.id,mt.doflag";
                $sql_suffix = "from sys_good mt ";
                $sql_suffix .= "where mt.id=$id";
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


    public function report_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $name = _REQUEST('name');
        $regdate_start = _REQUEST('regdate_start');
		$regdate_end = _REQUEST('regdate_end');
		$label_id = _REQUEST('label_id');
		$type_1_id = _REQUEST('type_1_id');
		$type_2_id = _REQUEST('type_2_id');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*,s.name as shop_name,s.username,c1.name as one_classfiy_name,c2.name as two_classfiy_name,c.account ";
        $sql_suffix = "from sys_report mt ";
        $sql_suffix .= "left join sys_shop s on mt.shop_id = s.id ";
        $sql_suffix .= "left join sys_client c on mt.client_id = c.id ";
        $sql_suffix .= "left join sys_classify c1 on s.one_classify_id = c1.id ";
        $sql_suffix .= "left join sys_classify c2 on s.two_classify_id = c2.id ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "";

        //筛选数据
        if($name) $sql_suffix .= "and (s.name like '%$name%' or s.id like '%$name%' or s.username like '%$name%') ";
        if($regdate_start) $sql_suffix .= "and mt.regdate>='$regdate_start' ";
		if($regdate_end) $sql_suffix .= "and mt.regdate<='$regdate_end' ";
		if($label_id) $sql_suffix .= "and mt.label_id like '%$label_id%' ";
		if($type_1_id) $sql_suffix .= "and s.one_classify_id='$type_1_id' ";
		if($type_2_id) $sql_suffix .= "and s.two_classify_id='$type_2_id' ";
        //查询数据
        $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        int_to_string($list_items,array(
        ));
        
        //UI部分
        $breadcrumb_data = '首页,商城管理,举报管理';
        $search_items = array(
        	array('name'=>'name','placeholder'=>'请输入商家名称、商家ID、商家账号','cls'=>'w230','_parser'=>'form_item/search/input'),
        	array('name'=>'regdate','label'=>'举报时间','_parser'=>'form_item/search/date'),
        	['name'=>'cascade_1','label'=>'选择分类',
                '_parser'=>'form_item/search/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,
                'related'=>[
                    ['name'=>'label_id','label'=>'标签'],
                ],
                'init_url'=>U(MODULE_NAME.'/label_list'),
            ],
            ['name'=>'cascade_1','label'=>'选择分类',
                '_parser'=>'form_item/search/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,
                'related'=>[
                    ['name'=>'type_1_id','label'=>'一级分类'],
                    ['name'=>'type_2_id','label'=>'二级分类'],
                ],
                'init_url'=>U(MODULE_NAME.'/classify_list'),
            ],
        );
        $buttons = array(
            array('text'=>'删除','title'=>'删除','icon'=>'del','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Shop/report_remove'),'url_param'=>array('id'=>'2_id')
            ),
            array('text'=>'批量删除','title'=>'批量删除','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_confirm','url'=>U('Shop/report_remove'),'url_param' => array('id'=>'2_id')
            ),
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'ID'),
            array('name'=>'shop_name','cls'=>'w100','title'=>'商家名称'),
            array('name'=>'shop_id','cls'=>'w50','title'=>'商家ID'),
            array('name'=>'username','cls'=>'w60','title'=>'商家账号'),
            array('name'=>'client_id','cls'=>'w60','title'=>'用户ID'),
            array('name'=>'account','cls'=>'w70','title'=>'用户账号'),
            array('name'=>'one_classfiy_name','cls'=>'w70','title'=>'商家一级分类'),
            array('name'=>'two_classfiy_name','cls'=>'w70','title'=>'商家二级分类'),
            array('name'=>'label','cls'=>'w100','title'=>'举报标签'),
            array('name'=>'content','cls'=>'w100','title'=>'举报详情'),
            array('name'=>'regdate','cls'=>'w100','title'=>'提交时间'),
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


    // 获取标签分类
    public function label_list(){
        $sqlstr = "select id,name from sys_label";
        $temp_array = $this -> get_list_bysql($sqlstr);
        sys_out_success(0,$temp_array);
    }


    //删除管理员
	public function report_remove(){
		$id = _POST('id');
		$sqlstr = "delete from sys_report where id in ($id)";
		$result = $this -> do_execute($sqlstr);
		sys_out_result($result);
	}

}