//whbmemo：定义系统弹窗形式基类
//子类继承时需要重写items和dockedItems
Ext.define('WebRoot.view.NorthView', {		
	extend:'Ext.panel.Panel',	
	alias:'widget.northview',//定义别名方便在别处引用	
	border:false,	
	id:'northview',	
	//collapsible:true,//可折叠    
    //collapsed:true,//默认为关闭状态
	//html:'<br>&nbsp;北京中佳银讯科技有限公司   &copy All rights reserved(2006-2012). &nbsp;&nbsp;联系电话：(010)-68012088; 联系人：XXX'	,		
	//height:28,	
	region:'north', 
	//split:false,//可分割		
	title:SYS_API_TITLE,		
	iconCls:'api',
	frame:false,
	height:51,//总窗体加上工具条的高度
	//html:'<div id="welcomeDIV">'+SYS_HELPER+'</div>',	
	bbar:[			
		{
			xtype:'displayfield',
			width:500,
			readOnly:true,
			value: '<span style="color:red;margin-left:2px">[系统提示：左侧菜单树中被"标注红色"的接口，表示与上一版本比较，已经变更]</span><br>' 
		},				              
	    '->',//->符号表示剩余按钮右对齐
		{
			xtype:"combo",	
			iconCls:'help',										
			editable:false,//不可编辑
			//labelStyle: 'cursor:move;',
			width:180,
			labelAlign:'right',
			fieldLabel:'主题换肤',       							 				      							 				
			displayField:'name',
			valueField:'value',  
			value:getCookie("SYS_SKIN"),  	  							
			store:whbSkinStore,
			listeners: {
				select: function(combo) {
					var theme = combo.getValue();
					setCookie("SYS_SKIN",theme,"h8");
					location.href='index.php?theme='+theme;
				}
			}
		},'-',		
		{  			
			id:'btnHelp',
            text:'系统说明',
            tooltip:'HelpInfor',
			iconCls:'help'
        }, '-',
		{
			id:'btnExport',
			text:'导出PDF文档',
			tooltip:'ExportPdf',
			iconCls:'pdf',
			handler:function(){
				var $_GET = (function(){
					var url = window.document.location.href.toString();
					var u = url.split("?");
					if(typeof(u[1]) == "string"){
						u = u[1].split("&");
						var get = {};
						for(var i in u){
							var j = u[i].split("=");
							get[j[0]] = j[1];
						}
						return get;
					} else {
						return {};
					}
				})();
				var version = $_GET['v'] ? $_GET['v'] : '1.0.0' ;
				version = version.replace(/[\.\/]/g,'');
				location.href=EXPORT_SERVICE_URL+'export_pdf&version='+version;
			}
		}, '-',
		{  			
			id:'btnDevelop',
            text:'开发计划书下载',
            tooltip:'DevelopPlan',
			iconCls:'word',
			handler:function(){
				location.href="../../../document/项目开发计划书.doc";
			}
        }, '-',		
		{      	
			id:'btnExit',
            text:'查阅历史版本',
            tooltip:'Other Version',
			iconCls:'detail'			
        }
	]
});

