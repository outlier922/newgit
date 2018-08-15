<?php header("Content-Type:text/html; charset=UTF-8");?>
<?php
require_once "../../../include/system.core.php";
define("MODEL_METHORD","v100/reg");
?>
<form id="form1" name="form1" method="post"  action="<?php echo SYS_WEB_SERVICE.MODEL_METHORD?>" enctype="multipart/form-data">

<input type="hidden" name="username" value="" />
<input type="hidden" name="token"  value="TK_072334_2" />
<input type="hidden" name="temp_token"  value="TK_339379_18678651029" />
<input type="hidden" name="code" value="123456" />

<?php echo("<br>当前测试方法名为： ".MODEL_METHORD);?> &nbsp;&nbsp;<input type="submit" name="submit" id="submit" value="提交" />
</form>
</body>
</html>