<?php
header("Content-Type:text/html; charset=UTF-8");
require_once "../../../include/system.core.php";
/*
$mobile="18560203398";
$content="尊敬的用户：您的验证码：".sys_create_code()."，10分钟内有效。工作人员不会索取，请勿泄漏。";
echo $content;
ext_send_sms($mobile,$content);
*/
echo ext_smscount_get();

?>