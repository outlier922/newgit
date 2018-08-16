<?php require_once("../include/head.inc.php");?>
<?php require_once(SYS_ROOT_PATH."include/language.inc.php");/* $apidoc = array(); */?>
<script>whbRemoveMask();</script>
<script src="../include/jquery.js"></script>

<div class="contentDIV">
	<p><img src="<?php echo SYS_EXTJS_URL?>images/apple2.gif" width="16" height="16" /> <span class="titlestyle">功能描述：<?php echo $apidoc["title"]?>接口</span></p>
	<p class="subtitlestyle">（一）服务接口请求地址：</p>
	<table width="90%" border="1" class="dbTable">
		<tr class="td_header">
			<td width="15%">字段名称</td>
			<td width="85%">字段信息</td>
		</tr>
		<tr>
			<td>请求的地址</td>
			<td>
				<?php
				$req_url="";
				if($method=="init")
				{
					$req_url=SYS_ROOT.'index.php/Webservice/Index/init';
					echo "[sys_root]index.php/webservice/index/init（不含版本号）";
				}
				else if($method=="webview")
				{
					echo "[sys_web_service]".$method."/parm/【下面所列的参数值】";
				}
				else if($method=="app_tui")
				{
				}
				else if($method=="alipaysign_get")
				{
					$req_url=SYS_PLUGINS.'OnlinePay/Alipay/alipaysign_get.php';
					echo "[sys_plugins]OnlinePay/Alipay/alipaysign_get.php";
				}
				else if($method=="unionpay_get")
				{
					$req_url=SYS_PLUGINS.'OnlinePay/Unionpay/unionpay_get.php';
					echo "[sys_plugins]OnlinePay/Unionpay/unionpay_get.php";
				}
				else if($method=="weixinpay_get")
				{
					$req_url=SYS_PLUGINS.'OnlinePay/Weixinpay/weixinpay_get.php';
					echo "[sys_plugins]OnlinePay/Weixinpay/weixinpay_get.php";
				}
				else
				{
					$req_url=SYS_ROOT.'index.php/Webservice/'.str_replace('.', '', _GET('v')).'/'.$method;
					echo "[sys_web_service]".$method;
				}
				?>
			</td>
		</tr>
	</table>
	<p class="subtitlestyle">（二）POST参数列表：必填字段</p>
	<table width="90%" border="1" class="dbTable">
		<tr class="td_header">
			<td width="23%">参数名称</td>
			<td width="25%">参数说明</td>
			<td width="52%">备注</td>
		</tr>
		<!-- 具体api -->
		<?php ?>
		<?php if(isset($apidoc) && !empty($apidoc)){?>

		<?php if(isset($apidoc['req_params']) && count($apidoc["req_params"])>0 ) {?>

		<?php foreach($apidoc["req_params"] as $req_key =>$req_param){?><!-- foreach开始 -->

		<?php if(count($req_param) == 1){?>
			<td colspan="3"><p class="inforstyle"><?php echo $req_param['title'];?></p></td><!-- 描述 -->
			<?php continue; }?>
		<!-- 样式 -->
		<tr class=" <?php echo empty($req_param['newflag']) ? ' ' : 'newflag ' ;?>
        <?php echo empty($req_param['deleteflag']) ? ' ' : 'deleteflag ' ;?>

   ">
			<td><?php echo $req_key;?></td>
			<td><?php echo $req_param['title'];?></td>
			<td  class='text-left'>
				<?php echo str_replace('$', ' <br /> ', $req_param['memo']) ;?>
			</td>
		</tr>

		<?php }?><!-- foreach end-->
		<?php }?><!-- req_params end-->
	</table>

	<!-- 新加一个对post参数的说明 -->
	<?php if(isset($apidoc['req_desc']) && !empty($apidoc['req_desc'])){ ?>
		<p class='inforstyle'>
			<?php echo $apidoc['req_desc'];?>
		</p>
	<?php }?>

	<!-- 直接放上模拟测试方法 -->
	<?php if(isset($apidoc) && !empty($apidoc)){?>
		<div  style="width:380px;float:left">
			<form method="post" enctype="multipart/form-data" target="_blank"
				  action="
			<?php
				  if($method == 'alipaysign_get'){echo SYS_PLUGINS.'OnlinePay/Alipay/alipaysign_get.php';}
				  else if($method == 'unionpay_get'){echo SYS_PLUGINS.'OnlinePay/Unionpay/unionpay_get.php';}
				  else if($method == 'init'){echo SYS_ROOT.'index.php/Webservice/Index/init';}
				  else{echo SYS_ROOT.'index.php/Webservice/'.str_replace('.', '', _GET('v')).'/'.$method;}
				  ?>
		"
			>
				<fieldset>
					<legend><span style='color:red'>模拟测试</span></legend>

					<ul>
						<?php
						if(isset($apidoc['req_params']) && count($apidoc["req_params"])>0 ) {?>
						<?php
						foreach($apidoc["req_params"] as $req_key =>$req_param){?><!-- foreach开始 -->
						<?php if(count($req_param) == 1){continue; }?>
						<li style="display: none;">
						 <div style="width:100px;float:left;">
			                               加密参数
			             </div>
			              <div style="width:200px;float:left;">
			              	<input   name="agent_from" type = "text" value="2"/>
			             
			                    <input   name="sign" type = "text" value="<?php echo md5(DATAKEY."|".sys_get_time()."|".$method)?>"/>
			     		 </div>
			     		<div style="clear: both;" ></div>
						</li>
						<li>
							<div style="width:100px;float:left;">
								<?php echo $req_param['title']?>：
							</div>
							<div style="width:200px;float:left;">
								<input
									name="<?php echo $req_key;?>"
									type = "<?php echo ($req_param['default']=='file' ? 'file' : 'text');?>"
									value="<?php
									if($req_key == 'token')
									{
										echo _SESSION('token') ? _SESSION('token') : '';
									}
									else{ echo $req_param['default'];}
									?>"
								>
							</div>
							<div style="clear: both;" ></div>
						</li>

						<?php }?><!-- foreach end-->
						<?php }?><!-- req_params end-->
					</ul>
					<input type="hidden" name='datetime' value='<?php echo sys_get_time();?>'/>
					<input type="hidden" name='sign' value='<?php echo md5(DATAKEY."|". sys_get_time()."|".$method);?>'/>
					<div style="margin-left: 250px;width:50px;height:20px;">
						<?php if($method == 'file_upload'){ ?>
						<input  type="submit" value="提交" name="submit">
						<?php }else{ ?>
						<input  type="button" value="提交" name="test" onclick="testOnClick()">
						<?php } ?>						
					</div>
				</fieldset>
			</form>
		</div>
	<?php }?>
	<div  style="width:380px;">
		json返回数据：
		<div style="width:100%" id="ret_div">

		</div>
	</div>

	<p class="subtitlestyle">（三）服务接口响应请求：</p>
	<table width="90%" border="1" class="dbTable">
		<tr class="td_header">
			<td width="51%">响应结果</td>
			<td width="31%">备注</td>
		</tr>

		<tr>
			<td><p>
					{"success":true,"msg":"操作成功"
					<?php echo (empty($apidoc['ret']) ? ' ': ',"infor":json信息串');?>
					}
				</p></td>

			<td><p><?php echo (empty($apidoc['ret']) ? ' ': '详见（四）特别备注');?></p></td>
		</tr>

		<?php if($apidoc["ret"]==1){//totalCount方式?>
			<tr class="inforstyle">
				<td colspan="2">infor形为：{[{item1}，{item2}]}</td>
			</tr>
		<?php }else if($apidoc["ret"]==2){?>
			<tr class="inforstyle">
				<td colspan="2">特别提示：任何一个取名"xxxx_list"形式的接口（无需分页的除外）,infor形为：{"totalCount":0,"listItems":[{item1}，{item2}]}<br>
					其中：totalCount 表示所有符合查询条件的总记录的个数（totalCount=0 表示暂无数据），listItems是分页时，每页的记录详情条目。</td>
			</tr>
		<?php }?>

		<?php require_once("../include/error.inc.php");?>
	</table>

	<?php if(isset($apidoc['code']) && count($apidoc['code']) > 0){?>
		<p><span class="subtitlestyle">（三）返回错误码，客户端一定要处理（除“参数不能为空（100）”、“登录令牌失效（200）”、“服务器错误（500）”的之外）</span></p>
		<table width="90%" border="1" class="dbTable">
			<tr class="td_header">
				<td width="16%">错误码</td>
				<td width="27%">错误码说明</td>
				<td width="57%">备注</td>
			</tr>
			<?php foreach ($apidoc['code'] as $code_key=>$code_value){?>
				<tr>
					<td><?php echo $code_key;?></td>
					<td><?php echo sys_get_msg($code_key);?></td>
					<td><?php echo $code_value;?></td>
				</tr>
			<?php }?>
		</table>
	<?php }?>

	<?php if(isset($apidoc["ret_infor"])){ ?>
		<!-- 返回信息 -->
		<p><span class="subtitlestyle">（四）特别备注</span>（infor字段说明，仅列出部分关键字段）</p>
		<table width="90%" border="1" class="dbTable">
			<tr class="td_header">
				<td width="16%">参数名称</td>
				<td width="27%">参数说明</td>
				<td width="57%">备注</td>
			</tr>

			<?php foreach($apidoc["ret_infor"] as $ret_key => $ret_param){?><!-- foreach start -->
			<?php if(count($ret_param) == 1){?>
				<td colspan="3"><p class="inforstyle"><?php echo $ret_param[0];?></p></td><!-- 描述 -->
				<?php continue; }?>
			<!-- 样式 -->
			<tr class=" <?php echo empty($ret_param['newflag']) ? ' ' : 'newflag ' ;?>
        <?php echo empty($ret_param['deleteflag']) ? ' ' : 'deleteflag ' ;?>

   ">
				<td><?php echo $ret_key;?></td>
				<td><?php echo $ret_param['title'];?></td>
				<td  class='text-left'>
					<?php echo str_replace('$', ' <br /> ', $ret_param['memo']) ;?>
				</td>
			</tr>

			<?php }?><!-- foreach end -->
		</table>
	<?php }?>

	<!-- 2层级字段 -->
	<?php if(isset($apidoc["ret_level"]) && $apidoc["ret_level"]==2){?>
		<?php foreach($apidoc["ret_infor"] as $ret_key => $ret_param){?><!-- foreach start -->

			<?php if(isset($ret_param['ret_infor']) && count($ret_param['ret_infor']) > 0){?>


				<p><span class="subtitlestyle"><?php echo $ret_key;?>字段说明</p>
				<table width="90%" border="1" class="dbTable">
					<tr class="td_header">
						<td width="16%">参数名称</td>
						<td width="27%">参数说明</td>
						<td width="57%">备注</td>
					</tr>
					<?php foreach ($ret_param['ret_infor'] as $ret_l2_key=>$ret_l2_param) {  ?>
						<?php if(count($ret_l2_param) == 1){?>
							<td colspan="3"><p class="inforstyle"><?php echo $ret_l2_param['title'];?></p></td><!-- 描述 -->
							<?php continue; }?>

						<!-- 样式 -->
						<tr class=" <?php echo empty($ret_l2_param['newflag']) ? ' ' : 'newflag ' ;?>
            <?php echo empty($ret_l2_param['deleteflag']) ? ' ' : 'deleteflag ' ;?>

       ">
							<td><?php echo $ret_l2_key;?></td>
							<td><?php echo $ret_l2_param['title'];?></td>
							<td class='text-left'>
								<?php echo str_replace('$', ' <br /> ', $ret_l2_param['memo']) ;?>
							</td>
						</tr>
					<?php }?>
				</table>
			<?php }?>

		<?php }?><!-- foreach end -->
	<?php }?>

	<?php if(count($apidoc["special"])>0){?>
		<p><span class="subtitlestyle">（五）其他相关说明</span></p>
		<?php foreach($apidoc["special"] as $special){?>
			<p><b><?php echo $special["title"];?></b></p>
			<?php if(is_array($special["memo"]) && count($special["memo"]) > 0){foreach($special["memo"] as $desc){?>
				<p style='font-size: 13px; color:blue'><?php echo $desc;?></p>
			<?php }}else{?>
				<p style='font-size: 13px; color:blue'><?php echo $special["memo"];?></p>
			<?php }?>
		<?php }?>
	<?php }?>


</div>

<?php }?>
<script>
	function indent (json) {

		result = '';
		pos = 0;
		strLen = json.length;
		indentStr = '\t';
		newLine = "\n";
		prevChar = '';
		outOfQuotes = true;

		for (i=0; i<=strLen; i++) {

			// Grab the next character in the string.
			//char = substr($json, $i, 1);
			char = json.substr(i,1);
			// Are we inside a quoted string?
			if (char == '"' && prevChar != '\\') {
				outOfQuotes = !outOfQuotes;
				// If this character is the end of an element,
				// output a new line and indent the next line.
			} else if((char == '}' || char == ']')  && outOfQuotes) {
				result += newLine;
				pos --;
				for (j=0; j<pos; j++) {
					result += indentStr;
				}
			}
			// Add the character to the result string.
			result += char;
			// If the last character was the beginning of an element,
			// output a new line and indent the next line.
			if ((char == ',' || char == '{' || char == '[') && outOfQuotes) {
				result += newLine;
				if (char == '{' || char == '[') {
					pos ++;
				}
				for (j = 0; j < pos; j++) {
					result += indentStr;
				}
			}
			prevChar = char;
		}

		return result;

	}
	function testOnClick()
	{
		var req_url="<?php echo $req_url?>";
		console.log(req_url);
		var d=$("form").serialize();
		$.post(req_url, d, function (result) {
			//alert(result);
			$("#ret_div").empty();
			var ret = indent(result);
			//var ret=eval("("+result+")");
			//$("#ret_div").html(ret);
			//result=JSON.stringify(result, null, '\t');
			$("#ret_div").html(ret);
		}, "text");

	}

	$(document).ready(function(){
		//alert("Hello again");

	});
</script>


<?php require_once("../include/foot.inc.php");?>
