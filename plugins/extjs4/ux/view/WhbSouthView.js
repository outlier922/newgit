//whbmemo：定义系统弹窗形式基类
//子类继承时需要重写items和dockedItems
Ext.define('Ext.ux.view.WhbSouthView', {		
	extend:'Ext.panel.Panel',	
	alias:'widget.whbsouthview',//定义别名方便在别处引用	
	border:false,	
	collapsible:true,//可折叠  
	collapsed:true,//默认为关闭状态 
	titleCollapse:true,//点击标题不再进行折叠
	split:false,//可分割	  
	frame:true,
	html:'<br>【技术支持】&copy山东河马信息技术有限公司  All rights reserved.&nbsp;&nbsp;&nbsp;&nbsp;【官方网站】<a href="http://www.hemaapp.com">http://www.hemaapp.com</a>'	,		
	height:80,	
	region:'south', 		
	title:'<div align="right">&copy;'+SYS_COMPANY+'&nbsp;&nbsp;版权所有</div>'	
});