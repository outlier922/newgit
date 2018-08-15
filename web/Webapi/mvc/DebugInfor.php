<?php require_once("include/head.inc.php");?>
<script>whbRemoveMask();</script>
<form id="form1">
    <div style="font-size:12px;margin-top:1%;margin-left:5%;line-height:2;">
      <p>
          <?php	
	if (!empty($_REQUEST['infor']))
    {
        echo $_REQUEST['infor'];
		die();
    }
    ?>
        <span class="titlestyle">  【接口说明】</span></p>
      <span class="subtitlestyle">1、项目根地址：</span><span class="inforstyle">[sys_root] =<?php echo SYS_ROOT?> </span>(该常量请务必配置在客户端，作为其他服务接口的前缀)
      <p class="inforstyle">特别注意：正式测试时需要换成客户所提供服务器的公网IP地址或域名（我方正式服务器的IP地址为：124.128.23.72）</p>
      <p><span class="subtitlestyle">2、项目 logo 图片固定访问地址为：</span><span class="inforstyle">[sys_root]images/logo.png </span>(帖子分享等模块会使用，尺寸 120*120)</p>
      <p><span class="subtitlestyle">2.1、分享软件的下载地址为：</span><span class="inforstyle">[sys_root]download/mobile/index.php </span></p>
      
      <p><span class="subtitlestyle">3、项目 init 初始化固定访问地址为：</span><span class="inforstyle">[sys_root]index.php/webservice/index/init</span>（不含版本号）</p>
      <p><span class="subtitlestyle"> 4、除init和第三方插件外，其他接口地址统一为：</span><span class="inforstyle">[sys_web_service]前缀+业务方法名</span> (含版本号,[sys_web_service]在init接口中返回)</p>
      <p class=inforstyle>众所周知，如果遵循统一标准，能够减少维护成本，故对于“业务方法名”，强制如下命名规则（有特殊情况的除外）：</p>
<p>（1）增加记录：方法名必须命名为：模块名_add，例如:client_add 表示增加一个用户;</p>
<p>（2）修改记录：方法名必须命名为：模块名_save，例如:client_save 表示修改用户资料;</p>
<p>（3）删除记录：方法名必须命名为：模块名_remove，例如:client_remove 表示删除一个用户;</p>
<p>（4）验证记录：方法名必须命名为：模块名_verify，例如:client_verify 表示验证用户合法;</p>
<p>（5）查询单条：方法名必须命名为：模块名_get，例如:client_get 表示查询单用户详情;</p>
<p>（6）查询多条：方法名必须命名为：模块名_list，例如:client_list 表示查询用户列表;</p>
<p class="subtitlestyle">5、所有服务接口，返回数据采用JSON格式，其中都必包含2个关键字段：<span class="inforstyle">success 和 msg</span></p>
<p>其中：<span class="inforstyle">success</span>取值为true或false，代表服务处理成功或失败，msg为服务器处理结果描述信息。</p>
<p>例如：用户登录服务，如果登录成功，服务器返回的数据为：<span class="inforstyle">{&quot;success&quot;:true,&quot;msg&quot;:&quot;登录成功&quot;}</span></p>
<p>(1)msg仅仅是为了方便您调试而提供的一般性描述性信息，可能会随着项目的进行而有所变化，故此字段不能成为您程序的判断依赖标准。</p>
<p>(2)如果<span class="inforstyle">success为false</span>，而失败情况又分不同情形时，服务器会提供一个error_code字段标识来加以区分，具体含义请查询 <a href="#" onclick="javascript:sysOpenTab('menu_errorcode','错误编码表','phpfiles/error_code.php')">错误编码表</a> 。</p>
<p class="subtitlestyle">6、为确保安全，所有声明的变量，不接受任何 GET 形式传递，请以 <span class="inforstyle">POST方式提交</span>。</p>
    </div>
    </form>
    
<?php require_once("include/foot.inc.php");?>