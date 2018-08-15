<?php
/*
|---------------------------------------------------------
|	文件功能：	公司XX项目webservice服务类
|	程序作者：	王海滨（移动互联部）
|	时间版本：	2014-06-20
|	特别说明：	(1)本类必须继承公司webservice框架BaseAction基类
|				(2)除去底层或中间函数，类内所有函数必须是public访问权限
| --------------------------------------------------------
*/

require_once 'BaseAction.class.php';
class V100Action extends BaseAction
{

	/**
	 * 用户注册
	 * @parent client_root
	 * @req_params temp_token 临时令牌 可以有效防止机器人恶意注册（该值从验证随机码接口获取）
	 * @req_params username 手机号
	 * @req_params password 密码
	 * @req_params referee_code 推荐码 可选填
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor token 正式token 注册成功后，服务器返回一个正式token
	 * @special
	 */
	public function client_add()
	{
		sys_check_temp_token();//检查登录
		$post_array = array('username','password');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);

		//5个必填字段
		$account = _POST('username');
		$password = _POST('password');
		$password = md5($password);
		$referee_code = _POST('referee_code');
		$client_id = 0;//初始化
		$sqlstr = "select id from sys_client where account='$account'";
		$client_id = $this->get_one_bysql($sqlstr);
		if (!empty($client_id)) sys_out_fail(sys_get_msg(105), 105);
		if(!empty($referee_code)){
			$sqlstr = "select id from sys_client where referee_code='$referee_code'";
			$referee_id = $this->get_one_bysql($sqlstr);
			if (empty($referee_id)) sys_out_fail('推荐码不存在', 106);
		}		
		$fieldstr = "account='$account',password='$password',paypassword='$password',write_code='$referee_code',regdate='" . sys_get_time() . "'";
		$sqlstr = " insert into sys_client set $fieldstr";
		$this->do_execute($sqlstr);
		$client_id = $this->get_insert_id();
		if ($client_id) {		 
			$sql_array = NULL;
			$sql_array[] = " update sys_client set referee_code='$account' where id =$client_id ";
			if($referee_id){
				$invite_score = $this->get_one_bysql("select invite_score from sys_config where id=1");
				$sql_array[] = " update sys_client set flag=2,score=score+$invite_score where id = $referee_id ";
				$phone_list = $this-->get_list_bysql("select * from sys_phone where phone='$account' and client_id=$referee_id");
				if($phone_list){
					$sql_array[] = " update sys_phone set iszhuce=1 where phone='$account' and client_id=$referee_id ";
				}
			}
			
			$this->do_transaction($sql_array);
			$temp_array[0]['token'] = sys_get_token($client_id, '');//封装token
			sys_out_success('注册成功', $temp_array);
		} else sys_out_fail();
	}

	
	/**
	 * 用户登陆
	 * @parent client_root
	 * @req_params username 用户登录名 手机号 18800000000
	 * @req_params password 密码 密码 123456
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
     * @ret_infor id 用户id
	 * @ret_infor account 用户账号
	 * @ret_infor nickname 用户昵称
	 * @ret_infor avatar 用户头像
	 * @ret_infor avatarbig 用户头像大图
	 * @ret_infor score 用户积分
	 * @ret_infor feeaccount 账户余额
	 * @ret_infor validflag 用户状态 1：正常;2：冻结	 
	 * @ret_infor remarks 备注
	 * @ret_infor flag 用户身份 1：普通;2：代理商
	 * @ret_infor lastlogintime 最后登录时间
	 * @ret_infor regdate 用户注册日期
	 * @ret_infor referee_code 邀请码
	 * @ret_infor phone 手机号码
     * @ret_infor paypassword 提现密码
     * @ret_infor alipay_account 支付宝账号
     * @ret_infor bank 银行名称
     * @ret_infor bankcard_name 户主姓名
     * @ret_infor bankcard 银行卡号
     * @ret_infor wealth 财气值
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function client_login()
	{
		$post_array = array('username','password');
		sys_check_post($post_array);//检查post完整性
		unset($post_array);
		$account = _POST('username');
		$password = _POST('password');

		$sqlstr = "select * from sys_client where id>0 and account='$account' ";
		$temp_array = $this->get_list_bysql($sqlstr);

		if (!$temp_array) sys_out_fail('账号不存在', 106);//用户账号不存在！
		if (md5($password) != $temp_array[0]['password']) sys_out_fail('账号/密码错误', 102);
		if ($temp_array[0]['validflag'] != '1') sys_out_fail('账号被冻结', 104);//账户被冻结

		$client_id = $temp_array[0]['id'];
		$name = $temp_array[0]['nickname'];
		$temp_array[0]['token'] = sys_get_token($client_id, $nickname);
		
		$sqlstr = " update sys_client set lastlogintime='" . sys_get_time() . "' where id =$client_id ";
		$this->do_execute($sqlstr);

		//插入登陆历史表
		$sqlstr = " insert into sys_login_history set client_id=$client_id,regdate='" . sys_get_time() . "'";
		$this->do_execute($sqlstr);
		sys_out_success(0, $temp_array);
	}


	/**
	 * 密码修改
	 * @parent client_root
	 * @req_params token 登录令牌
	 * @req_params old_password 旧密码  123456
	 * @req_params new_password 新密码  12345678
	 * @req_desc 特别提示： 客户端应该实现并自行判定2次新密码的输入，是否相同。
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function password_save()
	{
		$result=$this->pub_password_save();
		sys_out_result($result);
	}


	/**
	 * 提现密码修改
	 * @parent client_root
	 * @req_params token 登录令牌
	 * @req_params password 新密码  12345678
	 * @req_desc 特别提示： 客户端应该实现并自行判定2次新密码的输入，是否相同。
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function paypassword_save()
	{
		$result=$this->pub_paypassword_set();
		sys_out_result($result);
	}


	/**
	 * 意见反馈
	 * @parent client_root
	 * @req_params token 登录令牌
	 * @req_params advice 意见内容
	 * @req_params devicetype 手机硬件类型 1：苹果；2：安卓 2
	 * @req_params lastloginversion 登陆所用的系统版本号 记录用户的登录版本，方便服务器运维统计 1.0.0
	 * @req_params phonetype 手机品牌
	 * @req_params phoneversion 手机系统型号
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor
	 * @special 
	 */
	public function advice_save()
	{
		$post_array = array('advice','devicetype','lastloginversion','phonetype','phoneversion');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$advice = _POST('advice');
		$devicetype = _POST('devicetype');
		$lastloginversion = _POST('lastloginversion');
		$phonetype = _POST('phonetype');
		$phoneversion = _POST('phoneversion');
		$client_id = sys_get_cid();
		$adv_time = sys_get_time();
		$advice_id = $this->get_one_bysql("select id from sys_advice where client_id='$client_id'");
		if($advice_id){
			$sqlstr = " update sys_advice set advice='$advice',devicetype='$devicetype',lastloginversion='$lastloginversion',phonetype='$phonetype',phoneversion='$phoneversion',adv_time='$adv_time' where client_id = $client_id ";			
		}else{
			$sqlstr = " insert into sys_advice set advice='$advice',devicetype='$devicetype',lastloginversion='$lastloginversion',phonetype='$phonetype',phoneversion='$phoneversion',adv_time='$adv_time',client_id=$client_id ";
		}
		$result = $this->do_execute($sqlstr);
		sys_out_result($result);
	}

	
	/**
	 * 个人资料保存
	 * @parent client_root
	 * @req_params token 登录令牌
	 * @req_params nickname 用户昵称
	 * @req_params phone 手机号码
	 * @req_params referee_code 推荐码 可选填
	 * @req_desc 特别说明：个人资料中的头像请参考 上传文件 接口。
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function client_save()
	{	
		$post_array = array('nickname','phone');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$nickname = _POST('nickname');
		$phone = _POST('phone');
		$referee_code = _POST('referee_code');
		$client_id = sys_get_cid();
		$sqlstr = "select id from sys_client where nickname='$nickname' and id != $client_id";
		$referee_ids = $this->get_one_bysql($sqlstr);
		if ($referee_ids) sys_out_fail('该昵称已存在', 106);	
		$sql_array = NULL;	
		if(!empty($referee_code)){
			$sqlstr = "select write_code from sys_client where id='$client_id'";
			$write_code = $this->get_one_bysql($sqlstr);
			if($write_code){
				if ($write_code != $referee_code) sys_out_fail('推荐码已填，不能修改', 106);
			}else{
				$sqlstr = "select id from sys_client where referee_code='$referee_code'";
				$referee_id = $this->get_one_bysql($sqlstr);
				if (empty($referee_id)) sys_out_fail('推荐码不存在', 106);
				$invite_score = $this->get_one_bysql("select invite_score from sys_config where id=1");
				$sql_array[] = " update sys_client set flag=2,score=score+$invite_score where id = $referee_id ";
			}				
		}
		$sql_array[] = " update sys_client set nickname='$nickname',phone='$phone',write_code='$referee_code' where id = $client_id ";
		$result = $this->do_transaction($sql_array);
		sys_out_result($result);
	}


	/**
	 * 绑定上级
	 * @parent client_root
	 * @req_params token 登录令牌
	 * @req_params referee_code 推荐码
	 * @req_desc 
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function referee_save()
	{	
		sys_check_post_single('referee_code');//检查post必选参数完整性
		$referee_code = _POST('referee_code');
		$client_id = sys_get_cid();	
		$sql_array = NULL;
		$sqlstr = "select write_code from sys_client where id='$client_id'";
		$write_code = $this->get_one_bysql($sqlstr);
		if($write_code){
			if ($write_code != $referee_code){
				sys_out_fail('推荐码已填，不能修改', 106);
			}
			sys_out_success();
		}else{
			$sql_array[] = " update sys_client set write_code='$referee_code' where id = $client_id ";
			$sqlstr = "select id from sys_client where referee_code='$referee_code'";
			$referee_id = $this->get_one_bysql($sqlstr);
			if (empty($referee_id)) sys_out_fail('推荐码不存在', 106);
			$invite_score = $this->get_one_bysql("select invite_score from sys_config where id=1");
			$sql_array[] = " update sys_client set flag=2,score=score+$invite_score where id = $referee_id ";
			$result = $this->do_transaction($sql_array);
			sys_out_result($result);
		}		
	
	}

	
	/**
	 * 用户详情
	 * @parent client_root
	 * @req_params token 登录令牌
	 * @req_params id 用户id
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor id 用户id
	 * @ret_infor account 用户账号
	 * @ret_infor nickname 用户昵称
	 * @ret_infor avatar 用户头像
	 * @ret_infor avatarbig 用户头像大图
	 * @ret_infor score 用户积分
	 * @ret_infor feeaccount 账户余额
	 * @ret_infor validflag 用户状态 1：正常;2：冻结	 
	 * @ret_infor islive 是否激活 1：已激活;2：未激活	 
	 * @ret_infor isgetbag 是否领取激活红包 1：是;0：否
	 * @ret_infor remarks 备注
	 * @ret_infor flag 用户身份 1：普通;2：代理商
	 * @ret_infor lastlogintime 最后登录时间
	 * @ret_infor regdate 用户注册日期
	 * @ret_infor referee_code 邀请码
	 * @ret_infor phone 手机号码
     * @ret_infor paypassword 提现密码
     * @ret_infor alipay_account 支付宝账号
     * @ret_infor bank 银行名称
     * @ret_infor bankcard_name 户主姓名
     * @ret_infor bankcard 银行卡号
     * @ret_infor wealth 财气值
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function client_get()
	{
		sys_check_post_single('id');//检查post必选参数完整性
		$id=_POST('id');
		$sqlstr = "select * from sys_client where id='$id'";
		$member_list = $this->get_list_bysql($sqlstr);
		sys_out_success(0, $member_list);
	}


	/**
	 * 我的收藏
	 * @parent client_root
	 * @req_params token 登录令牌
	 * @req_params type 类型 1：商家;2：商品
	 * @req_params lng 经度 仅type=1是需填
	 * @req_params lat 纬度 仅type=1是需填
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 相关ID
	 * @ret_infor name 名称
	 * @ret_infor img 封面图片
	 * @ret_infor bigimg 封面大图
	 * @ret_infor star 评分
	 * @ret_infor averfee 人均消费
	 * @ret_infor distance 距离
	 * @ret_infor present_price 现价
	 * @ret_infor original_price 原价
	 * @ret_infor salenum 销量
	 * @special 
	 */
	public function love_list()
	{
		sys_check_post_single('type');//检查post必选参数完整性
		$type = _POST('type');
		$client_id = sys_get_cid();	
		if($type == 1){
			$lng=_POST('lng');
			$lat=_POST('lat');
			if(!$lng || !$lat){
				sys_out_fail("请将信息填写完毕");
			}
        	$distance_str=" round(12756274*asin(Sqrt(pow(sin(($lat-s.lat)*0.008726646),2)+Cos($lat*0.0174533)*Cos(s.lat*0.0174533)*pow(sin(($lng-s.lng)*0.008726646),2)))/1000,2) ";
			$love_lists = $this->get_list_bysql("select s.id,s.name,s.img,s.bigimg,s.star,s.averfee,$distance_str as distance from sys_love l left join sys_shop s on l.about_id=s.id where l.client_id=$client_id and l.type=$type order by l.id desc");
		}else if($type == 2){
			$love_lists = $this->get_list_bysql("select g.id,g.name,g.img,g.bigimg,g.present_price,g.original_price,g.salenum from sys_love l left join sys_good g on l.about_id=g.id where l.client_id=$client_id and l.type=$type order by id desc");
		}
		
		sys_out_success(0, $love_lists);
	}


	/**
	 * 通知列表
	 * @parent notice_root
	 * @req_params token 登录令牌
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 通知主键id
	 * @ret_infor regdate 发布时间
	 * @ret_infor client_id 相关用户ID
	 * @ret_infor content 通知内容
	 * @ret_infor from_id 通知来源作者主键id
	 * @ret_infor looktype 读取状态 0：未读；1：已读
	 * @ret_infor keytype 消息类型 1：系统消息；2：订单消息
	 * @special 
	 */
	public function mess_list()
	{
		$client_id = sys_get_cid();	
		$order_lists = $this->get_list_bysql("select * from sys_mess where client_id='$client_id' order by id desc");
		sys_out_success(0, $order_lists);
	}


	/**
	 * 查看是否有新通知
	 * @parent notice_root
	 * @req_params token 登录令牌
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor messnum 是否有新通知 0：没有;>0：有
	 * @special 
	 */
	public function mess_new()
	{
		$client_id = sys_get_cid();	
		$order_num = $this->get_one_bysql("select count(*) from sys_mess where client_id='$client_id' and looktype=0");
		$temp_array[0]['messnum'] = $order_num;
		sys_out_success(0, $temp_array);
	}

	/**
	 * 通知操作
	 * @parent notice_root
	 * @req_params token 登录令牌
	 * @req_params id 通知主键id 从通知列表获取
	 * @req_params operatetype 操作类型 1：全部置为已读(当打开消息通知页面时，直接请求全部设为已读)$2：删除单条$3：清空消息列表
	 * @req_desc 
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function mess_saveoperate()
	{
		$operatetype=_POST('operatetype');
		if($operatetype == 1 || $operatetype == 3){
			$post_array=array('operatetype');
		}else if($operatetype == 2){
			$post_array=array('id','operatetype');
		}		
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		
		$client_id=sys_get_cid();

		switch($operatetype)
		{
			case "1"://全部置为已读
				$sqlstr=" update sys_mess set looktype=1 where client_id=$client_id";
				break;
			case "2"://删除单条
				$mess_id=_POST('id');
				$sqlstr=" delete from sys_mess where id=$mess_id and client_id=$client_id";
				break;
			case "3"://删除全部
				$sqlstr=" delete from sys_mess where client_id=$client_id";
				break;
			default:
				sys_out_fail('operatetype取值范围不正确',101);
				break;
		}
		$result=$this->do_execute($sqlstr);
		sys_out_result($result);
	}

	
	/**
	 * 激活一元红包领取
	 * @parent prize_root 
	 * @req_params token 登录令牌
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special 
	 */
	public function redbag_get()
	{
		$client_id=sys_get_cid();
		$client_list = $this->get_list_bysql("select islive,isgetbag from sys_client where id=$client_id");
		$islive = $client_list[0]['islive'];
		$isgetbag = $client_list[0]['isgetbag'];
		if($islive ==1 && $isgetbag == 0){
			$sql_array = NULL;
			$sql_array[] = " insert into sys_cash set client_id=$client_id,regdate='" . sys_get_time() . "',score=1,cashflag=3,isget=1";
			$sql_array[] = " update sys_client set feeaccount=feeaccount+1,isgetbag=1 where id = $client_id ";			
		}else{
			sys_out_fail('不符合领取规则');
		}
		$result = $this->do_transaction($sql_array);
		sys_out_result($result);
	}


		
	/**
	 * 抽奖
	 * @parent prize_root 
	 * @req_params token 登录令牌
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor type 抽奖类型 1：红包;2：积分;3：手气不佳;4：猜一猜
	 * @ret_infor scoredetail_id 写入积分表的ID
	 * @ret_infor cash_id 写入财务表的ID
	 * @ret_infor shop_id 商家ID
	 * @ret_infor shopname 商家名称
	 * @ret_infor score 获得的积分或金额
	 * @ret_infor image_text_id 题目所在表中ID
	 * @ret_infor image_text_image 题图
	 * @special 
	 */
	public function prize_get()
	{
		$client_id = sys_get_cid();		
		$randnum = rand(1,100);
		$luckdraw_rate = $this->get_one_bysql("select luckdraw_rate from sys_set where id=1");
		$luckdraw_num = 100*$luckdraw_rate;
		if($randnum <= $luckdraw_num){
			$keytype = 1;
		}else{
			$keytype = 2;
		}
		$scoredetail_id = '';
		$cash_id = '';
		$score = '';
		$image_text_list = array();
		switch ($keytype){
			case "1"://抽奖
				$shop_list = $this->get_list_bysql("select * from sys_shop where validflag =1 order by rand() limit 1");
				$shop_id = $shop_list[0]['id'];
				$shopname = $shop_list[0]['name'];
				$redbag_rate = $shop_list[0]['redbag_rate'];
				$score_rate = $shop_list[0]['score_rate'];
				$redbag_num = $redbag_rate*100;
				$score_num = $score_rate*100;
				$score_start = $redbag_num + 1;
				$score_end = $redbag_num + $score_num;
				$rand_num = rand(1,100);
				if($rand_num <= $redbag_num){
					$flag = 1;
					$islive = $this->get_one_bysql("select islive from sys_client where id=$client_id");
					if($islive == 1){
						$wealth = $this->get_one_bysql("select wealth from sys_client where id=$client_id");
						if($wealth == 100){
							$flag = 3;
						}else if($wealth > 100){
							$zhuce_num = $this->get_one_bysql("select count(*) from sys_phone where client_id=$client_id and iszhuce=1");
							if($zhuce_num < 10){
								$score = 0.01;
								$sqlstr = " insert into sys_cash set client_id=$client_id,regdate='" . sys_get_time() . "',score='$score',cashflag=3,isget=2,shop_id=$shop_id";
								$this->do_execute($sqlstr);
								$cash_id = $this->get_insert_id();
							}else{
								$redbag = $shop_list[0]['redbag'];
								$redbag_type = $shop_list[0]['redbag_type'];
								if($redbag_type == 1){
									$gu_num = $shop_list[0]['gu_redbagchance'] * 100;
									$rand = rand(1,100);
									if($rand <= $gu_num){
										$score = $shop_list[0]['gu_redbag'];
										if($redbag < $shop_list[0]['gu_redbag']){
											$score = $redbag;
										}								
										$sqlstr = " insert into sys_cash set client_id=$client_id,regdate='" . sys_get_time() . "',score='$score',cashflag=3,isget=2,shop_id=$shop_id";
										$this->do_execute($sqlstr);
										$cash_id = $this->get_insert_id();
									}
								}else{
									$qu_num = $shop_list[0]['qu_redbagchance'] * 100;
									$rand = rand(1,100);
									if($rand <= $qu_num){
										$qu_minscore = $shop_list[0]['qu_minredbag'];
										$qu_maxscore = $shop_list[0]['qu_maxredbag'];
										$score = rand($qu_minscore*100,$qu_maxscore*100)/100;
										if($redbag < $shop_list[0]['gu_redbag']){
											$score = $redbag;
										}
										$sqlstr = " insert into sys_cash set client_id=$client_id,regdate='" . sys_get_time() . "',score='$score',cashflag=3,isget=2,shop_id=$shop_id";
										$this->do_execute($sqlstr);
										$cash_id = $this->get_insert_id();
									}
								}
							}
						}
						
					}else{
						$score = 0.01;
						$sqlstr = " insert into sys_cash set client_id=$client_id,regdate='" . sys_get_time() . "',score='$score',cashflag=3,isget=2,shop_id=$shop_id";
						$this->do_execute($sqlstr);
						$cash_id = $this->get_insert_id();
					}				
					
				}else if($score_start <= $rand_num && $score_end >= $rand_num){
					$flag = 2;
					$score_type = $shop_list[0]['score_type'];
					if($score_type == 1){
						$gu_num = $shop_list[0]['gu_scorechance'] * 100;
						$rand = rand(1,100);
						if($rand <= $gu_num){
							$score = $shop_list[0]['gu_score'];
							$sqlstr = " insert into sys_scoredetail set client_id=$client_id,regdate='" . sys_get_time() . "',score='$score',scoretype=2,isget=2,shop_id=$shop_id";
							$this->do_execute($sqlstr);
							$scoredetail_id = $this->get_insert_id();
						}
					}else{
						$qu_num = $shop_list[0]['qu_scorechance'] * 100;
						$rand = rand(1,100);
						if($rand <= $qu_num){
							$qu_minscore = $shop_list[0]['qu_minscore'];
							$qu_maxscore = $shop_list[0]['qu_maxscore'];
							$score = rand($qu_minscore,$qu_maxscore);
							$sqlstr = " insert into sys_scoredetail set client_id=$client_id,regdate='" . sys_get_time() . "',score='$score',scoretype=2,isget=2,shop_id=$shop_id";
							$this->do_execute($sqlstr);
							$scoredetail_id = $this->get_insert_id();
						}
					}
					
				}else{
					$flag = 3;
				}				
			 	break;
			 	
			case "2"://猜一猜
				$image_text_list = $this->get_list_bysql("select im.* from sys_image_text as im left join sys_shop s on im.shop_id=s.id where s.validflag =1 order by rand() limit 1");
				$flag = 4;
			 	break;	
			 		
			default:
				break;
				
		}
		$temp_array[0]['type'] = $flag;
		$temp_array[0]['scoredetail_id'] = $scoredetail_id;
		$temp_array[0]['cash_id'] = $cash_id;
		$temp_array[0]['shop_id'] = $shop_id;
		$temp_array[0]['shopname'] = $shopname;
		$temp_array[0]['score'] = $score;
		$temp_array[0]['image_text_id'] = $image_text_list[0]['id'];
		$temp_array[0]['image_text_image'] = $image_text_list[0]['image'];
		sys_out_success(0, $temp_array);
	}


	/**
	 * 判断答题
	 * @parent prize_root 
	 * @req_params token 登录令牌
	 * @req_params image_text_id 题目所在表中ID
	 * @req_params answer 用户填写的答案
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor true_answer 正确答案
	 * @ret_infor flag 答题结果 0：答错了;1：答对了手气不佳;2：答对并获得积分
	 * @ret_infor scoredetail_id 写入积分表的ID
	 * @ret_infor score 获得的积分	 
	 * @ret_infor shop_id 商家ID
	 * @ret_infor shopname 商家名称
	 * @special 
	 */
	public function answer_get()
	{
		$post_array = array('image_text_id','answer');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();
		$image_text_id = _POST('image_text_id');
		$answer = _POST('answer');
		$score = '';
		$scoredetail_id = '';
		$flag = 0;
		$image_text_list = $this->get_list_bysql("select * from sys_image_text where id = $image_text_id");
		$true_answer = $image_text_list[0]['answer'];
		if($answer == $true_answer){
			$type = $image_text_list[0]['type'];
			$shop_id = $image_text_list[0]['shop_id'];
			$shopname = $this->get_one_bysql("select name from sys_shop where validflag = 1 where id = $shop_id");
			$score_set = $this->get_list_bysql("select * from sys_set_image_text where id = 1");
			if($type == 1){
				$gu_num = $score_set[0]['gu_chance'] * 100;
				$rand = rand(1,100);
				if($rand <= $gu_num){					
					$score = $score_set[0]['gu_score'];
					$sqlstr = " insert into sys_scoredetail set client_id=$client_id,regdate='" . sys_get_time() . "',score='$score',scoretype=1,isget=2,shop_id=$shop_id";
					$this->do_execute($sqlstr);
					$scoredetail_id = $this->get_insert_id();
					$flag = 2;										
				}else{
					$flag = 1;
				}
			}else{
				$qu_num = $score_set[0]['qu_chance'] * 100;
				$rand = rand(1,100);
				if($rand <= $qu_num){
					$qu_minscore = $score_set[0]['qu_minscore'];
					$qu_maxscore = $score_set[0]['qu_maxscore'];
					$score = rand($qu_minscore,$qu_maxscore);
					$sqlstr = " insert into sys_scoredetail set client_id=$client_id,regdate='" . sys_get_time() . "',score='$score',scoretype=1,isget=2,shop_id=$shop_id";
					$this->do_execute($sqlstr);
					$scoredetail_id = $this->get_insert_id();	
					$flag = 2;									
				}else{
					$flag = 1;	
				}
			}
		}		
		$temp_array[0]['true_answer'] = $true_answer;
		$temp_array[0]['flag'] = $flag;
		$temp_array[0]['score'] = $score;
		$temp_array[0]['shop_id'] = $shop_id;
		$temp_array[0]['shopname'] = $shopname;
		$temp_array[0]['scoredetail_id'] = $scoredetail_id;
		sys_out_success(0, $temp_array);		
	}



	/**
	 * 获取相关商家的信息
	 * @parent prize_root 
	 * @req_params token 登录令牌
	 * @req_params type 抽奖类型 1：积分抽奖;2：红包抽奖;3：答题抽奖
	 * @req_params about_id 数据被写入表中的ID
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 商家id
     * @ret_infor score 积分或红包金额
	 * @ret_infor name 商家名称
	 * @ret_infor img 商家logo
	 * @ret_infor bigimg 商家logo大图
	 * @ret_infor telphone 商家联系电话
	 * @ret_infor linker 商家联系人
	 * @ret_infor content 商家详情
	 * @ret_infor address 商家地址
	 * @ret_infor lng 经度
	 * @ret_infor lat 纬度
	 * @ret_infor remarks 备注
	 * @ret_infor validflag 商家状态 1:正常;2:冻结
	 * @ret_infor regdate 添加时间
	 * @special 
	 */
	public function scoreorcash_get()
	{
		$post_array = array('type','about_id');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();
		$type = _POST('type');
		$about_id = _POST('about_id');
		$type_array = array(1,2,3);
		if(!in_array($type,$type_array)){
			sys_out_fail('参数传递错误', 101);
		}
		if($type == 2){
			$shop_list = $this->get_list_bysql("select c.score,s.* from sys_cash c left join sys_shop s on s.id = c.shop_id where c.id = $about_id and c.cashflag = 3 and c.isget = 2 and s.validflag = 1");
		}else{
			if($type == 1){
				$scoretype = 2;
			}else{
				$scoretype = 1;
			}
			$shop_list = $this->get_list_bysql("select sc.score,s.* from sys_scoredetail sc left join sys_shop s on s.id = sc.shop_id where sc.id = $about_id and sc.scoretype = $scoretype and sc.isget = 2 and s.validflag = 1");
		}
		if(!$shop_list){
			sys_out_fail('参数传递错误或该商家已下架', 101);
		}
		sys_out_success(0, $shop_list);
	}
	


	/**
	 * 获取积分或金额
	 * @parent prize_root 
	 * @req_params token 登录令牌
	 * @req_params type 抽奖类型 1：积分;2：红包;3：答题抽奖
	 * @req_params about_id 数据被写入表中的ID
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor tip 提示内容 0：提示根据UI写;1：必须提示部分红包金额无法领取
	 * @special 
	 */
	public function scoreorcash_save()
	{
		$post_array = array('type','about_id');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();
		$type = _POST('type');
		$about_id = _POST('about_id');
		$type_array = array(1,2,3);
		if(!in_array($type,$type_array)){
			sys_out_fail('参数传递错误', 101);
		}
		$tip = 0;
		$regdate = sys_get_time();
		$sqlstr_array=NULL;
		if($type == 2){
			$shop_list = $this->get_list_bysql("select * from sys_cash where id = $about_id and cashflag = 3 and isget = 2");
			if(!$shop_list){
				sys_out_fail('参数传递错误', 101);
			}
			$score = $shop_list[0]['score'];
			$islive = $this->get_one_bysql("select islive from sys_client where id=$client_id");
			if($islive == 1){
				$wealth = $this->get_one_bysql("select wealth from sys_client where id=$client_id");
				$wealth_use = $wealth - 100;
				if($wealth_use < $score){
					$score = $wealth_use;
					$tip = 1;
				}
				$shop_id = $shop_list[0]['shop_id'];			
				$redbaglist = $this->get_list_bysql("select redbag,wealth_redbag,recharge_redbag from sys_shop where id=$shop_id");
				$redbag = $redbaglist[0]['redbag'];
				if($redbag >= $score){
					$recharge_redbag = $redbaglist[0]['recharge_redbag'];
					if($recharge_redbag >= $score){
						$sqlstr_array[] = " update sys_shop set recharge_redbag=recharge_redbag-$score,redbag=redbag-$score where id = $shop_id ";	
						$sqlstr_array[] = " insert into sys_recharge_redbag set type=2,fee='$score',shop_id=$shop_id,regdate='$regdate'";						
					}else if($recharge_redbag > 0 && $recharge_redbag < $score){
						$last_score = $score-$recharge_redbag;
						$wealth_redbag = $redbaglist[0]['wealth_redbag'];
						if($wealth_redbag >= $last_score){
							$sqlstr_array[] = " update sys_shop set recharge_redbag=0,wealth_redbag=wealth_redbag-$last_score,redbag=redbag-$score where id = $shop_id ";
							$sqlstr_array[] = " insert into sys_recharge_redbag set type=2,fee='$recharge_redbag',shop_id=$shop_id,regdate='$regdate'";
						}else{
							sys_out_fail('红包不能领取');
						}						
					}
					$sqlstr_array[] = " update sys_client set wealth=wealth-$score where id = $client_id ";
				}else{
					sys_out_fail('红包不能领取');
				}
			}else{				
				$sqlstr_array[] = " update sys_client set feeaccount=feeaccount+$score where id = $client_id ";
			}			
			
		}else{
			if($type == 1){
				$scoretype = 2;
			}else{
				$scoretype = 1;
			}
			$shop_list = $this->get_list_bysql("select * from sys_scoredetail where id = $about_id and scoretype = $scoretype and isget = 2");
			if(!$shop_list){
				sys_out_fail('参数传递错误', 101);
			}
			$score = $shop_list[0]['score'];
			$sqlstr_array[] = " update sys_scoredetail set isget=1 where id = $about_id and isget=2";	
			$sqlstr_array[] = " update sys_client set score=score+$score where id = $client_id ";
		}
		$result = $this->do_transaction($sqlstr_array);
		if($result !== false && $type == 2){
			$redbaglist = $this->get_list_bysql("select redbag,wealth_redbag from sys_shop where id=$shop_id");
			$redbag = $redbaglist[0]['redbag'];
			$wealth_redbag = $redbaglist[0]['wealth_redbag'];
			if($redbag == 0 && $wealth_redbag == 0){
				$sqlstr = " update sys_shop set validflag=3 where id =$shop_id ";
				$this->do_execute($sqlstr);
			}
			if($tip == 1){
				$temp_array[0]['tip'] = 1;
				sys_out_success('领取成功，部分红包金额无法领取',$temp_array);
			}else{			
				$temp_array[0]['tip'] = 0;
				sys_out_success('领取成功:',$temp_array);
			}
		}else{
			sys_out_result($result);
		}
		
	}
	


	/**
	 * 一级广告列表
	 * @parent shop_root 
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 广告主键id
	 * @ret_infor adname 广告名称
	 * @ret_infor jumptype 跳转类型 1商品详情;2图文内容 
	 * @ret_infor goods_id 对应商品ID
	 * @ret_infor content 图文内容
	 * @ret_infor adimg 广告图片
	 * @ret_infor adbigimg 广告大图
	 * @ret_infor isshow 是否显示 1是;2否
	 * @ret_infor orderby 排序
	 * @ret_infor regdate 发布时间
	 * @special 
	 */
	public function one_ad_list()
	{
		$order_lists = $this->get_list_bysql("select mt.* from sys_ad mt where mt.isshow=1 and mt.adlevel=1 order by mt.orderby,mt.id desc");
		sys_out_success(0, $order_lists);
	}


	
	/**
	 * 二级广告列表
	 * @parent shop_root 
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 广告主键id
	 * @ret_infor adname 广告名称
	 * @ret_infor jumptype 跳转类型 1商品详情;2图文内容 
	 * @ret_infor goods_id 对应商品ID
	 * @ret_infor content 图文内容
	 * @ret_infor adimg 广告图片
	 * @ret_infor adbigimg 广告大图
	 * @ret_infor isshow 是否显示 1是;2否
	 * @ret_infor orderby 排序
	 * @ret_infor regdate 发布时间
	 * @special 
	 */
	public function two_ad_list()
	{
		$order_lists = $this->get_list_bysql("select mt.* from sys_ad mt where mt.isshow=1 and mt.adlevel=2 order by mt.orderby,mt.id desc");
		sys_out_success(0, $order_lists);
	}

		

	/**
	 * 一级分类
	 * @parent shop_root
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 分类主键id
	 * @ret_infor img 分类图标
	 * @ret_infor imgbig 分类大图标
	 * @ret_infor name 分类名称
	 * @ret_infor goodsnum 分类下商品个数
	 * @ret_infor regdate 添加时间
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function one_classify_list()
	{
		$one_classify_list = $this->get_list_bysql("select mt.* from sys_classify mt where mt.parentid=0 and mt.flag=1 limit 0,7");
		sys_out_success(0, $one_classify_list);
	}


	
	/**
	 * 二级分类
	 * @parent shop_root
	 * @req_params id 一级分类id
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 分类主键id
	 * @ret_infor img 分类图标
	 * @ret_infor imgbig 分类大图标
	 * @ret_infor name 分类名称
	 * @ret_infor goodsnum 分类下商品个数
	 * @ret_infor regdate 添加时间
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function two_classify_list()
	{
		sys_check_post_single('id');//检查post必选参数完整性
		$id=_POST('id');
		$two_classify_list = $this->get_list_bysql("select mt.* from sys_classify mt where mt.parentid=$id and mt.flag=1 limit 0,8");
		sys_out_success(0, $two_classify_list);
	}


	
	/**
	 * 全部分类
	 * @parent shop_root
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor children 分类集合 id:分类主键id$img:分类图标$name:分类名称$parentid:分类父级ID
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function classify_list()
	{
		$temp_array[0] = array();
		//先拿一级
		$result_1 = $this -> get_list_bysql("select id,parentid,name,img from sys_classify where parentid=0 and flag=1");
		$i = 0 ;
		foreach ($result_1 as $result_1_i)
		{
			$temp_array[0]['children'][$i] = array(
				'id'=>$result_1_i['id'],
				'parentid'=>$result_1_i['parentid'],
				'name'=>$result_1_i['name'],
				'img'=>$result_1_i['img'],
				'children'=>array()
			);

			//再拿二级
			$parentid_1 = $result_1_i['id'];
			$result_2 = $this -> get_list_bysql("select id,parentid,name,img from sys_classify where parentid=$parentid_1 and flag=1");

			$j = 0 ;
			foreach ($result_2 as $result_2_i)
			{
				$temp_array[0]['children'][$i]['children'][$j] = array(
					'id'=>$result_2_i['id'],
					'parentid'=>$result_2_i['parentid'],
					'name'=>$result_2_i['name'],
					'img'=>$result_2_i['img'],
				);
				$j ++ ;
			}

			$i++;
		}
		sys_out_success(0,$temp_array);
	}



	/**
	 * 商城首页推荐列表
	 * @parent shop_root
	 * @req_params city 定位城市
	 * @req_params lng 经度
	 * @req_params lat 纬度
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor img 封面图片
	 * @ret_infor name 名称
	 * @ret_infor star 评分
	 * @ret_infor averfee 人均价
	 * @ret_infor distance 距离（km）
	 * @ret_infor children 商品集 id:主键id$img:封面图片$name:名称$present_price:现价$original_price:原价$salenum:销量
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function shoporgood_list()
	{
		$post_array = array('city','lng','lat');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$citys=_POST('city');
		$city_list = $this->get_list_bysql("select o.*,c.name as province,ca.name as city from sys_opencity o left join sys_cascade_district c on o.district_1_id = c.id left join sys_cascade_district ca on o.district_2_id = ca.id where province like '%$citys%' or city like '%$citys%'");
		if($city_list){
			$province = $city_list[0]['province'];
			$city = $city_list[0]['city'];
			if(strpos($province, '市') !== false){
				$city_type = 1;
				$city_id = $city_list[0]['district_1_id'];
			}else{
				$city_type = 2;
				$city_id = $city_list[0]['district_2_id'];
			}
		}else{
			sys_out_fail('该城市暂时未开通');
		}
		$temp_array[0] = array();
		//先拿一级
		$client_id = sys_get_cid();
		$lng=_POST('lng');
		$lat=_POST('lat');
        $distance_str=" round(12756274*asin(Sqrt(pow(sin(($lat-lat)*0.008726646),2)+Cos($lat*0.0174533)*Cos(lat*0.0174533)*pow(sin(($lng-lng)*0.008726646),2)))/1000,2) ";
        if($city_type == 1){
	        $result_1 = $this -> get_list_bysql("select id,name,img,star,averfee,$distance_str as distance from sys_shop where validflag!=2 and isrecommend=1 and district_1_id=$city_id order by orderby asc");
        }else{
	        $result_1 = $this -> get_list_bysql("select id,name,img,star,averfee,$distance_str as distance from sys_shop where validflag!=2 and isrecommend=1 and district_2_id=$city_id order by orderby asc");
        }
		
		$i = 0 ;
		foreach ($result_1 as $result_1_i)
		{
			$temp_array[$i] = array(
				'id'=>$result_1_i['id'],
				'name'=>$result_1_i['name'],
				'img'=>$result_1_i['img'],
				'star'=>$result_1_i['star'],
				'averfee'=>$result_1_i['averfee'],
				'distance'=>$result_1_i['distance'],
				'children'=>array()
			);

			//再拿二级
			$parentid_1 = $result_1_i['id'];
			$result_2 = $this -> get_list_bysql("select id,name,img,present_price,original_price,salenum from sys_good where shop_id=$parentid_1 and flag=1 and doflag=2 and isrecommend=1 order by orderby asc limit 0,2");

			$j = 0 ;
			foreach ($result_2 as $result_2_i)
			{
				$temp_array[$i]['children'][$j] = array(
					'id'=>$result_2_i['id'],
					'name'=>$result_2_i['name'],
					'img'=>$result_2_i['img'],
					'present_price'=>$result_2_i['present_price'],
					'original_price'=>$result_2_i['original_price'],
					'salenum'=>$result_2_i['salenum'],
				);
				$j ++ ;
			}

			$i++;
		}
		sys_out_success(0,$temp_array);
	}


	/**
	 * 二级分类推荐列表
	 * @parent shop_root
	 * @req_params lng 经度
	 * @req_params lat 纬度
	 * @req_params one_classify_id 一级分类ID
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor img 封面图片
	 * @ret_infor name 名称
	 * @ret_infor star 评分
	 * @ret_infor averfee 人均价
	 * @ret_infor distance 距离（km）
	 * @ret_infor children 商品集 id:主键id$img:封面图片$name:名称$present_price:现价$original_price:原价$salenum:销量
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function two_shoporgood_list()
	{
		$post_array = array('lng','lat','one_classify_id');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$temp_array[0] = array();
		//先拿一级
		$client_id = sys_get_cid();
		$lng=_POST('lng');
		$lat=_POST('lat');
		$one_classify_id=_POST('one_classify_id');
        $distance_str=" round(12756274*asin(Sqrt(pow(sin(($lat-lat)*0.008726646),2)+Cos($lat*0.0174533)*Cos(lat*0.0174533)*pow(sin(($lng-lng)*0.008726646),2)))/1000,2) ";
        $result_1 = $this -> get_list_bysql("select id,name,img,star,averfee,$distance_str as distance from sys_shop where validflag!=2 and one_classify_id=$one_classify_id and isrecommend=1 order by orderby asc");
		
		$i = 0 ;
		foreach ($result_1 as $result_1_i)
		{
			$temp_array[$i] = array(
				'id'=>$result_1_i['id'],
				'name'=>$result_1_i['name'],
				'img'=>$result_1_i['img'],
				'star'=>$result_1_i['star'],
				'averfee'=>$result_1_i['averfee'],
				'distance'=>$result_1_i['distance'],
				'children'=>array()
			);

			//再拿二级
			$parentid_1 = $result_1_i['id'];
			$result_2 = $this -> get_list_bysql("select id,name,img,present_price,original_price,salenum from sys_good where shop_id=$parentid_1 and flag=1 and doflag=2 and isrecommend=1 order by orderby asc limit 0,2");

			$j = 0 ;
			foreach ($result_2 as $result_2_i)
			{
				$temp_array[$i]['children'][$j] = array(
					'id'=>$result_2_i['id'],
					'name'=>$result_2_i['name'],
					'img'=>$result_2_i['img'],
					'present_price'=>$result_2_i['present_price'],
					'original_price'=>$result_2_i['original_price'],
					'salenum'=>$result_2_i['salenum'],
				);
				$j ++ ;
			}

			$i++;
		}
		sys_out_success(0,$temp_array);
	}


	/**
	 * 商城商家详情
	 * @parent shop_root
	 * @req_params lng 经度
	 * @req_params lat 纬度
	 * @req_params shop_id 商家ID
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_level id 主键id
	 * @ret_level name 名称
	 * @ret_level star 评分
	 * @ret_level averfee 人均价
	 * @ret_level distance 距离（km）
	 * @ret_level address 地址
	 * @ret_level telphone 联系电话
	 * @ret_infor imgItems 商家图片集 id:图片id$imgurl:缩略图$imgurlbig:大图
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function shopshow_list()
	{
		$post_array = array('lng','lat','shop_id');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$temp_array[0] = array();
		$lng=_POST('lng');
		$lat=_POST('lat');
		$shop_id=_POST('shop_id');
        $distance_str=" round(12756274*asin(Sqrt(pow(sin(($lat-lat)*0.008726646),2)+Cos($lat*0.0174533)*Cos(lat*0.0174533)*pow(sin(($lng-lng)*0.008726646),2)))/1000,2) ";
        $result_1 = $this -> get_list_bysql("select id,name,star,averfee,address,telphone,$distance_str as distance from sys_shop where id=$shop_id and validflag!=2");
        $result_1[0]['imgItems'] = $this -> get_list_bysql("select * from sys_img where keytype=2 and keyid=$shop_id");
		sys_out_success(0,$result_1);
	}



	/**
	 * 热销商品
	 * @parent shop_root
	 * @req_params shop_id 商家ID
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_level id 主键id
	 * @ret_infor img 封面图片
	 * @ret_infor name 名称
	 * @ret_infor present_price 现价
	 * @ret_infor original_price 原价
	 * @ret_infor salenum 销量
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function hotgood_list()
	{
		sys_check_post_single('shop_id');//检查post必选参数完整性
		$shop_id=_POST('shop_id');
        $result_2 = $this -> get_list_bysql("select id,name,img,present_price,original_price,salenum from sys_good where shop_id=$shop_id and flag=1 and doflag=2 order by salenum desc,id desc");
		sys_out_success(0,$result_2);
	}


	
	/**
	 * 商家评价列表
	 * @parent shop_root
	 * @req_params shop_id 商家ID
	 * @req_params type 条件 0：全部评价;1：有图评价;2：低分评价;3：最新评价 
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_level id 主键id
	 * @ret_infor star 星评个数
	 * @ret_infor client_id 用户ID
	 * @ret_infor account 用户账号
	 * @ret_infor avatar 用户头像
	 * @ret_infor avatarbig 用户大头像
	 * @ret_infor content 评价内容
	 * @ret_infor regdate 日期
	 * @ret_infor anonymous 是否匿名 1：是；2：否
	 * @ret_infor imgItems 评价图片集 id:图片id$imgurl:缩略图$imgurlbig:大图
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function comment_list()
	{
		sys_check_post_single('shop_id');//检查post必选参数完整性
		$shop_id=_POST('shop_id');
		$type=_POST('type');
		if($type == 2){
			$sql_where = " and co.star <3";
		}else{
			$sql_where = "";
		}
        $result = $this -> get_list_bysql("select co.*,c.account,c.avatar,c.avatarbig from sys_comment co left join sys_client c on co.client_id = c.id where co.shop_id=$shop_id $sql_where order by co.id desc");
        foreach($result as $k=>&$v){
	        $o2order_id = $v['o2order_id'];
	        $v['imgItems'] = $this -> get_list_bysql("select * from sys_img where keytype=3 and keyid=$o2order_id");
	        if(!$v['imgItems'] && $type == 1){
		        unset($result[$k]);
	        }
        }
        unset($v);
		sys_out_success(0,$result);
	}



	/**
	 * 二级分类商家列表
	 * @parent shop_root
	 * @req_params lng 经度
	 * @req_params lat 纬度
	 * @req_params two_classify_id 二级分类ID
	 * @req_params type 选择条件 1：地区;2：其他 
	 * @req_params district_id 地区ID
	 * @req_params other_type 其他包含类型 1：距离优先;2：好评优先;3：人均最低;4：人均最高
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor img 封面图片
	 * @ret_infor name 名称
	 * @ret_infor star 评分
	 * @ret_infor averfee 人均价
	 * @ret_infor distance 距离（km）
	 * @ret_infor children 商品集 id:主键id$img:封面图片$name:名称$present_price:现价$original_price:原价$salenum:销量
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function two_shop_list()
	{
		$post_array = array('lng','lat','two_classify_id');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$temp_array[0] = array();
		//先拿一级
		$lng=_POST('lng');
		$lat=_POST('lat');
		$type=_POST('type');
		$two_classify_id=_POST('two_classify_id');
		$where = '';
		$orderby = '';
		if($type){
			if($type == 1){
				$district_id=_POST('district_id');
				if($district_id){
					$namepath = $this->get_one_bysql("select namepath from sys_cascade_district where id=$district_id");
					$num = count(explode(',',$namepath));
					if($num == 2){
						$where .= " and s.district_2_id=$district_id";
					}else if($num == 3){
						$where .= " and s.district_3_id=$district_id";
					}
				}else{
					sys_out_fail('请选择地区');
				}
			}else if($type == 2){
				$other_type=_POST('other_type');
				if($other_type){
					if($other_type == 1){
						$orderby .= "distance asc,";
					}else if($other_type == 2){
						$orderby .= "s.star desc,";
					}else if($other_type == 3){
						$orderby .= "s.averfee asc,";
					}else if($other_type == 4){
						$orderby .= "s.averfee desc,";
					}else{
						sys_out_fail('参数传递错误', 101);
					}					
				}else{
					sys_out_fail('请选择排序规则');
				}
			}else{
				sys_out_fail('参数传递错误', 101);
			}
		}else{
			$orderby .= "distance asc,";
		}
        $distance_str=" round(12756274*asin(Sqrt(pow(sin(($lat-lat)*0.008726646),2)+Cos($lat*0.0174533)*Cos(lat*0.0174533)*pow(sin(($lng-lng)*0.008726646),2)))/1000,2) ";
        $result_1 = $this -> get_list_bysql("select s.id,s.name,s.img,s.star,s.averfee,$distance_str as distance from sys_shop s where s.validflag!=2 and s.two_classify_id=$two_classify_id $where order by $orderby s.id desc");
		$i = 0 ;
		foreach ($result_1 as $result_1_i)
		{
			$temp_array[$i] = array(
				'id'=>$result_1_i['id'],
				'name'=>$result_1_i['name'],
				'img'=>$result_1_i['img'],
				'star'=>$result_1_i['star'],
				'averfee'=>$result_1_i['averfee'],
				'distance'=>$result_1_i['distance'],
				'children'=>array()
			);

			//再拿二级
			$parentid_1 = $result_1_i['id'];
			$result_2 = $this -> get_list_bysql("select id,name,img,present_price,original_price,salenum from sys_good where shop_id=$parentid_1 and flag=1 and doflag=2 and isrecommend=1 order by orderby asc limit 0,2");

			$j = 0 ;
			foreach ($result_2 as $result_2_i)
			{
				$temp_array[$i]['children'][$j] = array(
					'id'=>$result_2_i['id'],
					'name'=>$result_2_i['name'],
					'img'=>$result_2_i['img'],
					'present_price'=>$result_2_i['present_price'],
					'original_price'=>$result_2_i['original_price'],
					'salenum'=>$result_2_i['salenum'],
				);
				$j ++ ;
			}

			$i++;
		}
		sys_out_success(0,$temp_array);
	}
	

	
	/**
	 * 全商城搜索
	 * @parent shop_root
	 * @req_params city 定位城市
	 * @req_params lng 经度
	 * @req_params lat 纬度
	 * @req_params keyword 关键字
	 * @req_params type 1：分类;2：地区;3：其他
	 * @req_params one_classify_id 一级分类ID
	 * @req_params two_classify_id 二级分类ID
	 * @req_params district_id 地区ID
	 * @req_params other_type 1：距离优先;2：好评优先;3：人均最低;4：人均最高
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	  * @ret_infor id 主键id
	 * @ret_infor img 封面图片
	 * @ret_infor name 名称
	 * @ret_infor star 评分
	 * @ret_infor averfee 人均价
	 * @ret_infor distance 距离（km）
	 * @ret_infor children 商品集 id:主键id$img:封面图片$name:名称$present_price:现价$original_price:原价$salenum:销量
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function search_list()
	{
		$post_array = array('city','lng','lat','keyword');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$citys=_POST('city');
		$city_list = $this->get_list_bysql("select o.*,c.name as province,ca.name as city from sys_opencity o left join sys_cascade_district c on o.district_1_id = c.id left join sys_cascade_district ca on o.district_2_id = ca.id where province like '%$citys%' or city like '%$citys%'");
		if($city_list){
			$province = $city_list[0]['province'];
			$city = $city_list[0]['city'];
			if(strpos($province, '市') !== false){
				$city_type = 1;
				$city_id = $city_list[0]['district_1_id'];
			}else{
				$city_type = 2;
				$city_id = $city_list[0]['district_2_id'];
			}
		}else{
			sys_out_fail('该城市暂时未开通');
		}
		$temp_array[0] = array();
		//先拿一级
		$lng=_POST('lng');
		$lat=_POST('lat');
		$keyword=_POST('keyword');
		$type=_POST('type');
		$where = '';
		$orderby = '';
		if($type){
			if($type == 1){
				$one_classify_id=_POST('one_classify_id');
				$two_classify_id=_POST('two_classify_id');
				if($one_classify_id && $two_classify_id){
					$where .= " and s.one_classify_id=$one_classify_id and s.two_classify_id=$two_classify_id";
				}else{
					sys_out_fail('请精确选择商品类别');
				}
			}else if($type == 2){
				$district_id=_POST('district_id');
				if($district_id){
					$namepath = $this->get_one_bysql("select namepath from sys_cascade_district where id=$district_id");
					$num = count(explode(',',$namepath));
					if($num == 2){
						$where .= " and s.district_2_id=$district_id";
					}else if($num == 3){
						$where .= " and s.district_3_id=$district_id";
					}
				}else{
					sys_out_fail('请选择地区');
				}
			}else if($type == 3){
				$other_type=_POST('other_type');
				if($other_type){
					if($other_type == 1){
						$orderby .= "distance asc,";
					}else if($other_type == 2){
						$orderby .= "s.star desc,";
					}else if($other_type == 3){
						$orderby .= "s.averfee asc,";
					}else if($other_type == 4){
						$orderby .= "s.averfee desc,";
					}else{
						sys_out_fail('参数传递错误', 101);
					}					
				}else{
					sys_out_fail('请选择排序规则');
				}
			}else{
				sys_out_fail('参数传递错误', 101);
			}
		}else{
			$orderby .= "distance asc,";
		}
        $distance_str=" round(12756274*asin(Sqrt(pow(sin(($lat-lat)*0.008726646),2)+Cos($lat*0.0174533)*Cos(lat*0.0174533)*pow(sin(($lng-lng)*0.008726646),2)))/1000,2) ";
        if($city_type == 1){
	        $result_1 = $this -> get_list_bysql("select s.id,s.name,s.img,s.star,s.averfee,$distance_str as distance from sys_shop s left join sys_good g on s.id=g.shop_id where s.validflag!=2 and s.district_1_id=$city_id and g.flag=1 and g.doflag=2 and (s.name like '%$keyword%' or g.name like '%$keyword%') $where order by $orderby s.id desc");
        }else{
	        $result_1 = $this -> get_list_bysql("select s.id,s.name,s.img,s.star,s.averfee,$distance_str as distance from sys_shop s left join sys_good g on s.id=g.shop_id where s.validflag!=2 and s.district_2_id=$city_id and g.flag=1 and g.doflag=2 and (s.name like '%$keyword%' or g.name like '%$keyword%') $where order by $orderby s.id desc");
        }
		$i = 0 ;
		foreach ($result_1 as $result_1_i)
		{
			$temp_array[$i] = array(
				'id'=>$result_1_i['id'],
				'name'=>$result_1_i['name'],
				'img'=>$result_1_i['img'],
				'star'=>$result_1_i['star'],
				'averfee'=>$result_1_i['averfee'],
				'distance'=>$result_1_i['distance'],
				'children'=>array()
			);

			//再拿二级
			$parentid_1 = $result_1_i['id'];
			$result_2 = $this -> get_list_bysql("select id,name,img,present_price,original_price,salenum from sys_good where shop_id=$parentid_1 and flag=1 and doflag=2 and isrecommend=1 order by orderby asc limit 0,2");

			$j = 0 ;
			foreach ($result_2 as $result_2_i)
			{
				$temp_array[$i]['children'][$j] = array(
					'id'=>$result_2_i['id'],
					'name'=>$result_2_i['name'],
					'img'=>$result_2_i['img'],
					'present_price'=>$result_2_i['present_price'],
					'original_price'=>$result_2_i['original_price'],
					'salenum'=>$result_2_i['salenum'],
				);
				$j ++ ;
			}

			$i++;
		}
		sys_out_success(0,$temp_array);
	}

	
	/**
	 * 一级分类下搜索
	 * @parent shop_root
	 * @req_params lng 经度
	 * @req_params lat 纬度
	 * @req_params one_classify_id 一级分类ID
	 * @req_params keyword 关键字
	 * @req_params type 1：地区;3：其他
	 * @req_params two_classify_id 二级分类ID
	 * @req_params district_id 地区ID
	 * @req_params other_type 1：距离优先;2：好评优先;3：人均最低;4：人均最高
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor img 封面图片
	 * @ret_infor name 名称
	 * @ret_infor star 评分
	 * @ret_infor averfee 人均价
	 * @ret_infor distance 距离（km）
	 * @ret_infor children 商品集 id:主键id$img:封面图片$name:名称$present_price:现价$original_price:原价$salenum:销量
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function one_search_list()
	{
		$post_array = array('city','lng','lat','one_classify_id','keyword');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$temp_array[0] = array();
		//先拿一级
		$lng=_POST('lng');
		$lat=_POST('lat');
		$keyword=_POST('keyword');
		$type=_POST('type');
		$one_classify_id=_POST('one_classify_id');
		$where = '';
		$orderby = '';
		if($type){
			if($type == 1){			
				$two_classify_id=_POST('two_classify_id');
				if($two_classify_id){
					$where .= " and s.two_classify_id=$two_classify_id";
				}else{
					sys_out_fail('请精确选择商品类别');
				}
			}else if($type == 2){
				$district_id=_POST('district_id');
				if($district_id){
					$namepath = $this->get_one_bysql("select namepath from sys_cascade_district where id=$district_id");
					$num = count(explode(',',$namepath));
					if($num == 2){
						$where .= " and s.district_2_id=$district_id";
					}else if($num == 3){
						$where .= " and s.district_3_id=$district_id";
					}
				}else{
					sys_out_fail('请选择地区');
				}
			}else if($type == 3){
				$other_type=_POST('other_type');
				if($other_type){
					if($other_type == 1){
						$orderby .= "distance asc,";
					}else if($other_type == 2){
						$orderby .= "s.star desc,";
					}else if($other_type == 3){
						$orderby .= "s.averfee asc,";
					}else if($other_type == 4){
						$orderby .= "s.averfee desc,";
					}else{
						sys_out_fail('参数传递错误', 101);
					}					
				}else{
					sys_out_fail('请选择排序规则');
				}
			}else{
				sys_out_fail('参数传递错误', 101);
			}
		}else{
			$orderby .= "distance asc,";
		}
        $distance_str=" round(12756274*asin(Sqrt(pow(sin(($lat-lat)*0.008726646),2)+Cos($lat*0.0174533)*Cos(lat*0.0174533)*pow(sin(($lng-lng)*0.008726646),2)))/1000,2) ";
        $result_1 = $this -> get_list_bysql("select s.id,s.name,s.img,s.star,s.averfee,$distance_str as distance from sys_shop s left join sys_good g on s.id=g.shop_id where s.validflag!=2 and s.district_1_id=$city_id and s.one_classify_id=$one_classify_id and g.flag=1 and g.doflag=2 and (s.name like '%$keyword%' or g.name like '%$keyword%') $where order by $orderby s.id desc");
		$i = 0 ;
		foreach ($result_1 as $result_1_i)
		{
			$temp_array[$i] = array(
				'id'=>$result_1_i['id'],
				'name'=>$result_1_i['name'],
				'img'=>$result_1_i['img'],
				'star'=>$result_1_i['star'],
				'averfee'=>$result_1_i['averfee'],
				'distance'=>$result_1_i['distance'],
				'children'=>array()
			);

			//再拿二级
			$parentid_1 = $result_1_i['id'];
			$result_2 = $this -> get_list_bysql("select id,name,img,present_price,original_price,salenum from sys_good where shop_id=$parentid_1 and flag=1 and doflag=2 and isrecommend=1 order by orderby asc limit 0,2");

			$j = 0 ;
			foreach ($result_2 as $result_2_i)
			{
				$temp_array[$i]['children'][$j] = array(
					'id'=>$result_2_i['id'],
					'name'=>$result_2_i['name'],
					'img'=>$result_2_i['img'],
					'present_price'=>$result_2_i['present_price'],
					'original_price'=>$result_2_i['original_price'],
					'salenum'=>$result_2_i['salenum'],
				);
				$j ++ ;
			}

			$i++;
		}
		sys_out_success(0,$temp_array);
	}
	


	/**
	 * 热门搜索
	 * @parent shop_root
	 * @req_params
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor name 名称
	 * @ret_infor orderby 手动排序
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function hotword_list()
	{
        $result = $this -> get_list_bysql("select * from sys_hot_word where showflag=1 order by orderby asc,id desc");
		sys_out_success(0,$result);
	}



	/**
	 * 举报商家
	 * @parent shop_root
	 * @req_params token 登录令牌
	 * @req_params shop_id 商家ID
	 * @req_params label 举报标签名称（多个标签名称之间用英文逗号隔开）
	 * @req_params content 详细原因
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor 
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function reportshop_add()
	{
		$post_array = array('shop_id','label','content');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();	//用户id
		$shop_id=_POST('shop_id');
		$shop_list = $this->get_list_bysql("select * from sys_shop where id=$shop_id");
		if(!$shop_list){
			sys_out_fail('参数传递错误！');
		}
		$label=_POST('label');
		$content=_POST('content');
		$regdate = sys_get_time();
		$sqlstr = "insert into sys_report set client_id=$client_id,shop_id=$shop_id,label='$label',content='$content',regdate='$regdate'";
		$result = $this->do_execute($sqlstr);
		sys_out_result($result);
	}


	
	/**
	 * 收藏商家及商品
	 * @parent shop_root
	 * @req_params token 登录令牌
	 * @req_params type 类型 1：商家;2：商品
	 * @req_params about_id 相关ID
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor 
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function love_add()
	{
		$post_array = array('type','about_id');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();	//用户id
		$type=_POST('type');
		$about_id=_POST('about_id');
		if($type == 1){
			$list = $this->get_list_bysql("select * from sys_shop where id=$about_id");
			if(!$list){
				sys_out_fail('该商家不存在！');
			}
		}else if($type == 2){
			$list = $this->get_list_bysql("select * from sys_good where id=$about_id");
			if(!$list){
				sys_out_fail('该商品不存在！');
			}
		}else{
			sys_out_fail('参数传递错误！');
		}
		
		$regdate = sys_get_time();
		$sqlstr = "insert into sys_love set client_id=$client_id,about_id=$about_id,type=$type,regdate='$regdate'";
		$result = $this->do_execute($sqlstr);
		sys_out_result($result);
	}


	
	/**
	 * 商品详情
	 * @parent shop_root
	 * @req_params id 商品ID
	 * @req_params lng 经度
	 * @req_params lat 纬度
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor name 商品名称
	 * @ret_infor img 商品封面
	 * @ret_infor shop_id 商家ID
	 * @ret_infor content 商品描述
	 * @ret_infor present_price 现价
	 * @ret_infor original_price 原价
	 * @ret_infor salenum 销量
	 * @ret_infor commentnum 评论数量
	 * @ret_infor commentstar 评价等级
	 * @ret_infor shopname 商家名称
	 * @ret_infor address 商家地址
	 * @ret_infor telphone 商家联系电话
	 * @ret_infor distance 距离
	 * @ret_infor imgItems 商品图片集 id:图片id$imgurl:缩略图$imgurlbig:大图
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function good_get()
	{
		$post_array = array('id','lng','lat');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$id=_POST('id');
		$lng=_POST('lng');
		$lat=_POST('lat');
        $distance_str=" round(12756274*asin(Sqrt(pow(sin(($lat-lat)*0.008726646),2)+Cos($lat*0.0174533)*Cos(lat*0.0174533)*pow(sin(($lng-lng)*0.008726646),2)))/1000,2) ";
        $result_1 = $this -> get_list_bysql("select g.*,s.name as shopname,s.address,s.telphone,$distance_str as distance from sys_good g left join sys_shop s on g.shop_id = s.id where g.id=$id and g.flag=1 and g.doflag=2 and s.validflag!=2");
        $result_1[0]['imgItems'] = $this -> get_list_bysql("select * from sys_img where keytype=1 and keyid=$id");
		sys_out_success(0,$result_1);
	}


	/**
	 * 消费码列表
	 * @parent shop_root
	 * @req_params token 登录令牌
	 * @req_params orderid 订单id
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor o2order_id 订单ID
	 * @ret_infor out_no 消费码
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function out_no_list()
	{
		sys_check_post_single('orderid');//检查post必选参数完整性
		$client_id = sys_get_cid();	//用户id
		$orderid=_POST('orderid');
		$list = $this->get_list_bysql("select * from sys_o2order where id=$orderid and client_id=$client_id");
		if(!$list){
			sys_out_fail("传参错误");
		}
        $result = $this -> get_list_bysql("select * from sys_out_no where o2order_id=$orderid");
		sys_out_success(0,$result);
	}


	/**
	 * 已开通城市列表
	 * @parent shop_root
	 * @req_params 
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor city 城市名称
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function opencity_list()
	{
        $result = $this -> get_list_bysql("select city from sys_opencity");
		sys_out_success(0,$result);
	}


	/**
	 * 向商家付款信息获取
	 * @parent shop_root
	 * @req_params shop_id 商家ID
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor shopname 商家名称
	 * @ret_infor out_trade_no 交易编号
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function shoppay_get()
	{
		sys_check_post_single('shop_id');//检查post必选参数完整性
		$shop_id=_POST('shop_id');
		$name = $this->get_one_bysql("select name from sys_shop where id=$shop_id and validflag != 2");
		if(!$name){
			sys_out_fail("该商家不存在或已冻结");
		}
		$temp_array[0]['shopname'] = $name;//封装token
		$temp_array[0]['out_trade_no'] = 'SKY'.sys_get_payno();
		sys_out_success(0,$temp_array);
	}


	
	/**
	 * 商城订单添加
	 * @parent shop_root
	 * @req_params token 登录令牌
	 * @req_params good_id 商品ID
	 * @req_params num 购买数量
	 * @req_params totalfee 付款金额
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor order_id 新添加的订单ID
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function o2order_add()
	{
		$post_array = array('good_id','num','totalfee');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();	//用户id
		$good_id=_POST('good_id');
		$good_list = $this->get_list_bysql("select * from sys_good where id=$good_id and flag=1 and doflag=2");
		if(!$good_list){
			sys_out_fail('参数传递错误！');
		}
		$good_name = $good_list[0]['name'];
		$good_img = $good_list[0]['img'];
		$good_content = $good_list[0]['content'];
		$num=_POST('num');
		$totalfee=_POST('totalfee');
		$present_price = $good_list[0]['present_price'];
		$fee = $present_price * $num;
		if($fee != $totalfee){
			sys_out_fail('订单金额错误');
		}
		$out_trade_no=sys_get_payno();
		$regdate = sys_get_time();
		$endtime = date("Y-m-d",time()+30*24*3600);
		$sqlstr = "insert into sys_o2order set client_id=$client_id,out_trade_no='$out_trade_no',good_id=$good_id,num='$num',good_name='$good_name',good_content='$good_content',good_img='$good_img',orderflag=2,totalfee='$totalfee',is_verification=2,regdate='$regdate',payflag=1,endtime='$endtime'";
		$this->do_execute($sqlstr);
		$order_id = $this->get_insert_id();
		if($order_id){
			$temp_array[0]['order_id'] = $order_id;
			sys_out_success('添加成功', $temp_array);
		}else{
			sys_out_fail();
		}
	}


	/**
	 * 商城订单查询
	 * @parent shop_root
	 * @req_params token 登录令牌
	 * @req_params orderid 订单id
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor good_name 商品名称
	 * @ret_infor good_img 商品图片
	 * @ret_infor present_price 单价
	 * @ret_infor num 购买数量
	 * @ret_infor totalfee 总额
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function order_get()
	{
		sys_check_post_single('orderid');//检查post必选参数完整性
		$client_id = sys_get_cid();	//用户id
		$orderid=_POST('orderid');
		$list = $this->get_list_bysql("select * from sys_o2order where id=$orderid and client_id=$client_id");
		if(!$list){
			sys_out_fail("传参错误");
		}
		$good_id = $list[0]['good_id'];
		$good_list = $this->get_list_bysql("select * from sys_good where id=$good_id");
		$list[0]['good_name'] = $good_list[0]['name'];
		$list[0]['good_img'] = $good_list[0]['img'];
		$list[0]['present_price'] = $good_list[0]['present_price'];
		sys_out_success(0,$list);
	}


	
	/**
	 * 扫码订单添加
	 * @parent shop_root
	 * @req_params token 登录令牌
	 * @req_params shop_id 商家ID
	 * @req_params out_trade_no 交易编号
	 * @req_params totalfee 付款金额
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor order_id 新添加的订单ID
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function sysorder_add()
	{
		$post_array = array('shop_id','out_trade_no','totalfee');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();	//用户id
		$shop_id=_POST('shop_id');
		$shop_name = $this->get_one_bysql("select name from sys_shop where id=$shop_id and validflag != 2");
		if(!$shop_name){
			sys_out_fail("该商家不存在或已冻结");
		}
		$out_trade_no=_POST('out_trade_no');
		$totalfee=_POST('totalfee');
		$sqlstr = "insert into sys_sysorder set shop_id=$shop_id,shop_name='$shop_name',totalfee=$totalfee,out_trade_no='$out_trade_no',client_id=$client_id,payflag=2";
		$this->do_execute($sqlstr);
		$order_id = $this->get_insert_id();
		if($order_id){
			$temp_array[0]['order_id'] = $order_id;
			sys_out_success('添加成功', $temp_array);
		}else{
			sys_out_fail();
		}
	}



	/**
	 * 扫码消费记录
	 * @parent shop_root
	 * @req_params token 登录令牌
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor shop_id 商家ID
	 * @ret_infor shop_name 商家名称
	 * @ret_infor totalfee 金额
	 * @ret_infor out_trade_no 订单号
	 * @ret_infor client_id 用户ID
	 * @ret_infor paytime 支付时间
	 * @ret_infor payflag 支付状态 1：支付成功；2：未支付
	 * @ret_infor paytype 支付方式 1：支付宝；2：银联；3：微信；4：余额支付
	 * @special 
	 */
	public function sysorder_list()
	{
		$client_id = sys_get_cid();	
		$order_lists = $this->get_list_bysql("select * from sys_sysorder where client_id='$client_id' order by id desc");
		sys_out_success(0, $order_lists);
	}



	/**
	 * 扫码记录操作
	 * @parent shop_root
	 * @req_params token 登录令牌
	 * @req_params keytype 操作方式 1：清空
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor
	 * @special 
	 */
	public function sysorder_set()
	{
		sys_check_post_single('keytype');//检查post必选参数完整性
		$keytype=_POST('keytype');
		$client_id = sys_get_cid();	
		if($keytype == 1){
			$sqlstr = "delete from sys_sysorder where client_id='$client_id'";
		}
		$result = $this->do_execute($sqlstr);
		sys_out_result($result);
	}


	
	/**
	 * 余额支付
	 * @parent shop_root
	 * @req_params token 登录令牌
	 * @req_params ordertype 订单类型 1：商城订单；2：扫码订单
	 * @req_params orderid 订单ID
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor 
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function feeaccount_pay()
	{
		$post_array = array('ordertype','orderid');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();
		$orderid =_POST('orderid');
		$ordertype =_POST('ordertype');
		$sqlstr_con_array=NULL;
		$paytime = sys_get_time();
		if($ordertype == 1){
			$temp_array = $this->get_list_bysql("select * from sys_o2order where id=$orderid and client_id=$client_id");
			if(!$temp_array){
				sys_out_fail('该订单不存在');
			}
			$payflag = $temp_array[0]['payflag'];
			$out_trade_no = $temp_array[0]['out_trade_no'];
			$totalfee = $temp_array[0]['totalfee'];
			$good_id = $temp_array[0]['good_id'];
			$good_list = $this->get_list_bysql("select * from sys_good where id=$good_id");
			if(!$good_list || $good_list[0]['flag'] == 2){
				sys_out_fail('该商品已删除或已下架');
			}
			if($payflag != 1){
				sys_out_fail('该订单已支付，请勿重复支付');
			}
			$feeaccount = $this->get_one_bysql("select feeaccount from sys_client where id=$client_id");
			if($totalfee > $feeaccount){
				sys_out_fail('余额不足，请使用其他支付方式');
			}
			$sqlstr_con_array[] = "update sys_client set feeaccount=feeaccount-$totalfee where id=$client_id";
			$sqlstr_con_array[] = "insert into sys_cash set client_id=$client_id,regdate='$paytime',score=$totalfee,cashflag=5,isget=1";
			$sqlstr_con_array[] = "update sys_o2order set paytime='$paytime',paytype=4,payflag=2 where id=$orderid";
			//发送支付成功通知
			$content = '订单'.$out_trade_no.'已支付成功！请及时到店消费';
			send_mess(2,$content,$client_id);
			//写入通知列表
			$sqlstr_con_array[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$paytime',looktype=0,keytype=2";
			//更新商品表数据信息	
			$num = $temp_array[0]['num'];
			$good_id = $temp_array[0]['good_id'];
			$sqlstr_con_array[] = "update sys_good set salenum=salenum+$num where id='$good_id'";
			//生成核销码，并记录表中
			for ($x=0; $x<$num; $x++) {
			  	$out_no = date("YmdHis").rand(1,9999);
			  	$sqlstr_con_array[] = "insert into sys_out_no set o2order_id=$orderid,out_no='$out_no'";
			}
			$shop_id = $this->get_one_bysql("select shop_id from sys_good where id=$good_id");
			$sqlstr_con_array[] = "insert into sys_o2order_record set order_id=$orderid,shop_id=$shop_id,num=$num,is_verification=2,is_return=2,regdate='$paytime'";
		}else if($ordertype == 2){			
			$sqlstr = " select s.arrival_rate,s.wealth_rate,s.service_type,s.service_rate,s.service_fee,sy.totalfee,sy.shop_id,sy.payflag,sy.out_trade_no,s.name as shop_name,s.address from sys_sysorder sy left join sys_shop s on sy.shop_id = s.id where sy.id = $orderid and sy.client_id=$client_id";
			$temp_array = $this->get_list_bysql($sqlstr);
			if(!$temp_array){
				sys_out_fail('该订单不存在');
			}
			$payflag = $temp_array[0]['payflag'];
			$out_trade_no = $temp_array[0]['out_trade_no'];
			$totalfee = $temp_array[0]['totalfee'];
			if($payflag == 1){
				sys_out_fail('该订单已支付，请勿重复支付');
			}
			$feeaccount = $this->get_one_bysql("select feeaccount from sys_client where id=$client_id");
			if($totalfee > $feeaccount){
				sys_out_fail('余额不足，请使用其他支付方式');
			}
			$sqlstr_con_array[] = "update sys_sysorder set paytime='$paytime',paytype=4,payflag=1 where id=$orderid";
			$sqlstr_con_array[] = "insert into sys_cash set client_id=$client_id,regdate='$paytime',score=$totalfee,cashflag=5,isget=1";
			//发送支付成功通知
			$content = '扫码支付订单'.$out_trade_no.'已支付成功！';
			send_mess(2,$content,$client_id);
			//写入通知列表
			$sqlstr_con_array[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$paytime',looktype=0,keytype=2";
			$arrival_rate = $temp_array[0]['arrival_rate'];
			$wealth_rate = $temp_array[0]['wealth_rate'];
			$service_type = $temp_array[0]['service_type'];
			$shop_id = $temp_array[0]['shop_id'];
			$shop_name = $temp_array[0]['shop_name'];
			$address = $temp_array[0]['address'];
			if($service_type == 1){
				$arrival_fee = 	round($totalfee*$arrival_rate,2);				
				$wealth_fee = 	round($totalfee*$wealth_rate,2);
				$service_fee = 	$totalfee - $arrival_fee - $wealth_fee;										
			}else{
				$service_fee = $shop_list[0]['service_fee'];
				$totalfee = $totalfee - $service_fee;
				$arrival_fee = 	round($totalfee*$arrival_rate,2);				
				$wealth_fee = 	round($totalfee*$wealth_rate,2);					
			}
			$islive = $this->get_one_bysql("select islive from sys_client where id=$client_id");
			if($islive == 1){					
				$sqlstr_con_array[] = "update sys_shop set feeaccount=feeaccount+$arrival_fee,wealth_redbag=wealth_redbag+$wealth_fee,redbag=redbag+$wealth_fee where id=$shop_id";				
				$sqlstr_con_array[] = "update sys_client set wealth=wealth+$wealth_fee,feeaccount=feeaccount-$totalfee where id=$client_id";
				$sqlstr_con_array[] = "insert into sys_wealth_redbag set type=1,fee=$wealth_fee,regdate='$paytime',client_id=$client_id,shop_id=$shop_id";
				$sqlstr_con_array[] = "insert into sys_income set totalfee=$totalfee,type=1,client_id=$client_id,shop_id=$shop_id,shop_name='$shop_name',arrival_fee=$arrival_fee,wealth_fee=$wealth_fee,service_fee=$service_fee,address='$address',regdate='$paytime'";
			}else{
				$wealth_fee = 0;
				$service_fee = $totalfee - $arrival_fee;
				$sqlstr_con_array[] = "update sys_client set feeaccount=feeaccount-$totalfee where id=$client_id";
				$sqlstr_con_array[] = "update sys_shop set feeaccount=feeaccount+$arrival_fee where id=$shop_id";
				$sqlstr_con_array[] = "insert into sys_income set totalfee=$totalfee,type=1,client_id=$client_id,shop_id=$shop_id,shop_name='$shop_name',arrival_fee=$arrival_fee,wealth_fee=$wealth_fee,service_fee=$service_fee,address='$address',regdate='$paytime'";
			}
		}else{
			sys_out_fail('参数传递错误');
		}
		$result = $this->do_transaction($sqlstr_con_array);
		sys_out_result($result);			
	}

		
	/**
	 * 我的订单
	 * @parent order_root
	 * @req_params token 登录令牌
	 * @req_params payflag 订单状态 0：全部；1：待付款；2：待使用；3：待评价；4：已完成；5：已退款
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor out_trade_no 订单编号
	 * @ret_infor good_id 商品ID
	 * @ret_infor good_name 商品名称
	 * @ret_infor good_img 商品图片
	 * @ret_infor good_content 商品详情
	 * @ret_infor orderflag 订单状态 1：已发货；2：未发货
	 * @ret_infor totalfee 商品价格
	 * @ret_infor client_id 用户ID
	 * @ret_infor is_verification 是否核销 1：是；2：否
	 * @ret_infor num 购买商品数量
	 * @ret_infor paytype 支付方式 1：支付宝；2：银联；3：微信；4：余额支付
	 * @ret_infor regdate 下单时间
	 * @ret_infor paytime 支付时间
	 * @ret_infor payflag 订单状态 1：待付款；2：待使用；3：待评价；4：已完成；5：已退款
	 * @ret_infor endtime 到期时间
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function o2order_list()
	{
		sys_check_post_single('payflag');//检查post必选参数完整性
		$payflag=_POST('payflag');	
		$client_id = sys_get_cid();
		$type_array = array(0,1,2,3,4,5);
		if(!in_array($payflag,$type_array)){
			sys_out_fail('参数传递错误', 101);
		}
		if($payflag != 0){
			$sql_where = "and payflag=$payflag";
		}else{
			$sql_where = '';
		}
		$order_sql = "select * from sys_o2order where client_id=$client_id $sql_where order by id desc";
		$o2order_list = $this -> get_list_bysql($order_sql);
		sys_out_success(0, $o2order_list);
	}



	/**
	 * 订单详情
	 * @parent order_root
	 * @req_params token 登录令牌
	 * @req_params orderid 订单ID
	 * @req_params lng 经度
	 * @req_params lat 纬度
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor out_trade_no 订单编号
	 * @ret_infor good_id 商品ID
	 * @ret_infor good_name 商品名称
	 * @ret_infor good_img 商品图片
	 * @ret_infor good_content 商品详情
	 * @ret_infor orderflag 订单状态 1：已发货；2：未发货
	 * @ret_infor totalfee 商品价格
	 * @ret_infor client_id 用户ID
	 * @ret_infor is_verification 是否核销 1：是；2：否
	 * @ret_infor num 购买商品数量
	 * @ret_infor paytype 支付方式 1：支付宝；2：银联；3：微信；4：余额支付
	 * @ret_infor regdate 下单时间
	 * @ret_infor paytime 支付时间
	 * @ret_infor payflag 订单状态 1：待付款；2：待使用；3：待评价；4：已完成；5：已退款
	 * @ret_infor endtime 到期时间
	 * @ret_infor shop_name 商家名称
	 * @ret_infor address 商家地址
	 * @ret_infor telphone 商家联系电话
	 * @ret_infor distance 距离
	 * @ret_infor codeItems 消费码列表 id:主键id$out_no:消费码$is_do:是否使用1：是；2：否；3：已退款
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function o2order_get()
	{
		$post_array = array('orderid','lng','lat');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$orderid=_POST('orderid');
		$lng=_POST('lng');
		$lat=_POST('lat');	
		$client_id = sys_get_cid();
        $distance_str=" round(12756274*asin(Sqrt(pow(sin(($lat-s.lat)*0.008726646),2)+Cos($lat*0.0174533)*Cos(s.lat*0.0174533)*pow(sin(($lng-s.lng)*0.008726646),2)))/1000,2) ";
		$order_sql = "select o.*,s.name as shop_name,s.telphone,s.address,$distance_str as distance from sys_o2order o left join sys_good g on o.good_id=g.id left join sys_shop s on g.shop_id=s.id where o.client_id=$client_id and o.id=$orderid";
		$o2order_list = $this -> get_list_bysql($order_sql);
		$o2order_list[0]['codeItems'] = $this -> get_list_bysql("select * from sys_out_no where o2order_id=$orderid");
		sys_out_success(0, $o2order_list);
	}



	/**
	 * 订单操作
	 * @parent order_root
	 * @req_params token 登录令牌
	 * @req_params orderid 订单id
	 * @req_params keytype 操作类型 1：申请退款；2：删除订单
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor 
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function order_saveoperate()
	{
		$post_array = array('orderid','keytype');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();
		$orderid=_POST('orderid');		
		$keytype=_POST('keytype');
		if($keytype == 1){
			$out_no_count = $this -> get_one_bysql("select count(*) from sys_out_no where o2order_id=$orderid and is_do=1");
			$order_list = $this->get_list_bysql("select * from sys_o2order where id=$orderid and payflag=2 and client_id=$client_id and is_verification=2");
			if($out_no_count > 0 || !$order_list){
				sys_out_fail("该订单不能退款");
			}
			$num = $order_list[0]['num'];
			$good_id = $order_list[0]['good_id'];
			$totalfee = $order_list[0]['totalfee'];
			$shop_id = $this->get_one_bysql("select shop_id from sys_good where id=$good_id");
			$sqlstr_array=NULL;
			$sqlstr_array[] = "update sys_o2order set payflag=5 where id=$orderid and payflag=2 and client_id=$client_id";
			$sqlstr_array[] = "update sys_out_no set is_do=3 where o2order_id=$orderid and is_do=2";
			$sqlstr_array[] = "update sys_good set salenum=salenum-$num where id=$good_id";
			$sqlstr_array[] = "update sys_client set feeaccount=feeaccount+$totalfee,returnfee=returnfee+$totalfee where id=$client_id";
			$sqlstr_array[] = "insert into sys_cash set client_id=$client_id,regdate='" . sys_get_time() . "',score=$totalfee,cashflag=4,isget=1";
			//发送退款成功通知
			$content = '订单'.$out_trade_no.'已退款成功！';
			send_mess(2,$content,$client_id);
			//写入通知列表
			$sqlstr_array[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='" . sys_get_time() . "',looktype=0,keytype=2";
			$sqlstr_array[] = "update sys_o2order_record set is_return=1,regdate='" . sys_get_time() . "' where order_id=$orderid";
			$result = $this->do_transaction($sqlstr_array);
		}else{
			$order_list = $this->get_list_bysql("select * from sys_o2order where id=$orderid and payflag>3 and client_id=$client_id");
			if(!$order_list){
				sys_out_fail("该订单不能删除");
			}
			$sqlstr = "delete from sys_o2order where id=$orderid and payflag>3 and client_id=$client_id";
			$result = $this->do_execute($sqlstr);
		}
		sys_out_result($result);
	}



	/**
	 * 评价订单
	 * @parent order_root
	 * @req_params token 登录令牌
	 * @req_params orderid 订单id
	 * @req_params star 总分
	 * @req_params content 评价内容
	 * @req_params anonymous 是否匿名 1：是；2：否 
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor 
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function order_comment()
	{
		$post_array = array('orderid','star','content','anonymous');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();
		$orderid=_POST('orderid');
		$order_list = $this->get_list_bysql("select * from sys_o2order where id=$orderid and payflag=3");
		if(!$order_list){
			sys_out_fail("该订单不能评价");
		}	
		$star=_POST('star');
		$content=_POST('content');
		$anonymous=_POST('anonymous');
		$regdate = sys_get_time();
		$good_id = $order_list[0]['good_id'];
		$sqlstr_array=NULL;
		$shop_id = $this->get_one_bysql("select s.id from sys_o2order o left join sys_good g on o.good_id=g.id left join sys_shop s on g.shop_id=s.id where o.id=$orderid");
		$sqlstr_array[] = "insert into sys_comment set client_id=$client_id,star=$star,o2order_id=$orderid,content='$content',regdate='$regdate',anonymous=$anonymous,shop_id=$shop_id,good_id=$good_id";
		$sqlstr_array[] = "update sys_o2order set payflag=4 where id=$orderid and payflag=3 and client_id=$client_id";
		$shop_list = $this->get_list_bysql("select all_orders,all_star from sys_shop where id=$shop_id");
		$all_orders = $shop_list[0]['all_orders'];
		$all_star = $shop_list[0]['all_star'];
		$all_orders = $all_orders+1;
		$all_star = $all_star+$star;
		$star_aver = round($all_star/$all_orders);
		$sqlstr_array[] = "update sys_shop set all_orders=$all_orders,all_star=$all_star,star=$star_aver where id=$shop_id";
		$result = $this->do_transaction($sqlstr_array);
		sys_out_result($result);
	}
	

	
	/**
	 * 积分转化
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_params id 用户id
	 * @req_params score 兑换积分
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor 
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function score_tocash()
	{
		$post_array = array('id','score');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$id=_POST('id');		
		$score=_POST('score');
		$client_list = $this->get_list_bysql("select * from sys_client where id='$id'");
		$total_score = $client_list[0]['score'];
		$feeaccount = $client_list[0]['feeaccount'];
		if($score > $total_score){
			sys_out_fail('积分余额不足！', 101);
		}
		$config_list = $this->get_list_bysql("select * from sys_config where id=1");
		$score_rate = $config_list[0]['score_rate'];
		$max_cost = $config_list[0]['max_cost'];
		$cost = $score * $score_rate;
		if($cost > $max_cost){
			sys_out_fail('超过最大兑换额度！', 101);
		}
		$sqlstr_array=NULL;
		$sqlstr_array[] = "insert into sys_scoredetail set client_id='$id',score='$score',scoretype=4,regdate='".sys_get_time()."',isget=1";
		$feeaccount = $feeaccount + $cost;
		$total_score = $total_score - $score;
        $sqlstr_array[] = "update sys_client set feeaccount='$feeaccount',score='$total_score' where id='$id'";
        $sqlstr_array[] = "insert into sys_cash set client_id='$id',score='$cost',cashflag=2,regdate='".sys_get_time()."',isget=1";
		$result = $this->do_transaction($sqlstr_array);
		sys_out_result($result);
	}
	

	
	/**
	 * 积分转化记录
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_params id 用户id
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor client_id 相关用户id
	 * @ret_infor score 积分变动数
	 * @ret_infor scoretype 积分变动类型 1：答题积分;2：抽奖积分;3：推广积分;4：积分转化;5：商品兑换
	 * @ret_infor regdate 积分変动时间
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function score_get()
	{
		sys_check_post_single('id');//检查post必选参数完整性
		$id=_POST('id');	
		$pay_sql = "select * from sys_scoredetail where client_id=$id and isget=1 order by id desc";
		$pays = $this -> get_list_bysql($pay_sql);
		sys_out_success(0, $pays);
	}

	

	/**
	 * 添加银行卡
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_params bank 银行名称
	 * @req_params bankcard_name 户主姓名
	 * @req_params bankcard 银行卡号
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function card_add()
	{
		$post_array=array('bank','bankcard_name','bankcard');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);		
		$client_id = sys_get_cid();	//用户id	
		$bank = _POST("bank");		
		$bankcard_name = _POST("bankcard_name");
		$bankcard=_POST('bankcard');
		$sqlstr = "update sys_client set bank='$bank',bankcard_name='$bankcard_name',bankcard='$bankcard' where id='$client_id'";
		$result = $this->do_execute($sqlstr);
		sys_out_result($result);
	}


	/**
	 * 余额提现
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_params cash 提现金额
	 * @req_params type 提现类型 1支付宝;2银行卡
	 * @req_params alipay_account 支付宝账号 （银行卡提现时无需填写）	 	 
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function cash_withdrawal()
	{
		$post_array = array('type');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();	//用户id
		$list = $this->get_list_bysql("select islive,wealth from sys_client where id=$client_id");
		$islive = $list[0]['islive'];
		$wealth = $list[0]['wealth'];
		if($islive == 2 || $wealth <= 100){
			sys_out_fail("该用户不能提现");
		}
		$type = _POST('type');
		if($type == '1'){
			$post_array = array('cash','alipay_account');
			sys_check_post($post_array);//检查post必选参数完整性
			unset($post_array);
			$cash = _POST('cash');
			$alipay_account = _POST('alipay_account');	
		}else if($type == '2'){
			sys_check_post_single('cash');//检查post必选参数完整性
			$cash = _POST('cash');
			$bank_list = $this->get_list_bysql("select bank,bankcard_name,bankcard from sys_client where id=$client_id");
			$bank = $bank_list[0]['bank'];
			$bankcard_name = $bank_list[0]['bankcard_name'];
			$bankcard = $bank_list[0]['bankcard'];		
		}else{
			sys_out_fail(sys_get_msg(101), 101);
		}

		$feeaccount = $this->get_one_bysql("select feeaccount from sys_client where id=$client_id");
		if($cash > $feeaccount){
			sys_out_fail('账户余额不足！', 101);
		}
		$sqlstr = "insert into sys_cash set client_id='$client_id',score='$cash',cashflag=1,regdate='".sys_get_time()."',type=$type,isget=1";
        $result = $this -> do_execute($sqlstr);
        $cash_id = $this->get_insert_id();
        $sqlstr_array=NULL;
        if($result !== false && $cash_id){
	        $feeaccount = $feeaccount - $cash;
			if($type == 1){
				$sqlstr_array[] = "insert into sys_allcash set client_id='$client_id',score='$cash',keytype=1,regdate='".sys_get_time()."',type=1,flag=1,alipay_account='$alipay_account',cash_id=$cash_id";
				$sqlstr_array[] = "update sys_client set feeaccount='$feeaccount',alipay_account='$alipay_account' where id='$client_id'";
			}else{
				$sqlstr_array[] = "insert into sys_allcash set client_id='$client_id',score='$cash',keytype=1,regdate='".sys_get_time()."',type=2,flag=1,bank='$bank',bankcard_name='$bankcard_name',bankcard='$bankcard',cash_id=$cash_id";
				$sqlstr_array[] = "update sys_client set feeaccount='$feeaccount' where id='$client_id'";
			}
			$result = $this->do_transaction($sqlstr_array);
        }  
		sys_out_result($result);
	}


	/**
	 * 余额记录
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor id 主键id
	 * @ret_infor score 金额
	 * @ret_infor cashflag 变动类型 1：余额提现;2：积分转化;3：现金红包;4：退款入账;5：余额入账
	 * @ret_infor regdate 変动时间
	 * @special 特殊说明一 此接口，会随开发，增加部分字段
	 */
	public function cash_get()
	{
		$client_id = sys_get_cid();
		$pay_sql = "select * from sys_cash where client_id=$client_id and isget=1 order by id desc";
		$pays = $this -> get_list_bysql($pay_sql);
		sys_out_success(0, $pays);
	}


	
	/**
	 * 商品兑换
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_params goods_id 商品ID
	 * @req_params phone 联系电话
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor
	 * @special 
	 */
	public function goods_get()
	{
		$post_array = array('goods_id', 'phone');
		sys_check_post($post_array);//检查post必选参数完整性
		unset($post_array);
		$client_id = sys_get_cid();
		$goods_id = _POST('goods_id');
		$phone = _POST('phone');
		$goods_list = $this->get_list_bysql("select * from sys_goods where saleflag=1 and id=$goods_id");
		if(!$goods_list){
			sys_out_fail("传参出错！", 101);
		}
		$client_score = $this->get_one_bysql("select score from sys_client where id=$client_id");
		$goods_score = $goods_list[0]['score'];
		if($goods_score > $client_score){
			sys_out_fail("积分不足！", 107);
		}
		$out_trade_no=sys_get_payno();
		$goods_id = $goods_list[0]['id'];
		$goods_name = $goods_list[0]['goodsname'];
		$goods_img = $goods_list[0]['goodsimg'];
		$goods_big_img = $goods_list[0]['goodsbigimg'];
		$goods_score = $goods_list[0]['score'];
		$regdate = sys_get_time();
		$sqlstr_array=NULL;
		$sqlstr_array[] = " insert into sys_order set out_trade_no='$out_trade_no',goods_id=$goods_id,goods_name='$goods_name',goods_img='$goods_img',goods_big_img='$goods_big_img',orderflag=2,goods_score='$goods_score',phone='$phone',client_id=$client_id,regdate='$regdate'";
		$sqlstr_array[] = "update sys_client set score=score-$goods_score where id=$client_id";
		$sqlstr_array[] = "insert into sys_scoredetail set score=$goods_score,scoretype=5,regdate='$regdate',client_id=$client_id,isget=1";
		$result = $this->do_transaction($sqlstr_array);	
		sys_out_result($result);	
	}


	/**
	 * 商品兑换记录
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor id 订单id
	 * @ret_infor out_trade_no 订单编号	 
	 * @ret_infor goods_id 商品ID
	 * @ret_infor goods_name 商品名称
	 * @ret_infor goods_img 商品图片
	 * @ret_infor goods_big_img 商品大图
	 * @ret_infor orderflag 订单状态 1：已发货;2：未发货
	 * @ret_infor goods_score 商品积分
	 * @ret_infor phone 订单联系电话
	 * @ret_infor client_id 用户ID
	 * @ret_infor regdate 下单时间
	 * @special
	 */
	public function orderlist_get()
	{
		$client_id = sys_get_cid();
		$order_lists = $this->get_list_bysql("select * from sys_order where client_id=$client_id order by id desc");
		sys_out_success(0, $order_lists);
	}


	/**
	 * 清除商品兑换记录
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function orderlist_remove()
	{
		$client_id = sys_get_cid();
		$sqlstr = "delete from sys_order where client_id='$client_id'";
		$result = $this->do_execute($sqlstr);
		sys_out_result($result);
	}


	/**
	 * 好友列表
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_params phonelist 手机号码字符串（多个号码之间用英文逗号隔开，不允许存在空格）
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor id 用户id
	 * @ret_infor account 用户账号或手机号码
	 * @ret_infor nickname 用户昵称
	 * @ret_infor avatar 用户头像
	 * @ret_infor avatarbig 用户头像大图
	 * @ret_infor isclient 是否注册 0：未注册;1：已注册
	 * @ret_infor issend 是否邀请 0：未邀请;1：已邀请
	 * @special
	 */
	public function newclient_get()
	{
		//sys_check_post_single('phonelist');//检查post必选参数完整性
		$phonelist = _POST('phonelist');
		$phonelist = trim($phonelist);
		$phone_array = explode(',',$phonelist);
		$temp_array = array();
		$client_id = sys_get_cid();
		foreach($phone_array as $k=>$v){
			$client_list = $this->get_list_bysql("select id,account,nickname,avatar,avatarbig from sys_client where account=$v");
			if($client_list){
				if(!$client_list[0]['avatar']){
					$client_list[0]['avatar'] = '';
				}
				if(!$client_list[0]['avatarbig']){
					$client_list[0]['avatarbig'] = '';
				}
				if(!$client_list[0]['issend']){
					$client_list[0]['issend'] = '';
				}
				$temp_array[$k] = $client_list[0];
				$temp_array[$k]['isclient'] = 1;
			}else{
				$issend = 0;
				$phone_list = $this->get_one_bysql("select * from sys_phone where client_id=$client_id and phone='$v'");
				if($phone_list){
					$issend = 1;
				}
				$temp_array[$k]['issend'] = $issend;
				$temp_array[$k]['id'] = '';
				$temp_array[$k]['account'] = $v;
				$temp_array[$k]['nickname'] = '';
				$temp_array[$k]['avatar'] = '';
				$temp_array[$k]['avatarbig'] = '';
				$temp_array[$k]['isclient'] = 0;
			}
		}
		sys_out_success(0, $temp_array);
	}


	/**
	 * 邀请好友
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_params phone 手机号码（单个邀请）
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function newclient_add()
	{		
		sys_check_post_single('phone');//检查post必选参数完整性
		$phone = _POST('phone');
		$client_id = sys_get_cid();
		$client_list = $this->get_list_bysql("select * from sys_client where id=$client_id");
		$content = "【".SYS_ZH_NAME."】：手机号为".$client_list[0]['account']."的用户邀请你参与积分兑换活动,邀请码为".$client_list[0]['referee_code'].",活动APP下载地址为".SYS_ANDROID_URL;//编辑发送内容
		ext_send_sms($phone,$content);
		$regdate = sys_get_time();
		$sqlstr = " insert into sys_phone set client_id=$client_id,phone='$phone',regdate='$regdate'";
        $result = $this->do_execute($sqlstr);
        sys_out_result($result);
	}

	
	/**
	 * 分享积分获取
	 * @parent my_root
	 * @req_params token 登录令牌
	 * @req_desc
	 * @ret 1
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function sharing_points_get()
	{	
		$client_id = sys_get_cid();
		$sharing_points = $this->get_one_bysql("select sharing_points from sys_config where id=1");
		$sqlstr = " update sys_client set score=score+$sharing_points where id = $client_id ";
        $result = $this->do_execute($sqlstr);
        sys_out_result($result);
	}



	/**
	 * 商品列表
	 * @parent common_root
	 * @req_desc
	 * @ret 2
	 * @ret_level 1
	 * @ret_infor id 商品主键id
	 * @ret_infor goodsimg 商品图片
	 * @ret_infor goodsbigimg 商品大图
	 * @ret_infor goodsname 商品名称
	 * @ret_infor score 所需积分
	 * @ret_infor regdate 发布时间
	 * @ret_infor saleflag 商品状态 1上架;2下架
	 * @ret_infor remarks 备注
	 * @special 
	 */
	public function goods_list()
	{
		$order_lists = $this->get_list_bysql("select mt.* from sys_goods mt where mt.saleflag=1 order by mt.id desc");
		sys_out_success(0, $order_lists);
	}


    /**
     * 系统设置相关
     * @parent common_root
     * @req_desc
     * @ret 2
     * @ret_level 1
     * @ret_infor prize_name 奖品名称设置
     * @ret_infor score_rate 积分兑换比例设置
     * @ret_infor max_cost 最多可兑换余额
     * @ret_infor phone 举报电话
     * @special
     */
    public function config_set()
    {
        $config_lists = $this->get_list_bysql("select prize_name,score_rate,max_cost,phone from sys_config where id=1");
        sys_out_success(0, $config_lists);
    }


	/**
	 * webview相关
	 * @parent other
	 * @req_params token 登录令牌
	 * @req_params friendid 所要添加的对方主键id 对应所要访问客户的client_id 10
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function webview()
	{
		$parm=_GET('parm');
		switch($parm)
		{
			//新增后台配置模式
			case "aboutus":
				$sqlstr="select aboutus from sys_config where id=1";
				$content=$this->get_one_bysql($sqlstr);
				break;
			case "function_intr":
				$sqlstr="select function_intr from sys_config where id=1";
				$content=$this->get_one_bysql($sqlstr);
				break;
			case "protocal":
				$sqlstr="select protocal from sys_config where id=1";
				$content=$this->get_one_bysql($sqlstr);
				break;
            case "shop":
                $id = I('id');
                $sqlstr="select content from sys_shop where id=$id";
                $content=$this->get_one_bysql($sqlstr);
                break;
            case "good":
                $id = I('id');
                $sqlstr="select content from sys_good where id=$id";
                $content=$this->get_one_bysql($sqlstr);
                break;
            case "ad":
                $id = I('id');
                $sqlstr="select content from sys_ad where id=$id";
                $content=$this->get_one_bysql($sqlstr);
                break;
			default://默认走旧有图片模式
				$content="<img src='".SYS_ROOT_URL."images/code.png' width='100%'/>";
				break;
		}
		$this->show(
			'<html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0" />
                    <title>'.SYS_ZH_NAME.'</title>
                    <style>
                        *{margin: 0;padding: 0;}
                        .body{font-size:18px; line-height:26px;padding:10px;word-wrap:break-word; text-align:justify;}
                        .body img{max-width: 100%;}
                        .body img{width: 100%;}
                    </style>
                </head>
                <body>
                    <div class="body">'.preg_replace('/<\/?(td|tr|table|div)[^>]*?>/i','',$content).'</div>
                </body>
            </html>');
	}


	/**
	 * 推送类型
	 * @parent other
	 * @req_params
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function app_tui(){
		
	}


		
	/**
	 * 上传文件
	 * @parent file_root
	 * @req_params token 登录令牌
	 * @req_params keytype 上传操作类型 1：个人头像图片;2：评价订单图片;$其余依次递增扩展...$
	 * @req_params keyid 订单id 仅keytype=2时需填
	 * @req_params temp_file 文件 临时需要上传的文件控件名称$对应表单type="file"中的name值，相关文件请先在客户端压缩再上传（压缩尺寸宽度固定640） file
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function file_upload()
	{
		$client_id=sys_get_cid();//默认是传client_id
		$result=$this->pub_file_upload($client_id);
		sys_out_result($result);
	}


	/**
	 * 分享插件
	 * @parent menu_sys_plugins
	 * @req_params token 登录令牌
	 * @req_params friendid 所要添加的对方主键id 对应所要访问客户的client_id 10
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor
	 * @special
	 */
	public function plugins_share()
	{

	}
	

	/**
	 * 支付宝插件
	 * @parent menu_sys_plugins
	 * @req_params token 登录令牌
	 * @req_params paytype 支付类型 固定传1 1
	 * @req_params ordertype 订单类型 1：商城订单；2：扫码订单
	 * @req_params orderid 订单ID
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor alipaysign 参数串
	 * @special 特殊说明一 订单金额包括运费
	 * @special 特殊说明二 alipaysign形如：<br>"partner="2088201611767607"&seller_id="2088201611767607"&out_trade_no="GM_201409122105133908_2"&subject=""&body="XX项目"&total_fee="0.01"¬ify_url="http%3A%2F%2F192.168.2.19%2Fgroup1%2Fwhbthink%2F%2Fplugins%2FOnlinePay%2FAlipay%2Fnotify_url.php"&_input_charset="utf-8"&service="mobile.securitypay.pay"&payment_type="1"&sign_type="RSA"&sign="ir8gMpWYoz35zEIrfBNLbFFJNu7n3ZJV6FL643WXggkgpC8IvffuCeDyzRSrZaLfwfW%2B%2FLpbqfJmVz9z0I3gZBTYtRCoz9eJq59AsMRYrn7lTWVZAvUPodX2iPSeLAbfsSc0jmnZMzFKgwmGuHutAmRhWFJLab%2BAum%2FJafLgOiw%3D""<br>其中：<br>out_trade_no参数为我方服务器端生成的交易单号（交易单号包括购买单号和充值单号)<br>格式形如：<br>“2位业务类型前缀(大写字母)+14位时间戳+"ID"+client_id(非固定长度)”（举例：GM20140917172349ID2）；
	 */
	public function alipaysign_get()
	{

	}

	/**
	 * 银联插件
	 * @parent menu_sys_plugins
	 * @req_params token 登录令牌
	 * @req_params paytype 支付类型 固定传2 2
	 * @req_params ordertype 订单类型 1：商城订单；2：扫码订单
	 * @req_params orderid 订单ID
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor respCode 响应码 成功时为“00”
	 * @ret_infor respMsg 具体错误信息描述 当respCode<>“00”时才会返回此字段
	 * @ret_infor tn 银联交易流水号 非常重要，客户端需要用到
	 * @ret_infor reqReserved 我方服务器交易单号
	 * @special 特殊说明一 订单金额包括运费
	 * @special 特殊说明二 特别提示：客户端正式上线时，需要切换支付控件到生产环境，否则会报“无效订单”错误。reqReserved:格式形如：“2位业务类型前缀(大写字母)+14位时间戳+"ID"+client_id(非固定长度)”（举例：CZ20140917172349ID2）；
	 */
	public function unionpay_get()
	{

	}

	/**
	 * 微信插件
	 * @parent menu_sys_plugins
	 * @req_params token 登录令牌
	 * @req_params paytype 支付类型 固定传3 3
	 * @req_params ordertype 订单类型 1：商城订单；2：扫码订单
	 * @req_params orderid 订单ID
	 * @req_desc
	 * @ret 0
	 * @ret_level 1
	 * @ret_infor appid 公众账号ID 微信分配的公众账号ID
	 * @ret_infor partnerid 商户号 微信支付分配的商户号
	 * @ret_infor prepayid 预支付交易会话ID 微信返回的支付交易会话ID
	 * @ret_infor package 扩展字段 暂填写固定值Sign=WXPay
	 * @ret_infor noncestr 随机字符串 随机字符串，不长于32位。
	 * @ret_infor timestamp 时间戳 时间戳
	 * @ret_infor sign 签名
	 * @special 特殊说明一 订单金额包括运费
	 */
	public function weixinpay_get()
	{

	}
}

