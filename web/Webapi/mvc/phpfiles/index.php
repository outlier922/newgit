<?php
include_once '../../../../include/system.core.php';
$method = trim($_GET['m']);
// $verison = trim($_GET['v']);
// $preFile = 'V'.str_replace('.', '', $verison);
$preFile = trim($_GET['v']);
global $msg;
//动态输出
include_once '../../../Webapi/version/'.$preFile.'.api.php';//V100将换成用户访问的版本号

$apidoc = $api_phpfiles[$method];
//var_dump($api_phpfiles[$method]);die;
include 'templete.php';	
