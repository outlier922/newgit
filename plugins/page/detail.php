<?php
//引入系统核心文件
require_once("../../include/system.core.php");

$id = intval(_GET('id'));
$parm = _GET('parm');
var_dump($_GET);die;
$tip = '页面不存在';
$sql_helper = new Mysql();
if (isset($id) && !empty($id)) {
    $sqlstr = "SELECT `content` FROM `sys_".$parm."` WHERE `id`='$id' LIMIT 0,1 ";
	$content = $sql_helper->get_one_bysql($sqlstr);
	$content = $content ? $content : $tip;
}else
{
	$content = $tip;
}
sys_close_db($sql_helper);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0" />
<title>图文详情</title>
<style>
*{margin: 0;padding: 0;}
.body{font-size:18px; line-height:26px;padding:10px;word-wrap:break-word; text-align:justify;}
.body img{max-width: 100%;}
</style>
</head>
<body>
<div class="body">
<?php echo $content; ?>
</div>
</body>
</html>