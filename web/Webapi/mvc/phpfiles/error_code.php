<?php require_once("../include/head.inc.php");?>
<?php require_once(SYS_ROOT_PATH."include/language.inc.php");?>
<script>whbRemoveMask();</script>

<div class="contentDIV">
<p><img src="<?php echo SYS_EXTJS_URL?>images/apple2.gif" width="16" height="16" /> <span class="titlestyle">功能描述：错误编码汇总表</span></p>
<p>&nbsp;</p>
<table width="90%" border="1" class="dbTable">
  <tr class="td_header">
    <td width="37%">error_code标识</td>
    <td width="63%">描述说明</td>
  </tr>
  <?php 
    global $msg;
    
    foreach($msg as $msg_key => $msg_value)
    {
        if(!is_numeric($msg_key)) continue;
  ?>
  <tr>
    <td><?php echo $msg_key;?></td>
    <td><?php echo $msg[$msg_key]?></td>
  </tr>
  <tr>
  <?php   }   ?>   
  
</table>
<p class="inforstyle">提示： 在整个项目当中，所有的error_code，标识和描述说明具有唯一性。 例如： 102 错误编码在任何服务响应当中，都代表“ <?php echo $msg["102"]?>”。</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</div>


<?php require_once("../include/foot.inc.php");?>