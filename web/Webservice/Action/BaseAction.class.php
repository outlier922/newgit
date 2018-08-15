<?php
/*
| --------------------------------------------------------
| 	文件功能：	公司webservice框架Action基类
|	程序作者：	王海滨（移动互联部）
|	时间版本：	2014-06-20
|	特别说明：	(1)本类必须继承公司公共框架PublicAction基类
				 	(2)除去底层或中间函数，类内所有函数必须是public访问权限
					(3)只有跟版本号低耦合度的功能函数，才会被定义本类中
| --------------------------------------------------------
*/
$path = dirname(dirname(dirname(dirname(__FILE__))));
require_once $path.'/Lib/Action/PublicAction.class.php';
class BaseAction extends PublicAction {
    /**
    +------------------------------------------------------------
    | 公共函数
    +------------------------------------------------------------
    */
	// _initialize是TP封装后的初始化函数
	protected function _initialize() {
	    parent::_initialize();

		if(SYS_SAFE_MODE && ACTION_NAME!='webview' && ACTION_NAME!='push_add' && ACTION_NAME!='district_list'){
			$datetime = _POST('datetime');
			$sign = _POST('sign');
			//if(sys_get_timespan($datetime, sys_get_time(), 'i')>'60') sys_out_fail('非法操作',500);
			$signValue = md5(DATAKEY.'|'.$datetime.'|'.ACTION_NAME);
			if($signValue != $sign) sys_out_fail('签名错误',500);
		}

		//定义不需要检查登录令牌的接口数组
		$not_check_token_list = array(
			'push_add','code_get', 'code_verify','client_add','client_login','client_verify','password_reset','webview','goods_list','one_ad_list',
			'two_ad_list','bank_list','config_set','one_classify_list','two_classify_list','classify_list','shoporgood_list','shopshow_list','hotgood_list',
			'comment_list','search_list','one_search_list','hotword_add','hotword_list','good_get','opencity_list','shoppay_get','two_shoporgood_list',
			'two_shop_list'
		);
		if(!in_array(ACTION_NAME,$not_check_token_list))
			sys_check_token();//非指定方法名，统一进行登录令牌有效性检查

	}
	
	//定义推送方法，在PublicAction中通过pub_async_push异步调用
	public function push_add()
	{
		$keytype=_POST('keytype');
		$keyid=_POST('keyid');
		$content=_POST('content');
		$client_idlist=_POST('client_idlist');
		$from_id=_POST("from_id");
		$push_method=_POST('push_method');

		//sys_log("push_add".$push_method);
		//$push_method=1;
		if($push_method == 1){//向用户推
			$sqlstr="select channelid,devicetype from sys_client where id in($client_idlist) and channelid <>'' ";
			$client_array= $this -> get_list_bysql($sqlstr);
			if(count($client_array) > 1){//推送到指定多用户
				$cid_list = "";
				foreach ($client_array as $client_array_a){
					$cid_list .= $client_array_a['channelid'].',';
				}
				if($cid_list){
					$cid_list = $cid_list ? substr($cid_list,0,-1) : '';
					@sys_list_push($cid_list,$content,$keytype,$keyid);
				}
			}
			else if(count($client_array) == 1){//推送到单用户
				$sqlstr="select name,head_img from sys_admin where id=$from_id";
				$from_array=$this->get_list_bysql($sqlstr);
				$sqlstr="select devicetype,channelid from sys_client where id='$client_idlist' and channelid <>'' ";
				$client_array = $this -> get_list_bysql($sqlstr);
				if($client_array){
					$name="";
					$head_img="";
					if ($from_id>1)
					{
						$name=$from_array[0]['name'];
						$head_img=$from_array[0]['head_img'];
					}
					@sys_push(NULL,$client_array[0]['devicetype'],$client_array[0]['channelid'],$content,$keytype,$keyid,$name,$head_img);
				}
			}
		}
		else{//向app推
			@sys_app_push($content,$keytype,$keyid);
		}
	}

	public static function menu_root()
	{
		$api_menus = array(
			'init'=>array('title'=>'系统初始化'),

			'error_code'=>array('title'=>'错误编码表','index'=>'0'),

			'code_root'=>array('title'=>'验证相关','child'=>array(

			)),

			'client_root'=>array('title'=>'用户相关','child'=>array(

				'password_root'=>array('title'=>'忘记密码','child'=>array(

				)),
			)),

			'file_root'=>array('title'=>'文件相关','child'=>array(

			)),

			'common_root'=>array('title'=>'通用相关','child'=>array(

			)),

			'notice_root'=>array('title'=>'通知消息','child'=>array(

			)),
			
            'prize_root'=>array('title'=>'抽奖相关','child'=>array(

            )),
            
            'shop_root'=>array('title'=>'商城相关','child'=>array(

            )),
            
            
            'order_root'=>array('title'=>'订单相关','child'=>array(

            )),
            
			'my_root'=>array('title'=>'账户相关','child'=>array(

			)),
			
			'menu_sys_extend'=>array('title'=>'扩展功能','child'=>array(

			)),

			'menu_sys_plugins'=>array('title'=>'第三方插件','child'=>array(

			)),
			
			'other'=>array('title'=>'其他','child'=>array(

			)),
			
		);

		return $api_menus;
	}


	//页记录获取（底层函数）
	/**
	 * $keytype:对应sys_img表中keytype 2：帖子图文列表 3：动态图文列表..其余待扩展
	 */
	protected function service_get_page($field_list,$sql_suffix,$orderby_str=NULL,$keytype=2)
	{
		//die("=====");
		//校正current_page参数
		//@$current_page=(empty($_POST['current_page'])?0:$_POST['current_page']);//如果不传，置为0
		$page=_POST('page');//如果不传，置为0
		if(empty($page) || ($page < 0)) $page = 0;
		$start_index = $page * SYS_PAGE_SIZE;

		//修正orderby(默认按日期倒序排列)
		if(empty($orderby_str)) $orderby_str=" mt.id desc ";

		//获取总记录数
		$sqlstr = " select count(distinct mt.id)  " . $sql_suffix;
		$totalCount=$this->get_one_bysql($sqlstr);

		////调试用下面这2句
		$sqlstr = " select $field_list $sql_suffix order by $orderby_str ";
		sys_log($sqlstr);
		//die($sqlstr);

		$temp_array =NULL;//初始化结果数组
		//如果总数为0，则直接
		if($totalCount>0)
		{
			//添加翻页规则
			$sqlstr = " select $field_list $sql_suffix order by $orderby_str limit $start_index,".SYS_PAGE_SIZE;
			//die($sqlstr);
			$temp_array =$this->get_list_bysql($sqlstr);
		}

		$result_array['totalCount']=$totalCount;
		$result_array['listItems']=$temp_array;
		sys_out_success(0,$result_array);
	}
	//所有记录获取（底层函数，不再分页，一次获取全部）
	protected function service_get_all($field_list,$sql_suffix,$orderby_str=NULL)
	{
		//修正orderby(默认按日期倒序排列)
		if(empty($orderby_str)) $orderby_str=" mt.id desc ";

		//获取总记录数
		//$sqlstr = " select count(distinct mt.id)  " . $sql_suffix;
		$sqlstr = " select count(*)  " . $sql_suffix;
		//die($sqlstr);
		$totalCount=$this->get_one_bysql($sqlstr);

		////调试用下面这2句
		$sqlstr = " select $field_list $sql_suffix order by $orderby_str ";
		sys_log($sqlstr);

		$temp_array =NULL;//初始化结果数组
		//如果总数为0，则直接
		if($totalCount>0  && _POST('page')<=0)
		{
			$sqlstr = " select $field_list $sql_suffix order by $orderby_str ";
			//die($sqlstr);
			$temp_array =$this->get_list_bysql($sqlstr);
		}
		$result_array['totalCount']=$totalCount;
		$result_array['listItems']=$temp_array;
		sys_out_success(0,$result_array);
	}

	/**
	 * 银行列表
	 * @parent common_root
	 * @req_desc 
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor name 银行名称
	 * @special
	 */
	public function bank_list()
	{
		$result_array=$this->get_list_bysql("select * from sys_bank");
		sys_out_success(0,$result_array);
	}


	/**
	 * 随机码获取
	 * @parent code_root
	 * @req_params username 用户登录名 手机号码 18800000000
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special 特殊说明一 先申请，后验证
	 * @special 特殊说明二 特别提示：60秒过后，点击“重新发送验证码”时，再调用一遍此接口即可。
	 */
	public function code_get()
	{
		sys_check_post_single('username');//检查post必选参数完整性

		$username=_POST('username');
		$this->pub_check_mobile($username);
		sys_send_code($username);
		sys_out_success();
	}

	/**
	 * 随机码验证
	 * @parent code_root
	 * @req_params username 用户登录名 手机号码 1880000000
	 * @req_params code 4位随机号码 测试阶段固定向服务器提交“1234” 1234
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor temp_token 临时token 验证成功服务器会返回一个临时token，$系统注册模块或重设密码模块需要用到。
	 * @special 特殊说明一 先申请，后验证
	 */
	public function code_verify()
	{
		$post_array=array('username','code');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);

		$code =_POST('code');
		$username=_POST('username');

		//特别提示：sys_check_code里已经对错误进行了定义
		if(sys_check_code($code))
		{
			$temp_array[0]['temp_token']= sys_get_temp_token($username);
			sys_out_success(0,$temp_array);
		}
	}

	/**
	 * 用户验证
	 * @parent client_root,password_root
	 * @req_params account 用户登录名
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special 特殊说明一 （先验证用户名，再申请和验证随机码，最后调用重设密码）
	 */
	public function client_verify()
	{
		sys_check_post_single('account');

		//检查用户合法性
		$account=_POST('account');

		//注意本项目中昵称不允许重复
		$sqlstr = "select id,validflag from sys_client where account='$account' ";
		$temp_array =$this->get_list_bysql($sqlstr);

		//验证用户是否有效
		if(count($temp_array)==0) sys_out_fail(sys_get_msg(106),106);//不存在
		if($temp_array[0]['validflag']==2) sys_out_fail(sys_get_msg(104),104);//被冻结

		sys_out_success();
	}

	/**
	 * 密码重设
	 * @parent client_root,password_root
	 * @req_params temp_token 临时令牌 必须填写正确 zz
	 * @req_params new_password 新密码
	 * @req_desc 特别提示：重设密码时，客户端应该实现并自行判定2次新密码的输入，是否相同。
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special 特殊说明一 （先验证用户名，再申请和验证随机码，最后调用重设密码）
	 */
	public function password_reset()
	{
		$result=$this->pub_password_reset();
		sys_out_result($result);
	}

	

	/**
	 * 硬件保存
	 * @parent menu_sys_extend
	 * @req_params token 登录令牌
	 * @req_params deviceid 登陆手机硬件码 对应百度推送userid 10
	 * @req_params devicetype 登陆手机类型 1:苹果;2:安卓 2
	 * @req_params channelid 百度推送渠道id 方便直接从百度后台进行推送测试
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special 只有注册才能收到系统推送通知
	 */
	public function device_save()
	{
		$post_array=array('deviceid','devicetype','channelid');
		sys_check_post($post_array);//检查post完整性
		unset($post_array);

		$client_id=sys_get_cid();
		$deviceid = _POST('deviceid');
		$devicetype = _POST('devicetype');
		$channelid = _POST('channelid');
		$sqlstr="update sys_client set devicetype='$devicetype',deviceid='$deviceid',channelid='$channelid' where id=$client_id";
		$result=$this->do_execute($sqlstr);
		sys_out_result($result);
	}


	
	/**
	 * 用户退出
	 * @parent client_root
	 * @req_params token 登录令牌
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function client_loginout()
	{
		$client_id=sys_get_cid();
		$sqlstr = "update sys_client set channelid='',deviceid='' where id='$client_id'";
		$this->do_execute($sqlstr);
		sys_login_out();
		sys_out_success();
	}

}
