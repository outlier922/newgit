<?php
include __DIR__.'/base.php';
$data = $component['data'];
$default = $component['default'];
$value = $component['value'];
?>
<div class="row cl" style="<?=$display?>">
    <label class="form-label col-xs-4 col-sm-2">
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <?php foreach($data as $data_value=>$data_label){ ?>
            <span class="pd-10">
                <input type="checkbox" name="<?=$name?>" value="<?=$data_value?>" <?= in_array($data_value, $value) ? 'checked': '' ?> /> <?=$data_label?>
            </span>
        <?php }?>
    </div>
</div>
