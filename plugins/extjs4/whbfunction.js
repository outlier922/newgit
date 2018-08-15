/*
| --------------------------------------------------------
| 	文件功能： Extjs核心函数文件
|	程序作者：王海滨（技术部）
|	时间版本：2015-01-30
|	特别提示：本核心函数文件由王海滨专职维护，请勿更改
| --------------------------------------------------------
*/
//给window对象添加自定义加密解密函数
Ext.QuickTips.init();//开启提示
Ext.Loader.setConfig({enabled : true});//开启动态加载
//设置Ext自定义插件的路径
Ext.Loader.setPath('Ext.ux', SYS_EXTJS_URL+'ux/');
//如果想在子模块使用alias别名，则必须在此处声明自定义组件的引用
Ext.require([
//form引用区域
'Ext.ux.form.SearchField','Ext.ux.form.WhbCombo','Ext.ux.form.WhbComboBind','Ext.ux.form.WhbComboTree','Ext.ux.form.WhbKindEditor','Ext.ux.form.WhbColorPicker',
//view引用区域
'Ext.ux.view.WhbFormPanel','Ext.ux.view.WhbQueryForm','Ext.ux.view.WhbFormWindow','Ext.ux.view.WhbSysWindow','Ext.ux.view.WhbListView','Ext.ux.view.WhbTreePanel',
//store引用区域
//'Ext.ux.store.WhbTempStore',
'Ext.ux.store.WhbTreeStore',
//chart引用区域
'Ext.layout.container.Fit','Ext.chart.*'
]);

//系统AJAX请求全局拦截器（999是Session超时的错误编码）
Ext.Ajax.on('requestcomplete',whbCheckSession);
function whbCheckSession(conn,resp,options) {
	//console.log(resp);
	var respText = resp.responseText?Ext.decode(resp.responseText):{"msg":"未知错误，请重试！"};
	if(respText.error_code==999)
	{
		whbMsgErr("数据加载",respText.msg);
	}
}

//函数功能:弹出系统说明窗口
function whbOpenHelper(){
	Ext.widget("whbsyswindow", {
	    title:'系统说明',
	    width:420,
	    iconCls:'help',
		html:"1、《管理后台》必须用IE7.0以上版本浏览器方可正常访问。<br><br> 2、如果模块数据长时间不能够加载，请重新登录系统，然后尝试加载<br>"
	});
}

//打开多幅图片预览窗口
function whbOpenPreview(keytype,keyid){
	Ext.widget("whbsyswindow",{
		width:600,
		height:350,
		bodyStyle:"padding:0px;",
		title:'查看图片',
		//height:405,
		html:'<iframe scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="100%" height="100%" src='+ADMIN_ROOT_URL+'subSys/imageManager/subApp.php?keytype='+keytype+'&keyid='+keyid+'></iframe>'
	}).show();
}
//渲染GridView中附件下载超级模版列
function whbAttachFileRender(keyid, cellmeta, record) {
	if(!record.get('attachfile')) return "无";
	else
	{
		var indexLength=record.get('attachfile').indexOf('uploadfiles');
		var filename=record.get('attachfile').substr(indexLength+12,record.get('attachfile').length-(indexLength+12));
		return "<a href='"+record.get('attachfile')+"' target='_blank' >"+filename+"</a>";
	}
};
//渲染GridView中内容列，添加tooltip提示超长文本
function whbContentRender(value, metaData, record) {
	//只有内容超过10个字才会加tooltip
	if(value!=null && value.length>10)		metaData.tdAttr = 'data-qtip="'+value+'"';
    return value;
};
//渲染GridView中头像列
function whbImageRender(value, metaData, record) {
	 if(value!=null && value.length>3)
	 return "<img src="+value+" width=50 height=50 />";
};
//whb:系统通用打开用户选择框窗口（回传nickname和client_id两个参数）
//vestflag: 0:正式用户 1：马甲用户
function whbOpenClient(vestflag){
 	//创建Store数据源 (此处不能复用Ext.ux.store.WhbTempStore)
	var clientstore = Ext.create('Ext.data.Store', {
		fields: ['id','username','nickname','sex','district_name'],
		autoLoad:true,
		pageSize:ADMIN_PAGE_SIZE,
		remoteSort:true,//启用远程排序，Ext会传递sort=[{\property\:\regdate\,\direction\:\ASC\}]到后台handler
		proxy:{
			type:'ajax',
			url:ADMIN_SERVICE_URL+'client_list&vestflag='+vestflag,
			reader:{
				type:'json',//用json方式解析 ，其他方式：'array'
				root:'listItems',
				totalProperty:'totalCount'
			}
		}
	});
	//创建Grid表格 (此处能复用Ext.ux.view.WhbListView，但是columns必须重写)
	var clientgrid=Ext.widget('whblistview', {
		id:'clientlistview',//定义别名方便在别处引用
		store:clientstore,
		columns:[//必须重写
			{text:"ID",dataIndex:'id',width:40},
			{text:"用户帐号",dataIndex:'username',width:100},
			{text:"用户昵称",dataIndex:'nickname',width:80},
			{text:"性别",dataIndex:'sex',width:60},
			{text:"注册地区",dataIndex:'district_name',flex:1}
		],
		//注意：底部工具条的store需要重写
		dockedItems:[
			{//tbar
				xtype:'toolbar',
				dock:'top',
				items:[
					'->',//右对齐
					{
						emptyText:'用户账户/用户昵称',
						width:280,
						xtype:'searchfield',
						paramName:'searchKey',
						listeners:{
							beforerender:function(combo, record, index) {
								combo.store=Ext.getCmp('clientlistview').getStore();
							}
						}
					}
				]
			},
			{//bbar
					xtype:'pagingtoolbar',
					dock:'bottom',
					store:clientstore,//子模块需要重新定义
					displayInfo:true,
					displayMsg:'显示第 {0} - {1} 条数据,共 {2} 条',
					emptyMsg:"暂无任何数据"
			}
		]
	});
	Ext.widget("whbformwindow", {
		title: '选择用户'+(vestflag==1?'(仅马甲用户)':'(仅正式用户)'),
		iconCls:'client',
		height:300,	//二次list弹窗需要单独定义，防止视图越界
		bodyStyle:'padding:0,overflow-x:hidden;',//此二次弹窗屏蔽间距设置，隐藏水平滚动
		//子类需重写item
		//items:whbform,//items:[{}],
		items:['clientlistview'],
		//通用底部按钮，可重写
		dockedItems:[{
			xtype:'toolbar',
			dock:'bottom',
			items:[ '->',//右对齐
			{
				xtype:'button',
				id:'btnTurnYes',
				text:'确认',
				iconCls:'yes',
				handler:function() {
					//alert("转接中，请稍候");
					var view=Ext.getCmp('clientlistview');
					var row= view.getSelectionModel().getLastSelected();
					if(row){
						 this.ownerCt.ownerCt.close();
						 Ext.getCmp('nickname').setValue(row.get('nickname'));
						 Ext.getCmp('client_id').setValue(row.get('id'));
					}else{
						whbMsgAlert("请首先选择一个用户,然后确认！");
					}
				}
			},
			{
				xtype:'button',
				id:'btnTurnNo',
				text:'取消',
				iconCls:'close',
				handler:function() {
					this.ownerCt.ownerCt.close();
				}
			}]
		}]//dockedItems结束
	});
};
//打开后台用户选择窗口（限制单选）
function whbOpenAdmin(){
 	//创建Store数据源 (此处不能复用Ext.ux.store.WhbTempStore)
	var adminstore = Ext.create('Ext.data.Store', {
		fields:['id','loginname','realname','dept_name'],
		autoLoad:true,
		pageSize:ADMIN_PAGE_SIZE,
		remoteSort:true,//启用远程排序，Ext会传递sort=[{\property\:\regdate\,\direction\:\ASC\}]到后台handler
		proxy:{
			type:'ajax',
			url:ADMIN_SERVICE_URL+'admin_list',
			reader:{
				type:'json',//用json方式解析 ，其他方式：'array'
				root:'listItems',
				totalProperty:'totalCount'
			}
		}
	});
	//创建Grid表格 (此处能复用Ext.ux.view.WhbListView，但是columns必须重写)
	var clientgrid=Ext.widget('whblistview', {
		id:'adminlistview',//定义别名方便在别处引用
		store:adminstore,
		columns:[//必须重写
			{text:"ID",dataIndex:'id',width:40},
			{text:"部门名称",dataIndex:'dept_name',width:100},
			{text:"真实姓名",dataIndex:'realname',width:100},
			{text:"登录帐号",dataIndex:'loginname',flex:1}
		],
		//注意：底部工具条的store需要重写
		dockedItems:[
			{//tbar
				xtype:'toolbar',
				dock:'top',
				items:[
					'->',//右对齐
					{
						emptyText:'部门名称/真实姓名/登录账号',
						width:340,
						xtype:'searchfield',
						paramName:'searchKey',
						listeners:{
							beforerender:function(combo, record, index) {
								combo.store=Ext.getCmp('adminlistview').getStore();
							}
						}
					}
				]
			},
			{//bbar
					xtype:'pagingtoolbar',
					dock:'bottom',
					store:adminstore,//子模块需要重新定义
					displayInfo:true,
					displayMsg:'显示第 {0} - {1} 条数据,共 {2} 条',
					emptyMsg:"暂无任何数据"
			}
		]
	});
	Ext.widget("whbformwindow", {
		title: '选择用户',
		iconCls:'client',
		height:300,	//二次list弹窗需要单独定义，防止视图越界
		bodyStyle:'padding:0,overflow-x:hidden;',//此二次弹窗屏蔽间距设置，隐藏水平滚动
		//子类需重写item
		//items:whbform,//items:[{}],
		items:['adminlistview'],
		//通用底部按钮，可重写
		dockedItems:[{
			xtype:'toolbar',
			dock:'bottom',
			items:[ '->',//右对齐
			{
				xtype:'button',
				id:'btnTurnYes',
				text:'确认',
				iconCls:'yes',
				handler:function() {
					//alert("转接中，请稍候");
					var view=Ext.getCmp('adminlistview');
					var row= view.getSelectionModel().getLastSelected();
					if(row){
						this.ownerCt.ownerCt.close();
						Ext.getCmp('select_name').setValue(row.get('realname'));
						Ext.getCmp('select_id').setValue(row.get('id'));
					}else{
						 whbMsgAlert("请首先选择一个用户,然后确认！");
					}
				}
			},
			{
				xtype:'button',
				id:'btnTurnNo',
				text:'取消',
				iconCls:'close',
				handler:function() {
					this.ownerCt.ownerCt.close();
				}
			}
		  ]
		}]//dockedItems结束
	});
};
//打开后台用户选择窗口（支持多选,特殊情况会用到）
function whbOpenAdminList(){
 	//创建Store数据源 (此处不能复用Ext.ux.store.WhbTempStore)
	var adminstore = Ext.create('Ext.data.Store', {
		fields:['id','loginname','realname','dept_name'],
		autoLoad:true,
		pageSize:ADMIN_PAGE_SIZE,
		remoteSort:true,//启用远程排序，Ext会传递sort=[{\property\:\regdate\,\direction\:\ASC\}]到后台handler
		proxy:{
			type:'ajax',
			url:ADMIN_SERVICE_URL+'admin_list',
			reader:{
				type:'json',//用json方式解析 ，其他方式：'array'
				root:'listItems',
				totalProperty:'totalCount'
			}
		}
	});
	//创建Grid表格 (此处能复用Ext.ux.view.WhbListView，但是columns必须重写)
	var clientgrid=Ext.widget('whblistview', {
		id:'adminlistview',//定义别名方便在别处引用
		store:adminstore,
		multiSelect:true,//允许多选
		selModel:{selType:'checkboxmodel'},  //开启复选框选择模式Ext.selection.CheckboxModel
		columns:[//必须重写
			{text:"ID",dataIndex:'id',width:40},
			{text:"部门名称",dataIndex:'dept_name',width:100},
			{text:"真实姓名",dataIndex:'realname',width:100},
			{text:"登录帐号",dataIndex:'loginname',flex:1}
		],
		//注意：底部工具条的store需要重写
		dockedItems:[
			{//tbar
				xtype:'toolbar',
				dock:'top',
				items:[
					'->',//右对齐
					{
						emptyText:'部门名称/真实姓名/登录账号',
						width:340,
						xtype:'searchfield',
						paramName:'searchKey',
						listeners:{
							beforerender:function(combo, record, index) {
								combo.store=Ext.getCmp('adminlistview').getStore();
							}
						}
					}
				]
			},
			{//bbar
					xtype:'pagingtoolbar',
					dock:'bottom',
					store:adminstore,//子模块需要重新定义
					displayInfo:true,
					displayMsg:'显示第 {0} - {1} 条数据,共 {2} 条',
					emptyMsg:"暂无任何数据"
			}
		]
	});
	Ext.widget("whbformwindow", {
		title: '选择用户',
		iconCls:'client',
		height:300,	//二次list弹窗需要单独定义，防止视图越界
		bodyStyle:'padding:0,overflow-x:hidden;',//此二次弹窗屏蔽间距设置，隐藏水平滚动
		//子类需重写item
		//items:whbform,//items:[{}],
		items:['adminlistview'],
		//通用底部按钮，可重写
		dockedItems:[{
			xtype:'toolbar',
			dock:'bottom',
			items:[ '->',//右对齐
			{
				xtype:'button',
				id:'btnTurnYes',
				text:'确认',
				iconCls:'yes',
				handler:function() {
					//alert("转接中，请稍候");
					var view=Ext.getCmp('adminlistview');
					var selections=view.getSelectionModel().getSelection();
					//console.log(selections);
					 if(selections){
						this.ownerCt.ownerCt.close();
						var idList = [];
						var nameList=[];
						for(var i=0;i<selections.length;i++){
							idList.push(selections[i].get('id'));
							nameList.push(selections[i].get('realname'));
						};
						 //通过这2句实现多选
						 Ext.getCmp('idList').setValue(idList.join(","));
						 Ext.getCmp('nameList').setValue(nameList.join(","));
					 }else{
						 whbMsgAlert("请首先选择一个用户,然后确认！");
					 }
				}
			},
			{
				xtype:'button',
				id:'btnTurnNo',
				text:'取消',
				iconCls:'close',
				handler:function() {
					this.ownerCt.ownerCt.close();
				}
			}
		]
		}]//dockedItems结束
	});
};
//whb:系统通用地点选择接口（高德地图automap）
function whbOpenMap(){
	Ext.widget("whbsyswindow", {
		title:'选择地点',
		bodyStyle:'padding:0',
		maximizable:false,//是否带最大化按钮。
		width:840,
		height:420,
		iconCls:'pc',
		id:'whbmap',
		html:'<iframe  scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="100%" height="100%" src='+ADMIN_ROOT_URL+"//include/automap.inc.php"+'></iframe>'
	});//whbwin_end
}
//whb:系统通用发送短信接口
function whbOpenSms(mobile){
	var whbsmsform=Ext.widget("whbformpanel", {
        items:[
			{
                fieldLabel:'操作人员',
				disabled:true,
				value:getCookie("SYS_REALNAME"),
				name:'operator'//仅前台显示，后台并不接受处理此参数
			},
			{
                fieldLabel:'接收号码',
				emptyText:'请输入11位手机号码',
				minLength:11,
				maxLength:11,
				readOnly:true,//不能是disable
				value:mobile,
				name:'mobile'
			},
			{
				xtype: 'textarea',
				fieldLabel: '短信内容',
				width:250,//为了排版美观
				height:120,
				name: 'content',
				value:'',
				allowBlank:false,//允许为空
				emptyText:'短信内容不超过140字' //提示信息
			}
		]
    });
	Ext.widget("whbformwindow", {
		title:'发送短信',
		width:300,
		iconCls:'mobile',
		items:whbsmsform,
		dockedItems:[{
			xtype:'toolbar',
			dock:'bottom',
			items:[
			'->',//右对齐
			{
				xtype:'button',
				text:'发送',
				iconCls:'yes',
				//如果功能简单，就没必要再分MVC3层架构设计了。
				handler:function(){
					var tempForm=whbsmsform.getForm();
					//表单验证有效,才提交
					if (tempForm.isValid()) {
						tempForm.submit({
							url:ADMIN_SERVICE_URL+'sms_add',
							method:'POST',
							waitTitle:"系统提示",
							waitMsg:'正在发送，请稍侯 ...',
							//后台返回success:true走success
							success:function(parm, action){
								whbMsgAlert("恭喜，发送成功！");
								//this.ownerCt.ownerCt.close();
							},
							//后台返回success:false走failure
							failure:function(parm, action){
								var resp=action.response;//如此设置是为了和普通AJAX代码通用
								var respText = resp.responseText?Ext.decode(resp.responseText):{"msg":"未知错误，请重试！"};
								whbMsgErr('短信发送', respText.msg);
							}
						})//submit_end
					}//isValid_end
				}//handler_end
			},'-',
			{
				xtype:'button',
				text:'取消',
				tooltip:'Reset',
				iconCls:'reset',
				handler:function(m, e) {
					this.ownerCt.ownerCt.close();
				}
			}]
		}]//dockedItems结束
	});//whbwin_end
}
//whb:系统通用发送系统通知接口
function whbOpenNotice(fieldName){
	//fieldName有2种取值 id  或 client_id , 默认id
	if(fieldName==undefined || fieldName=="") 		fieldName="id";
	var view=Ext.getCmp('listview');
	var row= view.getSelectionModel().getLastSelected();
	var whbsubform=Ext.widget("whbformpanel", {
        id:'whbsubform',
		items:[{
            fieldLabel:'操作人员',
			disabled:true,
			value:getCookie("SYS_REALNAME"),
			name:'operator'//仅前台显示，后台并不接受处理此参数
		},
		{
            fieldLabel:'client_id',
			name:'client_id',
			hidden:true,
			value:row.get(fieldName)
		},
		{
            fieldLabel:'接收人',
			disable:true,
			value:row.get('nickname')
		},
		{
			xtype: 'textarea',
			fieldLabel: '通知内容',
			width:250,//为了排版美观
			height:120,
			name: 'content',
			value:'',
			maxLength:140,
			emptyText:'通知内容最多140字' //提示信息
		}]
    });
	Ext.widget("whbformwindow", {
		title:'发送系统通知',
		width:300,
		iconCls:'notice',
		items:whbsubform,
		dockedItems:[{
			xtype:'toolbar',
			dock:'bottom',
			items:[
			'->',//右对齐
			{
				xtype:'button',
				text:'发送',
				iconCls:'yes',
				//如果功能简单，就没必要再分MVC3层架构设计了。
				handler:function(){
					var tempForm=whbsubform.getForm();
					//表单验证有效,才提交
					if (tempForm.isValid()) {
						tempForm.submit({
							url:ADMIN_SERVICE_URL+'notice_add',
							method:'POST',
							waitTitle:"系统提示",
							waitMsg:'正在发送，请稍侯 ...',
							//后台返回success:true走success
							success:function(parm, action){
								whbMsgAlert("恭喜，发送成功！");
								Ext.getCmp('whbsubform').ownerCt.close();
							},
							//后台返回success:false走failure
							failure:function(parm, action){
								var resp=action.response;//如此设置是为了和普通AJAX代码通用
								var respText = resp.responseText?Ext.decode(resp.responseText):{"msg":"未知错误，请重试！"};
								whbMsgErr('发送', respText.msg);
							}
						})//submit_end
					}//isValid_end
				}//handler_end
			},'-',
			{
				xtype:'button',
				text:'取消',
				tooltip:'Reset',
				iconCls:'reset',
				handler:function(m, e) {
					this.ownerCt.ownerCt.close();
				}
			}]
		}]//dockedItems结束
	});//whbwin_end
}
//系统通用打开上传图片接口，keytype根据API来对应
function whbOpenUpload(keytype,rulemsg,showDuration,showOrderby,showContent){
	if(rulemsg==0) rulemsg="640*240";//默认以宽为主，3：2黄金比例
	showDuration=(showDuration==0?true:false);
	showOrderby=(showOrderby==0?true:false);
	showContent=(showContent==0?true:false);
	var view = Ext.getCmp('listview');
	var row = view.getSelectionModel().getLastSelected();
	var whbsubform=Ext.widget("whbformpanel", {
		id:'whbsubform',
        items: [//item_form_begin
			{
				xtype:'displayfield',
				readOnly:true,
				value: '<br><span style="color:red;margin-left:18px">提示：上传前请先将图片处理成规定尺寸（'+rulemsg+'）<br><br></span>'
			},
			{
				xtype: 'filefield',
				fieldLabel: '上传文件',
				name: 'temp_file',
				id:'temp_file',//必须定义
				buttonText:'浏览...'
			},
			{
				xtype : 'textfield',
				hidden:showContent,
				fieldLabel:'内容描述',
				name : 'content',
				value :'无'
			},
			{
				xtype : 'numberfield',
				hidden:showOrderby,
				fieldLabel:'图片次序',
				name : 'orderby',
				maxValue : 3,
				minValue : 0,
				value : 0
			},
			{
				xtype : 'numberfield',
				hidden:showDuration,
				fieldLabel:'时长秒数',
				name : 'duration',
				minValue : 1,
				value : 1
			},
			{
				xtype:'hidden',
				name:'keytype',
				value:keytype
			},
			{
				xtype:'hidden',
				name:'keyid',//此处id不允许为空
				value:row.get('id')
			},
			{
				xtype:'hidden',
				name:'client_id',//此处id不允许为空
				value:1//client_id=1表示后台管理员上传
			}
		]//item_form_end
    });
	Ext.widget("whbformwindow", {
		//重写4个属性
		title:'上传文件',
		//height:160,
		width:360,
		iconCls:'upload',
		items:whbsubform,
		modal:true,
		dockedItems:[{
			xtype:'toolbar',
			dock:'bottom',
			items:[
			'->',//右对齐
			{
				xtype:'button',
				text:'上传',
				iconCls:'yes',
				//如果功能简单，就没必要再分MVC3层架构设计了。
				handler:function(){
					//var tempForm=Ext.getCmp('whbform').getForm();
					var tempForm=whbsubform.getForm();
					//表单验证有效,才提交
					if (tempForm.isValid()) {
						tempForm.submit({
							url:ADMIN_SERVICE_URL+'file_upload',
							method:'POST',
							waitTitle:"系统提示",
							waitMsg:'正在上传文件，请稍侯 ...',
							//后台返回success:true走success
							success:function(parm, action){
								whbMsgAlert("恭喜，文件上传成功！");
								Ext.getCmp('whbsubform').ownerCt.close();
								Ext.getCmp('listview').store.load();
								//this.ownerCt.ownerCt.close();
							},
							//后台返回success:false走failure
							failure:function(parm, action){
								var resp=action.response;//如此设置是为了和普通AJAX代码通用
								var respText = resp.responseText?Ext.decode(resp.responseText):{};
								whbMsgErr('上传文件', respText.msg);
							}
						})//submit_end
					}//isValid_end
				}//handler_end
			},'-',
			{
				xtype:'button',
				text:'取消',
				tooltip:'Reset',
				iconCls:'reset',
				handler:function(m, e) {
					this.ownerCt.ownerCt.close();
				}
			}]
		}]//dockedItems结束
	});//whbwin_end

}
//===================以下为通用数据增，删，改，查区域====================//
//whb:系统通用打开新增数据窗口
function whbAdd(title){
	if(isEmpty(title))	title='数据';
	 Ext.widget("winformview", {
		title : '新增'+title,
		iconCls:'add'
	 }).show();
}
//whb:系统通用保存数据窗口的重载，（方便复杂弹窗嵌套时，根据viewID精准定位listview）
function whbSave(mt,action,viewID){
	//修正可选参数，实现函数重载
	if(action==undefined) action="simple_save&mt="+mt;
	if(viewID==undefined) viewID="listview";
	//console.log(ADMIN_SERVICE_URL+action);
	var tempForm=Ext.getCmp('whbsubform').getForm();

	//表单验证有效,才提交
	if (tempForm.isValid()) {
		tempForm.submit({
			url:ADMIN_SERVICE_URL+action,
			method:'POST',//post方式提交
			submitEmptyText:false,//特别注意：默认会提交emptyTxt
			waitTitle : "系统提示",
			waitMsg: '正在保存数据到服务器，请稍侯 ...',
			//后台返回success:true走failure
			success: function(parm, action){
				whbMsgSucc('数据保存');
				//关闭所打开的窗口并刷新前台listview
				Ext.getCmp('whbsubform').ownerCt.close();
				Ext.getCmp(viewID).store.load();
			},
			//后台返回success:false走failure
			failure: function(parm, action){
				var resp=action.response;//如此设置是为了和普通AJAX代码通用
				var respText = resp.responseText?Ext.decode(resp.responseText):{};
				whbMsgErr('数据保存',respText.msg);
			}
		})//submit_end
	}//isValid_end
}
//whb:系统通用删除数据函数的重载（方便复杂弹窗嵌套时，根据viewID精准定位listview）
function whbRemove(mt,action,viewID){
	//修正可选参数，实现函数重载
	if(action==undefined) action="simple_remove&mt="+mt;
	if(viewID==undefined) viewID="listview";
	//console.log(ADMIN_SERVICE_URL+action);
	var view=Ext.getCmp(viewID);
	var store=view.getStore();//一定要从Listview取STORE,保证前后台联动
	var selections=view.getSelectionModel().getSelection();
	Ext.Msg.confirm("警告",SYS_CONFIRM,function(value){
		if(value == 'yes'){
			var idList = [];
			for(i=0;i<selections.length;i++){
				idList.push(selections[i].get('id'));
			};

			//后台数据库删除,通过AJAX方法实现,只有500错误才走failure
			Ext.Ajax.request({
				url:ADMIN_SERVICE_URL+action,
				params:{"idList":idList.join(",")},	//此处必须命名为id,非常重要
				method:'POST',
				success: function(resp,opts) {
					whbMsgSucc('数据删除');
					store.remove(selections); //前台GRID删除
				},
				failure: function(resp,opts) {
					whbMsgErr('数据删除',"");
				}
			});	//ajax结束
		}//if结束
	});
}
//whb:系统通用删除无限级联数据函数
function whbCascadeRemove(mt){
	var view=Ext.getCmp('listview').getView();
	var row= view.getSelectionModel().getLastSelected();
	//根节点禁止删除
	if(row.get('id')==-1){
		whbMsgAlert("根目录禁止删除");
		return;
	}
	Ext.Msg.confirm("警告",SYS_CONFIRM,function(value){
		if(value == 'yes'){
			//后台数据库删除,通过AJAX方法实现
			Ext.Ajax.request({
				url:ADMIN_SERVICE_URL+'cascade_remove&mt='+mt,
				params:{"id":row.get('id')},
				method:'POST',
				success: function(resp,opts) {
					whbMsgSucc('数据删除');
					Ext.getCmp('listview').store.load();
				},
				failure: function(resp,opts) {
					whbMsgErr('数据删除',"");
				}
			});	//ajax结束
		}//if结束
	});
}

//whb:系统通用打开修改数据窗口
function whbEdit(title){
	if(isEmpty(title))	title='数据';
	Ext.widget("winformview", {
		title : '修改'+title,
		iconCls:'edit'
	}).show();
	var view=Ext.getCmp('listview');
	var row= view.getSelectionModel().getLastSelected();
	var tempForm=Ext.getCmp('whbsubform').getForm();
	tempForm.loadRecord(row);
}
//whb:系统通用打开查阅数据窗口
function whbDetail(title){
	if(isEmpty(title))	title='数据';
	Ext.widget("windetailview", {
		title : '查阅'+title+'详情',
		iconCls:'detail'
	}).show();
	var view=Ext.getCmp('listview');
	var row= view.getSelectionModel().getLastSelected();
	var tempForm=Ext.getCmp('whbsubform').getForm();
	tempForm.loadRecord(row);
}
//导出excel表
function whbExport(table){
	//判断是否需要传递searchkey字段
	var searchKey=Ext.getCmp('searchKey').value;
	if(!searchKey)
		//location.href=ADMIN_SERVICE_URL+"serverHandler/"+table+".php?act=list&export=true";
		location.href=ADMIN_SERVICE_URL+"act=list&export=true";
	else
		location.href=ADMIN_SERVICE_URL+"serverHandler/"+table+".php?act=list&export=true&searchKey="+searchKey;
}
//=======================以下为通用EXT函数区域=======================//
//函数功能：确保IE浏览器的日期型数据兼容
function whbConvertDate(parmStr) {
	if(parmStr == null || parmStr == '0000-00-00 00:00:00')
	{
		return '';
	}
    //如果只要日期parmStr = parmStr.toString().substring(0,10);
	return new Date(Date.parse(parmStr.replace(/-/g,"/")));//返回一个兼容IE的日期型数据
};

//函数功能:去除加载遮罩通用代码
function whbRemoveMask(){
	Ext.get("loading").remove();
	Ext.get("loading-mask").fadeOut({
		duration : 1,
		remove : true
	});
}
//函数功能:系统报错提示信息
function whbMsgAlert(msgStr){
	Ext.Msg.show({
    	title:"系统提示",
   		msg:msgStr,
    	buttons:Ext.Msg.OK,
		icon:Ext.Msg.WARNING
	});
}
//函数功能:AJAX执行成功时的提示函数
function whbMsgSucc(msgType){
	if(isEmpty(msgType))
		msgType="数据保存";
	Ext.Msg.show({
    	title:"系统提示",
   		msg:'恭喜，'+msgType+'成功！',
    	buttons:Ext.Msg.OK,
		icon:Ext.Msg.INFO
	});
}
//函数功能:AJAX执行失败时的提示函数
//errType取值说明:1登录 2数据保存
function whbMsgErr(msgType,msgStr){
	var tempMsg="<br><br>原因：";

	if(isEmpty(msgType))
		msgType="数据保存";
	if(isEmpty(msgStr))
		msgStr="服务器端错误，请重试！";

	tempMsg+=msgStr;

	Ext.Msg.show({
    	title:"系统报错",
   		msg:'Sorry，'+msgType+'失败！'+tempMsg,
    	buttons:Ext.Msg.OK,
		icon:Ext.Msg.WARNING
	});
}

//下载或播放音，视频文件（适用于Grid超级链接列）
function downloadFile(keyid, cellmeta, record) {
	if(!keyid) return "";
	else return "<input type='image' src='"+SYS_EXTJS_URL+"images/next.gif' onclick=\"window.open('"+keyid+"')\"/>";
};
//下载附件（适用于表单超级链接控件）
function downloadAttach() {
	window.open(Ext.getCmp('attachfile').value);
};

function sysOpenTab(id,titleText,hrefTarget){
	var tab = parent.Ext.getCmp('tab_' + id);
	if (!tab) {//如果还没打开过
		tab = Ext.widget("panel",{ //等价于tab = Ext.create("Ext.Panel",{
          id:'tab_' + id,
          closable:true,
          title:titleText,
          //iconCls:node.attributes.iconCls,
          border:false,
          layout:'fit',
		  autoScroll:true,
		  html:'<iframe id="iframeA" scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="100%" height="100%" src="'+hrefTarget+'"></iframe>'
		})//tab结束
     parent.Ext.getCmp('centerview').add(tab);

	 }//if!tab结束
	parent.Ext.getCmp('centerview').setActiveTab(tab);
}

//=======================以下为EXT 换肤函数区域=======================//
//皮肤文件集合store在此配置
var whbSkinStore=Ext.create('Ext.data.Store', {
	fields:['name','value'],
	data:[
		{'name':'商务蓝','value':'default'},
		{'name':'科技黑','value':'slate'}
		//{'name':'优雅银','value':'gray'},
		//{'name':'绅士灰','value':'access'},
	]
})	;
function whbChangeSkin()
{
	var theme;
	theme=getQueryString('theme');
	if(theme==null) theme=getCookie("SYS_SKIN");
	if(theme==null) theme="default";
	setCookie("SYS_SKIN",theme,"h8");//8小时过期
	document.getElementsByTagName("link")[1].href=SYS_EXTJS_URL+"resources/css/ext-all-"+theme+".css";
}
whbChangeSkin();

//=======================以下为通用JS函数区域=======================//
//获取URL传递的GET参数
function getQueryString(urlParm) {
    var reg = new RegExp("(^|&)" + urlParm + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}
//停止页面继续执行
function whbstop(){
	if(!!(window.attachEvent && !window.opera))
	{
		document.writeln("系统运行错误，请联系管理员。【王海滨 QQ:124257207】");
		document.execCommand("stop");
	} else {
		document.writeln("系统运行错误，请联系管理员。【王海滨 QQ:124257207】");
		window.stop();
	}
}
function whbGetTime(){
	var whb_date=new Date();
	var whb_format_time=whb_date.getFullYear()+"-"+String(whb_date.getMonth()+1)+"-"+whb_date.getDate()+" "+whb_date.getHours()+":"+whb_date.getMinutes()+":"+whb_date.getSeconds();
    return whb_format_time;
}
/*
 * 函数功能：获得时间差
 * 返回精度为：s(秒) m(分) h(时) d(天)
 * 调用方法： GetDateDiff("2010-10-11 00:00:00", "2010-10-11 00:01:40", "s")是计算秒数
 */
function GetDateDiff(startTime, endTime, diffType) {
	//将xxxx-xx-xx的时间格式，转换为 xxxx/xx/xx的格式
	startTime = startTime.replace(/-/g, "/");
	endTime = endTime.replace(/-/g, "/");
	//将计算间隔类性字符转换为小写
	diffType = diffType.toLowerCase();
	var sTime = new Date(startTime); //开始时间
	var eTime = new Date(endTime); //结束时间
	//作为除数的数字
	var divNum = 1;
	switch (diffType) {
	case "s":
	divNum = 1000;
	break;
	case "m":
	divNum = 1000 * 60;
	break;
	case "h":
	divNum = 1000 * 3600;
	break;
	case "d":
	divNum = 1000 * 3600 * 24;
	break;
	default:
	break;
	}
	return parseInt((eTime.getTime() - sTime.getTime()) / parseInt(divNum));
}

//JS写文件 (仅限IE使用，并且需要放开Internet选项-》安全-》自定义级别-》对没有标记安全的ACTIVEX进行启用)
function whb_log(filecontent){
	var whb_date=new Date();
	var whb_format_time=whb_date.getFullYear()+"-"+String(whb_date.getMonth()+1)+"-"+whb_date.getDate()+" "+whb_date.getHours()+":"+whb_date.getMinutes()+":"+whb_date.getSeconds();
    var fso, f, s ;
    fso = new ActiveXObject("Scripting.FileSystemObject");
    f = fso.OpenTextFile("D:\\mmzzb.log",8,true); //8表示打开文件并从文件尾部追加，true表示从文件没有则创建
    f.WriteLine("["+whb_format_time+"] "+filecontent);
    f.Close();
}
//函数功能：取随机整数,范围为mini-maxi(包含边界)
function getRndInt(mini,maxi)
{
	return parseInt(Math.random()*(maxi-mini+1)+mini);
}
//函数功能：取定长整数,长度为length
function getRndStr(length){
	var rnd="";
	for(var i=0;i<length;i++)
		rnd+=Math.floor(Math.random()*10);
	return rnd;
}
//从数组中取随机元素方法备忘
//var distirctArray=new Array("山东,济南","北京");
//alert(distirctArray[getRndInt(0,distirctArray.length-1)]);

//函数功能：修正JS replace的BUG
String.prototype.replaceAll  = function(s1,s2){
return this.replace(new RegExp(s1,"gm"),s2);   //这里的gm是固定的，g可能表示global，m可能表示multiple。
}
//函数功能：重写JS的格式化日期函数
//用法示例：var ddd = new Date(); ddd.format('yy-MM-dd hh:mm:ss'));

//函数功能：判断字符串是否为空
function isEmpty(str){
	if(str == null || str.length == 0)
		return true;
	return false;
}
//函数功能：去除字符串前后的空格
function trim(str){
	return str.replace(/(^\s*)|(\s*$)/g, '');
}
//函数功能：判断是否为中文
function isChinese(str){
	var str = str.replace(/(^\s*)|(\s*$)/g,'');
	if (!(/^[\u4E00-\uFA29]*$/.test(str) && (!/^[\uE7C7-\uE7F3]*$/.test(str))))
		return false;
	return true;
}
function isEmail(str){
	var reg=/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/;
	if(reg.test(str))
		return true;
	return false;
}
function isIP(str){
	var reg = /^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/;
	if(reg.test(str))
		return true;
	return false;
}

//////////////cookie相关操作
//设置cookie
//time参数说明：s20是代表20秒 h12代表12小时 d30代表30天
function setCookie(name,value,time){
    var strsec = getsec(time);//注意：使用了中间函数getsec
    var exp = new Date();
    exp.setTime(exp.getTime() + strsec*1);
    //这里的"path=/"代表整站都可以访问，否则不能跨页访问
	document.cookie = name + "="+ escape (whbEncode(value)) + ";expires=" + exp.toGMTString()+";path=/";
}
//读取cookies
function getCookie(name)
{
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
    if(arr=document.cookie.match(reg)) return unescape(whbDecode(arr[2]));
    else return null;
}
//删除cookies
function delCookie(name)
{
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}

//中间函数getsec
function getsec(str){
    var str1=str.substring(1,str.length)*1;
    var str2=str.substring(0,1);
    if (str2=="s"){
        return str1*1000;
    }else if (str2=="h"){
        return str1*60*60*1000;
    }else if (str2=="d"){
        return str1*24*60*60*1000;
    }
}
function whbEncode(parmStr) {
	var str_in = escape(parmStr);
	var num_out = "";
	for(i = 0; i < str_in.length; i++) {
		num_out += str_in.charCodeAt(i) - 23;
	}
	return  num_out;
}

function whbDecode(parmStr) {
	var str_out = "";
	var num_in;
	for(i = 0; i < parmStr.length; i += 2) {
		num_in = parseInt(parmStr.substr(i,[2])) + 23;
		num_in = unescape('%' + num_in.toString(16));
		str_out += num_in;
	}
	return unescape(str_out);
}