<?php
$data = $component['data'];
$name = $component['name'];
$values = $component['value'];
?>
<div class="row cl">
<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span><?php echo $component['label']; ?></label>
<div class="formControls col-xs-8 col-sm-9">
<?php foreach ($data as $key=>$value){?>
    <div class="radio-box" style="padding-left: 0px;">
        <input type="radio" id="collect_radio<?=$key?>" name="<?=$name?>" value="<?=$key?>" <?=$values == $key ? 'checked' : '';?> >
        <label for="collect_radio<?=$key?>"><?=$value?></label>
    </div>
<?php }?>
</div>
<div class="col-4"> </div>
</div>

