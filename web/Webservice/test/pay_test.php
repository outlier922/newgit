<?php
require_once "../../../include/system.core.php";
define("MODEL_METHORD","alipaysign_get.php");
define("ALIPAY_URL",SYS_ROOT."/plugins/OnlinePay/Alipay/");	//必须用公网IP:124.128.23.69:8008
?>
<form id="form1" name="form1" method="post"  action="<?php echo ALIPAY_URL.MODEL_METHORD?>" enctype="multipart/form-data">

<input type="hidden" name="token"  value="TK_9939_2" />
<input type="hidden" name="keytype"  value="1" />
<input type="hidden" name="total_fee"  value="0.01" />

<?php echo("<br>当前测试方法名为： ".MODEL_METHORD);?> &nbsp;&nbsp;<input type="submit" name="submit" id="submit" value="提交" />
</form>
</body>
</html>