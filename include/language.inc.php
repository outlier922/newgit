<?php
/*
| --------------------------------------------------------
| 	文件功能：系统提示信息配置文件
|	程序作者：王海滨（技术部）
|	编写时间：2014-06-20
| --------------------------------------------------------
*/
//定义错误编码描述
$msg["100"]		=  "POST参数不足！详见msg说明";
$msg["101"]		=  "POST参数错误！详见msg说明";//手机号码格式错误
$msg["102"]		=  "密码错误！";
$msg["103"]		=  "随机验证码错误！";
$msg["104"]		=  "登录账号被冻结！";
$msg["105"]		=  "账号已经被注册！";//手机号或邮箱已经被注册！
$msg["106"]		=  "用户账号不存在！";
$msg["107"]		=  "账户余额不足！";
$msg["108"]		=  "昵称已经被注册！";
$msg["109"]		=  "已进行过此操作，请勿重复！";

$msg["200"]		=  "登录令牌失效，请重新登录！";

$msg["300"]		=  "上传未知错误，请重试！";
$msg["301"]		=  "上传文件大于5M的限制！";
$msg["302"]		=  "上传文件类型不符合规范！";
$msg["304"]		=  "没有选择上传文件！";

$msg["402"]		=  "数量超过系统限制，禁止操作";
$msg["403"]		=  "没有模块操作权限！";
$msg["404"]		=  "即将访问的页面，已经不存在";
$msg["500"]		=  "系统繁忙，请稍候重试！";

$msg["600"]		=  "无法连接河马短信网关！";
$msg["601"]		=  "河马短信网关已经欠费！";

//定义系统邀请安装
$msg["msg_invite"]  = "邀请大家安装软件!";

?>