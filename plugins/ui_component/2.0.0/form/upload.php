<?php include __DIR__.'/base.php';?>
<div class="pd-20 <?=$class?>" style="<?=$style?>">
    <form method="post" class="form form-horizontal"
          upload_url="<?=$component['upload_url']?>" combine_url="<?=$component['combine_url']?>"
          form_url="<?=$component['form_url']?>" vod_url="<?=$component['vod_url']?>"
          data-rules="<?=$rules?>" data-messages="<?=$messages?>"
    >
        <?php foreach ($children as $child) _parse($child);?>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <input class="btn btn-primary radius" type="submit" onclick="upload_file()" value="&nbsp;&nbsp;上传&nbsp;&nbsp;">
                <input class="btn btn-primary radius" type="button" id="form-cancel" value="&nbsp;&nbsp;取消&nbsp;&nbsp;">
            </div>
        </div>
    </form>
</div>
<div id="overlay">
    <div class="dptk-progress">
        <div class="dptk-progress-bar text-overflow" id="progress-bar">0</div>
    </div>
</div>
<script>
    function upload_file() {
        showOverlay();
        var e = event || window.event,me = e.target || e.currentTarget;
        e.preventDefault(); e.stopPropagation();
        var form = $(me).closest('form');
        var file_element = $(form).find("input[type='file']")[0];
        var allow_extension = $(file_element).attr('extension');
        var file = file_element.files[0];
        if(!file){
            alert('请选择文件');
            hideOverlay();
            return;
        }
        var name = file.name,size = file.size, fArr = file.name.split('.'),fType = fArr[fArr.length - 1];
        fType = fType.toLowerCase();
        if(allow_extension.indexOf(fType) == -1){
            alert('格式不正确，只允许：'+allow_extension);
            hideOverlay();
            return;
        }


        //上传文件
        var upload_url = $(form).attr('upload_url'), combine_url = $(form).attr('combine_url') ,
            form_url = $(form).attr('form_url'),vod_url = $(form).attr('vod_url');
        var upload_file_name = 'db'+(new Date().getTime())+'.'+fType;
        var file_remote_url = '';
        var item_id = $("#item_id").val();//后期改为可变参数，一同传过去
        var slice_size = 5 * 1024 * 1024,slice_count = Math.ceil(size / slice_size),slice_current = 0;
        var file_door = true;
        var upload_interval = null;
        var begin = 0 , end = 0;
        var begin_time = new Date().getTime(),last_time = begin_time,start_time = begin_time,use_time = 0;
        var speed = 0 ,rate = 0, speed_k = 1.4;
        //合并文件
        function combine_function() {
            $.post(combine_url,{fileName:upload_file_name},function (rec) {
                rec = $.parseJSON(rec);
                var infor = rec['infor'];
                file_remote_url = infor['location'];
                form_function();
            });
        };
        //保存表单
        function form_function() {
            $.post(form_url,{attach_url:file_remote_url,item_id:item_id},function (rec) {
                hideOverlay();
                layer.msg("上传成功");
                setTimeout("parent.location.reload()",1000);
            });
        };
        //上传文件的处理函数
        var file_function = function () {
            if(slice_current == (slice_count - 1)){
                file_door = false;
                clearInterval(upload_interval);
                combine_function();
            }
            else{
                if(file_door){
                    file_door = false;
                    last_time = new Date().getTime();
                    var form_data = new FormData();
                    begin = end;
                    end = begin + slice_size;
                    form_data.append("part",file.slice(begin, end));
                    form_data.append("partNum", slice_current+1);
                    form_data.append("fileName", upload_file_name);

                    $.ajax({
                        url: upload_url,
                        type: "POST",
                        data: form_data,
                        async: true,        //异步
                        processData: false,  //很重要，告诉jquery不要对form进行处理
                        contentType: false,  //很重要，指定为false才能形成正确的Content-Type
                        success: function(rec){
                            file_door = true;
                            slice_current++;
                            begin_time = last_time;
                        }
                    });
                }
                else{
                    use_time = new Date().getTime() - start_time;//已用时
                    if(slice_current == 0){
                        //未计算出平均速度，假设平均速度
                        speed = 0.0002 / slice_count;
                        rate = speed * (new Date().getTime() - last_time);
                    }
                    else{
                        //已计算出平均速度
                        speed = begin / use_time;
                        speed = speed * speed_k;

                        if(slice_current == (slice_count -1)){//正在传最后一块
                            rate = (slice_current + (new Date().getTime() - last_time) * speed / (size - begin) )  / slice_count;
                        }
                        else{
                            rate = (slice_current + (new Date().getTime() - last_time) * speed / slice_size) / slice_count;
                        }
                    }

                    if(rate < (slice_current + 1) / slice_count){
                        if(rate < 1){
                            $("#progress-bar").css("width",(rate * 100).toFixed(1)+'%');
                            if(rate > 0.02){
                                $("#progress-bar").text((rate * 100).toFixed(1)+ '%');
                            }
                        }
                        else{
                            $("#progress-bar").css("width",'100%');
                            $("#progress-bar").text("正在合并文件...");
                        }
                    }
                }
            }
        };
        upload_interval = setInterval(file_function,200);
    }
</script>