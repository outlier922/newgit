<?php require_once("../include/head.inc.php");?>
<?php require_once(SYS_ROOT_PATH."include/language.inc.php");?>
<script>whbRemoveMask();</script>
<div class="contentDIV">
<p><img src="<?php echo SYS_EXTJS_URL?>images/apple2.gif" alt="" width="16" height="16" /> <span class="titlestyle">功能描述：获取银联交易签名串(内含我方交易单号)</span></p>
<p class="subtitlestyle">（一）服务接口请求地址：</p>
<table width="90%" border="1" class="dbTable">
  <tr class="td_header">
    <td width="15%">字段名称</td>
    <td width="85%">字段信息</td>
    </tr>
  <tr>
    <td>请求的地址</td>
    <td>[sys_plugins]OnlinePay/Unionpay/unionpay_get.php</td>
  </tr>
</table>
<p><span class="inforstyle">特别提示：[sys_plugins]定义请参考系统初始化接口说明。</span></p>
<p class="subtitlestyle">（二）POST参数列表：</p>
<table width="90%" border="1" class="dbTable">
    <tr class="td_header">
        <td width="122">参数名称</td>
        <td width="152">参数说明</td>
        <td width="597">备注</td>
    </tr>
    <tr>
        <td>token</td>
        <td width="152">登录令牌</td>
        <td width="597" align="left">&nbsp;</td>
    </tr>
    <tr>
        <td>paytype</td>
        <td width="152">支付类型</td>
        <td width="597">固定传2</td>
    </tr>
    <tr>
    <td>keytype</td>
    <td>业务类型</td>
    <td align="left"><p>&nbsp;</p>
        <p>1：账户余额充值 </p>
        <p>2：商品立即购买 </p>
        <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td>keyid</td>
    <td>业务相关id</td>
    <td align="left"><p>&nbsp;</p>
        <p>当keytype=1时, keyid=0 </p>
        <p>当keytype=2时, keyid=blog_id </p>
        <p>&nbsp;</p></td>
  </tr>
    <tr>
        <td>total_fee</td>
        <td>支付交易金额</td>
        <td align="left">单位：元(测试时统一传递0.01元)</td>
    </tr>
</table>
<p class="subtitlestyle">（三）服务接口响应请求：</p>
<table width="90%" border="1" class="dbTable">
    <tr class="td_header">
        <td width="40%">响应结果</td>
        <td width="42%">备注</td>
    </tr>
    <tr>
        <td><p>{&quot;success&quot;:true,&quot;msg&quot;:&quot;操作成功&quot;,&quot;infor&quot;:json信息串}</p></td>
        <td><p>详见（四）特别备注</p></td>
    </tr>
    <?php require_once("../include/error.inc.php");?>
</table>
<p><span class="subtitlestyle">（四）特别备注</span>（infor字段说明）</p>
<table width="90%" border="1" class="dbTable">
    <tr class="td_header">
        <td width="118">参数名称</td>
        <td width="210">参数说明</td>
        <td width="567">备注</td>
    </tr>
    <tr>
        <td>respCode</td>
        <td>响应码</td>
        <td><p>成功时为“00”</p></td>
    </tr>
    <tr>
        <td>respMsg</td>
        <td>具体错误信息描述</td>
        <td>当respCode&lt;&gt;“00”时才会返回此字段</td>
    </tr>
    <tr class="boldstyle">
        <td>tn</td>
        <td>银联交易流水号</td>
        <td>非常重要，客户端需要用到</td>
    </tr>
    <tr>
        <td>reqReserved</td>
        <td>我方服务器交易单号</td>
        <td>&nbsp;</td>
    </tr>
</table>
<p class="inforstyle">特别提示：客户端正式上线时，需要切换支付控件到生产环境，否则会报“无效订单”错误。</p>
<p class="inforstyle">reqReserved:</p>
<p class="inforstyle">格式形如：“2位业务类型前缀(大写字母)+14位时间戳+&quot;ID&quot;+client_id(非固定长度)”（举例：CZ20140917172349ID2）；</p>
<p>&nbsp;</p>
<p><br />
</p>
</div>

<?php require_once("../include/foot.inc.php");?>