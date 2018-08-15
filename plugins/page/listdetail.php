<?php
//引入系统核心文件
require_once("../../include/system.core.php");

$keytype = _GET('keytype');
$keyid = intval(_GET('keyid'));
$tip = '页面不存在';
$sql_helper = new Mysql();
if (isset($keyid) && !empty($keyid)) {
    switch ($keytype)
    {
        case 1:
            $table = "sys_blog_content";
            break;
        case 2:
            $table = "sys_blog_content_draft";
            break;
        default:
            break;
    }
	$sqlstr = "SELECT * FROM `".$table."` WHERE `blog_id`='$keyid' ";
	$content = $sql_helper->get_list_bysql($sqlstr);
    $blog_content = $content ? $content : $tip;
    
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
    <?php
        if (!empty($blog_content)){
        foreach ($blog_content as $bc){
        if (!empty($bc['imgurlbig']))
        {
     ?>
            <img src="<?php echo $bc['imgurlbig']?>" width="100%" />
    <?php
        }
        else
        {
    ?>
             <span style="font-size: 16px;color: #979797;float:left;width: 100%;word-break: break-all;">
                 <?php echo $bc['content']?>
             </span>
    <?php
        }
        }
        }
    ?>
</div>
</body>
</html>