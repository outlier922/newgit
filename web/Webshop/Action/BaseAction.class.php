<?php
class BaseAction extends AdminAction
{
	protected function _initialize(){
		define('UID',is_login());
		if( !UID ){// 还没登录 跳转到登录页面
			echo "<script>top.window.location = '".U('Login/index')."';</script>";
		}
		//检查权限
		$this->check_role();
	}
	//权限检测
	final protected function check_role(){
		$user = session('auth');
		if($user['role_id'] == 1) return true; //管理员允许访问任何页面
		//过滤不需要权限控制的页面
		switch (MODULE_NAME){
			case 'Index':
				return true;
				break;
			case '':
				break;
		}
		//允许所有登录用户
		$allow_action_a = array('admin_get','test');
		if(in_array(ACTION_NAME,$allow_action_a)){
			return true;
		}
		return true;
	}

	/**
	+----------------------------------------------------------------------
	| 公共方法
	+----------------------------------------------------------------------
	*/

	public function citys_get(){
		$field_list = " mt.id,mt.name ";
		$sql_suffix = " from sys_cascade_district mt where mt.level<=2 and mt.name like '%市' ";
		$sqlstr="select $field_list $sql_suffix";
		$temp_array=NULL;;
		$temp_array=$this->get_list_bysql($sqlstr);
		sys_out_success(0,$temp_array);
	}
}
