Ext.define('Ext.ux.view.WhbWestView', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.whbwestview',
	region: 'west',
	title: '功能菜单',
	iconCls: 'sysmenu',
	collapsible: true,
	titleCollapse:false,//点击标题不再进行折叠
	split: true,
	border: false,
	frame: true,
	width: 150,
	layout: 'fit',
	initComponent: function() {
		this.callParent()
	}
});