//定义本类主要是为了统一高级查询表单的样式
Ext.define('Ext.ux.view.WhbQueryForm', {
	extend:'Ext.form.Panel',
	alias:'widget.whbqueryform',
	border:false,
	frame:true,//可变换背景色.更加美观
	buttonAlign:'left',//底部button排列方式
	style:{
		border:"0px",//渲染状态下无边框
		padding:'5px'
	},	
	layout:{
		type:'table',
		columns:4
	},	
	//height:2*30//子类需要重新定义	
	defaultType:'textfield',
	fieldDefaults:{
        	labelAlign:'right',  
			emptyText:'请填写',
			width:250,//规定单个组件总宽度
			labelWidth:60//规定单个组件标签宽度	          	
    },
	//items:[{}],
	//通用底部按钮,buttons比dockedItems界面更友好
	buttons:[{
		id:'btnSearch',
		text:'查询',
		tooltip:'Search',
		//iconAlign:'right',//默认是Left	
		iconCls:'search'				
	},{
		text:'重置',	
		tooltip:'Reset',
		iconCls:'reset',		
		handler:function() {
			this.up('form').getForm().reset();		 
		}
	}],	
	initComponent:function() {		
		//为了兼容IE,重新调整高度
		//if(Ext.isIE)	this.height+=20;			
		this.callParent();//非常重要，不可删除	
	}
});