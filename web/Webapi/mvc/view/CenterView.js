Ext.define('WebRoot.view.CenterView', {
	extend:'Ext.TabPanel',	
	alias:'widget.centerview',//定义别名方便在别处引用
	id:'centerview',//重要勿删
	region:'center',
	border:false,
	frame:true,		
	activeTab: 0,
	//initComponent函数不可或缺
	initComponent:function() {			      
		this.items=[			
			{			
				title:'系统首页',
				html:'<iframe  scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="100%" height="100%" src='+SYS_API_ROOT+"DebugInfor.php"+'></iframe>'							
			}			
		]		
		this.callParent();
	}
});