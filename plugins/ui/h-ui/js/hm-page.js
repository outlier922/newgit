/**
 * 页面使用的函数
 */
function hm_inner(me,e) {
    var title = $(me).data('title');
    var width = $(me).data('width');
    var height = $(me).data('height');
    var url = $(me).data('url');
    var operate = $(me).data('operate');//1:顶部；2：row中
    var full = $(me).data('full');
    var checked_count = $(me).data('checked_count');
    var testing = $(me).data('testing');
    var target = $(me).data('target');
    var confirm_content = $(me).data('confirm_content');

    //纠正 width height
    width = width ? width : 800;
    height = height ? height : ($(window).height() - 50);

    //验证选中个数
    if(checked_count && operate == 1){
        //来自top，且需要验证选中个数
        //选中的个数
        var ids = getArray('id[]','checkbox');
        if(ids.length==0){
            layer.msg('您尚未选中任何记录！',{icon: 5,time:1000});
            return;
        }

        if(checked_count == 1){
            if(ids.length != 1){
                layer.msg('您选中记录超过1条！',{icon: 5,time:1000});
                return;
            }
        }
    }

    //验证数据
    if(testing){
        testing = $.parseJSON($.base64.atob(testing, true));
        var data = null;
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
        }
        if(operate == 1){//顶部
            var checked_input = $('input:checkbox[name="id[]"]:checked');
            for(var i=0;i<checked_input.length;i++){
                data = $($(checked_input[i]).closest('tr')[0]).data('data');
                data = $.parseJSON($.base64.atob(data, true));
                if(test_function(testing,data) === false)return;
            }
        }
        else if(operate == 2){//row
            data = $($(me).closest("tr")[0]).data('data');
            data = $.parseJSON($.base64.atob(data, true));
            if(test_function(testing,data) === false)return;
        }
    }

    //url参数
    var url_param_o = {};
    var url_param = $(me).data('url_param');
    if(url_param){
        url_param = $.parseJSON($.base64.atob(url_param, true));
        var tr_data = null;
        //数据来源
        for(var key in url_param){
            if(url_param[key].substr(0,2) == '1_'){//明文
                url_param_o[key] = url_param[key].substr(2);
            }
            else if(url_param[key].substr(0,2) == '2_'){//tr中的数据
                //@TODO 先按单个处理 --后期有业务需要的话，再扩展多个
                if(!tr_data){//tr不存在时获取
                    if(operate == 1)tr_data = $($('input:checkbox[name="id[]"]:checked').closest('tr')[0]).data('data');
                    else tr_data = $($(me).closest("tr")[0]).data('data');
                    tr_data = $.parseJSON($.base64.atob(tr_data, true));
                }
                url_param_o[key] = tr_data[url_param[key].substr(2)];
            }
            else if(url_param[key].substr(0,2) == '3_'){//check_count数据
                var checked_a = getArray('id[]','checkbox');
                url_param_o[key] = checked_a.toString();
            }
        }
    }

    //page参数
    var page_param_o = {};
    var page_param = $(me).data('page_param');
    if(page_param){
        page_param = $.parseJSON($.base64.atob(page_param, true));
        var tr_data = null;
        //数据来源
        for(var key in url_param){
            if(page_param[key].substr(0,2) == '1_'){//明文
                page_param_o[key] = page_param[key].substr(2);
            }
            else if(page_param[key].substr(0,2) == '2_'){//tr中的数据
                //@TODO 先按单个处理 --后期有业务需要的话，再扩展多个
                if(!tr_data){//tr不存在时获取
                    if(operate == 1)tr_data = $($('input:checkbox[name="id[]"]:checked').closest('tr')[0]).data('data');
                    else tr_data = $($(me).closest("tr")[0]).data('data');
                    tr_data = $.parseJSON($.base64.atob(tr_data, true));
                }
                page_param_o[key] = tr_data[page_param[key].substr(2)];
            }
            else if(page_param[key].substr(0,2) == '3_'){//check_count数据
                var checked_a = getArray('id[]','checkbox');
                page_param_o[key] = checked_a.toString();
            }
        }
    }


    if(target == 'inner_frame'){//内部打开一个frame
        if(url_param_o){
            url += http_build_query(url_param_o);
        }
        var layer_index = hm_layer_iframe_show(title,url,width,height);
        if(full) layer.full(layer_index);
    }
    else if(target == 'inner_confirm'){//内部打开一个confirm
        confirm_content = confirm_content ? confirm_content : "确认要“"+title+"”吗？";
        layer.confirm(confirm_content,function(index){
            //异步调用
            var wait_index = layer.load(0,{time: 100000*1000});
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
    else{
        alert(target + '还未定义');
    }
}
function hm_image_show(me,e) {
    var ext_data = $(me).data('data');
    var field_name = $(me).data('name');
    if(ext_data){
        var ext_data = $.parseJSON($.base64.atob(ext_data, true));
        var value = ext_data[field_name+'big'];
    }
    else{
        layer.confirm("大图不存在");
        return;
    }
    layer.open({
        type: 1,
        title: false,
        area:'320px',
        closeBtn: 0,
        shadeClose: true,
        content: "<img src="+value+" width='100%' />"
    });
}
