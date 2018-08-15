Ext.define('Ext.ux.view.WhbQueryBar', {
	extend:'Ext.toolbar.Toolbar',
	alias:'widget.whbquerybar',
	region:'north',
	border:false,	
	frame:false,	
	//height:30,
	items:[//子类需要重写
		{
			text:'刷新',
			tooltip:'Refresh',
			id:'btnRefresh',
			iconCls:'refresh',
			handler:function(m, e) {
				Ext.getCmp('listview').getStore().load();					
			}
		},'-', 
		{
			text:'新增',
			tooltip:'Add',
			id:'btnAdd',
			iconCls:'add'
		}, '-', {
			text:'修改',
			tooltip:'Edit',			
			id:'btnEdit',
			iconCls:'edit',
			disabled:true
		}, '-', {			
			text:'删除',
			tooltip:'Remove',
			id:'btnRemove',			
			iconCls:'remove',
			disabled:true
		},		
		'->',//右对齐
		{			
			emptyText:'名称/内容',				
			xtype:'searchfield',		
			id:'searchKey',	//必须定义，非常重要
			paramName:'searchKey',		
			listeners:{
				beforerender:function(me, record, index) {					
					me.store=Ext.getCmp('listview').getStore();	
				}
			}	
			////如果需要传递其余额外参数，可重写onTrigger2Click事件。				
			/*onTrigger2Click:function() {				
				var me = this,store = me.store,proxy = store.getProxy(),value = me.getValue();				
				proxy.extraParams[me.paramName] = value;
				//其他参数在此罗列
				//proxy.extraParams['secondparm'] = '';
				proxy.extraParams.start = 0;  
				store.loadPage(1);  				
				me.hasSearch = true;
				me.triggerEl.item(0).setDisplayed('block');
				me.doComponentLayout();
			}*/			
		}		
	]	
});