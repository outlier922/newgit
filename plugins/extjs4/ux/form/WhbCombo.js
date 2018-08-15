//whb:定义系统简单下拉列表框（仅含否，是）
Ext.define("Ext.ux.form.WhbCombo", {
	extend: 'Ext.form.field.ComboBox',
	alias: 'widget.whbcombo',
	fieldLabel: '字段名称',
	editable: false,
	displayField: 'name',
	valueField: 'id',
	store:Ext.create('Ext.data.Store', {
		fields: ['id', 'name'],
		data: [{
			"id": 0,
			"name": "否"
		},
		{
			"id": 1,
			"name": "是"
		}]
	}),
	initComponent: function() {
		this.callParent(arguments)
	}
});