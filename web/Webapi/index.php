<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
//加载系统顶层核心配置文件
require_once "../../include/config.inc.php"; 
?>
<title><?php echo SYS_ZH_NAME?>__API在线手册</title>
<!--引入自定义CSS，注意加载顺序和路径写法-->
<link rel="stylesheet" type="text/css" href="<?php echo SYS_EXTJS_URL?>whbstyle.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SYS_EXTJS_URL?>resources/css/ext-all-default.css" />
<script src="<?php echo SYS_EXTJS_URL?>bootstrap.js" ></script>
</head>
<body>
<script type="text/javascript">
Ext.onReady(function() {
	Ext.create("Ext.window.Window", {
		autoScroll:true,//自动加滚动条	
		autoShow:true,//自动显示，子类就不用调用show方法了
		iconCls:'api',
		//设置窗体背景色及边距,行距等内容
		bodyStyle:"padding:5px;line-height:18px;",	
		border:true,//比较美观	
		closable:false,//带关闭按钮
		constrain:true,//约束窗口只能在容器内移动	
		collapsible:true, 
		//maxHeight:500,
		frame:false,//设置成false更加美观   
		headerPosition: 'top',//系统弹窗标题头的位置，top,bottom2个选项
		html: '<div class="dbTable">'	
		+'<br><br><a href="mvc/?v=1.0.0">1.0.0 版</a>'		
		+'<br><br><a href="mvc/?v=1.0.1">1.0.1 版</a>'  
		//+'<br><br><a href="1.0.2/">1.0.2 版</a>'  	
		+'<br><br><hr>如果有后续更新版本，会在此处依次罗列；'
		+'<br>相邻版本的接口变更，菜单中将加红显示。<br></div>',	
		//html: '<div class="dbTable">	<br><br><a href="1.0.0/">1.0.0 版</a><br><br><hr>如果有后续更新版本，会在此处依次罗列；<br>相邻版本的接口变更，菜单中将加红显示。<br></div>',	
		//注意panel与window的不同panel中用item{baseCls:"x-plain"} 	
		plain:false,//主体背景是否透明，默认为false,	
		layout: 'anchor',//用相对宽度方式布局	 	
		maximizable:true,//是否带最大化按钮。
		modal:true,//是否显示为模式窗口，默认为false。	 
		resizable:false,//可改变窗口大小，默认为true
		title: '<?php echo SYS_ZH_NAME?>__API版本选择入口',	//居右方式：title:'<div align="right">系统表单</div>'		
		width:280//注意：调整此宽度，需要同时调整fieldDefaults中的width。	 
		
	});//whbwin_end

});
</script>
</body>
</html>
