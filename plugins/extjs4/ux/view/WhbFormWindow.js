//子类继承时需要重写items和dockedItems
Ext.define('Ext.ux.view.WhbFormWindow', {		
	extend: 'Ext.window.Window',
	alias:'widget.whbformwindow',//定义别名方便在别处引用		
	autoScroll:true,//自动加滚动条	
	autoShow:true,//自动显示，子类就不用调用show方法了
	animCollapse:false,//是否启用折叠动画效果,默认true
	//设置窗体背景色及边距,行距等内容(transparent,#fff)
	bodyStyle:"background:#fff;padding:10px;line-height:18px;",	
	border:true,//比较美观	
	closable:true,//带关闭按钮
	constrain:true,//约束窗口只能在容器内移动	
	collapsible:true, 
	frame:false,//设置成false更加美观   
	headerPosition: 'top',//系统弹窗标题头的位置，top,bottom2个选项
	//html: '<div class=hrDIV></div><font color=red>提示：必输项不能为空，请认真填写。</font>',	
	//注意panel与window的不同panel中用item{baseCls:"x-plain"} 	
	//plain:false,//主体背景是否透明，默认为false,	
	layout: 'anchor',//用相对宽度方式布局	 	
	maximizable:true,//是否带最大化按钮。
	modal:true,//是否显示为模式窗口，默认为false。	 
	//minimizable:true,//需要自定义最小化行为,不太常用。
	//height: 300,//高度自适应
	resizable:false,//可改变窗口大小，默认为true
	title: '系统表单',	//居右方式：title:'<div align="right">系统表单</div>'		
	width:520,//注意：调整此宽度，需要同时调整fieldDefaults中的width。
	//height:300,//如果不定义此属性，则系统默认自适应	  	
	
	//子类需重写item
	//items:whbform,//items:[{}],	
	//通用底部按钮，可重写
	dockedItems:[{
		xtype:'toolbar',
		dock:'bottom',
		items:[ '->',//右对齐
		{
			xtype:'button',
			text:'保存',
			id:'btnSave',
			iconCls:'save',
			action: 'save',
			scale:'small'
		},{
			xtype:'button',
			text:'关闭',
			iconCls:'close',
			handler:function() {
				this.ownerCt.ownerCt.close();				
			}
		}
		/*,{
			xtype:'button',
			text:'重置',
			iconCls:'cancel',
			handler:function() {
				if(Ext.getCmp('whbsubform')!=null)
				Ext.getCmp('whbsubform').getForm().reset();					
			}
		}*/
		]
	}]//dockedItems结束
});

/******以下注释为子类引用示例代码，非常重要，请勿删除
Ext.define('Role.view.WinFormView', {
	extend:'Ext.ux.view.WhbFormWindow',
	alias:'widget.winformview',	
	width:312,
	initComponent:function() {		
		var whbform=Ext.widget("whbformpanel", {
			id:'whbsubform',				
			items: [//item_form_begin		
				//此处堆放宽列
				{			
					anchor:'100%',//表格宽度用百分比表示									
					fieldLabel: '角色名称', 							           	
					name:'name'						
				},
				{
					anchor:'100%',
					xtype: 'textarea',											
					fieldLabel: '备注说明', 					           	
					name: 'memo',
					allowBlank:true	,//如果是选填项，必须重写
					emptyText:'暂无信息'				
				},
				{
					xtype:'hidden',
					name:'id',//用于编辑操作时隐藏传参，非常重要
					allowBlank:true,//必须重写,以处理新增情况
					emptyText:''	//必须重写,以处理新增情况		
				}	
			]//item_form_end        
    	});			
		this.items=whbform; 
		this.callParent(arguments);//继承时必须和initComponent配套使用，非常重要   
	}	
});
*****/
