//whb:定义系统通用颜色选择器
Ext.define("Ext.ux.form.WhbColorPicker",{
	extend: 'Ext.container.Container',
    alias: 'widget.whbcolorpicker',
    layout: 'hbox',
	initComponent:function() 
	{
		var mefieldLabel = this.fieldLabel;
		var mename = this.name;
		var meheight = this.height;
		var meid = this.id;
		this.items = 
		[
			{
				xtype: 'textfield',
				height: meheight,
				id:meid+'x',
				fieldLabel:mefieldLabel,
				name: mename,
				flex: 1,
				listeners:
				{
					change:function(me, newValue, oldValue)
					{
						me.bodyEl.down('input').setStyle('background-image', 'none');
						me.bodyEl.down('input').setStyle('background-color', newValue);
					}
				}
			},
			{
				xtype:'button',
				width:18,
				height: meheight,
				menu:
				{
					xtype:'colormenu',
					listeners: 
					{
						select: function(picker, color) 	
						{
							var amclr = Ext.getCmp(meid+'x');
							amclr.setValue('#'+color);
						}
					}
				}
			}
		];		
		this.callParent(arguments);//继承时必须和initComponent配套使用，非常重要 
	}
});