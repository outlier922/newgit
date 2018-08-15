<?php

include_once PLUGINPATH."test/config.php";

class Plugins_Goto_Admin {
	
	static $info = array(
		"name" => 'goto',
		"author"=>"宋红光",
		"desc"=>"欢迎测试插件系统",
		"version"=>"宋红光",
	);
	
	public static function html(){
		echo '<h1>'.MAIL.'</h2>';
	}
	
	public static function setting(){
		
	}
	
	public static  function test(){
		
	}
}



?>