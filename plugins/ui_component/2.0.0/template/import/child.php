<div class="container-fluid">
    <div class="ml-20 pt-20">
        <form class="form form-inline" action="<?=U('Child/child_temp_file_load')?>">
            <span class="mr-20">
                <input type="number" name="school_id" id="school_id" class="input-text" style="width:150px;" placeholder="输入学校ID">
            </span>
            <span class="btn-upload form-group mr-20">
                <input class="input-text upload-url" readonly type="text" placeholder="选择文件" style="width:200px;">
                <a href="javascript:void();" class="btn btn-success radius upload-btn"><i class="Hui-iconfont">&#xe642;</i> 浏览文件</a>
                <input type="file" id="temp-file" name="temp_file" class="input-file" accept="application/vnd.ms-excel">
            </span>
            <span class="mr-20">
                <input type="submit" id="load-data" class="btn btn-primary radius" value="开始加载" >
            </span>
        </form>
    </div>

    <div class="cl bg-1 bk-gray mt-20 ml-20 pd-5">
        <span class="f-l mr-5">
            <a class="btn btn-primary radius" href="javascript:;" id="import-start" url="<?=U('Child/child_by_one_import')?>">开始导入</a>
        </span>
        <span class="f-l mr-5">
            <a class="btn btn-primary radius" href="javascript:;" id="show-all">显示所有数据</a>
        </span>
        <span class="f-l mr-5">
            <a class="btn btn-primary radius" href="javascript:;" id="show-success">只显示成功数据</a>
        </span>
        <span class="f-l mr-5">
            <a class="btn btn-primary radius" href="javascript:;" id="show-fail">只显示失败数据</a>
        </span>
        <span class="f-l mr-5">
            <a class="btn btn-primary radius" href="javascript:;" id="export-fail" url="<?=U('Child/child_fail_export')?>">导出全部失败数据</a>
        </span>
        <span class="f-r c-red pt-10">注：导入成功的数据，不会二次导入</span>
    </div>
    <div class="mt-10 ml-20" style="height:400px;overflow-y: auto;">
        <table class="table table-border table-bordered table-hover">
            <thead>
            <tr>
                <th class="text-c w20"><input type="checkbox" name="" value=""></th>
                <th class="text-c">手机号</th>
                <th class="text-c">孩子姓名</th>
                <th class="text-c">班级ID</th>
                <th class="text-c w200">导入结果</th>
            </tr>
            </thead>
            <tbody id="import-table-data">
                <tr class="text-c"><td colspan="100">暂无数据</td></tr>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function () {
        $("#load-data").click(function (e) {
            e.preventDefault();e.stopPropagation();

            if(!$("#school_id").val()){alert('学校ID不能为空');return false;}

            var file = $("#temp-file").prop('files')[0];
            if(!file){alert('尚未选择文件');return false;}
            var name = file.name,size = file.size, fArr = file.name.split('.'),fType = fArr[fArr.length - 1];
            if(fType !='xls' && fType !='xlsx'){alert('文件格式不正确');return false;}

            var formData = new FormData();
            formData.append('temp_file', file);
            var wait_index = layer.load(0);
            $.ajax({
                url: $(e.target).closest('form').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,  // 告诉jQuery不要去处理发送的数据
                contentType: false,  // 告诉jQuery不要去设置Content-Type请求头
                success:function (rec) {
                    layer.close(wait_index);//关闭加载框
                    rec = $.parseJSON(rec);
                    if(rec.success){
                        //控制按钮
                        $("#school_id").attr('readonly','readonly');
                        var original_info = rec.infor;
                        var content = "";
                        if(original_info){
                            var info = $.parseJSON(original_info);
                            for(var i=0;i<info.length; i++){
                                content += '<tr class="status" data-original="'+$.base64.btoa(JSON.stringify(info[i]), true)+'">';
                                content += '<td class="text-c"><input type="checkbox" value="'+i+'" name="id[]"></td>';
                                for(var index in info[i]){
                                    content += "<td>"+info[i][index]+"</td>";
                                }
                                content += "<td>未导入</td>";
                                content += "</tr>";
                            }
                            $("#import-table-data").html(content);
                        }
                    }
                    else{
                        alert("加载失败，原因："+rec.msg);
                    }
                }
            });
        });
        $("#import-start").click(function (e) {
            e.preventDefault();e.stopPropagation();
            var me = $(e.target).closest('a'),import_url = $(me).attr("url");
            var checked_checkbox = get_checked_tr('id[]'),checked_length = checked_checkbox.length;
            if(checked_length == 0) {alert("请先勾选数据");return false;}
            var school_id = $("#school_id").val();
            var wait_index = layer.load(0);

            var cur_index = 0,door = true,interval,cur_interval = 1;//定时器控制变量
            var teacher_import = function () {
                if(cur_index >= checked_length){
                    layer.close(wait_index);
                    clearInterval(interval);
                }
                else{
                    if(door){
                        door = false;
                        var tr = $(checked_checkbox[cur_index]).closest('tr');
                        if($(tr).hasClass('status-success')){
                            cur_index++;
                            cur_interval = 1;
                            door = true;
                            return ;
                        }
                        var param = $.parseJSON($.base64.atob($(tr).data('original'),true));
                        param['school_id'] = school_id;

                        $.post(import_url,param,function (rec) {
                            rec = $.parseJSON(rec);
                            cur_index++;
                            cur_interval = 1;
                            door = true;
                            if(rec.success){
                                //添加成功class
                                if(!$(tr).hasClass('status-success'))$(tr).addClass('status-success');
                                $(tr).find("td:last").html('导入成功');
                            }
                            else{
                                if(!$(tr).hasClass('status-fail'))$(tr).addClass('status-fail');
                                $(tr).find("td:last").css("color","red");
                                $(tr).find("td:last").html('导入失败，原因：'+rec.msg);
                            }
                        });
                    }
                    else{
                        var tr = $(checked_checkbox[cur_index]).closest('tr');
                        var interval_count = cur_interval % 6, wait_str='';

                        for(var i=0; i<interval_count; i++) wait_str += "。";
                        $(tr).find("td:last").text("正在导入 "+wait_str);
                        cur_interval++;
                    }
                }
            }
            teacher_import();
            interval = setInterval(teacher_import,150);
        });

        $("#show-all").click(function (e) {
            $(".status").css("display","table-row");
        });

        $("#show-success").click(function (e) {
            $(".status").css("display","none");
            $(".status-success").css("display","table-row");
        });

        $("#show-fail").click(function (e) {
            $(".status").css("display","none");
            $(".status-fail").css("display","table-row");
        });

        $("#export-fail").click(function (e) {
            var me = $(e.target).closest('a');
            var fail_tr = $(".status-fail");
            if(fail_tr.length == 0) {
                alert("未发现错误数据！");return;
            }

            var data = [];
            for(var i=0;i<fail_tr.length; i++){
                var item = fail_tr[i];
                var item_data = $.parseJSON($.base64.atob($(item).data('original'),true));
                item_data['msg'] = $(item).find('td:last').text();
                data.push(item_data);
            }
            var wait_index = layer.load(0);
            $.post($(me).attr('url'),{data:JSON.stringify(data)},function (rec) {
                rec = $.parseJSON(rec);
                layer.close(wait_index);
                location.href = rec.infor['file_url'];
            });
        });
    });
</script>
