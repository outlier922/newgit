<?php
/*
| --------------------------------------------------------
| 	文件功能：	公司公共框架自定义Action基类
|	程序作者：	王海滨
|	时间版本：	2014-09-20
|	特别说明：	(1)本类必须继承ThinkPhp框架Action基类
				 	(2)类内所有函数全部是protected访问权限
					(3)只有Webservice,Website,Webadmin都会用到的函数，才会被定义本类中
					(4)本类由[王海滨]专职维护，如无特殊情况，请勿改动
| --------------------------------------------------------
*/

$path = dirname(dirname(dirname(__FILE__)));
//require_once(SYS_ROOT_PATH."include/extend.inc.php");
if (SYS_DEBUG_MODE) require_once $path.'/ThinkPHP/Lib/Core/Action.class.php';

class PublicAction extends Action {
	// _initialize是TP封装后的初始化函数，可以实现特定功能（比如限制IP访问等）
	protected function _initialize() {
	    //接口签名验证
//	    if(SYS_SAFE_MODE && ACTION_NAME!='webview' && ACTION_NAME!='push_add'){
//	        $datetime = _POST('datetime');
//	        $sign = _POST('sign');
//	        if(sys_get_timespan($datetime, sys_get_time(), 'i')>'60') sys_out_fail('非法操作',500);
//	        $signValue = md5(DATAKEY.'|'.$datetime.'|'.ACTION_NAME);
//	        if($signValue != $sign) sys_out_fail('签名错误',500);
//	    }
	}

	//默认非法访问（子类可灵活修改）
	protected function index(){
		$this->pub_deny_access();
    }

	//封装6个最常用的数据库操作函数(减少项目跨平台移植成本)___begin
	//得到查询的第1行，第1列
	protected function get_one_bysql($sqlstr)
	{
		$temp_array=M()->query($sqlstr);
		return reset($temp_array[0]);//reset定位到数组第一个元素，非常重要
	}
	//获取记录集
	protected function get_list_bysql($sqlstr)
	{
		$temp_array=M()->query($sqlstr);
		return $temp_array;
	}
	//得到最后插入的indsert_id
	protected function get_insert_id()
	{
		return M()->getLastInsID();
	}
	//执行插入或更新操作
	protected function do_execute($sqlstr)
	{
		return M()->execute($sqlstr);
	}
	//执行更新表冗余字段计数
	//updatetype 1:增加计数 2：减少计数
	//count 变更的数量(包括但不限于1)
	//$table:表名称
	//$field:字段名称
	//$id:$table表中的自增主键
	protected function do_update_count($updatetype,$count,$table,$field,$id)
	{
		if($updatetype==1)
			$sqlstr="update $table set $field=$field+$count where id=$id";
		else
			$sqlstr="update $table set $field=if($field-$count<0,0,$field-$count)  where id=$id";
		return  M()->execute($sqlstr);
	}
	//执行事务操作(INNODB引擎才能支持)
	protected function do_transaction($sqlstr_array)
	{
		$m=M();
		$m->startTrans();
		$result = true;//初始化
		foreach($sqlstr_array as $sqlstr)
		{
			sys_log($sqlstr);
			$result =  $m->execute($sqlstr);
			if($result===false) break;
		}
		if($result){
			$m->commit();//成功则提交
		}else{
			$m->rollback();//不成功回滚
		}
		return $result;
	}
	//封装6个最常用的数据库操作函数(减少项目跨平台移植成本)___end


	//非法访问时的提示信息，可以扩展为详细操作（比如记录来访IP等）
	protected function pub_deny_access() {
		exit(SYS_DENY_MSG);
	}

	//记录保存//(考虑到效率和安全性问题，不建议在webservice前台使用本函数，本框架中仅在webadmin后台使用)
	//此处根据第2个参数id是否为空，来智能判断是新增还是修改
	protected function pub_save($mt,$id=NULL)
	{
		$Form = M($mt);//注意：首字母必须大写才调用Model中的静态验证规则(如果没有model类直接调用M)
		//$this->show($Form);
		if($Form->create()) {//TP规定create()方法会自动调用Model中的静态验证规则
			if($id)
			{
				$Form->id=$id;//注意：一定要写在此处
				$result =   $Form->save();
			}
			else
			{
				$result =   $Form->add();
			}

			if($result !== false)  sys_out_success();
			else	sys_out_fail($Form->getError(),500);
		}else{//如果是create->model验证错误，则输出具体错误信息和错误编码
			sys_out_fail($Form->getError(),101);	//$this->error($Form->getError());
		}
		//TP 输出模式备忘：
		//$this->success('操作成功！');
		//$this->error('写入错误！');
	}

	//记录删除
	protected function pub_remove($mt,$idList)
	{
		//$result =   M($mt)->delete($id);
		$sqlstr="delete from $mt where id in($idList)";
		$result=$this->do_execute($sqlstr);
		sys_out_success();
	}

	//单记录获取（webserice模型读取单条数据）
	protected function pub_get($mt,$id)
	{
		//whbmemo：为了兼容system.core.php,此处必须是$temp_array[0]
		//$temp_array[0]=M($mt)->find($id);
		$sqlstr="select *  from $mt where id=$id";
		$temp_array=$this->get_list_bysql($sqlstr);
		if($temp_array)
			sys_out_success(0,$temp_array);
		else
			sys_out_404();
	}

	//检测获取短信验证码是否合法
	protected function pub_check_mobile($mobile)
	{
		sys_check_mobile($mobile);
		$datetime = sys_get_time();

		$map['mobile'] = $mobile;
		$map['regdate'] = array('like',date('Y-m-d').'%');
		$model = M('sys_sms');
		$list = $model->where($map)->order('id desc')->select();
		if ($list) {
			//限制同一手机号一分钟内只发一次
			if(sys_get_timespan($list[0]['regdate'], sys_get_time(), 'i') < '1') sys_out_fail('发送短信过于频繁', 500);
			//限制同一手机号一天只发3次
			if (count($list) >= 3) sys_out_fail('发送短信次数太多', 500);
		}
		$data['member_id'] = sys_get_cid();
		$data['mobile'] = $mobile;
		$data['ip'] = get_client_ip();
		$data['regdate'] = $datetime;
		$model->add($data);
	}

	//添加并发送系统通知提醒
	/*
	通知级别$leveltype 1：普通闪屏提醒 2：短信提醒（最高级别）
	通知类型$keytype  1系统通知 2订单通知
	关联主键$keyid,用于在系统通知中区分跳转不同页面
	client_id:通知所属用户主键id
	from_id:通知来源用户主键id(关联头像时使用,规定from_id=1表示是系统管理员)
	发送普通系统通知备忘：$this->pub_add_systemnotice(1,1,1,$content,$client_id,1)
	*/
	protected function pub_add_systemnotice($leveltype,$keytype,$keyid,$content,$client_id,$from_id=1,$push_method=1)
	{
		$this->pub_async_push($leveltype,$keytype,1,$content,$client_id,$from_id,$push_method);//异步推送
		return $result;
	}

	//通过异步方式实现百度推送（与sys_notice主业务分离，提升服务效率）
	//特别注意：此处是client_idlist,即支持多用户同时推送
	protected function pub_async_push($leveltype,$keytype,$keyid,$content,$client_idlist,$from_id,$push_method=1)
	{
		if(empty($content) || mb_strlen($content)>140) return;// 推送内容最长140个字

		$url =SYS_WEB_SERVICE."V100/push_add";//调用BaseAction中的具体推送方法
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "keytype=$keytype&keyid=$keyid&from_id=$from_id&content={$content}&client_idlist={$client_idlist}&push_method=$push_method");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//正式部署必须为1，防止向客户端输出
		//特别注意：调试时，将CURLOPT_RETURNTRANSFER置为0,可以查看具体输出
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);//设置成1秒后超时,不用等待返回结果
		$result=curl_exec($ch);
		curl_close($ch);
	}


	//重设密码
	protected function pub_password_reset()
	{
		$account=substr($_SESSION['temp_token'],8,strlen($_SESSION['temp_token'])-8);
		sys_check_temp_token();//检查登录

		sys_check_post_single('new_password');//检查post必选参数完整性
		$new_password=md5(_POST('new_password'));
		$sqlstr=" update sys_client set password = '$new_password' where account='$account'";
		$result=$this->do_execute($sqlstr);
		return $result;
	}


	//重设提现密码
	protected function pub_paypassword_set()
	{
		sys_check_post_single('password');
		$client_id=sys_get_cid();
		$new_password=md5(_POST('password'));
		$sqlstr=" update sys_client set paypassword = '$new_password' where id='$client_id'";
		$result=$this->do_execute($sqlstr);
		return $result;
	}

	//修改密码
	protected function pub_password_save()
	{
		$post_array=array('old_password','new_password');
		sys_check_post($post_array);//检查post参数完整性
		unset($post_array);

		$old_password=md5(_POST('old_password'));
		$new_password=md5(_POST('new_password'));		

		$client_id=sys_get_cid();
		$sqlstr="select password from sys_client where id=$client_id";
		$password=$this->get_one_bysql($sqlstr);
		if(!$password) sys_out_fail(sys_get_msg(106),106);

		//检查旧密码是否填写正确
		if($old_password !== $password) sys_out_fail(sys_get_msg(102),102);

		$sqlstr=" update sys_client set password = '$new_password' where id=$client_id";
		$result=$this->do_execute($sqlstr);
		return $result;
	}

	/////////////////////////////////////////////////////////////////////////////////////////
	//////////      以上区域属于项目逻辑固定处理函数，请勿随意改动             //////////
	/////////////////////////////////////////////////////////////////////////////////////////



	//文件上传	(当keytype<0时，表示是管理后台端上传)
	protected function pub_file_upload($client_id)
	{
		$post_array=array('keytype');
		sys_check_post($post_array);//检查post参数完整性
		unset($post_array);

		$keytype=_POST('keytype');

		//如果是传递非图片类型
		//if($keytype==4 || $keytype==5 || $keytype==8 || $keytype==9)
		//	$upload_array = sys_upload_file(2);//形参不等于1表示是上传其他类型（后台无需生成缩略图）

		//任何项目，最多只允许传递4张图片
		//if($orderby>3) sys_out_fail('$orderby传值错误！',101);

		switch($keytype)
		{
			case "1"://个人头像
				$upload_array = sys_upload_file(1,120,120);//个人头像缩略图按照正方形120*120截取
				$sqlstr="update sys_client set avatarbig='$upload_array[0]',avatar='$upload_array[1]' where id=$client_id";
				break;
			case "2"://帖子图片(更新sys_blog冗余字段)
				$keyid=_POST('keyid');
				if(!$keyid){
					sys_out_fail("请填写订单id");
				}
				$upload_array = sys_upload_file(1,240,240);//帖子图片宽、高比例为1：1
				$sqlstr = "insert into sys_img set keytype=3,keyid=$keyid,imgurl='$upload_array[1]',imgurlbig='$upload_array[0]',regdate='".sys_get_time()."'";
				break;
			//case "3"://个人背景
			//	$upload_array = sys_upload_file(1,640,400);//个人背景缩略图按照正方形400*400截取
			//	$sqlstr="update sys_client set backimg='$upload_array[0]' where id=$client_id";
			//	break;
			//case "4"://帖子音频(更新sys_blog冗余字段)
			//	$sqlstr="update sys_blog set audiourl='$upload_array[0]',duration='$duration'  where id=$keyid";
			//	break;
			//case "5"://帖子视频(更新sys_blog冗余字段)
			//	$sqlstr="update sys_blog set videourl='$upload_array[0]',duration='$duration'  where id=$keyid";
			//	break;
			//case "6"://帖子视频第一张图片
			//	$upload_array = sys_upload_file(1,400,400);//帖子图片宽、高比例为2：1
			//	$sqlstr="update sys_blog set imgurlbig='$upload_array[0]',imgurl='$upload_array[1]' where id=$keyid ";
			//	break;
			//case "7"://真聊天内插图片
			//	$upload_array = sys_upload_file(1,200,200);
			//	$temp_array[0]['item1']=$upload_array[1];
			//	$temp_array[0]['item2']=$upload_array[0];
			//	sys_out_success(0,$temp_array);
			//	break;
			//case "8":
			//case "9":
			//	$temp_array[0]['item1']=$upload_array[0];
			//	$temp_array[0]['item2']=$duration;
			//	sys_out_success(0,$temp_array);
			//	break;
			//case "10"://工匠个人形象
			//	$sqlstr = "select count(id) from sys_img where keytype=10 and keyid=$keyid";
			//	$count = $this -> get_one_bysql ( $sqlstr );
			//	if ( $count >= 3 ) sys_out_fail ( '最多3张个人形象图片' );
			//	$sqlstr="insert into sys_img set client_id=$client_id,keytype=$keytype,keyid=$keyid,imgurlbig='$upload_array[0]',imgurl='$upload_array[1]',orderby='$orderby',regdate='".sys_get_time()."'";
			//	break;
			//case "11"://评论内图片
   //             $upload_array = sys_upload_file(1,200,200);
			//	$sqlstr = "insert into sys_img set client_id=$client_id,keytype=$keytype,keyid=$keyid,imgurlbig='$upload_array[0]',imgurl='$upload_array[1]',orderby='$orderby',regdate='".sys_get_time()."'";
			//	break;
			//case "15"://通用图片上传
			//	$img_array = NULL;//设置初始值
			//	@$temp_file=$_FILES['temp_file'];//与前台表单type="file"中的name对应
			//	sys_check_upload($temp_file);//首先检测是否上传成功

			//	//判断文件格式是否合法_____________________________________________________begin
			//	//以下两句是为了取得上传文件的扩展名
			//	$temp_name =explode(".",$temp_file['name']);
			//	$ext_name =strtolower(end($temp_name));
			//	//当上传文件类型不符合规范时，需要报302错误
			//	if(stripos(SYS_LIMIT_EXT,$ext_name)===false) sys_out_fail(sys_get_msg(302),302);
			//	//当仅允许上传图片时却上传了其他类型，也需要报302错误
			//	if(stripos(SYS_IMAGE_EXT,$ext_name)===false)  sys_out_fail(sys_get_msg(302),302);

			//	$upload_array = sys_upload_file_local2($temp_file,1,$ext_name,640,640);
			//	$temp_array[0]['item1']=$upload_array[1];
			//	$temp_array[0]['item2']=$upload_array[0];
			//	$temp_array[0]['imgwidth']=$upload_array[2];
			//	$temp_array[0]['imgheight']=$upload_array[3];
			//	$temp_array[0]['orderby']=$orderby."";
			//	sys_out_success(0,$temp_array);
			//	break;
			default:
				sys_out_fail('keytype取值范围不合法！',101);
				break;
		}
		$result=$this->do_execute($sqlstr);
		//sys_log($sqlstr);
		return $result;
	}

	//验证核销码是否存在 return:true |false
    protected function get_code($shop_id){
	    $code = mt_rand(10000000,99999999);
        $sqlstr = "select count(*) from sys_card_no where out_no='$code' and shop_id=$shop_id";
        $client_count = $this -> get_one_bysql($sqlstr);
        if($client_count > 0){
	        get_code();
        }else{
	        return $code;
        }
    }
	

}
?>