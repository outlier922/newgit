<?php header("Content-Type:text/html; charset=UTF-8");?>
<?php
require_once "../../../include/system.core.php";
define("SYS_VERSION","v100/"); // 定义测试版本
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>接口测试 by qfsoft@163.com</title>
<style type="text/css">
.wrap { width:1200px; margin:0 auto;}
.form { width:400px; float:left; display:block;}
ul { list-style:none;}
</style>
</head>
<body>
<!-- ===========显示悬浮信息========= -->
<div style="position:fixed;right:0;bottom:300px;">
<?php 
error_reporting(0);
echo "<pre>";
echo "id=".$_SESSION['client_id']."<br>";
echo "token=".$_SESSION['token']."<br>";
echo "nickname=".$_SESSION['nickname']."<br>";
?>
</div><!-- / -->
<!-- ===========显示悬浮信息end========= -->
<div class="wrap">
<br>

<?php
define("MODEL_METHORD1","init");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE."index/".MODEL_METHORD1?>">
<fieldset>
<legend><?php echo(MODEL_METHORD1); ?>系统初始化</legend>
<ul>
<li>lastloginversion:<input type="text" name="lastloginversion" value="1.0.0" /></li></ul>
<input type="submit" name="submit3" id="submit3" value="提交" />
</fieldset>
</form>


<?php
define("MODEL_METHORD2","code_get");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD2?>">
<fieldset>
<legend><?php echo(MODEL_METHORD2); ?>申请随机验证码接口</legend>
<ul>
<li>username:<input type="text" name="username" value="13589136762" /></li>
</ul>
<input type="submit" name="submit0" id="submit0" value="登录" />
</fieldset>
</form>

<?php
define("MODEL_METHORD3","code_verify");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD3?>">
<fieldset>
<legend><?php echo(MODEL_METHORD3); ?>验证随机码</legend>
<ul>
<li>username:<input type="text" name="username" value="13589136762" /></li>
<li>code:<input type="text" name="code" value="1234" /></li>
</ul>
<input type="submit" name="submit0" id="submit0" value="登录" />
</fieldset>
</form>


<?php
define("MODEL_METHORD4","client_login");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD4?>">
<fieldset>
<legend><?php echo(MODEL_METHORD4); ?>用户登录</legend>
<ul>
<li>username:<input type="text" name="username" value="13589136762" /></li>
<li>password:<input type="text" name="password" value="123456" /></li>
<li>devicetype:<input type="text" name="devicetype" value="1" /></li>
<li>lastloginversion:<input type="text" name="lastloginversion" value="1.0.0" /></li>
</ul>
<input type="submit" name="submit0" id="submit0" value="登录" />
</fieldset>
</form>

<?php if(@$_SESSION['client_id']){  ?>

<?php
define("MODEL_METHORD5","client_loginout");
?>	
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD5?>">
<fieldset>
<legend><?php echo(MODEL_METHORD5); ?>退出登录</legend>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
</ul>
<input type="submit" name="submit0" id="submit0" value="退出登录" />
</fieldset>
</form>
<?php }else{ ?><a href="" title="">未登录</a> <?php } ?>


<?php
define("MODEL_METHORD6","client_add");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD6?>">
<fieldset>
<legend><?php echo(MODEL_METHORD6); ?>用户注册接口</legend>
<ul>
<ul>
<li>temp_token:<input type="text" name="temp_token" value="" /></li>
<li>username:<input type="text" name="username" value="13589136762" /></li>
<li>password:<input type="text" name="password" value="123456" /></li>
<li>nickname:<input type="text" name="nickname" value="thinkwind" /></li>
<li>sex:<input type="text" name="sex" value="男" /></li>
<li>selfsign:<input type="text" name="selfsign" value="ddd" /></li>
<li>email:<input type="text" name="email" value="无" /></li>
<li>mobile:<input type="text" name="mobile" value="" /></li>

</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD7","client_save");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD7?>">
<fieldset>
<legend><?php echo(MODEL_METHORD7); ?>保存用户资料</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>nickname:<input type="text" name="nickname" value="thinkwind" /></li>
<li>sex:<input type="text" name="sex" value="男" /></li>
<li>age:<input type="text" name="age" value="25" /></li>
<li>school_id:<input type="text" name="school_id" value="1" /></li>
<li>college_id:<input type="text" name="college_id" value="3" /></li>
<li>major_id:<input type="text" name="major_id" value="7" /></li>
<li>class_id:<input type="text" name="class_id" value="8" /></li>
<li>club_ids:<input type="text" name="club_ids" value="1,2" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD8","client_get");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD8?>">
<fieldset>
<legend><?php echo(MODEL_METHORD8); ?>获取用户个人资料</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>id:<input type="text" name="id" value="1" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>


<?php
define("MODEL_METHORD9","client_list");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD9?>">
<fieldset>
<legend><?php echo(MODEL_METHORD9); ?>获取成员列表</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>keytype:<input type="text" name="keytype" value="1" /></li>
<li>keyid:<input type="text" name="keyid" value="0" /></li>
<li>keyword:<input type="text" name="keyword" value="无" /></li>
<li>page:<input type="text" name="page" value="0" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD10","password_save");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD10?>">
<fieldset>
<legend><?php echo(MODEL_METHORD10); ?>修改并保存密码</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>keytype:<input type="text" name="keytype" value="1" /></li>
<li>old_password:<input type="text" name="old_password" value="" /></li>
<li>new_password:<input type="text" name="new_password" value="" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD11","client_verify");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD11?>">
<fieldset>
<legend><?php echo(MODEL_METHORD11); ?>验证用户名是否合法</legend>
<ul>
<ul>
<li>username:<input type="text" name="username" value="13589136762" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD12","password_reset");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD12?>">
<fieldset>
<legend><?php echo(MODEL_METHORD12); ?>重设密码接口</legend>
<ul>
<ul>
<li>temp_token:<input type="text" name="temp_token" value="" /></li>
<li>keytype:<input type="text" name="keytype" value="1" /></li>
<li>new_password:<input type="text" name="new_password" value="123456" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD15","client_accountpay");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD15?>">
<fieldset>
<legend><?php echo(MODEL_METHORD15); ?>用户账户余额付款</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>keytype:<input type="text" name="keytype" value="1" /></li>
<li>total_fee:<input type="text" name="total_fee" value="0.01" /></li>
<li>id_list:<input type="text" name="id_list" value="" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD16","file_upload");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD16?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD16); ?>上传文件</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>keytype:<input type="text" name="keytype" value="1" /></li>
<li>keyid:<input type="text" name="keyid" value="0" /></li>
<li>duration:<input type="text" name="duration" value="0" /></li>
<li>orderby:<input type="text" name="orderby" value="0" /></li>
<li>content:<input type="text" name="content" value="无" /></li>
<li>temp_file:<input type="file" name="temp_file" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD17","notice_list");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD17?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD17); ?>通知列表</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>page:<input type="text" name="page" value="0" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD18","notice_saveoperate");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD18?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD18); ?>通知操作接口</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>id:<input type="text" name="id" value="0" /></li>
<li>keytype:<input type="text" name="keytype" value="0" /></li>
<li>keyid:<input type="text" name="keyid" value="0" /></li>
<li>operatetype:<input type="text" name="operatetype" value="0" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>


<!-- 以上为通用的 -->

<?php
define("MODEL_METHORD19","blog_list");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD19?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD19); ?>商品列表</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>district:<input type="text" name="district" value="无" /></li>
<li>orderby:<input type="text" name="orderby" value="1" /></li>
<li>keytype:<input type="text" name="keytype" value="1" /></li>
<li>keyid:<input type="text" name="keyid" value="0" /></li>
<li>page:<input type="text" name="page" value="0" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>


<?php
define("MODEL_METHORD20","blog_get");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD20?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD20); ?>商品详情</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>id:<input type="text" name="id" value="1" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD21","cart_add");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD21?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD21); ?>放入购物车</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>keyid:<input type="text" name="keyid" value="1" /></li>
<li>rule_id:<input type="text" name="rule_id" value="2" /></li>
<li>rule:<input type="text" name="rule" value="蓝色XL号" /></li>
<li>buycount:<input type="text" name="buycount" value="1" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD22","cart_saveoperate");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD22?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD22); ?>购物车操作</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>id:<input type="text" name="id" value="" /></li>
<li>keytype:<input type="text" name="keytype" value="1" /></li>
<li>buycount:<input type="text" name="buycount" value="0" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>


<?php
define("MODEL_METHORD23","feeaccount_remove");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD23?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD23); ?>余额购买</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>keytype:<input type="text" name="keytype" value="1" /></li>
<li>keyid:<input type="text" name="keyid" value="0" /></li>
<li>address:<input type="text" name="address" value="0" /></li>
<li>password:<input type="text" name="password" value="0" /></li>
<li>score:<input type="text" name="score" value="0" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD24","ser_list");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD24?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD24); ?>我的邀请成员列表</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD25","ser_get");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD25?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD25); ?>我的服务专员</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<?php
define("MODEL_METHORD26","chatpush_add");
?>
<form class="form" method="post" action="<?php echo SYS_WEB_SERVICE.SYS_VERSION.MODEL_METHORD26?>" enctype="multipart/form-data">
<fieldset>
<legend><?php echo(MODEL_METHORD26); ?>真聊天消息推送</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="<?php echo $_SESSION['token']; ?>" /></li>
<li>msgtype:<input type="text" name="msgtype" value="1" /></li>
<li>content:<input type="text" name="content" value="dddd" /></li>
<li>recv_id:<input type="text" name="recv_id" value="4" /></li>
<li>group_id:<input type="text" name="group_id" value="0" /></li>
<li>group_name:<input type="text" name="group_name" value="无" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>


<form class="form" method="post" action="http://127.0.0.1/group2/hm_youcheng/index.php/Webservice/v100/pub_ser_limit" enctype="multipart/form-data">
<fieldset>
<legend>测试随机服务专员</legend>
<ul>
<ul>
<li>parent_id:<input type="text" name="parent_id" value="6" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<form class="form" method="post" action="http://127.0.0.1/group2/hm_youcheng/index.php/Webservice/v100/pub_update_usercount" enctype="multipart/form-data">
<fieldset>
<legend>测试新注册邀请成员</legend>
<ul>
<ul>
<li>parent_id:<input type="text" name="parent_id" value="6" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<form class="form" method="post" action="<?php echo SYS_PLUGINS; ?>OnlinePay/Union/unionpay_get.php" enctype="multipart/form-data">
<fieldset>
<legend>银联</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>paytype:<input type="text" name="paytype" value="2" /></li>
<li>keytype:<input type="text" name="keytype" value="" /></li>
<li>keyid:<input type="text" name="keyid" value="0" /></li>
<li>total_fee:<input type="text" name="total_fee" value="" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<form class="form" method="post" action="<?php echo SYS_PLUGINS; ?>OnlinePay/Alipay/alipaysign_get.php" enctype="multipart/form-data">
<fieldset>
<legend>支付宝</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>paytype:<input type="text" name="paytype" value="2" /></li>
<li>keytype:<input type="text" name="keytype" value="" /></li>
<li>keyid:<input type="text" name="keyid" value="0" /></li>
<li>total_fee:<input type="text" name="total_fee" value="" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

<form class="form" method="post" action="<?php echo SYS_PLUGINS; ?>OnlinePay/Weixinpay/weixinpay_get.php" enctype="multipart/form-data">
<fieldset>
<legend>微信</legend>
<ul>
<ul>
<li>token:<input type="text" name="token" value="" /></li>
<li>paytype:<input type="text" name="paytype" value="3" /></li>
<li>keytype:<input type="text" name="keytype" value="1" /></li>
<li>keyid:<input type="text" name="keyid" value="0" /></li>
<li>total_fee:<input type="text" name="total_fee" value="0.01" /></li>
</ul>
</ul>
<input type="submit" name="submit0" id="submit0" value="确定" />
</fieldset>
</form>

</body>
</html>