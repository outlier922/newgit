<?php
class LotteryAction extends BaseAction{
    public function luckdraw_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $keyid = _REQUEST('keyid');
        $validflag = _REQUEST('validflag');
        $one_classify_id = _REQUEST('one_classify_id');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*,c.name as one_classify_name ";
        $sql_suffix = "from sys_shop mt ";
        $sql_suffix .= "left join sys_classify c on mt.one_classify_id=c.id ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "mt.id desc";

        //筛选数据
        if($keyid) $sql_suffix .= "and (mt.id='$keyid' or mt.name='$keyid') ";
        if($validflag) $sql_suffix .= "and mt.validflag='$validflag' ";
        if($one_classify_id) $sql_suffix .= "and mt.one_classify_id='$one_classify_id' ";
        //查询数据
		$list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
		$set = $this->get_list_bysql("select * from sys_set where id=1");
        foreach($list_items as $k=>&$v){
	        $v['luckdraw_rate'] = $set[0]['luckdraw_rate'];
	        $v['guess_rate'] = $set[0]['guess_rate'];
	        if($v['redbag_type'] == 1){
		        $v['redbag'] = $v['gu_redbag'];
	        }else{
		        $v['redbag'] = $v['qu_minredbag'].'-'.$v['qu_maxredbag'];
	        }
	        if($v['score_type'] == 1){
		        $v['score'] = $v['gu_score'];
	        }else{
		        $v['score'] = $v['qu_minscore'].'-'.$v['qu_maxscore'];
	        }
        }
        int_to_string($list_items,array(
            'validflag'=>array('1'=>'正常','2'=>'冻结','3'=>'下架'),
            'redbag_type'=>array('1'=>'固定红包','2'=>'区间红包'),
            'score_type'=>array('1'=>'固定积分','2'=>'区间积分'),
        ));
        
        //UI部分
        $breadcrumb_data = '首页,抽奖管理,商家抽奖管理';
        $search_items = array(
        	array('name'=>'keyid','placeholder'=>'请输入商家ID、商家名称','cls'=>'w250','_parser'=>'form_item/search/input'),
            array('name'=>'validflag','_parser'=>'form_item/search/select','data'=>array(''=>'商家状态','1'=>'正常','2'=>'冻结','3'=>'下架'),'style'=>'width:120px;'),
            ['name'=>'cascade_1','label'=>'选择分类',
                '_parser'=>'form_item/search/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,'style'=>'width:120px;',
                'related'=>[
                    ['name'=>'one_classify_id','label'=>'商家类型'],
                ],
                'init_url'=>U(MODULE_NAME.'/classify_list'),
            ],     
        );
        $buttons = array(
        	array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Lottery/luckdraw_save'),'url_param'=>array('id'=>'2_id')
            ), 
        	array('text'=>'指定红包/积分规则','title'=>'指定商家红包/积分规则','icon'=>'add','full'=>0,'checked'=>2,'position'=>1,
                'target'=>'inner_frame','url'=>U('Lottery/luckdraw_save'),'url_param' => array('id'=>'2_id')
            ),    
            array('text'=>'设置全局抽奖概率','title'=>'全局抽奖概率设置','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('Lottery/set'),
            ),     
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w100','title'=>'商家ID'),
            array('name'=>'name','cls'=>'w100','title'=>'商家名称'),
            array('name'=>'one_classify_name','cls'=>'w100','title'=>'商家类型'),
            array('name'=>'validflag_text','cls'=>'w100','title'=>'商家状态'),
            array('name'=>'luckdraw_rate','cls'=>'w100','title'=>'抽奖出现概率'),
            array('name'=>'guess_rate','cls'=>'w100','title'=>'猜一猜出现概率'),
            array('name'=>'redbag_rate','cls'=>'w100','title'=>'红包概率'),
            array('name'=>'redbag_type_text','cls'=>'w100','title'=>'红包规则'),
            array('name'=>'redbag','cls'=>'w100','title'=>'红包额度'),
            array('name'=>'score_rate','cls'=>'w100','title'=>'积分概率'),
            array('name'=>'score_type_text','cls'=>'w100','title'=>'积分规则'),
            array('name'=>'score','cls'=>'w100','title'=>'积分额度'),
            array('name'=>'bad_rate','cls'=>'w100','title'=>'手气不佳概率'),
            array('name'=>'remarks','cls'=>'w100','title'=>'备注'),
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
    

	public function luckdraw_save(){
        if(IS_POST){
            $id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑      
            //获取字段
            $save_fields = array('redbag_rate','score_rate','score_type','redbag_type');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);

            $redbag_rate = _POST('redbag_rate');
            $score_rate = _POST('score_rate');
            $bad_rate = 1-$redbag_rate-$score_rate;
            if($bad_rate < 0){
	            sys_out_fail("红包出现概率与积分出现概率相加不能大于1");	            
            }
	        $fields_str .= ",bad_rate=$bad_rate";
	        
            $score_type = _POST('score_type');
            if($score_type == 1){
	            $gu_score = _POST('gu_score');
	            $gu_scorechance = _POST('gu_scorechance');
	            if(!$gu_score || !$gu_scorechance){
		            sys_out_fail("请将信息填写完毕");
	            }
	            $fields_str .= ",gu_score=$gu_score,gu_scorechance=$gu_scorechance";
            }else{
	            $qu_minscore = _POST('qu_minscore');
	            $qu_maxscore = _POST('qu_maxscore');
	            $qu_scorechance = _POST('qu_scorechance');
	            if(!$qu_minscore || !$qu_maxscore || !$qu_scorechance){
		            sys_out_fail("请将信息填写完毕");
	            }
	            $fields_str .= ",qu_minscore=$qu_minscore,qu_maxscore=$qu_maxscore,qu_scorechance=$qu_scorechance";
            }

            $redbag_type = _POST('redbag_type');
            if($redbag_type == 1){
	            $gu_redbag = _POST('gu_redbag');
	            $gu_redbagchance = _POST('gu_redbagchance');
	            if(!$gu_redbag || !$gu_redbagchance){
		            sys_out_fail("请将信息填写完毕");
	            }
	            $fields_str .= ",gu_redbag=$gu_redbag,gu_redbagchance=$gu_redbagchance";
            }else{
	            $qu_minredbag = _POST('qu_minredbag');
	            $qu_maxredbag = _POST('qu_maxredbag');
	            $qu_redbagchance = _POST('qu_redbagchance');
	            if(!$qu_minredbag || !$qu_maxredbag || !$qu_redbagchance){
		            sys_out_fail("请将信息填写完毕");
	            }
	            $fields_str .= ",qu_minredbag=$qu_minredbag,qu_maxredbag=$qu_maxredbag,qu_redbagchance=$qu_redbagchance";
            }

            $sqlstr = "update sys_shop set $fields_str where id in ($id)";
            $result = $this -> do_execute($sqlstr);
            
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $id_arr = explode(',',$id);
            $count = count($id_arr);
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
            	array('name'=>'id','_parser'=>'form_item/form/hidden','value'=>$id),
                array('_parser'=>'tab/default','_children'=>array(
                    array('label'=>'基本信息','_parser'=>'container/default','_children'=>array(              
                        array('name'=>'redbag_rate','label'=>'红包出现概率','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[0, '最小值0'],
                                'max'=>[1, '最大值1'],
                            ),
                        ),
                        array('name'=>'score_rate','label'=>'积分出现概率','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[0, '最小值0'],
                                'max'=>[1, '最大值1'],
                            ),
                        ),
                        array('name'=>'bad_rate','label'=>'手气不佳概率','readonly'=>'2','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[0, '最小值0'],
                                'max'=>[1, '最大值1'],
                            ),
                        ),
                        array('name'=>'score_type','label'=>'积分类型','required'=>1,
		                    '_parser'=>'form_item/collect/radio',
		                    'data'=>array('1'=>'固定积分','2'=>'区间积分')
		                ),
                        array('name'=>'gu_score','label'=>'固定积分','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'输入固定值',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                        array('name'=>'gu_scorechance','label'=>'固定中奖概率','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'填写中奖概率【小数形式】',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[0, '最小值0'],
                                'max'=>[1, '最大值1'],
                            ),
                        ),
                        array('name'=>'qu_minscore','label'=>'最小积分值','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'输入最小积分值',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                        array('name'=>'qu_maxscore','label'=>'最大积分值','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'输入最大积分值',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                        array('name'=>'qu_scorechance','label'=>'区间中奖概率','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'填写中奖概率【小数形式】',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[0, '最小值0'],
                                'max'=>[1, '最大值1'],
                            ),
                        ),
                        array('name'=>'redbag_type','label'=>'红包类型','required'=>1,
		                    '_parser'=>'form_item/collect/radio',
		                    'data'=>array('1'=>'固定红包','2'=>'区间红包')
		                ),
                        array('name'=>'gu_redbag','label'=>'固定红包','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'输入固定值',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                        array('name'=>'gu_redbagchance','label'=>'固定中奖概率','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'填写中奖概率【小数形式】',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[0, '最小值0'],
                                'max'=>[1, '最大值1'],
                            ),
                        ),
                        array('name'=>'qu_minredbag','label'=>'最小红包值','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'输入最小红包值',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                        array('name'=>'qu_maxredbag','label'=>'最大红包值','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'输入最大红包值',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                        array('name'=>'qu_redbagchance','label'=>'区间中奖概率','required'=>0,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'填写中奖概率【小数形式】',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[0, '最小值0'],
                                'max'=>[1, '最大值1'],
                            ),
                        ),
                    ))
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则

            //取值
            if($count == 1){
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


    public function set(){
        if(IS_POST){
            $GLOBALS['cur_operate'] = 2;//目前的操作类型，2^0:新增;2^1:编辑
            //获取字段
            $save_fields = array('luckdraw_rate');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);
            $luckdraw_rate = $_POST['luckdraw_rate'];
            $guess_rate = 1-$luckdraw_rate;
            $fields_str .= ",guess_rate='$guess_rate'";
            $sqlstr = "update sys_set set $fields_str where id=1";
            $result = $this -> do_execute($sqlstr);           
            sys_out_result($result);
        }
        else{
            $GLOBALS['cur_operate'] = 2;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
                array('_parser'=>'tab/default','_children'=>array(
                    array('label'=>'基本信息','_parser'=>'container/default','_children'=>array(
                        array('name'=>'luckdraw_rate','label'=>'全局抽奖出现概率','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[0, '最小值0'],
                                'max'=>[1, '最大值1'],
                            ),
                        ),
                        array('name'=>'guess_rate','label'=>'全局猜一猜出现概率','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text','readonly'=>2
                        ),
                    ))
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则
            //取值
            $field_list = "mt.*";
            $sql_suffix = "from sys_set mt ";
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


    public function image_text_list($_action_access=0){
        //访问控制变量
        $GLOBALS['_action_access'] = $_action_access;
        //声明变量
        $GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $shop_id = _REQUEST('shop_id');
        $type = _REQUEST('type');
        if(_POST('search-flag')){
            unset($_POST['page']);
            unset($_GET['page']);
        }
        //基本查询
        $field_list = "mt.*";
        $sql_suffix = "from sys_image_text mt ";
        $sql_suffix .= "where mt.id>0 ";
        $orderby_str = "mt.id desc";

        //筛选数据
        if($shop_id) $sql_suffix .= "and mt.shop_id='$shop_id' ";
        if($type) $sql_suffix .= "and mt.type='$type' ";
        //查询数据
        if ($export) $list_items=$this->get_list_bysql("select $field_list $sql_suffix order by $orderby_str");
        else {
            $list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
        }
        int_to_string($list_items,array(
            'type'=>array('1'=>'固定积分','2'=>'区间积分'),
        ));
        
        //UI部分
        $breadcrumb_data = '首页,抽奖管理,图文抽奖管理';
        $search_items = array(
        	array('name'=>'shop_id','placeholder'=>'请输入商家ID','cls'=>'w250','_parser'=>'form_item/search/input'),
            array('name'=>'type','_parser'=>'form_item/search/select','data'=>array(''=>'积分类型','1'=>'固定积分','2'=>'区间积分'),'style'=>'width:120px;'),        
        );
        $buttons = array(
        	array('text'=>'新建图题','title'=>'新建图题','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('Lottery/image_text_save'),
            ),
        	array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
                'target'=>'inner_frame','url'=>U('Lottery/image_text_save'),'url_param'=>array('id'=>'2_id')
            ), 
        	array('text'=>'答对赠积分设置','title'=>'答对赠积分设置','icon'=>'add','full'=>0,'position'=>1,
                'target'=>'inner_frame','url'=>U('Lottery/image_text_set'),
            ),
            array('text'=>'删除','title'=>'删除','icon'=>'del','full'=>1,'checked'=>1,'position'=>2,
                'target'=>'inner_confirm','url'=>U('Lottery/image_text_remove'),'url_param'=>array('id'=>'2_id')
            ),          
        );
        $table_headers = array(
            array('name'=>'id','cls'=>'w60','title'=>'ID'),
            array('name'=>'image','title'=>'题图','cls'=>'w50','_after_parser'=>array(
                '_parser'=>'image/thumb','imgurl'=>'3_image','imgurlbig'=>'3_bigimage'
            )),
            array('name'=>'answer','cls'=>'w100','title'=>'正确答案'),
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
    

	public function image_text_set(){
        if(IS_POST){
            $GLOBALS['cur_operate'] = 2;//目前的操作类型，2^0:新增;2^1:编辑           
            //获取字段
            $save_fields = array('gu_score','gu_chance','qu_minscore','qu_maxscore','qu_chance');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);

            $sqlstr = "update sys_set_image_text set $fields_str where id=1";
            $result = $this -> do_execute($sqlstr);
            
            sys_out_result($result);
        }
        else{
            $GLOBALS['cur_operate'] = 2;//目前的操作类型，2^0:新增;2^1:编辑
            $form_items = array(
                array('_parser'=>'tab/default','_children'=>array(
                    array('label'=>'基本信息','_parser'=>'container/default','_children'=>array(              
                        array('name'=>'gu_score','label'=>'固定积分值','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'输入固定积分值',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                        array('name'=>'gu_chance','label'=>'固定积分中奖概率','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'填写中奖概率【小数形式】',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
                        array('name'=>'qu_minscore','label'=>'区间最小积分','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'输入最小积分值',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                        array('name'=>'qu_maxscore','label'=>'区间最大积分','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'输入最大积分值',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字"),
                                'min'=>[1, '最小值1'],
                            ),
                        ),
                        array('name'=>'qu_chance','label'=>'区间积分中奖概率','required'=>1,
                            '_parser'=>'form_item/form/input','type'=>'text',
                            'placeholder'=>'填写中奖概率【小数形式】',
                            '_validation'=>array(
                                'isNumber'=>array(true,"必须是数字")
                            ),
                        ),
                    ))
                )),
            );
            form_validation_create($form_items,$rules,$messages);//获取验证规则

            //取值
            $field_list = "mt.*";
            $sql_suffix = "from sys_set_image_text mt ";
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


    public function image_text_save(){
        if(IS_POST){
			$id = _POST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            $shop_id = $_POST['shop_id'];
            $shop_list = $this->get_list_bysql("select * from sys_shop where id = '$shop_id'");
            if(!$shop_list){
	            sys_out_fail('不存在该商家ID', 106);
            }
            //获取字段
            $save_fields = array('answer','type','shop_id');
            $post_keys = array_keys($_POST);
            $post_fields = array_intersect($save_fields,$post_keys);//取公共
            $fields_str = fields2SqlStrByPost($post_fields);           

            if($id){//修改
            	if (!empty($_FILES['temp_file']['name'])) {
	                //$upload_array = sys_upload_file(1,1024,1024);
	                //$fields_str .= ",image='$upload_array[1]',bigimage='$upload_array[0]'";
					$temp_name =explode(".",$_FILES['temp_file']['name']);
					$ext_name =strtolower(end($temp_name));
					//首先按年月创建对应目录
					sys_mkdir(SYS_ROOT_PATH."uploadfiles/".date("Y")."/".date("m"));	
					//批量传递时，秒级别有可能覆盖，故再加个4位随机数,uploadfiles必须手动在项目根目录创建，且开放IIS来宾账户写权限	
					$save_file=SYS_ROOT_PATH."uploadfiles/".date("Y")."/".date("m")."/".sys_get_no().".".$ext_name;//重新命名，防止不同用户上传同名文件
					//PHP实际上是把你上传的文件从临时目录移动到指定目录,这句最关键,copy比 move_uploaded_file 通用性更强			
					move_uploaded_file($_FILES['temp_file']['tmp_name'],$save_file);//

					$imgurl = SYS_ROOT.substr($save_file,mb_strlen(SYS_ROOT_PATH));

					$fields_str .= ",image='$imgurl',bigimage='$imgurl'";
	            }
                $sqlstr = "update sys_image_text set $fields_str where id=$id";
                $result = $this -> do_execute($sqlstr);
            }
            else{//新增
            	//题图
	            if (!empty($_FILES['temp_file']['name'])) {
	                //$upload_array = sys_upload_file(1,1024,1024);
	                //$fields_str .= ",image='$upload_array[1]',bigimage='$upload_array[0]'";
					$temp_name =explode(".",$_FILES['temp_file']['name']);
					$ext_name =strtolower(end($temp_name));
					//首先按年月创建对应目录
					sys_mkdir(SYS_ROOT_PATH."uploadfiles/".date("Y")."/".date("m"));	
					//批量传递时，秒级别有可能覆盖，故再加个4位随机数,uploadfiles必须手动在项目根目录创建，且开放IIS来宾账户写权限	
					$save_file=SYS_ROOT_PATH."uploadfiles/".date("Y")."/".date("m")."/".sys_get_no().".".$ext_name;//重新命名，防止不同用户上传同名文件
					//PHP实际上是把你上传的文件从临时目录移动到指定目录,这句最关键,copy比 move_uploaded_file 通用性更强			
					move_uploaded_file($_FILES['temp_file']['tmp_name'],$save_file);//

					$imgurl = SYS_ROOT.substr($save_file,mb_strlen(SYS_ROOT_PATH));

					$fields_str .= ",image='$imgurl',bigimage='$imgurl'";
	                
	            }else{
		            sys_out_fail('请先上传题图', 106);
	            }
            	$score_list = $this->get_list_bysql("select * from sys_image_text where shop_id = '$shop_id'");
	            if($score_list){
		            sys_out_fail('该商家已设置积分类型', 106);
	            }
                $sqlstr = "insert into sys_image_text set $fields_str";
                $result = $this -> do_execute($sqlstr);
            }
            sys_out_result($result);
        }
        else{
            $id = _REQUEST('id');
            $GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
            if($id){
	            $form_items = array(
	            	array('name'=>'id','_parser'=>'form_item/form/hidden'),
	                array('_parser'=>'tab/default','_children'=>array(
	                    array('label'=>'基本信息','_parser'=>'container/default','_children'=>array(
	                    	array('name'=>'shop_id','label'=>'商家ID','required'=>1,
	                            '_parser'=>'form_item/form/input','type'=>'text',
	                            'readonly'=>2,
	                        ),
	                        array('name'=>'type','label'=>'红包类型：','_parser'=>'form_item/collect/radio','data'=>array('1'=>'固定额度','2'=>'区间额度'),),
	                        array('name'=>'image','label'=>'题图',
			                    '_parser'=>'form_item/form/image','required'=>1,
			                ),
							array('name'=>'answer','label'=>'正确答案',
								'_parser'=>'form_item/form/input','type'=>'text','required'=>1,
							),                       
	                    ))
	                )),
	            );
	            form_validation_create($form_items,$rules,$messages);//获取验证规则
	            //取值
                $field_list = "mt.*";
                $sql_suffix = "from sys_image_text mt ";
                $sql_suffix .= "where mt.id=$id ";
                $temp_array = $this->get_list_bysql("select $field_list $sql_suffix");
                form_item_add_value($form_items,$temp_array[0]);//赋值
            }else{
	            $form_items = array(
	            	array('name'=>'shop_id','label'=>'商家ID','required'=>1,
                        '_parser'=>'form_item/form/input','type'=>'text',
                        '_validation'=>array(
                            'isNumber'=>array(true,"必须是数字")
                        ),
                    ),
                    array('name'=>'type','label'=>'红包类型：','_parser'=>'form_item/collect/radio','data'=>array('1'=>'固定额度','2'=>'区间额度'),),
	                array('name'=>'image','label'=>'题图',
	                    '_parser'=>'form_item/form/image','required'=>1,
	                ),
					array('name'=>'answer','label'=>'正确答案',
						'_parser'=>'form_item/form/input','type'=>'text','required'=>1,
					),
	            );
	            form_validation_create($form_items,$rules,$messages);//获取验证规则
            }
            
            $component_data = array('_parser'=>'form/default',
                'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
                'rules' => $rules,'messages' => $messages,
            );
            _display($component_data);
        }
    }

    public function image_text_remove(){
        $id = _REQUEST('id');
        $sqlstr = "delete from sys_image_text where id in ($id)";
        $result = $this -> do_execute($sqlstr);
        sys_out_result($result);
    }	
}