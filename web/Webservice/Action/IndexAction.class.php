<?php
/*
| --------------------------------------------------------
| 	文件功能：	公司webservice框架初始化类
|	程序作者：	王海滨（移动互联部）
|	时间版本：	2014-06-20
|	特别说明：	(1)本类必须继承公司公共框架PublicAction基类					
				 	(2)本类仅仅提供init初始化方法
| --------------------------------------------------------
*/
// http://192.168.2.19/group1/hm_PHP/index.php/webservice/index/init
// http://192.168.2.19/group1/hm_PHP/index.php?g=webservice&m=index&a=init
class IndexAction extends PublicAction {
    
    protected function _initialize() {}

	public function init(){	
		sys_check_post_single('lastloginversion');//检查post必选参数完整性
		$lastloginversion = _POST('lastloginversion');		
		
		//苹果版需要对正在审核的版本进行敏感模块屏蔽___________begin
		if($lastloginversion=="1.0.0")
			$sys_show_iospay=0;//是否显示在线支付功能，专门应对苹果审核，商店审核通过后需要置为1
		else
			$sys_show_iospay=1;
		//苹果版需要对正在审核的版本进行敏感模块屏蔽___________end
			
		//对客户端所传版本号做转换处理
		$lastloginversion="v".str_replace(".","",$lastloginversion);

		
		//相关系统初始化信息，在此罗列	（注意：此处可以扩展为根据来路IP，返回不同服务器IP，实现双线路机制）	
		
		//将来如果抛弃ThinkPhp框架，可以用如下语句进行切换	
		//$temp_array[0]['sys_web_service']=SYS_WEB_SERVICE."$lastloginversion/?".C('VAR_ACTION')."=";		
		$temp_array[0]['sys_web_service']=SYS_WEB_SERVICE."$lastloginversion/";	
		$temp_array[0]['sys_plugins']=SYS_PLUGINS;	
		$temp_array[0]['sys_show_iospay']=$sys_show_iospay;	
		$temp_array[0]['sys_chat_ip']=SYS_CHAT_IP;
		$temp_array[0]['sys_chat_port']=SYS_CHAT_PORT;
		$temp_array[0]['android_must_update'] = ANDROID_MUST_UPDATE;	
		$temp_array[0]['android_last_version'] = ANDROID_LAST_VERSION;	
		$temp_array[0]['iphone_must_update'] = IPHONE_MUST_UPDATE;	
		$temp_array[0]['iphone_last_version'] = IPHONE_LAST_VERSION;	
		$temp_array[0]['sys_pagesize'] = SYS_PAGE_SIZE;
		$sys_service_phone=$this->get_one_bysql("select service_phone from sys_config order by id desc limit 1");
		$temp_array[0]['sys_service_phone'] =$sys_service_phone;
		$temp_array[0]['android_update_url'] = SYS_ANDROID_URL;
		$temp_array[0]['iphone_update_url'] = SYS_IPHONE_URL;
		$temp_array[0]['iphone_comment_url'] = SYS_IPHONE_URL;
		$temp_array[0]['msg_invite'] =sys_get_msg('msg_invite');	
		$temp_array[0]['start_img'] =$this->get_one_bysql("select startimg from sys_config order by id desc limit 1");		
		sys_out_success(0,$temp_array);
		
		//$this->show($temp_array);//此句可以打开ThinkPhP页面调试控制台
    }
}