//不知道为何在head.asp里一直配置不上，待调试
Ext.define('Ext.ux.view.WhbTreePanel',{  
	extend:'Ext.tree.Panel', 
	alias:'widget.whbtreepanel',//control中会用到，否则EXT会报substring即未引用错误
	autoHeight:true, //自适应行高
	border:false,		
	rootVisible:true,//是否显示根目录     
	region:'center',  
	multiSelect:false,
	store:'TempStore',//如果不是TempStore,子模块需要单独定义
	singleExpand:true,//某个时刻只有1个父类展开	  
	useArrows:false,//是否启用箭头形式，默认是加,减号				
	columns:[{
		text:'id',
		dataIndex:'id',
		hidden:true
	},{
		text:'parentid',	  
		dataIndex:'parentid',
		hidden:true
	},{
		text:'nodepath',		
		dataIndex:'nodepath',
		hidden:true
	},{
		xtype:'treecolumn',
		text:'名称',	
		width:200,			
		sortable:true,
		dataIndex:'name'
	},{
		text:'排序优先级',	
		flex:2,	  
		dataIndex:'orderby',
		sortable:true
	}
	],
	//根据用户的选择情况，智能分析当前工具栏按钮是否可用
	listeners:{
		selectionchange:function(sm, selections){		
		    //WHBMEMO:选择1个或多个均可用备忘
		    //if(Ext.getCmp("btnRemove")!= null)			  
				//Ext.getCmp("btnRemove").setDisabled(selections.length === 0);				
			//WHBMEMO:只有选择1个才可用
			if(Ext.getCmp("btnEdit")!= null)		
				Ext.getCmp("btnEdit").setDisabled(selections.length != 1);		
			if(Ext.getCmp("btnRemove")!= null)		
				Ext.getCmp("btnRemove").setDisabled(selections.length != 1);			
			if(Ext.getCmp("btnExtend1")!= null)		
				Ext.getCmp("btnExtend1").setDisabled(selections.length != 1); 	   				
		}
		/*,scope:this*/	
	},
	initComponent:function() {	
		//console.log(this);	
		this.callParent(arguments);//继承时必须和initComponent配套使用，非常重要   
	}	
});