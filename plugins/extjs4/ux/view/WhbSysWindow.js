//whbmemo：定义系统弹窗形式基类
//系统function.js中whbOpenHelper函数使用，请勿删除
//子类继承时需要重写items和dockedItems
Ext.define('Ext.ux.view.WhbSysWindow', {		
	extend:'Ext.ux.view.WhbFormWindow',	
	alias:'widget.whbsyswindow',//定义别名方便在别处引用
	//modal:false,//重写	
	title:'系统提示',	
	iconCls:'infor',
	dockedItems:[{
		xtype:'toolbar',
		dock:'bottom',
		items:[ '->',//右对齐
		{
			xtype:'button',
			text:'关闭',
			iconCls:'close',
			handler:function() {
				this.ownerCt.ownerCt.close();				
			}
		}
		]
	}]//dockedItems结束
	/*,initComponent:function() {			
		this.callParent();//继承时必须和initComponent配套使用，非常重要   
	}*/	
})