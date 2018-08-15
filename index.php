<?php
require_once "include/system.core.php";	//加载公司框架核心函数定义文件
//定义项目名称和路径
define('APP_DEBUG',SYS_DEBUG_MODE);// 开启调试模式(必须定义在此处，顺序不可颠倒)
//define('APP_DEBUG',true);// 开启调试模式(必须定义在此处，顺序不可颠倒)
//define('APP_NAME', 'webservice');
//define('APP_PATH', '');
define('APP_NAME', '');
define('APP_PATH', '');
//define('__ROOT__', '/'); //若有前台网站，此项配制需开启
//define('__APP__', '/index.php'); //若有前台网站，此项配制需开启
require_once( "ThinkPHP/ThinkPHP.php");//最后加载框架入口文件

/*
接口路径备忘：
http://192.168.2.19/group1/hm_PHP/index.php/webservice/index/init
http://192.168.2.19/group1/hm_PHP/index.php?g=webservice&m=index&a=init
http://192.168.2.19/group1/hm_PHP/index.php/Webadmin/?m=Manage&a=system_clear 
*/