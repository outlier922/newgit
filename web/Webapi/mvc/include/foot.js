var whb_oddcolor   = "#eeeeee";//定义奇数行颜色
var whb_evencolor  = "#ffffff";//定义偶数行颜色
var whb_mousecolor = "#99ccff";//定义选中行颜色

var whbstudio_tr = document.getElementsByTagName("tr");

function change_color() {
	for (i = 1; i < whbstudio_tr.length + 1; i++) {
		whbstudio_tr[i - 1].style.backgroundColor  = (i % 2 > 0) ? whb_oddcolor: whb_evencolor;
	}	
}

for (var i = 0; i < whbstudio_tr.length; i++) {
	whbstudio_tr[i].onmouseover = function() {
		this.tmpColor = this.style.backgroundColor;		
		this.style.backgroundColor = whb_mousecolor;		
	};
	whbstudio_tr[i].onmouseout = function() {
		this.style.backgroundColor = this.tmpColor ;		
	};
}
window.onload = change_color;//添加监听