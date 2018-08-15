<?php require_once("../include/head.inc.php");?>
<?php require_once(SYS_ROOT_PATH."include/language.inc.php");?>
<script>whbRemoveMask();</script>

<div class="contentDIV">
<p><img src="<?php echo SYS_EXTJS_URL?>images/apple2.gif" width="16" height="16" /> <span class="titlestyle">功能描述：获取分享插件接口</span></p>
<p>(1) 客户端1.0.0版本由我方客服帮助申请“微信”SDK分享，其他第三方分享统一利用"<span class="boldstyle">webview简单方式</span>"实现：</p>
<p>微信SDK 分享成功链接地址：[sys_plugins]share/sdk.php?id=[blog_id]</p>
<p>其他webview 链接地址为：[sys_plugins]share/webview.php?id=[blog_id]</p>
<p class="">(2) 如果客户坚持其他第三方也必须用“客户端SDK”分享模式，则在1.0.1版本统一添加（各分享接口由客户自行申请并提供）</p>
<p>此时服务器不再提供webview分享方式，所有客户端统一使用SDK分享方式实现。</p>
<p>产品/公告/咨询 分享SDK 分享成功链接地址为：[sys_plugins]share/sdk.php?id=[blog_id]</p>
<p>公司分享 SDK 分享成功链接地址为：[sys_plugins]share/sdk.php?company_id=[company_id]</p>
<p><span class="">(3) 无论哪种方式，</span><span class="">当blog_id=0时，均表示分享软件本身。</span></p>
<p><span class="inforstyle">特别提示：[sys_plugins]定义请参考系统初始化接口说明。</span></p>
<p>&nbsp;</p>
</div>

<?php require_once("../include/foot.inc.php");?>