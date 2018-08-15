<?php
require_once "../include/system.core.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo SYS_ZH_NAME?> 官网下载</title>
<link   type="text/css" rel="stylesheet" href="whbstyle.css" />
<script>
var ua = window.navigator.userAgent.toLowerCase();
//此处仅仅区分手机还是电脑
if ((ua.indexOf('android') != -1) || (ua.indexOf('iphone') != -1)) {
	location.replace('mobile/');
}
</script>
</head>
<body>
<div class="topDIV">
<br/>
<span style="margin-left:10px;margin-top:30px"><?php echo SYS_ZH_NAME?> 官网下载</span>
</div>
<div class="middleDIV">
<table class="alignCenter">
<tr><td>
<p><img src="images/code.png" width="180" height="180" /></p>
<p class="titlestyle"><?php echo SYS_ZH_NAME?> 扫描下载</p>
<p class="inforstyle">(特别提示：微信对非腾讯公司的产品设计有主动拦截功能，请换用其他扫描软件)</p>
</td></tr>
</table>
</div>
<div class="bottomDIV">
</br> Copyright &copy;  <?php echo SYS_COMPANY?> 版权所有  All Rights Reserved.</br> 
</div>
</body>
</html>