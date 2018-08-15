//whb:定义系统通用下拉列表框（无限级联树）
Ext.define("Ext.ux.form.WhbComboTree",{
	extend:'Ext.form.field.ComboBox',
	alias:'widget.whbcombotree',
	emptyText:'请选择', //提示信息 
	//listConfig:{resizable:true,minWidth:100,maxWidth:200},
	editable:false,	
	_idValue:null,
	_txtValue:null,
	valueField:'id',//子模块可以将此属性重写为'name',此时传递name而非id
	storeUrl:null, //用于向下面的tree.Panel中传递数据源
	store:new Ext.data.ArrayStore({fields:[],data:[[]]}),//非常重要，不可或缺		
	initComponent:function(){		
		this.callParent(arguments);	
		this.treeObj = Ext.widget('whbtreepanel',{			
			height:180,//非常重要，否则有可能耽误加载显示
			//width:200,
			//以下3个属性非常重要，请勿删除
			rootVisible:false,
			displayField:'name',
			valueField:this.valueField,	
			store:Ext.widget('whbtreestore',{
				//特别注意：treestore的其他属性直接继承自whbtreestore
				proxy:{
				  type:'ajax',
				  url:this.storeUrl //'js/tree.json'
				}
			}),	
			columns:null//非常重要，不可删除	
		}),//treeObj结束
		this.treeRenderId = Ext.id();
		this.tpl = "<tpl><div id='"+this.treeRenderId+"'></div></tpl>"; 
		this.on({
			'expand':function(){
				if(!this.treeObj.rendered&&this.treeObj&&!this.readOnly){
					Ext.defer(function(){
						this.treeObj.render(this.treeRenderId);
					},200,this);
				}
			}
		});//on结束
		this.treeObj.on('itemclick',function(view,rec){
			if(rec){
				//this.setValue(this._txtValue = rec.get('name'));
				this.setValue(rec.get('name'));
				this._idValue = rec.get(this.valueField);
				this.collapse();
			}
		},this);
		//无法自动加载_idValue和_txtValue并传递到后台，统一到control处理
		/*this.treeObj.on('load',function(view,rec){
			//console.log(rec);
			this.setValue('请选择');			
		},this);*/						
	},//initComponent结束
	
	//以下3个方法均为重写Ext.form.field.ComboBox的方法
	getValue:function(){//获取id值
		return this._idValue;
	},
	getTextValue:function(){//获取text值
		return this._txtValue;
	},
	setLocalValue:function(id,txt){//设值
		this._idValue = id;
		this.setValue(this._txtValue = txt);
	}
});