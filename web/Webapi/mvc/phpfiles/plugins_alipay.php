<?php require_once("../include/head.inc.php");?>
<?php require_once(SYS_ROOT_PATH."include/language.inc.php");?>
<script>whbRemoveMask();</script>


<div class="contentDIV">
<p><img src="<?php echo SYS_EXTJS_URL?>images/apple2.gif" width="16" height="16" /> <span class="titlestyle">功能描述：获取支付宝交易签名串(内含我方交易单号)</span></p>
<p class="boldStyle">（一）服务接口请求地址：</p>
<table width="90%" border="1" class="dbTable">
  <tr class="td_header">
    <td width="15%">字段名称</td>
    <td width="85%">字段信息</td>
    </tr>
  <tr>
    <td>具体的地址</td>
    <td>[sys_plugins]OnlinePay/Alipay/alipaysign_get.php</td>
  </tr>
</table>
<p class="inforstyle">特别提示：[sys_plugins]定义请参考系统初始化接口说明。</p>
<p class="boldStyle">（二）POST参数列表：</p>
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
        <td width="597">固定传1</td>
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
<p class="boldStyle">（三）服务接口响应请求：</p>
<table width="90%" border="1" class="dbTable">
    <tr class="td_header">
        <td width="51%">响应结果</td>
        <td width="31%">备注</td>
    </tr>
    <tr>
        <td><p>{&quot;success&quot;:true,&quot;msg&quot;:&quot;操作成功&quot;,&quot;infor&quot;:[{&quot;alipaysign&quot;:参数串}]}</p></td>
        <td><p>详见底部特别说明</p></td>
    </tr>
    <?php require_once("../include/error.inc.php");?>
</table>
<p class="inforstyle">特别说明：</p>
<p>alipaysign形如：</p>
<p>&quot;partner=&quot;2088201611767607&quot;&amp;seller_id=&quot;2088201611767607&quot;&amp;out_trade_no=&quot;GM_201409122105133908_2&quot;&amp;subject=&quot;&quot;&amp;body=&quot;XX项目&quot;&amp;total_fee=&quot;0.01&quot;¬ify_url=&quot;http%3A%2F%2F192.168.2.19%2Fgroup1%2Fwhbthink%2F%2Fplugins%2FOnlinePay%2FAlipay%2Fnotify_url.php&quot;&amp;_input_charset=&quot;utf-8&quot;&amp;service=&quot;mobile.securitypay.pay&quot;&amp;payment_type=&quot;1&quot;&amp;sign_type=&quot;RSA&quot;&amp;sign=&quot;ir8gMpWYoz35zEIrfBNLbFFJNu7n3ZJV6FL643WXggkgpC8IvffuCeDyzRSrZaLfwfW%2B%2FLpbqfJmVz9z0I3gZBTYtRCoz9eJq59AsMRYrn7lTWVZAvUPodX2iPSeLAbfsSc0jmnZMzFKgwmGuHutAmRhWFJLab%2BAum%2FJafLgOiw%3D&quot;&quot;</p>
<p>  其中：<br />
  out_trade_no 参数为我方服务器端生成的交易单号（交易单号包括购买单号和充值单号)</p>
<p>格式形如：“2位业务类型前缀(大写字母)+14位时间戳+&quot;ID&quot;+client_id(非固定长度)”（举例：GM20140917172349ID2）；</p>
<p>&nbsp;</p>
</div>

<?php require_once("../include/foot.inc.php");?>