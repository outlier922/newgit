<?php
return array(
	//'配置项'=>'配置值'
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => DB_HOST, // 服务器地址
	'DB_NAME'   => DB_NAME, // 数据库名
	'DB_USER'   => DB_USER, // 用户名
	'DB_PWD'    => DB_PWD, // 密码
	'DB_CHARSET' => DB_CHARSET, // 编码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => 'sys_', // 数据库表前缀
	
	'LOG_RECORD'=>true,//开启了日志记录   
    'LOG_RECORD_LEVEL'  => array('ERR','WARN','NOTIC','INFO','DEBUG','SQL'),  // 允许记录的日志级别
	
		
	'VAR_PAGE'=>'page',//修改翻页参数（1是为了安全，2可以兼容公司先前框架（page可与extjs无缝兼容）(TP默认是p)
	//'VAR_ACTION' =>'action',//可以修改控制参数
	
	'URL_PATHINFO_DEPR'=>'/',//修改URL模式分隔符，减少路径层次(不能是'_'下划线，与公司框架action方法命名规则冲突)(TP默认是'/')
	//特别说明：//调试阶段可以设置为'/',正式部署可以设置为'-'
	'URL_CASE_INSENSITIVE' => true, //true表示URL不区分大小写
	
	'SHOW_PAGE_TRACE'=>SYS_DEBUG_MODE ? true : false,//开启TP调试控制台
	 
	'TMPL_CACHE_ON'=>false,//关闭模板缓存
	'DB_FIELD_CACHE'=>false,//关闭数据库缓存				
	'DB_SQL_BUILD_LENGTH' => 30, // SQL缓存的队列长度（DB_FIELD_CACHE=true时生效）	
	
	//采用独立分组模式配置____________________________begin	
	 //需按各组实际需求，选择性手动建立(Common,Conf,Action,Tpl,Model等目录）
	'APP_GROUP_MODE'=>1,//采用独立分组模式（TP3.1.3新增）
	'APP_GROUP_PATH'=>'web',//独立分组根文件夹名称
	'APP_GROUP_LIST'=>'Website,Webadmin,Webservice',//独立分组目录列表	,首字母必须大写
   	'DEFAULT_GROUP'=>'Website',//默认分组入口目录名称	
	//采用独立分组模式配置____________________________end	
	
	 //'TMPL_TEMPLATE_SUFFIX'=>'.html',//定义模板后缀名  
	 //'TMPL_FILE_DEPR'=>'_'  ，//定义模块&模板目录分隔符，形如：User_add.html 
	 //'DB_DSN' => 'mysql://root:wangbin@localhost:3306/biaobiao'
	 //whbmemo:其他常量可以在此依次罗列，其他页面可利用C('NAME')直接访问
	 //以下配置不是特别重要
	 //'HTML_CACHE_ON'=>false,//关闭静态缓存	  
	 
);
?>