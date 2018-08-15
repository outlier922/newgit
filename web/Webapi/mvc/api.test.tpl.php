<?php error_reporting(E_ALL & ~E_NOTICE);?>
<HTML>
<HEAD>
<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
<style>
	.container{
		margin-left:60px;
		margin-top:15px;
	}
	.wrap{
		min-height:200px;
	}
	.wrap li{
		list-style-type: none;
	}
	
	.fieldlabel{
		float:left;
		width:80px;
		height:25px;
		overflow:hidden;
		text-overflow:ellipsis;
		white-space:nowrap;
	}
	.field{
		float:left;
		width:170px;
		height:25px;
	}
	.bitian{
		height:25px;
		float:left;
	}
	
	/* 样式 */
.deleteflag{text-decoration:line-through;}
.newflag{color:red;}
</style>
<TITLE>接口测试</TITLE>
</HEAD>
<BODY>
	<div class="container">
	<!-- 一般通用 -->
	<div class = "wrap" style="width:380px;float:left;" >
	   <form method="post" enctype="multipart/form-data" action="<?php echo SYS_ROOT;?>index.php/Webservice/V100/test" >
	       <fieldset><legend>测试函数[test]</legend>
	       <ul>
    	       <li>
    	           <div class="fieldlabel">参数1：</div>
    	           <div class="field">
    	               <input name='key1' type='text' value='' />
    	           </div>
    	       </li>
    	       <li>
    	           <div class="fieldlabel">参数2：</div>
    	           <div class="field">
    	               <input name='key2' type='text' value='' />
    	           </div>
    	       </li>
    	       <li>
    	           <div class="fieldlabel">参数3：</div>
    	           <div class="field">
    	               <input name='key3' type='text' value='' />
    	           </div>
    	       </li>
    	       <li>
    	           <div class="fieldlabel">文件1：</div>
    	           <div class="field">
    	               <input name='file1' type='file' value='' />
    	           </div>
    	       </li>
	       </ul>
	       <div style="clear:both;"></div>
						<div style="float:right;margin-right:40px"><input  type="submit" value="提交" name="submit"></div>
	       </fieldset>
	   </form>
	</div>
	
	<div class = "wrap" style="width:380px;float:left;" >
	   <form method="post" enctype="multipart/form-data" action="<?php echo SYS_ROOT;?>'index.php/Webservice/V100/test_baidu_push" >
	       <fieldset><legend>测试百度云[test_baidu_push]</legend>
	       <ul>
    	       <li>
    	           <div class="fieldlabel">username：</div>
    	           <div class="field">
    	               <input name='username' type='text' value='18765878052' />
    	           </div>
    	       </li>
    	       <li>
    	           <div class="fieldlabel">content：</div>
    	           <div class="field">
    	               <input name='content' type='text' value='推送内容' />
    	           </div>
    	       </li>
    	       <li>
    	           <div class="fieldlabel">keytype：</div>
    	           <div class="field">
    	               <input name='keytype' type='text' value='1' />
    	           </div>
    	       </li>
    	       <li>
    	           <div class="fieldlabel">keyid：</div>
    	           <div class="field">
    	               <input name='keyid' type='text' value='0' />
    	           </div>
    	       </li>
	       </ul>
	       <div style="clear:both;"></div>
						<div style="float:right;margin-right:40px"><input  type="submit" value="提交" name="submit"></div>
	       </fieldset>
	   </form>
	</div>
	
	<?php $api_data = $api_phpfiles;?>
		<?php foreach ($api_data as $key => $apis){?>
			<div class = "wrap" style="width:380px;float:left;" >
				<form method="post" enctype="multipart/form-data"  
						action="
						<?php 
						  if($key == 'alipaysign_get'){echo SYS_PLUGINS.'OnlinePay/Alipay/alipaysign_get.php';}
						  else if($key == 'unionpay_get'){echo SYS_PLUGINS.'OnlinePay/Unionpay/unionpay_get.php';}
						  else if($key == 'init'){echo SYS_ROOT.'index.php/Webservice/Index/init';}
						  else{echo SYS_ROOT.'index.php/Webservice/'.$ser_version.'/'.$key;}
						?>
						">
					<fieldset>
						<legend class = "<?php if(isset($apis['deleteflag'])){?> deleteflag <?php }?> ">
							<?php echo $apis["title"].'['.$key.']';?>
						</legend>
						<p><?php echo $apis["desc"];?></p>
						
						<ul>
							<?php if(!empty($apis["req_params"])){?>
								<?php foreach($apis["req_params"] as $fieldName=>$api){?>
									<?php if(!empty($api)){?>
										<li class="
										<?php if(!empty($api["deleteflag"])){?> deleteflag <?php }?>
										<?php if(!empty($api["newflag"])){?> newflag <?php }?>
										">
											<div class="fieldlabel"><?php echo $api['title']?>：</div>
											<div class="field">
												<input 
													name="<?php echo $fieldName;?>" 
													type = "<?php echo ($api['default']=='file' ? 'file' : 'text');?>"
													
													value="<?php echo $api['default'];?>"
												>
											</div>
										</li>
									<?php }?>
								<?php }?>
							<?php }?>
						</ul>
						<div style="clear:both;"></div>
						<div style="float:right;margin-right:40px"><input  type="submit" value="提交" name="submit"></div>
					</fieldset>
				
				</form>
			</div>
		<?php }?>
	</div>
</BODY>
</HTML>