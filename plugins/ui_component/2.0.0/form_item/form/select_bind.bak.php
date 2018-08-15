<?php
include dirname(__FILE__).'/base.php';
$init_url = $component -> get_init_url();
?>

<div class="row cl">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <select class="select select_bind" name="<?=$name?>" placeholder="<?=$placeholder?>" id="<?=$name?>"
                value="<?=$value?>" data-init_url="<?=$init_url?>" data-value="<?=$value?>"
        >
            <option value="0">请选择<?=$label?></option>
        </select>
    </div>
</div>
