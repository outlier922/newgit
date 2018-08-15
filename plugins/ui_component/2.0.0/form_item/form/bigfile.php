<?php include __DIR__.'/base.php';?>
<?php
$file_name = $component['file_name'];//上传文件的名字
$file_name = $file_name ? $file_name : 'temp_file';
$file_upload_url = $component['upload_url'];
$file_upload_url = $file_upload_url ? $file_upload_url : SYS_ROOT."index.php/Webadmin/Base/big_file_upload";
?>

<link rel="stylesheet" type="text/css" href="<?=SYS_UI_PLUGINS;?>webuploader-0.1.5/webuploader.css" />
<script type="text/javascript" src="<?=SYS_UI_PLUGINS;?>webuploader-0.1.5/webuploader.js"></script>

<div class="row cl">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <span class="btn-upload form-group">
            <input class="input-text upload-url" readonly type="text" name="<?=$name;?>" value="<?=$value?>" id="<?=$name?>"
                <?=$required;?>  style="">
            <a href="javascript:void();" class="btn btn-primary radius upload-btn"><i class="Hui-iconfont">&#xe642;</i> 浏览文件</a>
            <input type="file" name="<?=$file_name;?>" class="input-file">
        </span>
        <br />
        <div class="upload-process" style="height: 20px;line-height:20px;width:0;background-color: blue;text-align: center;color:white;"></div>
    </div>
</div>


<script>
    $(function () {
        var uploader = WebUploader.create({
            // swf文件路径
            swf: '<?=SYS_UI_PLUGINS;?>webuploader-0.1.5/Uploader.swf',
            // 文件接收服务端。
            server: '<?=$file_upload_url?>',
            chunked: true,
            chunkSize: 4 * 1024 * 1024,
            threads: 1,

            // formData定义的键，都将通过POST提交到服务器处理程序
            formData: {
                guid: new Date().getTime() + '' + Math.ceil(Math.random() * 1000000)
            },

            // auto 有文件后，立即上传
            auto: true
        });

        // 添加文件
        $(".input-file").change(function () {
            var $file = $(".input-file");
            var oFile = $file.get(0).files[0];
            uploader.addFile(oFile);
        });

        uploader.on("uploadProgress", function (file, percentage) {
            var $process = $(".upload-process");
            $process.css('width', (percentage * 100) + '%');
            $process.text("上传进度："+Math.ceil(percentage * 100) + '%');
        });

        uploader.on("uploadSuccess", function (file, response) {
            $(".upload-process").text("上传完成");
            var filepath = response.infor.filepath;
            $('#<?=$name?>').val(filepath);

            // 删除file
            $(".upload-btn").remove();
            $(".input-file").remove();


        });
    });
</script>


