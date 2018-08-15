//引入模型定义
Ext.require(['Ext.ux.model.WhbComboTreeModel']);
//WHB：Extjs4 TreeStore本身load是有问题的，此处重写实现刷新
Ext.override(Ext.data.TreeStore, {
    load : function(options) {
        options = options || {};
        options.params = options.params || {};
        var me = this, node = options.node || me.tree.getRootNode(), root;
        if (!node) {
            node = me.setRootNode( {
                expanded : true
            });
        }
        if (me.clearOnLoad) {
            node.removeAll(false);
        }
        Ext.applyIf(options, {
            node : node
        });
        options.params[me.nodeParam] = node ? node.getId() : 'root';
        if (node) {
            node.set('loading', true);
        }
        return me.callParent( [ options ]);
    }
});
Ext.define('Ext.ux.store.WhbTreeStore', {
	extend:'Ext.data.TreeStore',
	alias:'widget.whbtreestore',
	autoLoad:true,
	//设定model为级联树模型
	model:'Ext.ux.model.WhbComboTreeModel',
	//folderSort:true,//是否按照文件夹排序
	//注意：此处的root需要与Ext.tree.Panel中的rootVisible:true配套使用

    nodeParam : 'parentid',//获取下级时，请求参数2015-05-06
	root:{
		id:'0',
        name:'根目录',
        expanded:true 
    }
});

//用法示例备忘
/*Ext.define('Dept.store.DeptStore', {
	extend:'Ext.ux.store.WhbTreeStore',
	alias:'widget.deptstore',	
	model:'Dept.store.TempModel',	
	proxy:{
    	type:'ajax',           
        //url:ADMIN_ROOT_URL+'tree.json'
		url:ADMIN_ROOT_URL+'subSys/sysManager/deptManager/svResponse.asp?act=listTree'
    }  
});*/