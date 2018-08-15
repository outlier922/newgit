<?php include dirname(__FILE__).'/base.php';?>

<div class="row cl">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <ul>
            <?php
                if($value){
                    $imgurl_items_a = explode(';',$value);
                }
                else{
                    $imgurl_items_a = array();;
                }

                foreach ($imgurl_items_a as $imgurl_s){
                    $imgurl_a = explode(',',$imgurl_s);
                    $imgurl = $imgurl_a[0];
                    $imgurlbig = $imgurl_a[1];
            ?>

            <li class="f-l" style="position: relative;margin:10px;width:72px;height:72px;overflow: hidden;">
                <img class="js_ajax_image" style="width:72px;" src="<?=$imgurl?>" data-imgurl="<?=$imgurl?>" data-imgurlbig="<?=$imgurlbig?>">
                <span style="cursor: pointer;display:block;right:0px;top:-5px;position:absolute;font-size:20px;" onclick="close_image(this,event)" class="Hui-iconfont Hui-iconfont-close2"></span>
            </li>
            <?php }?>
            <li class="f-l">
                <input type="file" id="file" multiple="" style="display:none"/>
                <img onclick="more_image_upload(this,event)" src="<?=SYS_UI_URL?>images/plus.png">
            </li>
        </ul>
        <input id="imgurl_items" type="hidden" value="<?=$value?>" name="imgurl_items">
    </div>
</div>

<script>
    function more_image_upload(me,e) {
        e = e || window.event;
        if($('.js_ajax_image').length >= 5){
            alert("最多上传5张图片");
            return false;
        }

        $(me).prev().click();
    }
    
    $('#file').change(function (e) {
        var me = e.target;
        var formData = new FormData();
        formData.append('file_type', 1);
        formData.append('temp_file', e.target.files[0]);
        if(1){
            var wait_index = layer.load(0,100 * 1000);
            $.ajax({
                url: upload_file_url,
                type: 'POST',
                data: formData,
                processData: false,  // 告诉jQuery不要去处理发送的数据
                contentType: false,  // 告诉jQuery不要去设置Content-Type请求头
                success:function (rec) {
                    layer.close(wait_index);//关闭加载框
                    rec = $.parseJSON(rec);
                    if(rec.success){
                        var content = ''+
                            '<li class="f-l" style="position: relative;margin:10px;width:72px;height:72px;overflow: hidden;">'+
                            '<img class="js_ajax_image" style="width:72px;" src="'+ rec['infor'][1] +'" data-imgurl="'+ rec['infor'][1] +'" data-imgurlbig="'+ rec['infor'][0] +'">'+
                            '<span style="cursor: pointer;display:block;right:0px;top:-5px;position:absolute;font-size:20px;" onclick="close_image(this,event)" class="Hui-iconfont Hui-iconfont-close2"></span>'+
                            '</li>';
                        $(me).parent().before(content);
                        image_items_change();
                    }
                    else{
                        alert("上传失败，原因："+rec.msg);
                    }
                }
            });
        }
    });
    
    function close_image(me,e) {
        $(me).parent().remove();
        image_items_change();
    }
    
    function image_items_change() {
        var image_items_value = "";
        $('.js_ajax_image').each(function (i,node) {
            image_items_value += $(node).data('imgurl')+","+$(node).data('imgurlbig')+";";
        });
        if(image_items_value) image_items_value = image_items_value.substring(0,image_items_value.length-1);
        $("#imgurl_items").val(image_items_value);
    }
</script>
