<?php require_once("../include/head.inc.php");?>
<?php require_once(SYS_ROOT_PATH."include/language.inc.php");?>

<script>whbRemoveMask();</script>

<div class="contentDIV">
  <p><img src="<?php echo SYS_EXTJS_URL?>images/apple2.gif" width="16" height="16" /> <span class="titlestyle">功能描述:<span class="subtitlestyle">webview参数</span></span></p>

  <p class="subtitlestyle">请求地址：[sys_web_service]+&quot;webview/parm/【下面的参数】"</p>
  <table width="90%" border="1" class="dbTable">
    <tr class="td_header">
      <td width="15%">parm</td>
      <td width="15%">说明</td>
      <td width="50%">备注</td>
    </tr>
    <tr>
      <td>aboutus</td>
      <td>关于我们</td>
      <td>[sys_web_service]+"webview/parm/aboutus</td>
    </tr>
    <tr>
      <td>function_intr</td>
      <td>使用说明</td>
      <td>[sys_web_service]+"webview/parm/function_intr</td>
    </tr>
    <tr>
      <td>protocal</td>
      <td>注册协议</td>
      <td>[sys_web_service]+"webview/parm/protocal</td>
    </tr>
    <tr>
      <td>shop</td>
      <td>商家详情</td>
      <td>[sys_web_service]+"webview/parm/shop/id/商家id</td>
    </tr>
    <tr>
      <td>shop</td>
      <td>商品详情</td>
      <td>[sys_web_service]+"webview/parm/good/id/商品id</td>
    </tr>
    <tr>
      <td>ad</td>
      <td>广告详情</td>
      <td>[sys_web_service]+"webview/parm/ad/id/广告id</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
    </tr>

  </table>
</div>

<?php require_once("../include/foot.inc.php");?>
