<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>jquery/plugins/jquery.base64.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>jquery/plugins/jquery.form.min.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>layer/2.4/layer.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>laypage/laypage.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>icheck/jquery.icheck.min.js"></script>

<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>jquery.validation/1.14.0/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>jquery.validation/1.14.0/messages_zh.min.js"></script>

<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>My97DatePicker/WdatePicker.js"></script>

<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>lightbox2/2.8.1/js/lightbox.min.js"></script>

<script type="text/javascript" src="<?php echo SYS_UI_URL?>js/H-ui.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_URL?>js/H-ui.admin.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_URL?>js/admin.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_URL?>js/hm-page.js"></script>

<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>ueditor/1.4.3/ueditor.config.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>ueditor/1.4.3/ueditor.all.min.js"> </script>
<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" src="<?php echo SYS_UI_PLUGINS?>jquery.dptk.combobind/combobind.js"></script>

<script>
    //获取选中的元素
    function get_checked_tr(name,element) {
        return $('input:checkbox[name="'+name+'"]:checked');
    }
    //构建http请求参数
    function http_query_implode(pieces) {
        var key,value,str='';
        for(key in pieces){
            value = pieces[key];
            str += key + '=' + value + '&';
        }
        return str ? str.substr(0,str.length-1) : '';
    }
    //打开弹窗
    function iframe_show(title,url,w,h,close_button) {
        if (title == null || title == '') {
            title = false;
        }
        if (url == null || url == '') {
            url = "404.html";
        }
        if (w == null || w == '') {
            w = 800;
        }
        if (h == null || h == '') {
            h = ($(window).height() - 50);
        }
        if(close_button === null || close_button === '') close_button = 1;

        return layer.open({
            type: 2,
            area: [w+'px', h +'px'],
            fix: true, //固定
            scrollbar: true,//false
            maxmin: true,
            shade:0.4,
            closeBtn: close_button,
            title: title,
            content: url,
        });
    }
    //判断是否选中
    function out_checked(new_value,default_value) {
        return new_value == default_value ? 'selected="selected"' : '';
    }
    //遮罩相关
    //显示遮罩
    function showOverlay() {
        $("#overlay").height(page_height());
        $("#overlay").width(page_width());

        // fadeTo第一个参数为速度，第二个为透明度
        // 多重方式控制透明度，保证兼容性，但也带来修改麻烦的问题
        $("#overlay").fadeTo(0, 0.5);
    }
    //隐藏遮罩
    function hideOverlay() {
        $("#overlay").fadeOut(0);
    }
    //获取页面高度
    function page_height() {
        return document.body.scrollHeight;
    }
    //获取页面宽度
    function page_width() {
        return document.body.scrollWidth;
    }
    //双击选择地图
    $(function(){
        $('.dptk-amap-item').dblclick(function(){
	        var lat = $(this).attr("data-lat");
	    	var lng = $(this).attr("data-lng");
            var index = layer.open({
                type: 2,
                area: ['300px', '300px'],
                fix: false, //不固定
                maxmin: true,
                shade:0.4,
                title: false,
                content: "plugins/amap/amap.php?lat="+lat+"&lng="+lng
            });
            layer.full(index);
        });
    });

    $(function() {
	    //复选框
	    $(".permission-list dt input:checkbox").click(function(){
			$(this).closest("dl").find("dd input:checkbox").prop("checked",$(this).prop("checked"));
			var l = $(this).parents(".permission-list").find("dd").find("input:checked").length;
			if(l==0){
				$(this).parents(".permission-list").find("dt:first input:checkbox").prop("checked",false);
			}else{
				$(this).parents(".permission-list").find("dt:first input:checkbox").prop("checked",true);
			}
		});
        //双击更改选中状态
        $(".data_tr td").dblclick(function(){
            var _checkbox = $(this).parent().find("input:checkbox")[0];
            if(_checkbox){
                _checkbox.checked = !_checkbox.checked;
                $(_checkbox).attr("checked",!$(_checkbox).attr("checked"));
            }
        });

        //关闭表单
        $("#form-cancel").click(function(){
            layer_close('');
        });

        //if(0){
            //加载select-bind的数据
            var select_bind_tags = $(".dptk-js-select-bind");
            if(select_bind_tags.length > 0){
                for(var i=0; i<select_bind_tags.length; i++){
                    var select_bind = select_bind_tags[i];
                    $.post($(select_bind).data('init_url'),{},function (rec) {
                        rec = $.parseJSON(rec);
                        var infor = rec.infor,temp_str = "";
                        $.each(infor,function(index,item){
                            temp_str += "<option value='" + item.id + "' " + out_checked($(select_bind).attr('value'),item.id) +">" + item.name + "</option>";
                        });
                        $(select_bind).append(temp_str);
                    });
                }
            }
        //}


        //加载select_bind的数据
        var select_bind_tags = $(".dptk-js-select-bind");
        if(select_bind_tags.length > 0){
            for(var i=0; i<select_bind_tags.length; i++){
                var select_bind = select_bind_tags[i];
                $.ajax({
                    type : "post",
                    url:$(select_bind).data('init_url'),
                    async : false,
                    success : function(rec){
                        rec = $.parseJSON(rec);
                        var infor = rec.infor,temp_str = "";
                        $.each(infor,function(index,item){
                            temp_str += "<option value='" + item.id + "' " + out_checked($(select_bind).attr('value'),item.id) +">" + item.name + "</option>";
                        });
                        $(select_bind).append(temp_str);
                    }
                });
            }
        }

        //if(0){
            //加载select_bind的数据
            var select_bind_tags = $(".dptk-js-select-bind");
            if(select_bind_tags.length > 0){
                for(var i=0; i<select_bind_tags.length; i++){
                    var select_bind = select_bind_tags[i];
                    $.ajax({
                        type : "post",
                        url:$(select_bind).data('init_url'),
                        async : false,
                        success : function(rec){
                            rec = $.parseJSON(rec);
                            var infor = rec.infor,temp_str = "";
                            $.each(infor,function(index,item){
                                temp_str += "<option value='" + item.id + "' " + out_checked($(select_bind).attr('value'),item.id) +">" + item.name + "</option>";
                            });
                            $(select_bind).append(temp_str);
                        }
                    });
                }
            }

            //级联数据
            var cascade_wrappers = $(".dptk-cascade-wrapper");
            if(cascade_wrappers.length > 0){
                for(var i=0; i<cascade_wrappers.length; i++){
                    var cascade_wrapper = cascade_wrappers[i];
                    var last_parent_id = 0;
                    var cascade_select_binds = $(cascade_wrapper).find('.dptk-js-cascade-select-bind');
                    for(var j=0; j<cascade_select_binds.length; j++){
                        var cascade_select_bind = cascade_select_binds[j];
                        var cascade_select_bind_value = $(cascade_select_bind).attr('value');
                        cascade_select_bind_value = parseInt(cascade_select_bind_value);

                        //只有当前元素的值不是0 或者是第一个元素时才执行
                        if(j === 0 ||  cascade_select_bind_value ){
                            $.ajax({
                                type : "post",
                                url:$(cascade_select_bind).data('init_url'),
                                data:{parentid:last_parent_id},
                                async : false,
                                success : function(rec){
                                    rec = $.parseJSON(rec);
                                    var infor = rec.infor,temp_str = "";
                                    $.each(infor,function(index,item){
                                        temp_str += "<option value='" + item.id + "' " + out_checked(cascade_select_bind_value,item.id) +">" + item.name + "</option>";
                                    });
                                    $(cascade_select_bind).append(temp_str);

                                    last_parent_id = cascade_select_bind_value;
                                }
                            });
                        }
                    }

                    //注册切换事件
                    $(".dptk-js-cascade-select-bind").change(function (e) {
                        var me = e.target;
                        var next_cascade_select = $(me).closest('.dptk-cascade-select-wrapper').nextAll().find('.dptk-js-cascade-select-bind');
                        if(next_cascade_select.length > 0){
                            //清空所有值
                            //next_cascade_select.empty();
                            next_cascade_select.find("option[value!=0]").remove();

                            //添加第一个元素的值
                            var first_next_cascade_select = next_cascade_select[0];
                            $.ajax({
                                type : "post",
                                url:$(first_next_cascade_select).data('init_url'),
                                data:{parentid:$(me).val()},
                                async : false,
                                success : function(rec){
                                    rec = $.parseJSON(rec);
                                    var infor = rec.infor,temp_str = "";
                                    $.each(infor,function(index,item){
                                        temp_str += "<option value='" + item.id + "' " + out_checked($(cascade_select_bind).attr('value'),item.id) +">" + item.name + "</option>";
                                    });
                                    $(first_next_cascade_select).append(temp_str);
                                }
                            });
                        }
                    });
                }
            }

        //}

        //dptk_combobind控件
        $(".dptk-combobind").dptk_combobind();

        //ajax提交表单
        var form_layer = $("#form-layer");
        if(form_layer.length > 0){
            //表单校验的数据
            var rules = $(form_layer).data('rules'),messages=$(form_layer).data('messages');
            rules = rules ? $.parseJSON($.base64.atob(rules, true)) : {};
            messages = messages ? $.parseJSON($.base64.atob(messages, true)) : {};
            //校验表单，并ajax提交
            $(form_layer).validate({
                onkeyup:false,
                focusCleanup:false,
                success:"valid",
                submitHandler:function(form){
                    var wait_index = layer.load(0);
                    $(form_layer).ajaxSubmit({
                        success: function(rec) {	                        
                            layer.close(wait_index);
                            rec = $.parseJSON(rec);
                            if(rec.success){
	                            if(rec.msg){
		                            layer.msg(rec.msg,{icon:6});
	                            }else{
		                            layer.msg("操作成功",{icon:6});
	                            }
                                //刷新上一页
                                setTimeout("parent.location.reload()",1000);
                            }
                            else{
                                layer.alert("操作失败，原因："+rec.msg,{icon:5});
                            }
                        },
                        fail:function () {
                            layer.close(wait_index);
                            //提示
                            layer.alert("网络错误",{icon: 7});
                        }
                    });
                },
                rules:rules,
                messages:messages
            });
        }
        //富文本
        $.Huitab("#tab-system .tabBar span","#tab-system .tabCon","current","click","0");
        $(".rich_edit").each(function (index,rich) {
            var ue = UE.getEditor($(rich).attr("id"),{
                toolbars: [[
                    'fullscreen','undo', 'redo' , '|',
                    'bold', 'forecolor' , 'removeformat', 'autotypeset', 'pasteplain' , '|',
                    'paragraph', 'fontfamily', 'fontsize', '|',
                    'justifyleft', 'justifycenter' , '|',
                    'directionalityltr', 'directionalityrtl', 'indent', '|',
                    'link', 'unlink' ,  '|',
                    'simpleupload', '|','imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                    'wordimage', '|' ,
                    'inserttable', 'insertrow' , 'deleterow', 'insertcol', 'deletecol' , 'mergecells', 'splittocells', '|'
                ]],
                autoHeightEnabled:false,
                elementPathEnabled : false
            });
        });

        //按钮操作
        $(".dptk-js-button").click(function (e) {
            var me = $(e.target).closest('a');
            e.preventDefault();e.stopPropagation();
            var position = $(me).data('position');//按钮的所在位置
            var title = $(me).data('title');
            var full = $(me).data('full');
            var width = $(me).data('width');
            var height = $(me).data('height');
            var checked_count = $(me).data('checked');
            var target = $(me).data('target');
            var confirm_content = $(me).data('confirm_content');
            var url = $(me).data('url');
            var url_param = $(me).data('url_param');
            var testing = $(me).data('testing');
            var close_button = $(me).data('close_button');
            var minmax_button = $(me).data('minmax');
            var checked_checkbox = get_checked_tr('id[]');
            //纠正变量
            width = width ? width : 800;
            height = height ? height : ($(window).height() - 120);

            //验证选中个数
            if(position == 'top'){
                if(checked_count==1){
                    if(checked_checkbox.length==0){
                        layer.msg('您尚未选中任何记录！',{icon: 5,time:1000});
                        return;
                    }
                    else if(checked_checkbox.length>1){
                        layer.msg('您选中记录超过1条！',{icon: 5,time:1000});
                        return;
                    }
                }
                else if(checked_count > 1){
                    if(checked_checkbox.length==0){
                        layer.msg('您尚未选中任何记录！',{icon: 5,time:1000});
                        return;
                    }
                }
            }

            //验证合法性
            if(testing){
                testing = $.parseJSON($.base64.atob(testing, true));
                if(!$.isEmptyObject(testing)){
                    var test_function = function(testing,data){
                        for(var field in testing){
                            for(var test_value in testing[field]){
                                if(test_value == data[field]){
                                    layer.alert(testing[field][test_value]);
                                    return false;
                                }
                            }
                        }
                        return true;
                    };

                    var tr_data = null;
                    if(position == 'top'){
                        for(var i=0; i<checked_checkbox.length; i++){
                            tr_data = $($(checked_checkbox[i]).closest('tr')[0]).data('data');
                            tr_data = $.parseJSON($.base64.atob(tr_data, true));
                            if(test_function(testing,tr_data) === false)return;
                        }
                    }
                    else if(position == 'row'){
                        tr_data = $($(me).closest("tr")[0]).data('data');
                        tr_data = $.parseJSON($.base64.atob(tr_data, true));
                        if(test_function(testing,tr_data) === false)return;
                    }
                }
            }

            //url参数
            var url_param_o = {};
            var url_original_param = $.parseJSON($.base64.atob(url_param, true));
            var tr_data = null;
            for(var key in url_original_param){
                var param_prefix = url_original_param[key].substr(0,2);
                var param_key = url_original_param[key].substr(2);
                if(param_prefix == '1_'){
                    //明文
                    url_param_o[key] = param_key;
                }
                else if(param_prefix == '2_'){
                    //tr中的数据
                    if(position == 'top'){
                        var temp_value = [];
                        for(var i=0; i<checked_checkbox.length; i++){
                            tr_data = $($(checked_checkbox[i]).closest('.data_tr')[0]).data('data');
                            tr_data = $.parseJSON($.base64.atob(tr_data, true));
                            temp_value.push(tr_data[param_key]);
                        }
                        url_param_o[key] = temp_value.toString();
                    }
                    else if(position == 'row'){
                        tr_data = $($(me).closest("tr")[0]).data('data');
                        tr_data = $.parseJSON($.base64.atob(tr_data, true));
                        url_param_o[key] = tr_data[param_key];
                    }
                }
            }

    
            //处理
            if(target == 'inner_frame'){
                //弹窗打开
                var http_query_param = http_query_implode(url_param_o);
                url += (http_query_param ? ('&'+http_query_param) : '');
                var layer_index =iframe_show(title,url,width,height,close_button,minmax_button);
                if(full) layer.full(layer_index);
            }
            else if(target == 'inner_confirm'){
                confirm_content = confirm_content ? confirm_content : "确认要“"+title+"”吗？";
                layer.confirm(confirm_content,{scrollbar:false},function(index){
                    //异步调用
                    var wait_index = layer.load(0);
                    $.post(url,url_param_o,function(res){
                        layer.close(wait_index);//关闭等待框
                        res = $.parseJSON(res);
                        if(!res.success){
                            layer.alert(res.msg);
                        }
                        else{
                            layer.alert(title+"成功",{icon:6},function(){
                                location.reload();
                            });
                        }
                    });
                });
            }
        });
        //查看大图
        $(".dptk-image-big-show").click(function (e) {
            var me = $(e.target).closest('a');
            var imgurlbig = $(me).data('imgurlbig');
            layer.open({
                type: 1,
                title: false,
                area:'500px',
                closeBtn: 0,
                fix:true,
                shadeClose: true,
                scrollbar: true,
                content: "<img src="+imgurlbig+" width='100%' />"
            });
        });
        //调转到详情
        $(".dptk-js-url-get").click(function (e) {
            var me = $(e.target).closest('a'),
                url = $(me).data('url'),target = $(me).data('target'),title = $(me).data('title'),full=$(me).data('full');
            if(target == 'inner_frame'){
                var layer_index = iframe_show(title,url);
                if(full) layer.full(layer_index);
            }
            else if(target == 'frame'){
                Hui_admin_tab(me);
            }
        });
        //签到相关
        $(".dptk-js-sign-wrap td .wrap").click(function (e) {
            e.stopPropagation();
            var that = this;
            var sign = $(that).find('.sign');
            var sign_str = '';
            for(var i=0,sign_item; sign_item=sign[i]; i++){
                sign_str += $(sign_item).data('sign') + '<p>';
            }
            if(sign_str){
                layer.tips(sign_str, that,{tips: 1,time:3000,area:"300px"});
            }
        });
        $(".dptk-js-sign-pre").click(function (e) {
            window.location = $(this).data('url');
        });
        $(".dptk-js-sign-next").click(function (e) {
            window.location = $(this).data('url');
        });
        //按钮中的跳转
        $(".dptk-js-button-a").click(function (e) {
            var me = $(e.target).closest('a');
            e.preventDefault();e.stopPropagation();
            var position = $(me).data('position');//按钮的所在位置
            var title = $(me).data('title');
            var full = $(me).data('full');
            var width = $(me).data('width');
            var height = $(me).data('height');
            var checked_count = $(me).data('checked');
            var target = $(me).data('target');
            var url = $(me).data('url');
            var url_param = $(me).data('url_param');
            var checked_checkbox = get_checked_tr('id[]');

            //纠正变量
            width = width ? width : 800;
            height = height ? height : ($(window).height() - 120);

            //验证选中个数
            if(position == 'top'){
                if(checked_count==1){
                    if(checked_checkbox.length==0){
                        layer.msg('您尚未选中任何记录！',{icon: 5,time:1000});
                        return;
                    }
                    else if(checked_checkbox.length>1){
                        layer.msg('您选中记录超过1条！',{icon: 5,time:1000});
                        return;
                    }
                }
                else if(checked_count > 1){
                    if(checked_checkbox.length==0){
                        layer.msg('您尚未选中任何记录！',{icon: 5,time:1000});
                        return;
                    }
                }
            }

            //url参数
            var url_param_o = {};
            var url_original_param = $.parseJSON($.base64.atob(url_param, true));
            var tr_data = null;
            for(var key in url_original_param){
                var param_prefix = url_original_param[key].substr(0,2);
                var param_key = url_original_param[key].substr(2);
                if(param_prefix == '1_'){
                    //明文
                    url_param_o[key] = param_key;
                }
                else if(param_prefix == '2_'){
                    //tr中的数据
                    if(position == 'top'){
                        var temp_value = [];
                        for(var i=0; i<checked_checkbox.length; i++){
                            tr_data = $($(checked_checkbox[i]).closest('tr')[0]).data('data');
                            tr_data = $.parseJSON($.base64.atob(tr_data, true));
                            temp_value.push(tr_data[param_key]);
                        }
                        url_param_o[key] = temp_value.toString();
                    }
                    else if(position == 'row'){
                        tr_data = $($(me).closest("tr")[0]).data('data');
                        tr_data = $.parseJSON($.base64.atob(tr_data, true));
                        url_param_o[key] = tr_data[param_key];
                    }
                }
            }

            //处理
            var http_query_param = http_query_implode(url_param_o);
            url += (http_query_param ? ('&'+http_query_param) : '');
            if(target == 'inner_frame'){
                //弹窗打开
                var layer_index =iframe_show(title,url,width,height);
                if(full) layer.full(layer_index);
            }
            else if(target == 'frame'){
                $(me).attr('_href',url);
                Hui_admin_tab(me);
            }
        });
    });    

</script>