<?php
include __DIR__.'/base.php';
$init_url = $component['init_url'];
?>

<div class="row cl">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <select class="select input-text dptk-js-select-bind" name="<?=$name?>" placeholder="<?=$placeholder?>" id="<?=$name?>"
                value="<?=$value?>"
                data-init_url="<?=$init_url?>"
        >
        </select>
    </div>
</div>
