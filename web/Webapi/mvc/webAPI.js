Ext.application({
	name:'WebRoot',// 为应用程序起一个名字,相当于命名空间
	appFolder:"",	
	controllers:['Control'],
	launch:function() {// 程序运行起点处	
		Ext.create('Ext.container.Viewport', {
			layout:'border',//将整个布局分为东西南北中五个部分，除中间其他区域可省
			frame:false,
			items:[
			{	
				xtype:'northview'				
           	},	
           	{
				xtype:'westview'			
			  
         	},           		              
          	{
				xtype:'centerview'			
			  
         	},
			{
			  xtype:'southview'	 //直接使用父类界面                                   
          	}
			]
		});	
		
		whbRemoveMask();//去除总遮罩	
	}
});
