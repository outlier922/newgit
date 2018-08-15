<?php
//下载次数统计
require_once "../../include/system.core.php";
_GET('sign') == 'tbM8zKTArnm36PI' or die('页面不存在');

$sql_helper=new Mysql();
$sqlstr = "select download from sys_config where id=1";
$download = $sql_helper->get_one_bysql($sqlstr);
//若下载次数大于设置最大下载次数，则退出
if($download >= SYS_IPHONE_SHOW_MAXDOWN)
    die('下载次数过多，请将软件上传苹果Store');
else{
    $sql_helper->do_update_count(1,1,'sys_config','download',1);
//     header("Location:".SYS_IPHONE_SHOW_URL);
} 
sys_close_db($sql_helper);
?>
<a id="alink" href="<?php echo SYS_IPHONE_SHOW_URL;?>" style="visibility: hidden;"></a> 
<script>document.getElementById("alink").click();</script>