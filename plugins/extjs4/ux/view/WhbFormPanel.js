//whbmemo：定义系统表单面板形式基类
//子类继承时需要重写items和dockedItems
Ext.define('Ext.ux.view.WhbFormPanel', {		
	extend:'Ext.form.Panel',
	alias:'widget.whbformpanel',//定义别名方便在别处引用	
	border:false,//比较美观	
	frame:false,//比较美观	
	bodyStyle:'background:transparent;padding:5px;text-align:left',
	//html: '<div class=hrDIV></div><font color=red>提示：必输项不能为空，请认真填写。</font>',	
	//注意panel与window的不同panel中用item{baseCls:"x-plain"}	
	layout:'anchor',//用相对宽度方式布局	*/ 
	defaultType:'textfield',//注意：不能放在外层  	                    
	fieldDefaults:{
			anchor:'98%',	  
			//width:250,//注意：此字段与whbsysform的width联动    
        	labelAlign:'right',  
			labelWidth:70,//非常重要,不可或缺(可以借此调整表单元素间距)			
            msgTarget:'side',			
			allowBlank:false,//是否允许为空    
            emptyText:'必填项',
			blankText:'必填项不能为空，请认真填写！'				
    }
	//子类需重写item	
	//items:[{}]	
});

/********************************
//使用方法备忘：

//whbform_begin	
		var whbform=Ext.widget("whbformpanel", {
			id:'whbsubform',
			//item_form_begin				
			items:[	
			    {
			        xtype : 'fieldset',
			        collapsible : true,
			        title : '必填项',
			        layout : 'anchor',
			        //item_fieldset1_begin		
			        items : [//此处堆放需要表格排版的窄列	
			            {
					        xtype: 'container',	
					        anchor:'100%',//表格宽度用百分比表示					
					        layout:{
						        type:'table',//内部容器用表格风格排版
						        columns:2										
					        },  
					        defaultType: 'textfield',//非常重要，请勿删除
					        //item_container_begin				             
					        items: [										
						        {	
							        fieldLabel:'登录账号',														
							        name:'loginName'								
						        },
						        {	
							        fieldLabel:'登录密码',
							        value:'1234',				
							        name:'loginPwd'				
						        },
						        {
							        xtype:"whbcombo",
							        name:'roleID',						
							        fieldLabel:'角色权限',										
							        store:'Role.store.RoleStore'
						        },										
						        {////WHB:着重理解whbcombotree自定义组件的用法(cascade级联)
							        xtype:"whbcombotree",
							        name:'deptID',//便于加载前台listview中的显示值				
							        fieldLabel:'所属部门', 							
							        storeUrl:DeptStoreUrl			
						        },
						        {	
							        fieldLabel:'真实姓名',
							        //value:'无',				
							        name:'realName'				
						        },
						        {	
							        fieldLabel:'手机号码',
							        maxLength:11,
							        //value:'13793161540',				
							        name:'mobile'				
						        }						        					        						        	        																		
					        ]//item_container_end	
                        }
				    ]//item_fset1_end   
		        },
		        
		        //whbmemo:为了节省后台空指针异常处理代码，此处选填信息全部需要设置Value默认值
		        
		        {
			        xtype : 'fieldset',
			        collapsible : true,
			        title : '选填项',			       
			        layout : 'anchor',			     
			        //item_fieldset2_begin		
			        items : [//此处堆放需要表格排版的窄列	
			            {
					        xtype: 'container',	
					        anchor:'100%',//表格宽度用百分比表示					
					        layout:{
						        type:'table',//内部容器用表格风格排版
						        columns:2										
					        },  
					        defaultType: 'textfield',//非常重要，请勿删除					       
					        //item_container_begin				             
					        items: [				        				
						        {//WHB:着重理解直接传name值的用法
							        xtype:"combo",
							        name:'sex',											
							        editable:false,//不可编辑
							        fieldLabel:'用户性别',       							 				
							        displayField:'name',
							        valueField:'name',    	  							
							        store:Ext.create('Ext.data.Store', {								
								        fields:['name'],
								        data:[
									        {"name":"男"},
									        {"name":"女"}							
								        ]						
							        }),
							        
							        //选填信息必须重写以下2个属性。
							        allowBlank:true,
							        emptyText:''
							        		        
						        },				
						        {
							        xtype:'numberfield',
							        name:'age',								        		
							        maxValue:100,
							        minValue:0,
							        fieldLabel:'用户年龄',
							        
							        //选填信息必须重写以下2个属性。
							        allowBlank:true,
							        emptyText:''						        			        
						        },
						        ///datefield赋值不成功，估计是由于ACCESS数据库问题，待调试
						        {	
							        xtype:'datefield',			
							        name:'birthday',							        
							        fieldLabel:'出生年月',
							        format:'Y-m-d',	
							        
							        //选填信息必须重写以下2个属性。
							        allowBlank:true,
							        emptyText:''					        						        
							        													
						        },						
						        {//WHB:着重理解whbcombo自定义组件的用法(非cascade级联)							        
							        xtype:"whbcombo",								
							        name:'isValid',		
							        fieldLabel:'允许登录',
							        //whbmemo:直接设置value不可以，因为传"是"而不传1，后台会报错
							        listeners: {  
							            //whbmemo:监听load不行                                     
                                        "afterRender":function() {                                                                                 
        　　                                this.setValue(1);//                                    
                                         }
                                    },				
						        }																
					        ]//item_container_end	
                        }
                        ,
			            //此处堆放宽列及hidden隐藏域
		                {					
			                xtype: 'textarea',											
			                fieldLabel: '备注说明', 	
			                width:500,//为了排版美观				           	
			                name: 'memo',
			                
			                //每个选填字段必须单独重写
							allowBlank:true,//允许为空
							emptyText:'无' //提示信息				
		                },
		                {
			                xtype:'hidden',
			                name:'id',//用于编辑操作时隐藏传参，非常重要
			                
			                //每个选填字段必须单独重写
							allowBlank:true,//允许为空
							emptyText:'' //提示信息			
		                }
				    ]//item_fset2_end  				     
		        }
		    ]//item_formend      
    	});	//whbform_end	

*/