Ext.define('Ext.ux.form.WhbCheckCode',{
	extend:'Ext.form.field.Text', 
	alias:'widget.whbcheckcode',
	inputType:'codefield',
	width:140,
	codeUrl:Ext.BLANK_IMAGE_URL,
	isLoader:true,
	onRender:function(ct,position){
		this.callParent(arguments);
		this.codeEl = ct.createChild({ tag: 'img', src: Ext.BLANK_IMAGE_URL});
		this.codeEl.addCls('x-form-code');
		this.codeEl.on('click', this.loadCodeImg, this);	
		this.loadCodeImg();
	},
	alignErrorIcon: function() {
		this.errorIcon.alignTo(this.codeEl, 'tl-tr', [2, 0]);
	},
	loadCodeImg: function() {
		this.codeEl.set({ src: this.codeUrl + '?id=' + Math.random() });
	}
})