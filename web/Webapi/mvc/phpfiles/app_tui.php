<?php require_once("../include/head.inc.php");?>
<?php require_once(SYS_ROOT_PATH."include/language.inc.php");?>

<script>whbRemoveMask();</script>

<div class="contentDIV">
  <p><img src="<?php echo SYS_EXTJS_URL?>images/apple2.gif" width="16" height="16" /> <span class="titlestyle">功能描述:<span class="subtitlestyle">推送消息类型列表</span></span></p>

  <p class="subtitlestyle">推送返回信息</p>
  <table width="90%" border="1" class="dbTable">
    <tr class="td_header">
      <td width="15%">keyType</td>
      <td width="15%">keyid</td>
      <td width="50%">说明</td>
    </tr>
    <tr>
      <td>1</td>
      <td>0</td>
      <td>系统消息</td>
    </tr>
    <tr>
      <td>2</td>
      <td>1</td>
      <td>商城订单消息</td>
    </tr>
    <tr>
      <td>2</td>
      <td>2</td>
      <td>扫码订单消息</td>
    </tr>
    <tr>
      <td>2</td>
      <td>3</td>
      <td>优惠券订单消息</td>
    </tr>

  </table>
</div>

<?php require_once("../include/foot.inc.php");?>
