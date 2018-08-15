//whb:定义系统通用下拉列表框（id,name）
Ext.define("Ext.ux.form.WhbComboBind", {
	extend: 'Ext.form.field.ComboBox',
	alias: 'widget.whbcombobind',
	fieldLabel: '字段名称',
	editable: false,
	emptyText:'请选择', //提示信息 
	displayField: 'name',
	valueField: 'id',
	queryMode:'local',//非常重要	
	typeAhead:true,//设置在输入过程中是否自动选择匹配的剩余部分文本，默认为false	
	storeUrl:null, //用于向下面的tree.Panel中传递数据源
	store:null,//在init中动态更改
	initComponent: function() {		
		this.store=Ext.create('Ext.data.Store', {
			autoLoad:true,//非常重要
			fields: ['id', 'name'],
			proxy:{
				type:'ajax',
				url:this.storeUrl,
				reader:{
					type:'json',//用json方式解析 ，其他方式：'array'
					root:'listItems', 
					totalProperty:'totalCount'
				}	
			}	
		}),
		this.callParent(arguments);
	}
});