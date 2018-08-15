<?php
/*
| --------------------------------------------------------
| 	文件功能：系统顶层核心配置文件
|	程序作者：王海滨（技术部）
|	时间版本：2014-11-26
| --------------------------------------------------------
*/
/*
| --------------------------------------------------------
定义系统调试模式开关,false:关闭 true:开启（可打印详细错误信息,正式上线请设置为false）
特别注意：
（1）验证码1234和苹果测试推送严格依赖此开关标记
当SYS_DEBUG_MODE为true时，验证码为1234，苹果推送为测试环境推送；
当SYS_DEBUG_MODE为false时，验证码为真实码，苹果推送必须AppStore上线后才能收到
（2）支付宝或银联在调试模式下会记录日志到 插件根目录\log.txt
| --------------------------------------------------------
*/
define("SYS_DEBUG_MODE",true);
define("SYS_SAFE_MODE",true);
define("SYS_SERVER_TYPE",2);//1:本机localhost 2:公司测试机146  3：公司正式服务器 4:客户正式服务器
define("SYS_GROUP_NAME","group16");//    group1是开发小组名，需要换成你自己的
define("SYS_UPLOAD_MAX",5);//上传文件最大限制，单位：M（需要php.ini环境支持）
/*
| --------------------------------------------------------
定义项目基本配置信息
| --------------------------------------------------------
*/
define("HEMA_PID","254");//定义公司分配的项目编号（非常重要,比如调用公司短信网关等）
define("HEMA_PWD","kSHtFVxMvmEOiP5");//定义公司分配的项目登录密码，非常重要
define("DATAKEY","I5Zm4rNXKLdBi3AM");//定义公司分配的项目唯一串  bd769d30ca30703650344e9d1fce8da3
define("SYS_EN_NAME","hm_cjml");	//定义项目英文名称
define("SYS_ZH_NAME","超级玛丽");	//定义项目中文名称（不允许超过6个汉字）
define("SYS_COMPANY","日照市东港区林竹商贸有限公司");  //定义客户公司名称
define("SYS_SERVICE_PHONE","0531-67804172");	//定义客户公司电话
define("SYS_DEFAULT_IMAGE","");	//定义客户公司电话

define("ANDROID_MUST_UPDATE","0"); 		//安卓客户端强制更新，0：不强制 1：强制
define("ANDROID_LAST_VERSION","1.0.0");//安卓最新版本号，客户端升级使用
define("IPHONE_MUST_UPDATE","0"); 		//苹果客户端强制更新，0：不强制 1：强制
define("IPHONE_LAST_VERSION","1.0.0");	//苹果最新版本号，客户端升级使用

switch(SYS_SERVER_TYPE)
{
	case 1://本机localhost
		define("SYS_SERVER_IP","127.0.0.1");
		define("SYS_ROOT","http://".SYS_SERVER_IP."/".SYS_EN_NAME."/");	//定义项目根地址(网络绝对路径)
		//define("SYS_ROOT","http://".SYS_SERVER_IP."/".SYS_GROUP_NAME."/".SYS_EN_NAME."/");	//定义项目根地址(网络绝对路径)
		//define("SYS_ROOT_URL","/".SYS_GROUP_NAME."/".SYS_EN_NAME."/");			//定义项目根地址（本地相对路径）
		define("SYS_ROOT_URL","/".SYS_EN_NAME."/");			//定义项目根地址（本地相对路径）
		define('DB_HOST', '127.0.0.1');	//数据库服务器主机地址(此处可以使用内网IP提升速度)
		define('DB_USER', 'root'); 				//数据库帐号
		define('DB_PWD', ''); 		//数据库密码
		break;
	case 2://公司测试机146
		define("SYS_SERVER_IP","124.128.23.74");
		//define("SYS_SERVER_IP","124.128.23.74");	//等同146服务器
		define("SYS_ROOT","http://".SYS_SERVER_IP.":8008/".SYS_GROUP_NAME."/".SYS_EN_NAME."/");	//定义项目根地址(网络绝对路径)
		define("SYS_ROOT_URL","/".SYS_GROUP_NAME."/".SYS_EN_NAME."/");			//定义项目根地址（本地相对路径）
		define('DB_HOST', '192.168.2.146');	//数据库服务器主机地址(此处可以使用内网IP提升速度)
		define('DB_USER', 'group16'); 				//数据库帐号
		define('DB_PWD', 'hm_chenyingying'); 		//数据库密码
		break;
	case 3://公司正式服务器
		define("SYS_SERVER_IP","124.128.23.74:8008");	//此处必须是域名
		define("SYS_ROOT","http://".SYS_SERVER_IP."/".SYS_EN_NAME."/");	//定义项目根地址(网络绝对路径)
		define("SYS_ROOT_URL","/".SYS_EN_NAME."/");					//定义项目根地址(本地相对路径)
		define('DB_HOST', '127.0.0.1');		//数据库服务器主机地址(此处可以使用内网IP提升速度)
		define('DB_USER', 'group1'); 			//数据库帐号
		define('DB_PWD', 'group1_1234'); 	//数据库密码
		break;
	case 4://客户正式服务器
		define("SYS_SERVER_IP","139.224.234.246");			//域名或公网IP均可
		define("SYS_ROOT","http://".SYS_SERVER_IP."/");	//定义项目根地址(网络绝对路径)
		define("SYS_ROOT_URL","/");		//定义项目根地址（本地相对路径）
		define('DB_HOST', '127.0.0.1');		//数据库服务器主机地址(此处可以使用内网IP提升速度)
		define('DB_USER', 'root'); 		//数据库帐号
		define('DB_PWD', 'Linghangcn1213'); 		//数据库密码
		break;
	default:
		die("服务器类型配置错误");
}
/*
| --------------------------------------------------------
定义数据库连接信息（正式部署时需要换成自己的）
| --------------------------------------------------------
*/
define('DB_NAME', SYS_EN_NAME); 	//数据库名
define('DB_CHARSET', 'utf8'); 		//数据库字符集(utf8mb4支持emoji表情符号,必须是5.5.3以上版本）
/*
| ------------------------------------------------------------------------
定义真实聊天服务器信息（真聊天需要单独架设聊天服务器）
| ------------------------------------------------------------------------
*/
define("SYS_CHAT_IP",SYS_SERVER_IP);	//聊天服务器IP地址
define("SYS_CHAT_PORT","5222");				//聊天服务器端口

/*
| --------------------------------------------------------------------
定义分布式存储信息（FTP方式实现）(注意此处默认了分布式服务器的WEB端口是80)
DFS_LOCAL_IP 和 DFS_NET_IP 可以是一个
| --------------------------------------------------------------------
*/
define("SYS_DFS",false);	//定义是否启用DFS分布式图片存储功能
define("DFS_LOCAL_IP","192.168.x.xxx");	//定义当前工作分布式服务器内网IP地址（内网传输速度快）
define("DFS_NET_IP","xxx.xxx.xxx.xxx");		//定义当前工作分布式服务器外网IP地址（外网用于地址解析）
define("DFS_FTP_PORT","21");					//DFS_FTP默认21端口
define("DFS_FTP_USER","username");			//DFS_FTP登录用户名
define("DFS_FTP_PWD","yourpassword");	//DFS_FTP登录密码

/*
| --------------------------------------------------------
定义百度云推送2个key配置信息（注意各项目不同）（此处有特殊性，不能换用define常量定义模式）
| --------------------------------------------------------
*/
$apiKey = "3dUq2DoXNHIXKMxH4YYQVaDB";//河马测试APP
$secretKey = "pKRowOW0OdbzBqT8xAHu5Ab07BltXdrD";//河马测试APP

/*
| --------------------------------------------------------
定义SMTP邮件连接信息
| --------------------------------------------------------
*/
define("MAIL_SMTP","smtp.exmail.qq.com");
define("MAIL_USER","service@hemaapp.cn");
define("MAIL_PWD","123456aA");
/*
| --------------------------------------------------------
定义业务耦合信息（此区域请根据业务情况适当配置和扩展）
| --------------------------------------------------------
*/
define("SYS_ANDROID_URL",SYS_ROOT."download/hm_lhkj".ANDROID_LAST_VERSION.".apk");//定义Android升级路径
define("SYS_IPHONE_URL","");//定义iPhone store升级路径，填空表示暂未上架苹果store
define("SYS_IPHONE_SHOW_URL","");//定义iPhone分发路径，填空表示暂未上架苹果store
define("SYS_IPHONE_SHOW_MAXDOWN","50");//定义iPhone分发最大下载次数，防止查封苹果证书
//↓↓↓↓↓↓↓以下配置信息，如无特殊需要，请勿随便改动↓↓↓↓↓↓↓

/*
| --------------------------------------------------------------------
定义系统运行级别信息（以下区域内的配置，如无特殊情况，请勿改动）
| --------------------------------------------------------------------
*/

define("SYS_ROOT_PATH",dirname(dirname(__FILE__))."/");	 //定义项目根地址(本地绝对路径)system.core.php使用
define("SYS_PAGE_SIZE",20);								//定义列表页面数量大小
define("SYS_DENY_MSG","非法访问！请联系管理员。【王海滨 QQ:277005727】");//定义错误提示信息
define("SYS_ERROR_MSG","系统运行异常，请联系管理员。【王海滨 QQ:277005727】");//定义错误提示信息
define("SYS_WEB_SERVICE",SYS_ROOT."index.php/Webservice/");		//定义WebService配置路径(网络绝对路径)
define("SYS_PLUGINS",SYS_ROOT."plugins/");						//定义插件根路径（网络绝对路径）
define("SYS_PLUGINS_URL",SYS_ROOT_URL."plugins/");		//定义插件根路径（本地相对路径）
define("SYS_EXTJS_URL",SYS_ROOT_URL."plugins/extjs4/");	//定义ExtJS配置路径（本地相对路径）
define("SYS_HUI_URL",SYS_ROOT_URL."plugins/H-ui/");	//定义H-ui配置路径（本地相对路径）
define("SYS_HPLUSUI_URL",SYS_ROOT_URL."plugins/H+ui/");	//定义H+ui配置路径（本地相对路径）
define("ADMIN_ROOT_URL",SYS_ROOT_URL."web/Webadmin/");	//定义管理后台根路径（本地相对路径）

define("SYS_UI_URL",SYS_ROOT_URL."plugins/ui/h-ui/");	//定义ui配置路径（本地相对路径）
define("SYS_UI_PLUGINS",SYS_ROOT_URL."plugins/ui_plugins/");	//定义ui插件配置路径（本地相对路径）
define("SYS_UI_COMPONENT",SYS_ROOT_PATH."plugins/ui_component/2.0.0/");	//定义UI组件配置路径（本地绝对路径）
define("SYS_UI_ASSETS",SYS_ROOT_URL."plugins/assets/");	//定义管理后台UI配置路径（本地绝对路径）
/*
| --------------------------------------------------------
定义头部信息
| --------------------------------------------------------
*/
error_reporting(E_ALL & ~E_NOTICE);//设置系统错误提示级别，非常重要
date_default_timezone_set('PRC'); //设置时区为中国，非常重要
session_start();//启用session机制，非常重要,切勿删除
//header必须写在seesion_start之后，因为seesion_start之前不能有任何输出
header("Content-Type:text/html; charset=UTF-8");
?>