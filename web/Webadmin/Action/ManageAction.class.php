<?php
//管理员相关
class ManageAction extends BaseAction
{
	/**
	+----------------------------------------------------------------------
	|管理员相关
	+----------------------------------------------------------------------
	 */
	//管理员列表
	public function admin_list($_action_access=0){
		//访问控制变量
		$GLOBALS['_action_access'] = $_action_access;
		//声明变量
		$GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
        $realname = $_POST['realname'];
		if(_POST('search-flag')){
			unset($_POST['page']);
			unset($_GET['page']);
		}
		//基本查询
		$field_list = "mt.*";
		$field_list .= ",r.name as role_name";
		$sql_suffix = "from sys_admin mt  ";
		$sql_suffix .= "left join sys_role r on mt.roleid=r.id ";
		$sql_suffix .= "where 1=1 ";
		$orderby_str = "mt.id desc";
		//筛选数据
		if($realname){
			$sql_suffix .= "and mt.realname like '%$realname%' ";
		}
		$list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
		int_to_string($list_items,array(
			'islogin'=>array('0'=>'否','1'=>'是')
		));
		//UI部分
		$breadcrumb_data = '首页,管理员相关,管理员管理';
		$search_items = array(
		    array('name'=>'realname','placeholder'=>'请输入管理员姓名','cls'=>'w250','_parser'=>'form_item/search/input'),
		);
		$buttons = array(
			array('text'=>'添加','title'=>'添加管理员','icon'=>'add','full'=>0,'position'=>1,
				'target'=>'inner_frame','url'=>U('Manage/admin_save'),
			),
			array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
				'target'=>'inner_frame','url'=>U('Manage/admin_save'),'url_param'=>array('id'=>'2_id'),
				'testing'=>array('id'=>array('1'=>'admin管理员禁止修改'))
			),
			array('text'=>'删除','title'=>'删除','full'=>0,'checked'=>1,'position'=>2,
				'target'=>'inner_confirm','url'=>U('Manage/admin_remove'),'url_param' => array('id'=>'2_id')
			),
			array('text'=>'批量删除','title'=>'批量删除','icon'=>'del','full'=>0,'checked'=>2,'position'=>1,
				'target'=>'inner_confirm','url'=>U('Manage/admin_remove'),'url_param' => array('id'=>'2_id')
			),
		);
		$table_headers = array(
			array('name'=>'id','title'=>'ID'),
            array('name'=>'account','title'=>'登录账号'),
            array('name'=>'role_name','title'=>'管理员角色'),
            array('name'=>'realname','title'=>'真实姓名'),
			array('name'=>'islogin_text','title'=>'是否允许登录'),
			array('name'=>'login','title'=>'登录次数'),
			array('name'=>'lastlogintime','title'=>'最后登录时间'),
			array('name'=>'regdate','title'=>'注册时间'),
			array('name'=>'remarks','title'=>'备注')
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
	//保存管理员
	public function admin_save(){
		if(IS_POST){
			$id = _POST('id');
			$GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
			$regdate = sys_get_time();

			//根据post参数生成field字符串
			$loginpass = $_POST['loginpass'];
			if($loginpass){
				$password = md5($loginpass);
			}
			$save_fields = array('account','realname','roleid','islogin','remarks');
			$post_keys = array_keys($_POST);
			$post_fields = array_intersect($save_fields,$post_keys);//取公共
			$fields_str = fields2SqlStrByPost($post_fields);

			if($id){//修改
				if($loginpass){
					$fields_str .= ",password='$password',loginpass='$loginpass'";
				}
				$sqlstr = "update sys_admin set $fields_str where id=$id";
				$result = $this -> do_execute($sqlstr);
			}
			else{//新增
				$account = $_POST['account'];
				if($this -> admin_exists($account)) sys_out_fail("管理员已存在，请换一个！");
				$fields_str .= ",regdate='$regdate',password='$password',loginpass='$loginpass'";
				$sqlstr = "insert into sys_admin set $fields_str";
				$result = $this -> do_execute($sqlstr);
			}
			sys_out_result($result);
		}
		else{
			$id = _REQUEST('id');
			$GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
			$form_items = array(
				array('name'=>'id','_parser'=>'form_item/form/hidden'),
				array('name'=>'account','label'=>'登录账号','placeholder'=>'最多20字符',
					'_parser'=>'form_item/form/input','type'=>'text','readonly'=>2,
					'_validation'=>array(
						'maxlength'=>array(20,"最多20个字符"),
					),
				),
                array('name'=>'realname','label'=>'姓名',
                    '_parser'=>'form_item/form/input','type'=>'text','placeholder'=>'必填字段，最多10个字符',
                    '_validation'=>array(
                        'maxlength'=>array(20,"最长20个字符")
                    ),
                ),
				array('_parser'=>'form_item/form/input','type'=>'password',
					'name'=>'loginpass','label'=>'密码','placeholder'=>'请输入密码，不能低于6位','_operate'=>1,
					'_validation'=>array(
						'minlength'=>array(6,'密码最少6位')
					),
				),
				//array('name'=>'roleid','label'=>'角色',
				//	'_parser'=>'form_item/form/select_bind','type'=>'text','placeholder'=>'必填字段',
				//	'init_url'=>U('Manage/role_select_list'),
				//),
				['name'=>'cascade_1','label'=>'角色',
                    '_parser'=>'form_item/form/cascade_select_bind','type'=>'text','placeholder'=>'','required'=>1,
                    'related'=>[
                        ['name'=>'roleid','label'=>'选择角色'],
                    ],
                    'init_url'=>U(MODULE_NAME.'/role_select_list'),
                ],
                array('name'=>'islogin','label'=>'允许登录',
                    '_parser'=>'form_item/collect/radio',
                    'data'=>array('否','是')
                ),
				array('name'=>'remarks','label'=>'备注','placeholder'=>'','required'=>0,
					'_parser'=>'form_item/form/textarea',
					'_validation'=>array(),
				),
			);
			form_validation_create($form_items,$rules,$messages);//获取验证规则
			if($id){
				//取值
				$field_list = "mt.*";
				$sql_suffix = "from sys_admin mt ";
				$sql_suffix .= "where mt.id=$id ";
				$temp_array = $this -> get_list_bysql("select $field_list $sql_suffix");
				form_item_add_value($form_items,$temp_array[0]);
			}
			$component_data = array('_parser'=>'form/default',
				'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
				'rules' => $rules,'messages' => $messages,
			);
			_display($component_data);
		}
	}
	//删除管理员
	public function admin_remove(){
		$id = _POST('id');
		$id_arr=explode(',',$id);
		if (in_array(1,$id_arr)) sys_out_fail("admin超级管理员无法删除",403);
		$sqlstr = "delete from sys_admin where id in ($id)";
		$result = $this -> do_execute($sqlstr);
		sys_out_result($result);
	}

	/**
	+----------------------------------------------------------------------
	|角色相关
	+----------------------------------------------------------------------
	*/
	//角色列表
	public function role_list($_action_access=0){
		//访问控制变量
		$GLOBALS['_action_access'] = $_action_access;
		//声明变量
		$GLOBALS['page_count'] = 10;$GLOBALS['totalcount'] = 0;
		if(_POST('search-flag')){
			unset($_POST['page']);
			unset($_GET['page']);
		}
		$name = I('name');
		//基本查询
		$field_list = "mt.*";
		$sql_suffix = "from sys_role mt  ";
		$sql_suffix .= "where 1=1 ";
		if($name){
			$sql_suffix .= "and mt.name like '%$name%'  ";
		}		
		$orderby_str = "mt.id desc";
		$list_items = $this -> admin_get_page($field_list,$sql_suffix,$orderby_str);
		//UI部分
		$breadcrumb_data = '首页,管理员相关,角色管理';
		$search_items = array(
            array('name'=>'name','placeholder'=>'请输入角色名称','cls'=>'w250','_parser'=>'form_item/search/input'),
        );
		$buttons = array(
			array('text'=>'新建角色','title'=>'新建角色','icon'=>'add','full'=>0,'position'=>1,
				'target'=>'inner_frame','url'=>U('Manage/role_save'),
			),
			array('text'=>'编辑','title'=>'编辑','full'=>0,'checked'=>1,'position'=>2,
				'target'=>'inner_frame','url'=>U('Manage/role_save'),'url_param' => array('id'=>'2_id'),
				'testing'=>array('id'=>array('1'=>'超级管理员角色禁止修改'))
			),
			array('text'=>'删除','title'=>'删除','full'=>0,'checked'=>1,'position'=>2,
				'target'=>'inner_confirm','url'=>U('Manage/role_remove'),'url_param' => array('id'=>'2_id'),
				'testing'=>array('id'=>array('1'=>'超级管理员角色禁止删除'))
			),
		);
		$table_headers = array(
			array('name'=>'id','title'=>'角色ID'),
			array('name'=>'name','title'=>'角色名称'),
			array('name'=>'content','title'=>'角色描述')
		);
		$component_data = array(
			'_parser'=>'container/default',
			'_children'=>array(
				array('_parser'=>'breadcrumb/default','data'=>$breadcrumb_data),
				array('_parser'=>'container/content',
					'_children'=>array(
						array('_parser' => 'form/search', 'cls' => 'text-c', 'action' => U(MODULE_NAME . '/' . ACTION_NAME),
                            '_children' => $search_items
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
	//保存角色
	public function role_save(){
		if(IS_POST){
			$id = _POST('id');
			$menuId_r = $_POST['menuId'] ? $_POST['menuId'] : sys_out_fail("请选择角色权限！");
			$GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
			//根据post参数生成field字符串
			$save_fields = array('name','content');
			$post_keys = array_keys($_POST);
			
			$post_fields = array_intersect($save_fields,$post_keys);//取公共
			$fields_str = fields2SqlStrByPost($post_fields);
			if($id){//修改
				$sqlstr = "update sys_role set $fields_str where id=$id";
				$result = $this -> do_execute($sqlstr);
			}
			else{//新增
				$sqlstr = "insert into sys_role set $fields_str";
				$result = $this -> do_execute($sqlstr);
				$id = $this->get_insert_id();
			}
			if($result !== false && $id){
				//删除原有的
				$result = $this -> do_execute("delete from sys_role_priv where role_id=$id");
				//添加新的
				$sqlstr = "insert into sys_role_priv(role_id,menu_id) values ";
				foreach ($menuId_r as $menu_id){
					$sqlstr .= "($id,$menu_id),";
				}
				$sqlstr = substr($sqlstr,0,-1);
				$result = $this -> do_execute($sqlstr);				
			}						
			sys_out_result($result);
		}
		else{
			$id = _REQUEST('id');
			$GLOBALS['cur_operate'] = $id ? 2 : 1;//目前的操作类型，2^0:新增;2^1:编辑
			$menus = $this -> _cascade_get_role(0,'sys_menu');
			$having_r = $this -> get_list_bysql("select menu_id from sys_role_priv where role_id=$id");
			$having_menus = array();
			foreach($having_r as $role_having_r_i){
				$having_menus[] = $role_having_r_i['menu_id'];
			}
			$form_items = array(
				array('name'=>'id','_parser'=>'form_item/form/hidden'),
				array('name'=>'name','label'=>'名称','placeholder'=>'最多20字符',
					'_parser'=>'form_item/form/input','type'=>'text',
					'_validation'=>array(
						'maxlength'=>array(20,"最多20个字符"),
					),
				),
				array('name'=>'content','label'=>'备注','placeholder'=>'','required'=>0,
					'_parser'=>'form_item/form/textarea',
					'_validation'=>array(),
				),
				array('label'=>'角色权限',
					'_parser'=>'form/checked',
					'menus'=> $menus,'having_menus'=>$having_menus,
					'role_id'=>$id
				),			
			);
			form_validation_create($form_items,$rules,$messages);//获取验证规则
			if($id){
				//取值
				$field_list = "mt.*";
				$sql_suffix = "from sys_role mt ";
				$sql_suffix .= "where mt.id=$id ";
				$temp_array = $this -> get_list_bysql("select $field_list $sql_suffix");
				form_item_add_value($form_items,$temp_array[0]);
			}		
			form_validation_create($form_items,$rules,$messages);//获取验证规则
			$component_data = array('_parser'=>'form/default',
				'action' => U(MODULE_NAME.'/'.ACTION_NAME),'_children'=>$form_items,
				'rules' => $rules,'messages' => $messages,
			);
			_display($component_data);
		}
	}
	//删除角色
	public function role_remove(){
		$id = _POST('id');
		$sqlstr = "delete from sys_role where id in ($id)";
		$result = $this -> do_execute($sqlstr);
		if($result){
			$sqlstr = "delete from sys_role_priv where role_id in ($id)";
			$result = $this -> do_execute($sqlstr);
		}
		sys_out_result($result);
	}


    //获取管理员详情
    public function admin_get(){
        $id = _REQUEST('id');
        if(!$id) layer_out_fail("参数传递不正确");

        $field_list = "mt.*";
        $field_list .= ",r.name as role_name";
        $sql_suffix = "from sys_admin mt ";
        $sql_suffix .= "left join sys_role r on mt.roleid=r.id ";
        $sql_suffix .= "where mt.id=$id ";
        $sqlstr = "select $field_list $sql_suffix ";
        $result_r = $this -> get_list_bysql($sqlstr);
        int_to_string($result_r,array(
            'validflag'=>array('1'=>'正常','0'=>'冻结')
        ));
        $temp_array = $result_r[0];
        $fields = array(
            array('title'=>'登录名','value'=>$temp_array['account']),
            array('title'=>'真实姓名','value'=>$temp_array['realname']),
            array('title'=>'所属角色','value'=>$temp_array['role_name']),
            array('title'=>'账号备注','value'=>$temp_array['remarks']),
            array('title'=>'登录次数','value'=>$temp_array['login']),
            array('title'=>'上次登录id','value'=>$temp_array['lastloginip']),
            array('title'=>'上次登录时间','value'=>$temp_array['lastlogintime']),
            array('title'=>'添加时间','value'=>$temp_array['regdate']),
        );
        $component_data = array('_parser'=>'table/detail','title'=>'详情',
            'fields'=>$fields
        );
        _display($component_data);
    }
	
}