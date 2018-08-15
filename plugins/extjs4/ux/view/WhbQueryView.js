Ext.define('Ext.ux.view.WhbQueryView', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.whbqueryview',
	region: 'north',
	border: false,
	frame: false,
	dockedItems: [{
		xtype: 'querybar',
		dock: 'top'
	}],
	items: [{
		xtype: 'panel',
		id: 'searchForm',
		hideMode: 'visibility',
		items: [{
			xtype: 'queryform'
		}]
	}]
});