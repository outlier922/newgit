<?php include __DIR__.'/base.php';?>
<div class="row cl" style="<?=$display?>">
    <label class="form-label col-xs-4 col-sm-2">
        <?=$red_star?>
        <?=$label;?>：
    </label>
    <div class="formControls col-xs-8 col-sm-9">
        <input  class="input-text <?=$class?>" id="<?=$name?>" style="<?=$style?>"  onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
                name="<?=$name?>" placeholder="<?=$placeholder?>" value="<?=$value?>" <?=$readonly?>
        />
    </div>
</div>
