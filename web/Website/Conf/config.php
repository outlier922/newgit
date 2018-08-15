<?php
// 配置网站前台文件
return array(
	'URL_MODEL'=>0,//注意此处采用普通URL模式（为了通用性和代码的可移植性）
	//静态缓存
	'HTML_CACHE_ON' =>false,
	//默认错误跳转对应的模板文件
 	'TMPL_ACTION_ERROR' => 'common:jump',
 	//默认成功跳转对应的模板文件
 	'TMPL_ACTION_SUCCESS' => 'common:jump',
 	// 定义 I 函数过滤方法
	'DEFAULT_FILTER' => 'htmlspecialchars,stripslashes,trim',
);
?>
