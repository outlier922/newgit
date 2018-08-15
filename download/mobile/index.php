<?php
require_once "../../include/system.core.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- whbmemo:以下 meta 用于适应各种手机屏幕-->
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
<title>手机应用软件下载</title>
<style type="text/css">
body{margin: 0;}
.mobileDIV{margin:0 auto;max-width: 640px;width: 100%;text-align: center;}
.logoDIV{margin: 10% 0 30%;}
.mobileDIV p{margin: 16px 0;}
</style>
</head>
<body>
<div class="mobileDIV" id="mobileImg">
	<div class="logoDIV">
		<img src="../../images/logo.png" alt="logo" width="120">
		<h2><?php echo SYS_ZH_NAME?></h2>
	</div>
	<p>
	   <?php if(SYS_IPHONE_URL){?>
		<a href="<?php echo SYS_IPHONE_URL?>">
		<?php }else if(SYS_IPHONE_SHOW_URL){?>
		<a href="download.php?sign=tbM8zKTArnm36PI" >
		<?php }else{?>
		<a href="javascript:;" onclick="alert('暂未上架苹果Store')">
		<?php }?>
			<img src="../images/iphone.png" alt="iPhone下载" height="50">
		</a>
	</p>
	<P>
		<a href="<?php echo SYS_ANDROID_URL?>">
			<img src="../images/android.png" alt="Android下载" height="50">
		</a>
	</P>
</div>
<script language="javascript">
var ua = window.navigator.userAgent.toLowerCase();
if (ua.indexOf('micromessenger') != -1) {
	document.getElementById('mobileImg').innerHTML="<img style='width:320px;' src='../images/weixin.jpg' border='0' class='mobile' />";
}
</script>
</body>
</html>