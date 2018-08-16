<?php
/*
| --------------------------------------------------------
| 	文件功能：业务扩展函数定义文件（所有函数均以ext_开头）（表示extend扩展）
|	程序作者：XXXX（技术部）
|	时间版本：2014-09-20
|	特别提示：本文件作为System.core.php文件的扩展和补充，由system.core.php自动加载
| --------------------------------------------------------
*/

function ext_myfunc()
{
	echo "test";
}


//发送通知
function ext_add_notice($keytype,$keyid,$content,$client_id,$from_id=1,$push_method=1){
	$sql_helper = new Mysql();

	//首先插入系统通知表
	$regdate = sys_get_time();
	$sqlstr = "insert into sys_mess(content,client_id,from_id,looktype,regdate,keytype,keyid) values ";
	$client_id_a = explode(',',$client_id);
	$sql_values = "";
	foreach ($client_id_a as $client_id_a_i){
		$sql_values .= "('$content',$client_id_a_i,$from_id,0,'$regdate',$keytype,$keyid),";
	}
	if($client_id_a){
		$sqlstr .= substr($sql_values,0,-1);
		$sql_helper -> do_execute($sqlstr);
	}

	sys_close_db($sql_helper);

	//然后发送系统通知
	$url =SYS_WEB_SERVICE."V100/push_add";//调用BaseAction中的具体推送方法
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "keytype=$keytype&keyid=$keyid&from_id=$from_id&content={$content}&client_idlist={$client_id}&push_method=$push_method");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//正式部署必须为1，防止向客户端输出
	//特别注意：调试时，将CURLOPT_RETURNTRANSFER置为0,可以查看具体输出
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);//设置成1秒后超时,不用等待返回结果
	$result=curl_exec($ch);
	curl_close($ch);
}

//发送通知
function add_notice($keytype,$keyid,$content,$client_id,$from_id=1,$push_method=1){
	//然后发送系统通知
	$url =SYS_WEB_SERVICE."V100/push_add";//调用BaseAction中的具体推送方法
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "keytype=$keytype&keyid=$keyid&from_id=$from_id&content={$content}&client_idlist={$client_id}&push_method=$push_method");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//正式部署必须为1，防止向客户端输出
	//特别注意：调试时，将CURLOPT_RETURNTRANSFER置为0,可以查看具体输出
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);//设置成1秒后超时,不用等待返回结果
	$result=curl_exec($ch);
	curl_close($ch);
}

//支付宝或银联支付成功核心业务处理函数
/*
out_trade_no:我方订单号（格式形如："14位时间戳+"ID"+client_id(非固定长度)"（举例：20140917172349ID2））
trade_no:支付宝或银联流水号
total_fee:交易金额
keytype:1支付宝手机端 2银联手机端 3：支付宝网站端 4：银联网站端 
*/
function ext_pay_success($out_trade_no,$trade_no,$total_fee,$keytype)
{		
	$sql_helper=new Mysql();
	//注意此处client_id必须截取,不能采用session机制
	$type_prefix = substr($out_trade_no,0,3);
	if($type_prefix == 'SKY'){
		$ordertype = 2;
		$client_id=substr($out_trade_no,19,strlen($out_trade_no)-19);
	}else if($type_prefix == 'YHQ'){
		$ordertype = 3;
		$client_id=substr($out_trade_no,19,strlen($out_trade_no)-19);
	}else{
		$ordertype = 1;
		$client_id=substr($out_trade_no,16,strlen($out_trade_no)-16);
	}
	
	$sqlstr_con_array=NULL;
	//修改支付状态
	$paytime = sys_get_time();
	if($ordertype == 1){
		//防止重复异步
		$sqlstr = " select * from sys_o2order where out_trade_no = '$out_trade_no'";
		$temp_array = $sql_helper->get_list_bysql($sqlstr);	
		$id = $temp_array[0]['id'];	
		$sqlstr_con_array[] = "update sys_o2order set trade_no='$trade_no',paytime='$paytime',paytype=$keytype,payflag=2 where out_trade_no='$out_trade_no'";
		//发送支付成功通知
		$content = '订单'.$out_trade_no.'已支付成功！请及时到店消费';
		send_mess(2,$content,$client_id,1);
		//写入通知列表
		$sqlstr_con_array[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$paytime',looktype=0,keytype=2,type=1,keyid=$id";
		//更新商品表数据信息	
		$num = $temp_array[0]['num'];
		$good_id = $temp_array[0]['good_id'];
		$shop_id = $sql_helper->get_one_bysql("select shop_id from sys_good where id=$good_id");
		$sqlstr_con_array[] = "update sys_good set salenum=salenum+$num where id='$good_id'";
		$sqlstr_con_array[] = "update sys_shop set salenum=salenum+$num where id='$shop_id'";
		//生成核销码，并记录表中
		for ($x=0; $x<$num; $x++) {
		  	$out_no = date("YmdHis").rand(1,9999);
		  	$sqlstr_con_array[] = "insert into sys_out_no set o2order_id=$id,out_no='$out_no'";
		}		
		$sqlstr_con_array[] = "insert into sys_o2order_record set order_id=$id,shop_id=$shop_id,num=$num,is_verification=2,is_return=2,regdate='$paytime'";
	}else if($ordertype == 2){
		$sqlstr = " select s.arrival_rate,s.wealth_rate,s.service_type,s.service_rate,s.service_fee,sy.totalfee,sy.shop_id,s.name as shop_name,s.address from sys_sysorder sy left join sys_shop s on sy.shop_id = s.id where sy.out_trade_no = '$out_trade_no'";
		$temp_array = $sql_helper->get_list_bysql($sqlstr);
		//发送支付成功通知
		$content = '扫码支付订单'.$out_trade_no.'已支付成功！';
		send_mess(2,$content,$client_id,2);
		//写入通知列表
		$sqlstr_con_array[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$paytime',looktype=0,keytype=2,type=2";
		$arrival_rate = $temp_array[0]['arrival_rate'];
		$wealth_rate = $temp_array[0]['wealth_rate'];
		$service_type = $temp_array[0]['service_type'];
		$totalfee = $temp_array[0]['totalfee'];
		$shop_id = $temp_array[0]['shop_id'];
		$shop_name = $temp_array[0]['shop_name'];
		$address = $temp_array[0]['address'];
		if($service_type == 1){
			$arrival_fee = 	round($totalfee*$arrival_rate,2);				
			$wealth_fee = 	round($totalfee*$wealth_rate,2);
			$service_fee = 	$totalfee - $arrival_fee;									
		}else{
			$service_fee = $shop_list[0]['service_fee'];
			$arrival_fee = $totalfee - $service_fee;				
			$wealth_fee = 	round($totalfee*$wealth_rate,2);					
		}
		$islive = $sql_helper->get_one_bysql("select islive from sys_client where id=$client_id");
		if($islive == 1){					
			$sqlstr_con_array[] = "update sys_shop set feeaccount=feeaccount+$arrival_fee,wealth_redbag=wealth_redbag+$wealth_fee,redbag=redbag+$wealth_fee where id=$shop_id";				
			$sqlstr_con_array[] = "update sys_client set wealth=wealth+$wealth_fee where id=$client_id";
			$sqlstr_con_array[] = "insert into sys_wealth_redbag set type=1,fee=$wealth_fee,regdate='$paytime',client_id=$client_id,shop_id=$shop_id";
			$sqlstr_con_array[] = "insert into sys_income set totalfee=$totalfee,type=4,client_id=$client_id,shop_id=$shop_id,shop_name='$shop_name',arrival_fee=$arrival_fee,wealth_fee=$wealth_fee,service_fee=$service_fee,address='$address',regdate='$paytime'";
		}else{
			$wealth_fee = 0;
			$sqlstr_con_array[] = "update sys_shop set feeaccount=feeaccount+$arrival_fee where id=$shop_id";
			$sqlstr_con_array[] = "insert into sys_income set totalfee=$totalfee,type=4,client_id=$client_id,shop_id=$shop_id,shop_name='$shop_name',arrival_fee=$arrival_fee,wealth_fee=$wealth_fee,service_fee=$service_fee,address='$address',regdate='$paytime'";
		}
		$sqlstr_con_array[] = "update sys_sysorder set trade_no='$trade_no',paytime='$paytime',paytype=$keytype,payflag=1,wealth='$wealth_fee' where out_trade_no='$out_trade_no'";
	}else if($ordertype == 3){
		$sqlstr_con_array[] = "update sys_cardorder set trade_no='$trade_no',paytime='$paytime',paytype=$keytype,payflag=1 where out_trade_no='$out_trade_no'";
		$order_list = $sql_helper->get_list_bysql("select id,num,total_score,total_price,card_id,shop_id from sys_cardorder where out_trade_no='$out_trade_no'");
		$id = $order_list[0]['id'];
		$num = $order_list[0]['num'];
		$total_score = $order_list[0]['total_score'];
		$card_id = $order_list[0]['card_id'];
		$total_price = $order_list[0]['total_price'];
		$shop_id = $order_list[0]['shop_id'];
		//发送支付成功通知
		$content = '优惠券支付订单'.$out_trade_no.'已支付成功！';
		send_mess(2,$content,$client_id,3);
		//写入通知列表
		$sqlstr_con_array[] = "insert into sys_mess set client_id='$client_id',content='$content',from_id=1,regdate='$paytime',looktype=0,keytype=2,type=3,keyid=$id";			
		$sqlstr_con_array[] = "update sys_card set stock=stock-$num,convertnum=convertnum+$num where id=$card_id";				
		$sqlstr_con_array[] = "insert into sys_scoredetail set score=$total_score,scoretype=5,regdate='$paytime',client_id=$client_id,isget=1";
		$sqlstr_con_array[] = "update sys_shop set feeaccount=feeaccount+$total_price,cardfee=cardfee+$total_price where id=$shop_id";
	}
	$sql_helper->do_transaction($sqlstr_con_array);
	sys_close_db($sql_helper);							
}


function ext_pay_fail($out_trade_no){
	$sql_helper=new Mysql();
	$sqlstr_con_array=NULL;
	$type_prefix = substr($out_trade_no,0,3);
	if($type_prefix == 'YHQ'){
		$client_id=substr($out_trade_no,19,strlen($out_trade_no)-19);
		$order_list = $sql_helper->get_list_bysql("select id,total_score from sys_cardorder where out_trade_no='$out_trade_no'");
		$total_score = $order_list[0]['total_score'];
		$orderid = $order_list[0]['id'];
		$sqlstr_con_array[] = "update sys_client set score=score+$total_score where id = $client_id";
		$sqlstr_con_array[] = "delete from sys_cardorder where id=$orderid and payflag=2";
		$sqlstr_con_array[] = "delete from sys_card_no where cardorder_id=$orderid and is_do=2";
		$sql_helper->do_transaction($sqlstr_con_array);
	}
	sys_close_db($sql_helper);
}


function send_mess($keytype,$content,$client_id,$keyid=0,$from_id=1){
	$url =SYS_WEB_SERVICE."V100/push_add";//调用BaseAction中的具体推送方法
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "keytype=$keytype&keyid=$keyid&from_id=$from_id&content={$content}&client_idlist={$client_id}&push_method=1");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//正式部署必须为1，防止向客户端输出
	//特别注意：调试时，将CURLOPT_RETURNTRANSFER置为0,可以查看具体输出
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);//设置成1秒后超时,不用等待返回结果
	$result=curl_exec($ch);
	curl_close($ch);
}




//短信网关函数定义区域________________________________________begin
//发送手机短信(统一调用河马短信网关)
//mobile：手机号码，必须是11位
//content: 短信内容
//测试语句：
//sys_send_sms("18678651029","验证码：4502");
function ext_send_sms($mobile,$content)
{
	if(empty($mobile) || empty($content)) return 0;	
	//if(mb_strlen($content,'utf-8')>65) return 0;	// 短信内容最长65个字
	//$content=$content."(".SYS_ZH_NAME.")";
	$url ="http://oa.hemaapp.cn/index.php/Webadmin/?m=Manage&a=sms_send3";
	//$url ="http://oa.dpthinking.com/index.php/Webadmin/?m=Manage&a=sms_send3";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "hema_pid=".HEMA_PID."&hema_pwd=".HEMA_PWD."&mobile=$mobile&content={$content}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//正式部署必须为1，防止向客户端输出
	//特别注意：调试时，将CURLOPT_RETURNTRANSFER置为0，查看河马短信网关返回的结果
	$result=curl_exec($ch);//河马网关返回一个整数 0:失败 1：成功 2：欠费	
	curl_close($ch);		
	if($result==0) sys_out_fail(sys_get_msg(600),600);//网络故障
	if($result==2) sys_out_fail(sys_get_msg(601),601);//余额不足
	$ip = get_client_ip(0,true);
	$regdate = sys_get_time();
	$sql_helper=new Mysql();
	$sql_helper->do_execute("INSERT INTO sys_sms set mobile='$mobile',ip='$ip',regdate='$regdate'");
	//echo $result;
} 
//查询手机短信剩余条数
//测试语句：
//sys_smscount_get();
function ext_smscount_get()
{
	/*
	$url ="http://oa.hemaapp.cn/index.php/Webadmin/?m=Manage&a=smscount_get";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "hema_pid=".HEMA_PID."&hema_pwd=".HEMA_PWD);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//正式部署必须为1，防止向客户端输出
	//特别注意：调试时，将CURLOPT_RETURNTRANSFER置为0，查看河马短信网关返回的结果
	$result=curl_exec($ch);//河马网关返回一个整数 0:失败 1：成功 2：欠费	
	curl_close($ch);	
	return $result;
	*/
	$url ="http://222.73.117.158/msg/QueryBalance";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "account=JIUYI_888&pswd=4008559191aA");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//正式部署必须为1，防止向客户端输出
	//特别注意：调试时，将CURLOPT_RETURNTRANSFER置为0，查看河马短信网关返回的结果
	$result=curl_exec($ch);//河马网关返回一个整数 0:失败 1：成功 2：欠费
	curl_close($ch);
	$ret_arr=explode(',',$result);
	return $ret_arr[2];
}
//短信网关函数定义区域________________________________________end

//扩展流媒体上传核心函数区域_________________________________________begin
/*流媒体客户端地址解析协议：
(1)服务器存储videourl
(2)苹果需要添加http://前缀+/playlist.m3u8后缀,即真实地址=http://{videourl}/playlist.m3u8
(3)安卓仅需添加rtsp://前缀,即真实地址=rtsp://{videourl}
(4)网站仅需添加rtmp://前缀,即真实地址=rtmp://{videourl}
(5)真实http地址：需要把":1935/vod"去掉
*/
define("EXT_STREAM_INSTANCE","vod");				//定义流媒体实例名称
function ext_upload_file($file_type,$thumbWidth=120,$thumbHeight=120)
{
	$img_array = NULL;//设置初始值		
	@$temp_file=$_FILES['temp_file'];//与前台表单type="file"中的name对应
	sys_check_upload($temp_file);//首先检测是否上传成功
	
	//判断文件格式是否合法________________________________________begin
	//以下两句是为了取得上传文件的扩展名
	$temp_name =explode(".",$temp_file['name']);
	$ext_name =strtolower(end($temp_name));		
	//当上传文件类型不符合规范时，需要报302错误
	if(stripos(SYS_LIMIT_EXT,$ext_name)===false) sys_out_fail(sys_get_msg(302),302);	
	//当仅允许上传图片时却上传了其他类型，也需要报302错误
	if($file_type==1 && stripos(SYS_IMAGE_EXT,$ext_name)===false)  sys_out_fail(sys_get_msg(302),302);	
	//当不允许上传图片时却上传了图片，也需要报302错误
	if($file_type>1 && stripos(SYS_IMAGE_EXT,$ext_name)!==false) 
	{
		sys_out_fail(sys_get_msg(302),302);
		////调试专用，打印出错误类型	
		//$out_array['status'] = 0;//成功：1 失败 0			
		//$out_array['msg'] = sys_get_msg("302");
		//$out_array['error_code'] = $ext_name;		
		//sys_out_json($out_array);//返回	
	}	
	//判断文件格式是否合法_________________________________________end
	
	if(SYS_DFS)
	{
		return ext_upload_file_dfs($temp_file,$file_type,$ext_name,$thumbWidth,$thumbHeight);
	}
	else
	{
		return sys_upload_file_local($temp_file,$file_type,$ext_name,$thumbWidth,$thumbHeight);
	}
}

//上传DFS
function ext_upload_file_dfs($temp_file,$file_type,$ext_name,$thumbWidth,$thumbHeight)
{
	//whb:采用DFS架构时，各项目的临时上传文件均统一保存到远端服务器根目录htdocs\uploadfiles文件夹下
	//(rsync同步软件已经排除此文件夹的同步，以防并发冲突)
	//首先创建对应目录
	sys_mkdir($_SERVER['DOCUMENT_ROOT']."/uploadfiles");
	//秒级别有可能覆盖，故通过sys_create_code再加个6位随机数	
	$save_file=$_SERVER['DOCUMENT_ROOT']."/uploadfiles/".sys_get_no().".".$ext_name;
	//PHP实际上是把你上传的文件从临时目录移动到指定目录,这句最关键,copy比 move_uploaded_file 通用性更强			
	move_uploaded_file($temp_file['tmp_name'],$save_file);				

	//如果是图片类型，则生成缩略图
	if($file_type==1)
	{
		$img_array=sys_make_thumb($save_file,$thumbWidth,$thumbHeight);	
		//将图片从WEB上传到DFS服务器
		$img_array[0]=ext_upload_file_dfs_core($img_array[0]);//上传原图
		$img_array[1]=ext_upload_file_dfs_core($img_array[1]);//上传缩略图
	}	
	else $img_array[0]=ext_upload_file_dfs_core($save_file);			
					
	return $img_array;//返回文件保存的路径	
}
//上传DFS核心函数（形参为本地文件名，返回DFS存储文件名）
function ext_upload_file_dfs_core($local_filename)
{	
	$ftp = new WhbFtp(DFS_LOCAL_IP,DFS_FTP_PORT,DFS_FTP_USER,DFS_FTP_PWD);	//打开FTP连接 
	$dfs_name=EXT_STREAM_INSTANCE."/".date("Y")."/".date("m")."/".basename($local_filename);//按年月区分文件夹
	$result=$ftp->upload_file($local_filename,$dfs_name,true); 		//上传文件(true表示没有目录时自动创建)
	if($result) 
	{
		//删除web上的临时文件
		@unlink($local_filename);
		//流媒体默认1935端口（客户端下载时需要把":1935/vod"去掉）
		$file_save_url=DFS_NET_IP.":1935/vod/".$dfs_name;
	}
	else
	{
		$file_save_url='';
	}
	$ftp->close();
	return $file_save_url;//返回文件保存的路径	
}
//扩展流媒体上传核心函数区域_________________________________________end

//获取IP地址
function getClientIP()
{
    if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])
    {
        $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
    }
    elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])
    {
        $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
    }
    elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"])
    {
        $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
    }
    elseif (getenv("HTTP_X_FORWARDED_FOR"))
    {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    }
    elseif (getenv("HTTP_CLIENT_IP"))
    {
        $ip = getenv("HTTP_CLIENT_IP");
    }
    elseif (getenv("REMOTE_ADDR"))
    {
        $ip = getenv("REMOTE_ADDR");
    }
    else
    {
        $ip = "Unknown";
    }
    return $ip;
}
//接口的描述
function getActionDes($actionName)
{
    $actionArray = array(
        'e_blog_save'=>'商品保存',

    );
    return $actionArray[$actionName];
}
//获取接口参数
function bf_request_string(&$request)
{
    $str = '';
    foreach ($request as $key=>$value)
    {
        if($key == '_dc' || $key == '_URL_') continue;
        $str .= "&{$key}={$value}";
    }
    return $str;
}
/**
+----------------------------------------------------------------------
| 后台模板用到的函数
+----------------------------------------------------------------------
 */
//页面错误提现
function layer_out_fail($error_msg="页面不存在"){
	$str = "<script>";
	$str .= "parent.layer.msg('".$error_msg."',{time:1500});";
	$str .= "parent.layer.close(parent.layer.getFrameIndex(window.name));";
	$str .= "</script>";
	echo $str;
	die;
}
//解析组件
function _parse(&$component){
	$parser = $component['_parser'];
	ob_start();
	include SYS_UI_COMPONENT.$parser.".php";
	ob_end_flush();
}
//展示页面
function _display(&$component){
	$str = $_SERVER["QUERY_STRING"];
	$act = substr($str , 5 , 5);
	ob_start();
	if($act == 'admin'){
		include SYS_UI_COMPONENT."common/header.php";
	}else{
		include SYS_UI_COMPONENT."common/header1.php";
	}
	
	_parse($component);
	include SYS_UI_COMPONENT."common/footer.php";
	ob_end_flush();
	die;
}
//递归向表单中添加值
function form_item_add_value(&$form,&$data){
    for($i=0;$i<count($form);$i++){
        $parser = $form[$i]['_parser'];
        if(substr($parser,0,9) != 'form_item'){
            form_item_add_value($form[$i]['_children'],$data);
        }
        else{
            if($form[$i]['related']){//相关联元素
                for($j=0; $j<count($form[$i]['related']); $j++){
                    $form[$i]['related'][$j]['value'] = $data[$form[$i]['related'][$j]['name']];
                }
            }
            else{
                if(!isset($form[$i]['value']) || $form[$i]['value'] === ''){
                    $form[$i]['value'] = $data[$form[$i]['name']];
                }
            }
        }
    }
}
//生成表单校验器 --递归调用
function form_validation_create(&$form_items,&$rules,&$messages){
	foreach ($form_items as $key=>$form_item){
		$parser = $form_item['_parser'];
		if(substr($parser,0,9) !== 'form_item'){
			form_validation_create($form_items[$key]['_children'],$rules,$messages);
		}
		else{
			$field_name = $form_item['name'];
			$validation_items = $form_item['_validation'];
			//必选项
			if(!isset($form_item['required']) || $form_item['required'] !== 0) {
				$rules[$field_name]['required'] = true;
				$messages[$field_name]['required'] = '“'.$form_item['label']."”是必填参数";
			}
			
			foreach($validation_items as $validation_name=>$validation_item){
				foreach ($validation_items as $validation_name=>$validation_item){
					$rules[$field_name][$validation_name] = $validation_item[0];//验证的值
					$messages[$field_name][$validation_name] = $validation_item[1] ? $validation_item[1] : '';//验证提示信息
				}
			}
		}
	}
}
//获取字段名
function form_edit_fields_get(&$form){
	$fields = array();
	foreach($form as $item){
		$field_name = $item['name'];
		$operate = $item['operate'] ? $item['operate'] : 3;
		if($GLOBALS['cur_operate'] & $operate != $GLOBALS['cur_operate']) continue;
		$fields[] = "mt.".$field_name;
	}
	return $fields;
}
//递归数组，为数组赋值
function component_reset_value(&$component,$data){
	foreach($component as $key=>$value){
		if(is_array($value)){
			component_reset_value($component[$key],$data);
		}else{
			$value_prefix = substr($value,0,2);
			$value_suffix = substr($value,2);
			if(in_array($value_prefix,array('1_','3_'))){
				if($value_prefix == '1_'){
					$component[$key] = $value_suffix;
				}
				else if($value_prefix == '3_'){
					$component[$key] = $data[$value_suffix];
				}
			}
		}
	}
}
//检测是否登录
function is_login(){
	$user = session('auth');
	return (empty($user)) ? 0 : $user['uid'];
}
//检测是否登录2
function is_logins(){
	$user = session('client_id');
	return (empty($user)) ? 0 : $user;
}

function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
	if($data === false || $data === null ){
		return $data;
	}
	$data = (array)$data;
	foreach ($data as $key => $row){
		foreach ($map as $col=>$pair){
			if(isset($row[$col]) && isset($pair[$row[$col]])){
				$data[$key][$col.'_text'] = $pair[$row[$col]];
			}
		}
	}
	return $data;
}
//用户头像，若为空则用默认头像
function get_avatar($avatar){
	return $avatar ? $avatar : SYS_ROOT."images/default_avatar.png";
}
//检测是否选中
function is_checked($id,$arr){
	return in_array($id, $arr) ? 'checked' : '';
}
//导出excel文件
function hm_excel_export($filename, $excel_headers, $sql_portions){
	set_time_limit(0);
	require_once SYS_ROOT_PATH."plugins/PHPExcel/PHPExcel.php";
	$sql_helper = new Mysql();
	$count_sqlstr = "select count(*) $sql_portions[1] ";
	$total_count = $sql_helper -> get_list_bysql($count_sqlstr);
	$objPHPExcel = new PHPExcel();

	$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
	//设置垂直，水平居中
	$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	//设置表头
	$tem_i = 0;
	foreach($excel_headers as $field_name => $field_a){
		//过滤不显示的字段
		if(isset($field_a['show']) && !$field_a['show'])continue;
		if(isset($field_a['export']) && !$field_a['export'])continue;

		//修正宽度
		$temp_w = 100;
		$temp_w = isset($field_a['width']) ? $field_a['width'] : $temp_w;

		//设置标题
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$tem_i].'1', $field_a['title']);

		//设置加粗
		$objPHPExcel->getActiveSheet()->getStyle($cellName[$tem_i].'1')->getFont()->setBold(true);

		//设置宽度
		$objPHPExcel->getActiveSheet()->getColumnDimension($cellName[$tem_i])->setWidth(ceil($temp_w/5));

		$tem_i++;
	}
	//填充数据
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.SYS_ZH_NAME.'_'.$filename.'_'.date('YmdHis').'.xls"');
	header('Cache-Control: max-age=0');
	for($i=0;$i<100;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($i+2),$i);
		sleep(1);
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
}

//专用用数据库组装sql
function fields2SqlStrByPost(&$fields)
{
	if(!is_array($fields)) return '';
	if(count($fields) == 0) return '';
	if(count($_POST) == 0) return '';

	$returnStr = '';
	foreach ($fields as $field)
	{
		$fieldValue = _POST($field);
		$returnStr .= " $field = '$fieldValue',";
	}

	if(strlen($returnStr) > 0 )$returnStr = substr($returnStr, 0, -1);
	return $returnStr;
}

//key、value的数组拼接起来
function kv_implode($glue,$kv){
	$str = "";
	foreach ($kv as $k=>$v){
		$str .= $k."=".$v.$glue;
	}
	return $str ? substr($str,0,-1) : '';
}
//获取密码 --后期去掉该函数
function password_create($password){
	return md5(md5(DATAKEY.md5($password)));
}
//生成基本密码
function base_password_create($password,$data_key=''){
	return md5($data_key.md5($password));
}
//截取第n个分隔符之后的数据
function substring_index($subject, $delimit, $count){
	if($count < 0){
		return implode($delimit, array_slice(explode($delimit, $subject), $count));
	}else{
		return implode($delimit, array_slice(explode($delimit, $subject), 0, $count));
	}
}
//截取第n-m个分隔符之间的数据
function substring_index_to_index($subject,$delimit,$index,$count=1){
	return implode($delimit, array_slice(explode($delimit, $subject), $index, $count));
}
//导出数据
function ext_export($fileName,&$table_header,&$expTableData){
	require_once SYS_ROOT_PATH."plugins/PHPExcel/PHPExcel.php";
	//print_r($expTableData);die;
	$objPHPExcel = new PHPExcel();
	$dataNum = count($expTableData);//数据条数
	if($dataNum > 5000) sys_out_fail("数据超过5000条，无法导出");
	$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
	//设置垂直，水平居中
	$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	//表头适配器 @TODO 后期做成对象形式，对象有直接生成表头的适配器
	$fields = array();
	foreach($table_header as $table_header_i){
		if(!$table_header_i['_after_parser']){
			$fields[$table_header_i['name']] = array('title'=>$table_header_i['title']);
		}
		else{
			$after_parser = $table_header_i['_after_parser']['_parser'];
			if($after_parser == 'td/card'){
				foreach($table_header_i['_after_parser']['_children'] as $temp){
					$fields[substr($temp['value'],2)] = array('title'=>$temp['title']);
				}
			}
			else if($after_parser == 'td/associated_card'){
				foreach($table_header_i['_after_parser']['_children'] as $temp){
					foreach($temp as $temp_2){
						$fields[substr($temp_2['value'],2)] = array('title'=>$temp_2['title']);
					}
				}
			}
			else{
				$fields[substr($table_header_i['_after_parser']['text'],2)] = array('title'=>$table_header_i['title']);
			}
		}
	}
	//设置表头
	$tem_i = 0;
	foreach($fields as $field_name => $field_a){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$tem_i].'1', $field_a['title']);
		//设置表头加粗
		$objPHPExcel->getActiveSheet()->getStyle($cellName[$tem_i].'1')->getFont()->setBold(true);
		//设置宽度
		if(isset($field_a['width'])){
			$objPHPExcel->getActiveSheet()->getColumnDimension($cellName[$tem_i])->setWidth(ceil($field_a['width'])/5);
		}
		else{
			$objPHPExcel->getActiveSheet()->getColumnDimension($cellName[$tem_i])->setWidth(20);
		}
		$tem_i++;
	}
	//填充数据
	for($i=0;$i<$dataNum;$i++){
		$temp_j = 0;
		foreach($fields as $field_name => $field_a){
			if(isset($field_a['type']) && $field_a['type'] == 'numeric'){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValueExplicit($cellName[$temp_j].($i+2), $expTableData[$i][$field_name],PHPExcel_Cell_DataType::TYPE_STRING);
			}
			else{
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$temp_j].($i+2), $expTableData[$i][$field_name]);
			}
			$temp_j++;
		}
	}
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.SYS_ZH_NAME.'_'.$fileName.'_'.date('YmdHis').'.xls"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;die;
}

/**
+----------------------------------------------------------------------
| 其它函数
+----------------------------------------------------------------------
 */
//统计时获取长度
function ext_collect_type_length($date_type){
	if($date_type == 1) return 4;
	else if($date_type == 2) return 7;
	else if($date_type == 3) return 10;
	die("参数错误");
}

?>