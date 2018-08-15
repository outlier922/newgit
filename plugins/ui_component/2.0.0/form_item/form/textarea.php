<?php include __DIR__.'/base.php';?>
<div class="row cl">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <textarea name="<?=$name;?>" <?=$readonly?> placeholder="<?=$placeholder;?>" type="text" class="textarea" id="<?=$name?>" datatype='*'><?=$value?></textarea>
    </div>
</div>
