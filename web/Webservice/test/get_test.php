<?php

//加载公司框架核心文件
require_once "../../../include/system.core.php";

//sys_send_sms("18678651029","验证码：45031");

//define("MODEL_METHORD","index/init"); 
define("MODEL_METHORD","v100/client_login");
//client_login,file_upload,advice_save,advice_save,blog_saveoperate,reply_list,feeaccount_remove
//define("MODEL_METHORD","admin/admin_remove");
//verify_code,reg,get_team_list,save_operate,get_blog_list,get_blog_detail
//1216138651@qq.com,wanghaibin0921@163.com,888888@qq.com
?>
<form id="form1" name="form1" method="post"  action="<?php echo SYS_WEB_SERVICE.MODEL_METHORD?>" enctype="multipart/form-data">

<input type="hidden" name="id" value="2" />

<input type="hidden" name="token"  value="TK_3978_5" />
<input type="hidden" name="temp_token"  value="TK_3850_18678651029" />
<input type="hidden" name="code" value="1234" />
<input type="hidden" name="name" value="玉兰广场" />
<input type="hidden" name="content" value="这是一段测试文本" />

<input type="hidden" name="keytype" value="5"/>
<input type="hidden" name="keyid" value="1" /><!--117.127243,36.67905-->
<input type="hidden" name="keyword" value="无" />
<input type="hidden" name="operatetype" value="5"/>
<input type="hidden" name="parentid" value="0" />
<input type="hidden" name="selfsign" value="生命不息折腾不止" />
<input type="hidden" name="email" value="s@s.com" />
<input type="hidden" name="page" value="0" />

<!--登录注册-->
<input type="hidden" name="username" value="18800000000" /><!--18678651029-->
<input type="hidden" name="password" value="123456" /> <!--123456的MD5：e10adc3949ba59abbe56e057f20f883e-->
<input type="hidden" name="nickname" value="WHBdsdf" />
<input type="hidden" name="sex" value="男" />
<input type="hidden" name="district_name" value="山东省,济南市" />
<input type="hidden" name="devicetype" value="1" />
<input type="hidden" name="deviceid" value="5dd2f8b4db6e4016bdad8187c802e355" />
<input type="hidden" name="lastloginversion" value="1.0.0" />
<input type="hidden" name="clienttype" value="1" />

<!--简单聊天-->
<input type="hidden" name="to_id" value="2" />
<input type="hidden" name="msgtype" value="1" />

<!--上传文件-->
<input type="file" name="temp_file" />
<input type="hidden" name="orderby" value="1" />
<input type="hidden" name="duration" value="0" />

<!--添加地点-->
<input type="hidden" name="site_id" value="8" />
<input type="hidden" name="lng" value="117.127243" />
<input type="hidden" name="lat" value="36.67905" />
<input type="hidden" name="radius" value="100" />
<input type="hidden" name="icon_id" value="4" />

<input type="hidden" name="sealflag" value="1" />

<input type="hidden" name="mobile_list" value="18678651029,18678651028,18678651023" />
<input type="hidden" name="total_fee" value="0.01" />

<!--发表帖子-->
<input type="hidden" name="startdate" value="2013-11-13 13:40:40" />
<input type="hidden" name="enddate" value="2013-11-13 13:40:40" />

<!--好友相关-->
<input type="hidden" name="friendid" value="4" />

<!--修改密码-->
<input type="hidden" name="old_password" value="123456" />
<input type="hidden" name="new_password" value="123456" />

<!--保存头像-->
<input type="hidden" name="img_id" value="31" />

<!--获取信息-->
<input type="hidden" name="district_id" value="223" />

<?php echo("<br>当前测试方法名为： ".SYS_WEB_SERVICE.MODEL_METHORD);?> &nbsp;&nbsp;<input type="submit" name="submit" id="submit" value="提交" />
</form>
</body>
</html>