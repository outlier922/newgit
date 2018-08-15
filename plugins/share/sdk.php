<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once "../../include/system.core.php";?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- whbmemo:以下 meta 用于适应各种手机屏幕-->
<meta name="viewport" content="width=320px, user-scalable=no, initial-scale=1, maximum-scale=1" />
<title><?php echo SYS_ZH_NAME;?></title>
<link   type="text/css" rel="stylesheet" href="whbstyle.css" />
</head>
<body>
<center>
    <p>
<?php
//查找出帖子内容
$id=_GET('id');
$company_id=_GET('company_id');
//查询帖子内容
if($id>0||$company_id>0)
{
//	$sql_helper=new Mysql();
//	$sqlstr="select content from sys_blog where id=$id";
//	$content=$sql_helper->get_one_bysql($sqlstr);
//	$content=substr($content,0,140);//截取140字
//	sys_close_db($sql_helper);

    if ($id>0)
        header("location: ".SYS_ROOT."index.php/goods/index.html?blog_id=$id");
    else if ($company_id>0)
        header("location: ".SYS_ROOT."index.php/company/index.html?company_id=$id");
}else{//如果是分享软件本身
	//$content="亲，欢迎下载并试用".SYS_ZH_NAME."!";
    header("location: ".SYS_ROOT."download/mobile/");
}
?>
    </p>
    <table width="320" border="0">
        <tr>
            <td width="11%"><img src="<?php echo SYS_ROOT."/images/logo.png"?>" width="30" height="30"></td>
            <td width="89%"><?php echo SYS_ZH_NAME;?>_APP</td>
        </tr>
        <tr>
            <td colspan="2"><div class=hrDIV></div></td>
        </tr>
        <tr>
            <td colspan="2"><?php echo $content;?></td>
        </tr>
         <tr>
            <td colspan="2"><div class=hrDIV></div></td>
        </tr>
        <tr>
            <td colspan="2" align="right">来自《<?php echo SYS_ZH_NAME;?>》的分享内容</td>
        </tr>
    </table>
</center>
</body>
</html>