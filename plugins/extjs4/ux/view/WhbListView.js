Ext.define('Ext.ux.view.WhbListView',{
    extend:'Ext.grid.Panel',
    alias:'widget.whblistview',//control中会用到，否则EXT会报substr,J即未引用错误  
    autoHeight:true,
	border:true,
	columnLines:true,//显示列线条
	frame:false,	
	store:'TempStore',//如果不是TempStore,子模块需要单独定义
	//enableDragDrop:true,//列宽可拖动
	region:'center',	
	stripeRows:true,//斑马线风格	 
	//simpleSelect:true,//只能选择单行，默认此项
	multiSelect:true,//允许多选		
	selModel:{selType:'checkboxmodel'},  //开启复选框选择模式Ext.selection.CheckboxModel	
	viewConfig:{//非常重要，请勿删除
    	forceFit:true //自动适应每个列的宽度
 	},		
	columns:[
		//如果是单选模式需要特殊处理
		/*{
			header:'选择',
			width:40,
			dataIndex:'isOne',
			renderer:function(){
				return '<input type="radio" align="center" name="isOnename" id="isOneid"/>';
			}
		},*/
		{text:"系统主键",dataIndex:'id',hidden:true}
		////隐藏域字段_________begin
		//{dataIndex:'cityId',hidden:true},
		////隐藏域字段_________end
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
				Ext.getCmp("btnRemove").setDisabled(selections.length < 1);
			if(Ext.getCmp("btnDetail")!= null)
				Ext.getCmp("btnDetail").setDisabled(selections.length != 1);
			if(Ext.getCmp("btnExtend1")!= null)
				Ext.getCmp("btnExtend1").setDisabled(selections.length != 1); 
			if(Ext.getCmp("btnExtend2")!= null)
				Ext.getCmp("btnExtend2").setDisabled(selections.length != 1);
		}
		/*,scope:this*/	
	},
	dockedItems:[{
		xtype:'pagingtoolbar',
		dock:'bottom',		
		store:'TempStore',//如果不是TempStore,子模块需要单独定义
	    displayInfo:true,  	    
	    displayMsg:'显示第 {0} - {1} 条记录,共 {2} 条',
	    emptyMsg:"暂无任何记录"   
	}] 
});