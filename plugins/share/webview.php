<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>
<body>
<?php
//引入系统核心文件
require_once("../../include/system.core.php");
$id=_GET('id');
//查询帖子内容
if($id>0)
{
	$sql_helper=new Mysql();
	$sqlstr="select content from sys_blog where id=$id";
	$content=$sql_helper->get_one_bysql($sqlstr);
	$content=substr($content,0,140);//截取140字
	sys_close_db($sql_helper);		
}else{//如果是分享软件本身
	$content="亲，欢迎下载并试用".SYS_ZH_NAME."!";	
}
$content.="  【来自：".SYS_ZH_NAME."】";
$callback_url=SYS_ROOT."/download/mobile/index.php";//分享成功的链接，统一定位到软件下载页
?>
一键分享到：<div class="bdsharebuttonbox">
<!--
<a href="#" class="bds_qq" data-cmd="qq" title="分享到QQ">QQ好友</a>
<a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信">微信朋友圈</a>
-->
<a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间">QQ空间</a>
<a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博">新浪微博</a>
<a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博">腾讯微博</a>
<a href="#" class="bds_t163" data-cmd="t163" title="分享到网易微博">网易微博</a>
<a href="#" class="bds_douban" data-cmd="douban" title="分享到豆瓣网">豆瓣网</a>
<a href="#" class="bds_kaixin001" data-cmd="kaixin001" title="分享到开心网">开心网</a>
</div>
<script>
window._bd_share_config = {
	"common": {
		"bdSnsKey": {},		
		"bdMini": "1",
		"bdStyle": "0",//0-3 共4种样式
		"bdSize": "64",
		"bdMiniList": false,
		/*"bdText": "自定义分享内容",
		"bdPic": "自定义分享图片地址",
		"bdDesc": '自定义分享摘要',	
		"bdUrl": '自定义分享url地址'	*/
		"bdText": "<?php echo $content?>",
		"bdPic": "<?php echo SYS_ROOT?>images/logo.png",
		"bdDesc": "来自<?php echo SYS_ZH_NAME?>",	
		"bdUrl": "<?php echo $callback_url?>"
	},
	"share": {
		"bdSize":32
	}
};
with(document) 0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=' + ~ ( - new Date() / 36e5)];
</script>
</body>
</html>

