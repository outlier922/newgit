Ext.define('Ext.ux.view.WhbMenuPanel',{  
	extend:'Ext.tree.Panel', 
	alias:'widget.whbmenupanel',//control中会用到，否则EXT会报substring即未引用错误
	border:false,		
	rootVisible:true,//是否显示根目录 
	height:800,//非常重要，请勿删除	    
	//multiSelect:false,
	//singleExpand:false,//某个时刻只有1个父类展开  
	//useArrows:false,//是否启用箭头形式，默认是加,减号		
	//子类需要重写root
//	root:{
//			text:'首页功能',
//			iconCls:'home',
//			expanded:true,
//			children:[//子菜单							
//			{
//				text:'功能A区',
//				expanded:false,
//				children:[
//				{
//					id:'menuA1',//一定要分配
//					text:'A1',
//					hrefTarget:ADMIN_ROOT_URL+'DebugInfor.aspx?infor=模块正在建设中',
//					leaf:true
//				},
//				{
//					id:'menuA2',//一定要分配
//					text:'A2',
//					hrefTarget:ADMIN_ROOT_URL+'DebugInfor.aspx?infor=模块正在建设中',			
//					leaf:true
//				}
//				]
//			},
//			{
//				text:'功能B区',
//				expanded:false,
//				children:[
//				{
//					id:'menuB1',//一定要分配
//					text:'B1',
//					hrefTarget:ADMIN_ROOT_URL+'DebugInfor.aspx?infor=模块正在建设中',											
//					leaf:true
//				},
//				{
//					id:'menuB2',//一定要分配
//					text:'B2',
//					hrefTarget:ADMIN_ROOT_URL+'DebugInfor.aspx?infor=模块正在建设中',				
//					leaf:true
//				}
//				]			
//			}]//子菜单结束        
//    },//root结束	
	
	//监听菜单点击事件，非常重要
	listeners:{
        itemclick:function(tree,record){	
            //console.log(parent.Ext.getCmp('centerview'));	//特别注意该用法				   
            if (record.get('leaf')) { //如果是叶子
                //alert('ID:'+record.get('id')+'Text:'+record.get('text'));		
                //alert(record.get('hrefTarget'));  
                //alert(record.get('description'));    
                             	
                var tab = parent.Ext.getCmp('tab_' + record.get('id'));
                if (!tab) {//如果还没打开过
                    tab = Ext.widget("panel",{ //等价于tab = Ext.create("Ext.Panel",{ 
                        id:'tab_' + record.get('id'),
                        closable:true,
                        title:record.get('text'),
                        //iconCls:node.attributes.iconCls,          
                        border:false,
                        layout:'fit', 
                        autoScroll:true,            
                        html:'<iframe scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="100%" height="100%" src="'+record.get('hrefTarget')+'"></iframe>' 
	                })//tab结束
                    parent.Ext.getCmp('centerview').add(tab);
                }//if!tab结束
                parent.Ext.getCmp('centerview').setActiveTab(tab);
            }////如果是叶子结束   			
        }//itemclick结束
    },
	initComponent:function() {		
		this.callParent(arguments);//继承时必须和initComponent配套使用，非常重要   
	}	
});