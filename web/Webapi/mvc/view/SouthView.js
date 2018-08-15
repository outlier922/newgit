//whbmemo：定义系统弹窗形式基类
//子类继承时需要重写items和dockedItems
Ext.define('WebRoot.view.SouthView', {		
	extend:'Ext.panel.Panel',	
	alias:'widget.southview',//定义别名方便在别处引用	
	border:false,	
	collapsible:true,//可折叠   
	split:false,//可分割		 
    collapsed:true,//默认为关闭状态
	frame:true,
	html:'<br>&nbsp;&nbsp;'+SYS_HELPER,		
	height:80,	
	region:'south', 		
	title:'<div align="right">&copy;山东河马信息技术有限公司&nbsp;&nbsp技术开发部</div>'	
});