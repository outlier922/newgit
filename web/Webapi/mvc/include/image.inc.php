infor中还包含多幅图片信息，字段名称为 imagelist，具体信息解释如下：<br>
<table width="90%" border="1" class="dbTable">
  <tr class="td_header">
    <td width="16%">参数名称</td>
    <td width="24%">参数说明</td>
    <td width="60%">备注</td>
  </tr>
  <tr>
    <td>id</td>
    <td>图片主键id</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>client_id</td>
    <td>所属用户主键id</td>
    <td>&nbsp;</td>
  </tr> <tr>
    <td>keytype</td>
    <td>关联模块类型</td>
    <td><p id="ext-gen1042"> 1帖子 2待续</p></td>
  </tr>
  <tr>
    <td>keyid</td>
    <td>关联模块类型主键</td>
    <td><p id="ext-gen1042">比如:blog_id</p></td>
  </tr>
  <tr>
    <td>imgurl</td>
    <td>图片缩略图地址</td>
    <td><p id="ext-gen1042">&nbsp;</p></td>
  </tr>
  <tr>
    <td>imgurlbig</td>
    <td>图片大图地址(原始图片)</td>
    <td><p id="ext-gen1042">&nbsp;</p></td>
  </tr>
  <?php require_once("../../include/image.inc.php");?>
  <tr>
    <td>orderby</td>
    <td>排序规则</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>regdate</td>
    <td>图片上传时间</td>
    <td>&nbsp;</td>
  </tr>
</table>