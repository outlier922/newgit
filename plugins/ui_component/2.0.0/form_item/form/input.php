<?php include __DIR__.'/base.php';?>
<div class="row cl" style="<?=$display?>">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <input type="<?=$type?>" class="input-text" datatype='*' id="<?=$name?>"
               name="<?=$name?>" placeholder="<?=$placeholder?>" value="<?=$value?>" <?=$readonly?>
        />
    </div>
</div>
