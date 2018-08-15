//WebRootCtrl为控制层
Ext.define('WebRoot.controller.Control', {
	extend:'Ext.app.Controller',
	views:['NorthView','WestView','CenterView','SouthView'],// 声明该控制层要用到的view
	init:function() {		
		//初始化控件		 	   
		this.control({    
		    'northview': { 
                init: function(){	                    
                    //console.log('aaaa');	                                
				}
            },         
			//给顶部工具条添加监听函数______begin 					                          
			'northview button[id=btnHelp]': { 
                click: function(){				
					whbOpenHelper();					
				}
            },			
			'northview button[id=btnExit]': { 
                click: function(){					
					 Ext.Msg.confirm("系统提示","确定要退出当前版本么？",function(btnValue){
						if(btnValue == 'yes'){ 		        
							location.href="../index.php";	
						}
					});	
				}
            }			
		   //给顶部工具条添加监听函数______end		   
       });//this.control结束
	}
});