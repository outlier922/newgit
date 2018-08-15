Ext.define('WebRoot.view.WestView', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.westview',
	region: 'west',
	title: '菜单',
	iconCls: 'sysmenu',
	collapsible: true,
	split: true,
	border: false,
	frame: true,
	width: 200,
	layout: 'fit',
	initComponent: function() {
		var tree = Ext.create('Ext.tree.Panel', {
			rootVisible: true,
			root: {
				text: '根目录',
				iconCls: 'menuroot',
				expanded: true,
				children: []
			}
		});
		
		var $_GET = (function(){
		    var url = window.document.location.href.toString();
		    var u = url.split("?");
		    if(typeof(u[1]) == "string"){
		        u = u[1].split("&");
		        var get = {};
		        for(var i in u){
		            var j = u[i].split("=");
		            get[j[0]] = j[1];
		        }
		        return get;
		    } else {
		        return {};
		    }
		})();
		
		//通过URL获取version 
		
		//方法一
		//URL .../Webapi/1.0.0/
		//如：V100
		//var href = window.location.href;
		//var version = href.substring(href.indexOf('ebapi/')).substring(6).replace(/[\.\/]/g,'');
		//方法二
		function getVerson()
		{
			var version = $_GET['v'] ? $_GET['v'] : '1.0.0' ;
			version = version.replace(/[\.\/]/g,'');
			return 'V'+version;
		}
		
		Ext.Ajax.request({
//		    url: SYS_ROOT_URL+'web/Webservice/Action/docs/'+getVerson()+'.api.menu.php',
			url: 'index.menu.php?v='+($_GET['v'] ? $_GET['v'] : '1.0.0' ),
		    async:false,
		    success: function(response, opts) 
		    {
		    	var oResp = Ext.decode(response.responseText);
		    	tree.getRootNode().appendChild(oResp);
		    }
		});
		
		tree.on("itemclick",
		function(view, record) {
			if (record.get('leaf')) {
				var tab = Ext.getCmp('tab_' + record.get('id'));
				if (!tab) {
					tab = Ext.widget("panel", {
						id: 'tab_' + record.get('id'),
						closable: true,
						title: record.get('text'),
						border: false,
						layout: 'fit',
						autoScroll: true,
						html: '<iframe id="iframeA" scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="100%" height="100%" src="' + record.get('hrefTarget') + '"></iframe>'
					})
					 Ext.getCmp('centerview').add(tab);
				}
				Ext.getCmp('centerview').setActiveTab(tab);
			}
		});
		this.items = [tree];
		this.callParent();
	}
});