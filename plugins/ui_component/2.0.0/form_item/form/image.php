<?php include __DIR__.'/base.php';?>
<?php
$file_name = $component['file_name'];
$file_name = $file_name ? $file_name : 'temp_file';
?>

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
            <input type="file" multiple name="<?=$file_name;?>" class="input-file">
        </span>
    </div>
</div>
