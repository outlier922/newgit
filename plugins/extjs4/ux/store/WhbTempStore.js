Ext.define('Ext.ux.store.WhbTempStore', {
	extend:'Ext.data.Store',
	alias:'widget.whbtempstore',
	autoLoad:true,	
	pageSize:ADMIN_PAGE_SIZE,	
	remoteSort:true,//启用远程排序，Ext会传递sort=[{\property\:\regdate\,\direction\:\ASC\}]到后台handler
	storeUrl:null, //
	proxy:{
		type:'ajax',
		//url:this.storeUrl,//不知道为什么不管用,故改用	beforeload 监听模式
		reader:{
			type:'json',//用json方式解析 ，其他方式：'array'
			root:'listItems', 
			totalProperty:'totalCount'
		} 	
	},
	listeners:{
		beforeload:function() {		
			//console.log('++++++'+this.storeUrl);
			this.getProxy().url=this.storeUrl;
		}
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

/*Ext.override(Ext.data.TreeStore, {
    load:function(options) {
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
            node:node
        });
        options.params[me.nodeParam] = node ? node.getId() : 'root';
        if (node) {
            node.set('loading', true);
        }
        return me.callParent([options]);
    }
});
*/