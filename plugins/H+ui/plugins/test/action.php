<?php

class Plugins_Test_Action {
	
	public static function addCss(){
	
	}
	
	public static function addJs(){
		
	}
	
	public static function addHtml(){
		echo "<h1>sdfds</h1>";
	}
	
	public static function run(){
		
	}
}


Frame_Plugin::register("header_top","Plugins_Test_Action::addHtml");



?>